<?php
class AdminController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Admin dashboard
    public function dashboard() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        // Get statistics
        $stats = $this->getStats();
        
        // Get recent quiz results
        $recent_results = $this->getRecentResults(5);
        
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/admin/dashboard.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Get application statistics
    private function getStats() {
        $stats = [];

        // Count users by role
        $stmt = $this->db->query("
            SELECT role, COUNT(*) as count 
            FROM utilisateurs 
            GROUP BY role
        ");
        $stats['users_by_role'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Count modules
        $stmt = $this->db->query("SELECT COUNT(*) FROM modules");
        $stats['modules_count'] = $stmt->fetchColumn();

        // Count evaluations
        $stmt = $this->db->query("SELECT COUNT(*) FROM evaluations");
        $stats['evaluations_count'] = $stmt->fetchColumn();
        
        // Ajout de statistiques plus détaillées
        // Nombre de quiz par module
        $stmt = $this->db->query("
            SELECT m.nom, COUNT(e.id) as quiz_count
            FROM modules m
            LEFT JOIN evaluations e ON m.id = e.module_id
            GROUP BY m.id
            ORDER BY quiz_count DESC
            LIMIT 5
        ");
        $stats['quizzes_by_module'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Moyenne des résultats par module
        $stmt = $this->db->query("
            SELECT m.nom, AVG(r.score) as avg_score
            FROM modules m
            JOIN evaluations e ON m.id = e.module_id
            JOIN resultats r ON e.id = r.evaluation_id
            GROUP BY m.id
            ORDER BY avg_score DESC
            LIMIT 5
        ");
        $stats['avg_scores_by_module'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Activité récente (nombre de quiz pris par jour sur les 7 derniers jours)
        $stmt = $this->db->query("
            SELECT DATE(date_soumission) as date, COUNT(*) as count
            FROM resultats
            WHERE date_soumission >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(date_soumission)
            ORDER BY date
        ");
        $stats['recent_activity'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        return $stats;
    }

    // Manage users
    public function users() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        // Get all users
        $stmt = $this->db->query("
            SELECT * FROM utilisateurs 
            ORDER BY role, nom
        ");
        $users = $stmt->fetchAll();

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/admin/users.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Edit user
    public function editUser() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        $user_id = isset($_GET['id']) ? $_GET['id'] : 0;
        $error = null;
        $success = null;

        // Get user details
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            header('Location: index.php?page=admin&action=users');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Update user
                $stmt = $this->db->prepare("
                    UPDATE utilisateurs 
                    SET nom = ?, email = ?, role = ?, promotion = ?, groupe = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['nom'],
                    $_POST['email'],
                    $_POST['role'],
                    $_POST['promotion'],
                    $_POST['groupe'],
                    $user_id
                ]);

                // Update password if provided
                if (!empty($_POST['password'])) {
                    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $this->db->prepare("
                        UPDATE utilisateurs 
                        SET mot_de_passe = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$hashedPassword, $user_id]);
                }

                $success = 'Utilisateur mis à jour avec succès!';
            } catch (PDOException $e) {
                $error = 'Erreur lors de la mise à jour: ' . $e->getMessage();
            }
        }

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/admin/edit_user.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Delete user
    public function deleteUser() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        $user_id = isset($_GET['id']) ? $_GET['id'] : 0;

        // Don't allow deleting yourself
        if ($user_id == $_SESSION['user_id']) {
            header('Location: index.php?page=admin&action=users');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();

                // Delete user's results and responses
                $stmt = $this->db->prepare("
                    DELETE r FROM reponses r
                    JOIN resultats res ON r.resultat_id = res.id
                    WHERE res.etudiant_id = ?
                ");
                $stmt->execute([$user_id]);

                // Delete user's results
                $stmt = $this->db->prepare("DELETE FROM resultats WHERE etudiant_id = ?");
                $stmt->execute([$user_id]);

                // Delete user
                $stmt = $this->db->prepare("DELETE FROM utilisateurs WHERE id = ?");
                $stmt->execute([$user_id]);

                $this->db->commit();
                header('Location: index.php?page=admin&action=users');
                exit;
            } catch (PDOException $e) {
                $this->db->rollBack();
                $error = 'Erreur lors de la suppression: ' . $e->getMessage();
            }
        }

        // Get user details
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            header('Location: index.php?page=admin&action=users');
            exit;
        }

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/admin/delete_user.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Get recent quiz results
    private function getRecentResults($limit = 5) {
        // Convert $limit to an integer to ensure it's safe
        $limit = (int)$limit;
        
        $stmt = $this->db->prepare("
            SELECT r.*, e.titre as evaluation_titre, m.nom as module_nom, 
                   u.nom as etudiant_nom
            FROM resultats r
            JOIN evaluations e ON r.evaluation_id = e.id
            JOIN modules m ON e.module_id = m.id
            JOIN utilisateurs u ON r.etudiant_id = u.id
            ORDER BY r.date_soumission DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Gérer les modules
    public function manageModules() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    
        // Get all modules with teacher names
        $stmt = $this->db->query("
            SELECT m.*, u.nom as enseignant_nom 
            FROM modules m
            JOIN utilisateurs u ON m.enseignant_id = u.id
            ORDER BY m.created_at DESC
        ");
        $modules = $stmt->fetchAll();
    
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/admin/modules.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
    
    // Modifier un module
    public function editModule() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    
        $module_id = isset($_GET['id']) ? $_GET['id'] : 0;
        $error = null;
        $success = null;
    
        // Get module details
        $stmt = $this->db->prepare("SELECT * FROM modules WHERE id = ?");
        $stmt->execute([$module_id]);
        $module = $stmt->fetch();
    
        if (!$module) {
            header('Location: index.php?page=admin&action=modules');
            exit;
        }
    
        // Get all teachers for the dropdown
        $stmt = $this->db->query("SELECT id, nom FROM utilisateurs WHERE role = 'enseignant' ORDER BY nom");
        $teachers = $stmt->fetchAll();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Update module
                $stmt = $this->db->prepare("
                    UPDATE modules 
                    SET nom = ?, description = ?, enseignant_id = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['nom'],
                    $_POST['description'],
                    $_POST['enseignant_id'],
                    $module_id
                ]);
    
                $success = 'Module mis à jour avec succès!';
                
                // Refresh module data
                $stmt = $this->db->prepare("SELECT * FROM modules WHERE id = ?");
                $stmt->execute([$module_id]);
                $module = $stmt->fetch();
            } catch (PDOException $e) {
                $error = 'Erreur lors de la mise à jour du module: ' . $e->getMessage();
            }
        }
    
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/admin/edit_module.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
    
    // Supprimer un module
    public function deleteModule() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    
        $module_id = isset($_GET['id']) ? $_GET['id'] : 0;
        $error = null;
    
        // Get module details
        $stmt = $this->db->prepare("SELECT * FROM modules WHERE id = ?");
        $stmt->execute([$module_id]);
        $module = $stmt->fetch();
    
        if (!$module) {
            header('Location: index.php?page=admin&action=modules');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();
    
                // Delete all responses for evaluations in this module
                $stmt = $this->db->prepare("
                    DELETE r FROM reponses r
                    JOIN questions q ON r.question_id = q.id
                    JOIN evaluations e ON q.evaluation_id = e.id
                    WHERE e.module_id = ?
                ");
                $stmt->execute([$module_id]);
    
                // Delete all options for questions in evaluations in this module
                $stmt = $this->db->prepare("
                    DELETE o FROM options o
                    JOIN questions q ON o.question_id = q.id
                    JOIN evaluations e ON q.evaluation_id = e.id
                    WHERE e.module_id = ?
                ");
                $stmt->execute([$module_id]);
    
                // Delete all questions for evaluations in this module
                $stmt = $this->db->prepare("
                    DELETE q FROM questions q
                    JOIN evaluations e ON q.evaluation_id = e.id
                    WHERE e.module_id = ?
                ");
                $stmt->execute([$module_id]);
    
                // Delete all results for evaluations in this module
                $stmt = $this->db->prepare("
                    DELETE r FROM resultats r
                    JOIN evaluations e ON r.evaluation_id = e.id
                    WHERE e.module_id = ?
                ");
                $stmt->execute([$module_id]);
    
                // Delete all evaluations for this module
                $stmt = $this->db->prepare("DELETE FROM evaluations WHERE module_id = ?");
                $stmt->execute([$module_id]);
    
                // Delete the module
                $stmt = $this->db->prepare("DELETE FROM modules WHERE id = ?");
                $stmt->execute([$module_id]);
    
                $this->db->commit();
                $_SESSION['success'] = "Module supprimé avec succès.";
                header('Location: index.php?page=admin&action=modules');
                exit;
            } catch (PDOException $e) {
                $this->db->rollBack();
                $error = 'Erreur lors de la suppression du module: ' . $e->getMessage();
            }
        }
    
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/admin/delete_module.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
    
    // Gérer les évaluations
    public function manageEvaluations() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    
        // Get all evaluations with module and teacher names
        $stmt = $this->db->query("
            SELECT e.*, m.nom as module_nom, u.nom as enseignant_nom
            FROM evaluations e
            JOIN modules m ON e.module_id = m.id
            JOIN utilisateurs u ON m.enseignant_id = u.id
            ORDER BY e.id DESC
        ");
        $evaluations = $stmt->fetchAll();
    
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/admin/evaluations.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
    
    // Modifier une évaluation
    public function editEvaluation() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    
        $evaluation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $error = null;
        $success = null;
    
        // Get evaluation details
        $stmt = $this->db->prepare("
            SELECT e.*, m.nom as module_nom
            FROM evaluations e
            JOIN modules m ON e.module_id = m.id
            WHERE e.id = ?
        ");
        $stmt->execute([$evaluation_id]);
        $evaluation = $stmt->fetch();
    
        if (!$evaluation) {
            header('Location: index.php?page=admin&action=evaluations');
            exit;
        }
    
        // Get all modules for the dropdown
        $stmt = $this->db->query("
            SELECT m.id, CONCAT(m.nom, ' (', u.nom, ')') as nom_complet
            FROM modules m
            JOIN utilisateurs u ON m.enseignant_id = u.id
            ORDER BY m.nom
        ");
        $modules = $stmt->fetchAll();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Update evaluation
                $stmt = $this->db->prepare("
                    UPDATE evaluations 
                    SET titre = ?, description = ?, module_id = ?, duree = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['titre'],
                    $_POST['description'],
                    $_POST['module_id'],
                    $_POST['duree'],
                    $evaluation_id
                ]);
    
                $success = 'Évaluation mise à jour avec succès!';
                
                // Refresh evaluation data
                $stmt = $this->db->prepare("
                    SELECT e.*, m.nom as module_nom
                    FROM evaluations e
                    JOIN modules m ON e.module_id = m.id
                    WHERE e.id = ?
                ");
                $stmt->execute([$evaluation_id]);
                $evaluation = $stmt->fetch();
            } catch (PDOException $e) {
                $error = 'Erreur lors de la mise à jour de l\'évaluation: ' . $e->getMessage();
            }
        }
    
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/admin/edit_evaluation.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
    
    // Supprimer une évaluation
    public function deleteEvaluation() {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    
        $evaluation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $error = null;
    
        // Get evaluation details
        $stmt = $this->db->prepare("
            SELECT e.*, m.nom as module_nom
            FROM evaluations e
            JOIN modules m ON e.module_id = m.id
            WHERE e.id = ?
        ");
        $stmt->execute([$evaluation_id]);
        $evaluation = $stmt->fetch();
    
        if (!$evaluation) {
            header('Location: index.php?page=admin&action=evaluations');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();
    
                // Delete all responses related to this evaluation
                $stmt = $this->db->prepare("
                    DELETE r FROM reponses r
                    JOIN questions q ON r.question_id = q.id
                    WHERE q.evaluation_id = ?
                ");
                $stmt->execute([$evaluation_id]);
    
                // Delete all results for this evaluation
                $stmt = $this->db->prepare("DELETE FROM resultats WHERE evaluation_id = ?");
                $stmt->execute([$evaluation_id]);
    
                // Delete all options for questions in this evaluation
                $stmt = $this->db->prepare("
                    DELETE o FROM options o
                    JOIN questions q ON o.question_id = q.id
                    WHERE q.evaluation_id = ?
                ");
                $stmt->execute([$evaluation_id]);
    
                // Delete all questions for this evaluation
                $stmt = $this->db->prepare("DELETE FROM questions WHERE evaluation_id = ?");
                $stmt->execute([$evaluation_id]);
    
                // Delete the evaluation
                $stmt = $this->db->prepare("DELETE FROM evaluations WHERE id = ?");
                $stmt->execute([$evaluation_id]);
    
                $this->db->commit();
                $_SESSION['success'] = "Évaluation supprimée avec succès.";
                header('Location: index.php?page=admin&action=evaluations');
                exit;
            } catch (PDOException $e) {
                $this->db->rollBack();
                $error = 'Erreur lors de la suppression de l\'évaluation: ' . $e->getMessage();
            }
        }
    
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/admin/delete_evaluation.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
}