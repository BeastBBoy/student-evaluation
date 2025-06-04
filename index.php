<?php
ob_start(); // Start output buffering
// Define paths first
define('ROOT_PATH', __DIR__);
define('VIEWS_PATH', ROOT_PATH . '/views');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Set session parameters before starting the session
ini_set('session.cookie_lifetime', 3600); // 1 heure
ini_set('session.gc_maxlifetime', 3600); // 1 heure

// Start the session
session_start();

// Inclure les fichiers de configuration
require_once CONFIG_PATH . '/config.php';

// Check for class conflicts and load controllers
if (!class_exists('AuthController')) {
    require_once CONTROLLERS_PATH . '/AuthController.php';
}

if (!class_exists('HomeController')) {
    require_once CONTROLLERS_PATH . '/home_controller.php';
}

// Récupérer les paramètres de l'URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Système de routage
// Add these to your existing controller includes
require_once CONTROLLERS_PATH . '/ModuleController.php';
require_once CONTROLLERS_PATH . '/EvaluationController.php';
require_once CONTROLLERS_PATH . '/StudentController.php';
// Add TeacherController to your existing controller includes
require_once CONTROLLERS_PATH . '/TeacherController.php';
// Add AdminController to your existing controller includes
require_once CONTROLLERS_PATH . '/AdminController.php';

// Update your routing switch statement to include these new controllers
switch ($page) {
    case 'home':
        $controller = new HomeController();
        break;
        
    case 'auth':
        $controller = new AuthController();
        break;
        
    case 'modules':
        $controller = new ModuleController();
        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'view':
                $controller->view();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->index();
                break;
        }
        break;
        
    // In the evaluations case of your switch statement
    case 'evaluations':
        $controller = new EvaluationController();
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'createQuiz':
                $controller->createQuiz();
                break;
            case 'editQuiz': // Nouvelle action
                $controller->editQuiz();
                break;
            case 'take':
                $controller->take();
                break;
            case 'result':
                $controller->result();
                break;
            case 'results':
                $controller->results();
                break;
            case 'listQuizzes':
                $controller->listQuizzes();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->listQuizzes();
                break;
        }
        break;
        
    case 'student':
        $controller = new StudentController();
        break;
        
    case 'teacher':
        $controller = new TeacherController();
        switch ($action) {
            case 'dashboard':
                $controller->dashboard();
                break;
            // Add other teacher actions here as needed
            default:
                $controller->dashboard();
                break;
        }
        break;
        
    case 'admin':
        $controller = new AdminController();
        switch ($action) {
            case 'dashboard':
                $controller->dashboard();
                break;
            case 'users':
                $controller->users();
                break;
            case 'editUser':
                $controller->editUser();
                break;
            case 'deleteUser':
                $controller->deleteUser();
                break;
            // Nouvelles actions pour gérer les modules
            case 'modules':
                $controller->manageModules();
                break;
            case 'editModule':
                $controller->editModule();
                break;
            case 'deleteModule':
                $controller->deleteModule();
                break;
            // Nouvelles actions pour gérer les évaluations
            case 'evaluations':
                $controller->manageEvaluations();
                break;
            case 'editEvaluation':
                $controller->editEvaluation();
                break;
            case 'deleteEvaluation':
                $controller->deleteEvaluation();
                break;
            default:
                $controller->dashboard();
                break;
        }
        break;
        
    case 'profile':
        require_once CONTROLLERS_PATH . '/UserController.php';
        $controller = new UserController();
        $controller->profile();
        break;
        
    default:
        // Page non trouvée
        header('HTTP/1.0 404 Not Found');
        echo "<div class='alert alert-danger'>Page non trouvée</div>";
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/home/index.php';
        require_once VIEWS_PATH . '/templates/footer.php';
        exit;
}

// Appeler la méthode d'action sur le contrôleur
if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    // Action non trouvée
    header('HTTP/1.0 404 Not Found');
    echo "<div class='alert alert-danger'>Action non trouvée</div>";
    require_once VIEWS_PATH . '/templates/header.php';
    require_once VIEWS_PATH . '/home/index.php';
    require_once VIEWS_PATH . '/templates/footer.php';
}
