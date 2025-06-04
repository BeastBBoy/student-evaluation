<?php
require_once 'config/database.php';

class AuthController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Modified register method to handle both GET and POST requests
    public function register() {
        // For GET requests, just display the registration form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process form submission
            try {
                // Validate passwords match
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    $error = 'Les mots de passe ne correspondent pas.';
                } else {
                    // Check if email already exists
                    $stmt = $this->db->prepare("SELECT id FROM utilisateurs WHERE email = ?");
                    $stmt->execute([$_POST['email']]);
                    if ($stmt->fetch()) {
                        $error = 'Cette adresse email est déjà utilisée.';
                    } else {
                        // Hash password
                        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        
                        // Set promotion and groupe based on role
                        $role = $_POST['role'] ?? 'etudiant';
                        $promotion = ($role === 'enseignant') ? 'N/A' : $_POST['promotion'];
                        $groupe = ($role === 'enseignant') ? 'N/A' : $_POST['groupe'];
                        
                        // Insert new user
                        $stmt = $this->db->prepare("
                            INSERT INTO utilisateurs (nom, date_naissance, promotion, groupe, email, mot_de_passe, role)
                            VALUES (?, ?, ?, ?, ?, ?, ?)
                        ");
                        
                        $stmt->execute([
                            $_POST['nom'],
                            $_POST['date_naissance'],
                            $promotion,
                            $groupe,
                            $_POST['email'],
                            $hashedPassword,
                            $role
                        ]);
                        $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                    }
                }
            } catch (PDOException $e) {
                $error = 'Erreur lors de l\'inscription: ' . $e->getMessage();
            }
        }

        // Display the registration form with any error/success messages
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/auth/register.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    public function login() {
        // Process login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get user input
                $email = $_POST['email'];
                $password = $_POST['password'];
                
                // Find user by email
                $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                // Verify user exists and password is correct
                if ($user && password_verify($password, $user['mot_de_passe'])) {
                    // Login successful - store user data in session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nom'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Redirect to dashboard based on role
                    // Redirect to dashboard based on role
                    if ($user['role'] === 'enseignant') {
                        header('Location: index.php?page=teacher&action=dashboard');
                    } else if ($user['role'] === 'etudiant') {
                        header('Location: index.php?page=student&action=dashboard');
                    } else if ($user['role'] === 'admin') {
                        header('Location: index.php?page=admin&action=dashboard');
                    } else {
                        header('Location: index.php?page=home');
                    }
                    exit;
                } else {
                    // Login failed
                    $error = 'Email ou mot de passe incorrect.';
                }
            } catch (PDOException $e) {
                $error = 'Erreur lors de la connexion: ' . $e->getMessage();
            }
        }
        
        // Display the login form with any error messages
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/auth/login.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
    
    public function logout() {
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header('Location: index.php?page=auth&action=login');
        exit;
    }
}