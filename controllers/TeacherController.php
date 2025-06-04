<?php
class TeacherController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Teacher dashboard
    public function dashboard() {
        // Check if user is logged in and is a teacher
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        // Get teacher's modules
        $stmt = $this->db->prepare("
            SELECT * FROM modules 
            WHERE enseignant_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $modules = $stmt->fetchAll();

        // Get recent evaluations
        $stmt = $this->db->prepare("
            SELECT e.*, m.nom as module_nom
            FROM evaluations e
            JOIN modules m ON e.module_id = m.id
            WHERE m.enseignant_id = ?
            ORDER BY e.id DESC
            LIMIT 5
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $recent_evaluations = $stmt->fetchAll();

        // Get recent results
        $stmt = $this->db->prepare("
            SELECT r.*, u.nom as etudiant_nom, e.titre as evaluation_titre
            FROM resultats r
            JOIN utilisateurs u ON r.etudiant_id = u.id
            JOIN evaluations e ON r.evaluation_id = e.id
            JOIN modules m ON e.module_id = m.id
            WHERE m.enseignant_id = ?
            ORDER BY r.date_soumission DESC
            LIMIT 10
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $recent_results = $stmt->fetchAll();

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/teacher/dashboard.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
}