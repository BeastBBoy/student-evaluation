<?php
class StudentController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Student dashboard
    public function dashboard() {
        // Check if user is logged in and is a student
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'etudiant') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        // Get available modules
        $stmt = $this->db->prepare("
            SELECT m.*, u.nom as enseignant_nom
            FROM modules m
            JOIN utilisateurs u ON m.enseignant_id = u.id
            ORDER BY m.nom
        ");
        $stmt->execute();
        $modules = $stmt->fetchAll();

        // Get student's results
        $stmt = $this->db->prepare("
            SELECT r.*, e.titre as evaluation_titre, m.nom as module_nom
            FROM resultats r
            JOIN evaluations e ON r.evaluation_id = e.id
            JOIN modules m ON e.module_id = m.id
            WHERE r.etudiant_id = ?
            ORDER BY r.date_soumission DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $resultats = $stmt->fetchAll();

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/student/dashboard.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // View available evaluations for a module
    public function moduleEvaluations() {
        // Check if user is logged in and is a student
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'etudiant') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

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
            header('Location: index.php?page=student&action=dashboard');
            exit;
        }

        // Get available evaluations
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("
            SELECT e.*,
                   (SELECT COUNT(*) FROM resultats WHERE etudiant_id = ? AND evaluation_id = e.id) as already_taken
            FROM evaluations e
            WHERE e.module_id = ? AND e.date_debut <= ? AND e.date_fin >= ?
            ORDER BY e.date_debut
        ");
        $stmt->execute([$_SESSION['user_id'], $module_id, $now, $now]);
        $evaluations = $stmt->fetchAll();

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/student/module_evaluations.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
}