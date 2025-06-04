<?php
class ModuleController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // List all modules
    public function index() {
        // Get all modules
        $stmt = $this->db->query("
            SELECT m.*, u.nom as enseignant_nom 
            FROM modules m
            JOIN utilisateurs u ON m.enseignant_id = u.id
            ORDER BY m.created_at DESC
        ");
        $modules = $stmt->fetchAll();

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/modules/index.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Create a new module
    public function create() {
        // Check if user is logged in and is a teacher or admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'enseignant' && $_SESSION['user_role'] !== 'admin')) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if form has already been submitted (using a session variable)
            if (!isset($_SESSION['form_submitted']) || $_SESSION['form_submitted'] !== $_POST['form_submitted']) {
                try {
                    // Create module
                    $stmt = $this->db->prepare("
                        INSERT INTO modules (nom, description, enseignant_id)
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([
                        $_POST['nom'],
                        $_POST['description'],
                        $_SESSION['user_id']
                    ]);

                    // Store form submission identifier in session
                    $_SESSION['form_submitted'] = $_POST['form_submitted'];
                    
                    $success = 'Module créé avec succès!';
                    
                    // Optional: Redirect to modules list to prevent refresh issues
                    // header('Location: index.php?page=modules');
                    // exit;
                } catch (PDOException $e) {
                    $error = 'Erreur lors de la création du module: ' . $e->getMessage();
                }
            } else {
                $success = 'Module déjà créé!';
            }
        } else {
            // Reset form submission tracking for GET requests
            unset($_SESSION['form_submitted']);
        }

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/modules/create.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // View a specific module
    public function view() {
        $module_id = isset($_GET['id']) ? $_GET['id'] : 0;

        // Get module details
        $stmt = $this->db->prepare("
            SELECT m.*, u.nom as enseignant_nom 
            FROM modules m
            JOIN utilisateurs u ON m.enseignant_id = u.id
            WHERE m.id = ?
        ");
        $stmt->execute([$module_id]);
        $module = $stmt->fetch();

        if (!$module) {
            header('Location: index.php?page=modules');
            exit;
        }

        // Get evaluations for this module
        $stmt = $this->db->prepare("
            SELECT * FROM evaluations 
            WHERE module_id = ?
            ORDER BY id DESC
        ");
        $stmt->execute([$module_id]);
        $evaluations = $stmt->fetchAll();

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/modules/view.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Edit a module
    public function edit() {
        // Check if user is logged in and is a teacher or admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'enseignant' && $_SESSION['user_role'] !== 'admin')) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    
        $module_id = isset($_GET['id']) ? $_GET['id'] : 0;
    
        // Si c'est un admin, on peut modifier n'importe quel module
        if ($_SESSION['user_role'] === 'admin') {
            $stmt = $this->db->prepare("SELECT * FROM modules WHERE id = ?");
            $stmt->execute([$module_id]);
        } else {
            // Si c'est un enseignant, vérifier que le module lui appartient
            $stmt = $this->db->prepare("
                SELECT * FROM modules 
                WHERE id = ? AND enseignant_id = ?
            ");
            $stmt->execute([$module_id, $_SESSION['user_id']]);
        }
        
        $module = $stmt->fetch();
    
        if (!$module) {
            header('Location: index.php?page=modules');
            exit;
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Update module
                $stmt = $this->db->prepare("
                    UPDATE modules 
                    SET nom = ?, description = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['nom'],
                    $_POST['description'],
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
        require_once VIEWS_PATH . '/modules/edit.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Delete a module
    public function delete() {
        // Check if user is logged in and is a teacher or admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'enseignant' && $_SESSION['user_role'] !== 'admin')) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        $module_id = isset($_GET['id']) ? $_GET['id'] : 0;
    
        // Si c'est un admin, on peut supprimer n'importe quel module
        if ($_SESSION['user_role'] === 'admin') {
            $stmt = $this->db->prepare("SELECT * FROM modules WHERE id = ?");
            $stmt->execute([$module_id]);
        } else {
            // Si c'est un enseignant, vérifier que le module lui appartient
            $stmt = $this->db->prepare("
                SELECT * FROM modules 
                WHERE id = ? AND enseignant_id = ?
            ");
            $stmt->execute([$module_id, $_SESSION['user_id']]);
        }
        
        $module = $stmt->fetch();
    
        if (!$module) {
            header('Location: index.php?page=modules');
            exit;
        }
    
        // Le reste du code reste inchangé
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
                header('Location: index.php?page=modules');
                exit;
            } catch (PDOException $e) {
                $this->db->rollBack();
                $error = 'Erreur lors de la suppression du module: ' . $e->getMessage();
            }
        }

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/modules/delete.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
}