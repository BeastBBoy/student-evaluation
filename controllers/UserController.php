<?php
class UserController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Display and update user profile
    public function profile() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $error = null;
        $success = null;

        // Get user details
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            header('Location: index.php?page=home');
            exit;
        }

        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Update basic info
                $stmt = $this->db->prepare("
                    UPDATE utilisateurs 
                    SET nom = ?, email = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['nom'],
                    $_POST['email'],
                    $user_id
                ]);

                // Update password if provided
                if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
                    if ($_POST['password'] !== $_POST['confirm_password']) {
                        $error = 'Les mots de passe ne correspondent pas.';
                    } else {
                        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $stmt = $this->db->prepare("
                            UPDATE utilisateurs 
                            SET mot_de_passe = ?
                            WHERE id = ?
                        ");
                        $stmt->execute([$hashedPassword, $user_id]);
                    }
                }

                if (!$error) {
                    $success = 'Profil mis à jour avec succès!';
                    
                    // Refresh user data
                    $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $user = $stmt->fetch();
                }
            } catch (PDOException $e) {
                $error = 'Erreur lors de la mise à jour: ' . $e->getMessage();
            }
        }

        // Get user's quiz results if they are a student
        $results = [];
        if ($_SESSION['user_role'] === 'etudiant') {
            $stmt = $this->db->prepare("
                SELECT r.*, e.titre as evaluation_titre, m.nom as module_nom
                FROM resultats r
                JOIN evaluations e ON r.evaluation_id = e.id
                JOIN modules m ON e.module_id = m.id
                WHERE r.etudiant_id = ?
                ORDER BY r.date_soumission DESC
            ");
            $stmt->execute([$user_id]);
            $results = $stmt->fetchAll();
        }

        // Get user's modules if they are a teacher
        $modules = [];
        if ($_SESSION['user_role'] === 'enseignant') {
            $stmt = $this->db->prepare("
                SELECT * FROM modules
                WHERE enseignant_id = ?
                ORDER BY nom ASC
            ");
            $stmt->execute([$user_id]);
            $modules = $stmt->fetchAll();
        }

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/users/profile.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
}