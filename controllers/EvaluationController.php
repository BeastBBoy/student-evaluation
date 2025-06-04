<?php
class EvaluationController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    // Create a new evaluation
    public function create() {
        // Check if user is logged in and is a teacher
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }

        $module_id = isset($_GET['module_id']) ? $_GET['module_id'] : 0;

        // Check if module exists and belongs to this teacher
        $stmt = $this->db->prepare("
            SELECT * FROM modules 
            WHERE id = ? AND enseignant_id = ?
        ");
        $stmt->execute([$module_id, $_SESSION['user_id']]);
        $module = $stmt->fetch();

        if (!$module) {
            header('Location: index.php?page=modules&action=dashboard');
            exit;
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();

                // Create evaluation
                // In the create method, modify the SQL statement and parameters
                // Around line 30-40
                $stmt = $this->db->prepare("
                    INSERT INTO evaluations (titre, description, module_id, duree)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $_POST['titre'],
                    $_POST['description'],
                    $module_id,
                    $_POST['duree']
                ]);

                $evaluation_id = $this->db->lastInsertId();

                // Process questions
                foreach ($_POST['questions'] as $index => $question) {
                    // Skip empty questions
                    if (empty($question['texte'])) continue;

                    // Insert question - make sure this is only executed once per question
                    $stmt = $this->db->prepare("
                        INSERT INTO questions (evaluation_id, texte, type, points)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $evaluation_id,
                        $question['texte'],
                        $question['type'],
                        $question['points']
                    ]);

                    $question_id = $this->db->lastInsertId();

                    // Process options for QCM questions
                    if ($question['type'] === 'qcm' && isset($question['options'])) {
                        foreach ($question['options'] as $option) {
                            // Skip empty options
                            if (empty($option['texte'])) continue;

                            $est_correcte = isset($option['est_correcte']) ? 1 : 0;

                            $stmt = $this->db->prepare("
                                INSERT INTO options (question_id, texte, est_correcte)
                                VALUES (?, ?, ?)
                            ");
                            $stmt->execute([
                                $question_id,
                                $option['texte'],
                                $est_correcte
                            ]);
                        }
                    }
                }

                $this->db->commit();
                $success = 'Évaluation créée avec succès!';
            } catch (PDOException $e) {
                $this->db->rollBack();
                $error = 'Erreur lors de la création de l\'évaluation: ' . $e->getMessage();
            }
        }

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/evaluations/create.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Method for students to take a quiz
    public function take() {
        // Check if user is logged in and is a student
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'etudiant') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    
        $quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
        // Check if quiz exists
        $stmt = $this->db->prepare("
            SELECT e.*, m.nom as module_nom 
            FROM evaluations e
            JOIN modules m ON e.module_id = m.id
            WHERE e.id = ?
        ");
        $stmt->execute([$quiz_id]);
        $quiz = $stmt->fetch();
    
        if (!$quiz) {
            header('Location: index.php?page=evaluations&action=listQuizzes');
            exit;
        }
    
        // Check if student has already taken this quiz
        $stmt = $this->db->prepare("
            SELECT * FROM resultats 
            WHERE etudiant_id = ? AND evaluation_id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $quiz_id]);
        $existing_result = $stmt->fetch();
    
        if ($existing_result) {
            header('Location: index.php?page=evaluations&action=result&id=' . $existing_result['id']);
            exit;
        }
    
        // Get quiz questions
        $stmt = $this->db->prepare("
            SELECT * FROM questions 
            WHERE evaluation_id = ?
            ORDER BY id ASC
        ");
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll();
    
        // Get options for each question
        foreach ($questions as &$question) {
            $stmt = $this->db->prepare("
                SELECT * FROM options 
                WHERE question_id = ?
                ORDER BY id ASC
            ");
            $stmt->execute([$question['id']]);
            $question['options'] = $stmt->fetchAll();
        }
    
        // Process form submission
        $error = null;
        $success = null;
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();
    
                $total_points = 0;
                $earned_points = 0;
    
                // Calculate total points and earned points
                foreach ($questions as $question) {
                    $total_points += $question['points'];
                    
                    // Check if answer is correct
                    if (isset($_POST['answer'][$question['id']])) {
                        $answer_id = $_POST['answer'][$question['id']];
                        
                        // Find the selected option
                        foreach ($question['options'] as $option) {
                            if ($option['id'] == $answer_id && $option['est_correcte']) {
                                $earned_points += $question['points'];
                                break;
                            }
                        }
                    }
                }
    
                // Calculate score as percentage
                $score = ($total_points > 0) ? ($earned_points / $total_points) * 100 : 0;
                
                // Save result
                $stmt = $this->db->prepare("
                    INSERT INTO resultats (etudiant_id, evaluation_id, score, date_soumission)
                    VALUES (?, ?, ?, NOW())
                ");
                $stmt->execute([$_SESSION['user_id'], $quiz_id, $score]);
                
                $result_id = $this->db->lastInsertId();
                
                // Save answers
                foreach ($questions as $question) {
                    if (isset($_POST['answer'][$question['id']])) {
                        $answer_id = $_POST['answer'][$question['id']];
                        
                        $stmt = $this->db->prepare("
                            INSERT INTO reponses (resultat_id, question_id, option_id)
                            VALUES (?, ?, ?)
                        ");
                        $stmt->execute([$result_id, $question['id'], $answer_id]);
                    }
                }
                
                $this->db->commit();
                
                // Redirect to result page
                header('Location: index.php?page=evaluations&action=result&id=' . $result_id);
                exit;
                
            } catch (Exception $e) {
                $this->db->rollBack();
                $error = "Une erreur est survenue lors de la soumission du quiz: " . $e->getMessage();
            }
        }
    
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/evaluations/take_quiz.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Method to display quiz result
    public function result() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
        
        $result_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Get result information
        $stmt = $this->db->prepare("
            SELECT r.*, e.titre, e.module_id, m.nom as module_nom, u.nom as etudiant_nom
            FROM resultats r
            JOIN evaluations e ON r.evaluation_id = e.id
            JOIN modules m ON e.module_id = m.id
            JOIN utilisateurs u ON r.etudiant_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$result_id]);
        $result = $stmt->fetch();
        
        if (!$result) {
            $_SESSION['error'] = "Résultat non trouvé.";
            header('Location: index.php?page=evaluations&action=listQuizzes');
            exit;
        }
        
        // Check permissions - only admin, teacher of the module, or the student who took the quiz can view
        if ($_SESSION['user_role'] !== 'admin' && 
            ($_SESSION['user_role'] !== 'enseignant' || !$this->isTeacherOfModule($_SESSION['user_id'], $result['module_id'])) && 
            ($_SESSION['user_role'] !== 'etudiant' || $_SESSION['user_id'] != $result['etudiant_id'])) {
            $_SESSION['error'] = "Vous n'avez pas la permission de voir ce résultat.";
            header('Location: index.php?page=home');
            exit;
        }
        
        // Get quiz information
        $stmt = $this->db->prepare("
            SELECT e.*, m.nom as module_nom
            FROM evaluations e
            JOIN modules m ON e.module_id = m.id
            WHERE e.id = ?
        ");
        $stmt->execute([$result['evaluation_id']]);
        $quiz = $stmt->fetch();
        
        // Get questions with user answers
        $stmt = $this->db->prepare("
            SELECT q.*, r.option_id as user_answer
            FROM questions q
            LEFT JOIN reponses r ON q.id = r.question_id AND r.resultat_id = ?
            WHERE q.evaluation_id = ?
            ORDER BY q.id ASC
        ");
        $stmt->execute([$result_id, $result['evaluation_id']]);
        $questions = $stmt->fetchAll();
        
        // Get options for each question and determine if answer is correct
        foreach ($questions as &$question) {
            $stmt = $this->db->prepare("
                SELECT * FROM options 
                WHERE question_id = ?
                ORDER BY id ASC
            ");
            $stmt->execute([$question['id']]);
            $question['options'] = $stmt->fetchAll();
            
            // Check if answer is correct
            $question['is_correct'] = false;
            foreach ($question['options'] as $option) {
                if ($option['id'] == $question['user_answer'] && $option['est_correcte']) {
                    $question['is_correct'] = true;
                    break;
                }
            }
        }
        
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/evaluations/result.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
    
    // Helper method to check if a teacher is assigned to a module
    private function isTeacherOfModule($teacher_id, $module_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM modules 
            WHERE id = ? AND enseignant_id = ?
        ");
        $stmt->execute([$module_id, $teacher_id]);
        return $stmt->fetchColumn() > 0;
    }

    // List all results for a teacher
    public function results() {
        // Check if user is logged in and is a teacher or admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'enseignant' && $_SESSION['user_role'] !== 'admin')) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    
        $resultats = [];
        
        if ($_SESSION['user_role'] === 'admin') {
            // Pour les administrateurs, afficher tous les résultats
            $stmt = $this->db->query("
                SELECT r.*, u.nom as etudiant_nom, e.titre as evaluation_titre, m.nom as module_nom
                FROM resultats r
                JOIN utilisateurs u ON r.etudiant_id = u.id
                JOIN evaluations e ON r.evaluation_id = e.id
                JOIN modules m ON e.module_id = m.id
                ORDER BY r.date_soumission DESC
            ");
            $resultats = $stmt->fetchAll();
        } else {
            // Pour les enseignants, afficher uniquement les résultats de leurs modules
            // Get all modules for this teacher
            $stmt = $this->db->prepare("
                SELECT id FROM modules WHERE enseignant_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $modules = $stmt->fetchAll();
        
            if (!empty($modules)) {
                // Get module IDs
                $module_ids = array_column($modules, 'id');
                $placeholders = implode(',', array_fill(0, count($module_ids), '?'));
        
                // Get all results for evaluations in these modules
                $stmt = $this->db->prepare("
                    SELECT r.*, u.nom as etudiant_nom, e.titre as evaluation_titre, m.nom as module_nom
                    FROM resultats r
                    JOIN utilisateurs u ON r.etudiant_id = u.id
                    JOIN evaluations e ON r.evaluation_id = e.id
                    JOIN modules m ON e.module_id = m.id
                    WHERE m.id IN ($placeholders)
                    ORDER BY r.date_soumission DESC
                ");
                $stmt->execute($module_ids);
                $resultats = $stmt->fetchAll();
            }
        }
    
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/evaluations/results.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Method for teachers to create quiz questions
    public function createQuiz() {
        // Check if user is logged in and is a teacher
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
    
        $module_id = isset($_GET['module_id']) ? $_GET['module_id'] : 0;
    
        // Check if module exists and belongs to this teacher
        $stmt = $this->db->prepare("
            SELECT * FROM modules 
            WHERE id = ? AND enseignant_id = ?
        ");
        $stmt->execute([$module_id, $_SESSION['user_id']]);
        $module = $stmt->fetch();
    
        if (!$module) {
            header('Location: index.php?page=modules&action=dashboard');
            exit;
        }
    
        $error = null;
        $success = null;
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check for duplicate submission using a session token
            $form_token = isset($_POST['form_token']) ? $_POST['form_token'] : '';
            
            if (empty($form_token) || !isset($_SESSION['quiz_form_token']) || $form_token !== $_SESSION['quiz_form_token']) {
                // This is a new submission, process it
                try {
                    $this->db->beginTransaction();
    
                    // Create quiz
                    $stmt = $this->db->prepare("
                        INSERT INTO evaluations (titre, description, module_id, duree)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $_POST['titre'],
                        $_POST['description'],
                        $module_id,
                        $_POST['duree']
                    ]);
                    
                    $quiz_id = $this->db->lastInsertId();
                    
                    // Process questions
                    if (isset($_POST['questions']) && is_array($_POST['questions'])) {
                        foreach ($_POST['questions'] as $question) {
                            if (empty($question['texte'])) continue;
                            
                            // Insert question
                            $stmt = $this->db->prepare("
                                INSERT INTO questions (evaluation_id, texte, type, points)
                                VALUES (?, ?, 'qcm', ?)
                            ");
                            $stmt->execute([
                                $quiz_id,
                                $question['texte'],
                                $question['points']
                            ]);
                            
                            $question_id = $this->db->lastInsertId();
                            
                            // Process options
                            if (isset($question['options']) && is_array($question['options'])) {
                                foreach ($question['options'] as $option) {
                                    if (empty($option['texte'])) continue;
                                    
                                    $est_correcte = isset($option['est_correcte']) ? 1 : 0;
                                    
                                    $stmt = $this->db->prepare("
                                        INSERT INTO options (question_id, texte, est_correcte)
                                        VALUES (?, ?, ?)
                                    ");
                                    $stmt->execute([
                                        $question_id,
                                        $option['texte'],
                                        $est_correcte
                                    ]);
                                }
                            }
                        }
                    }
                    
                    $this->db->commit();
                    
                    // Store the token in session to prevent duplicate submissions
                    $_SESSION['quiz_form_token'] = $form_token;
                    
                    // Redirect to prevent form resubmission
                    header('Location: index.php?page=modules&action=view&id=' . $module_id . '&success=quiz_created');
                    exit;
                } catch (PDOException $e) {
                    $this->db->rollBack();
                    $error = 'Erreur lors de la création du quiz: ' . $e->getMessage();
                }
            } else {
                // This is a duplicate submission, redirect
                header('Location: index.php?page=modules&action=view&id=' . $module_id . '&success=quiz_created');
                exit;
            }
        }
    
        // Generate a new token for the form
        $_SESSION['quiz_form_token'] = md5(uniqid(mt_rand(), true));
        $form_token = $_SESSION['quiz_form_token'];
    
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/evaluations/create_quiz.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    } // End of createQuiz method

    // List available quizzes for students
    public function listQuizzes() {
        // Check if user is logged in and is a student, admin, or teacher
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'etudiant' && $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'enseignant')) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
        
        // Get all modules for selection
        $stmt = $this->db->query("
            SELECT m.*, u.nom as enseignant_nom 
            FROM modules m
            JOIN utilisateurs u ON m.enseignant_id = u.id
            ORDER BY m.nom ASC
        ");
        $modules = $stmt->fetchAll();
        
        $quizzes = [];
        $taken_quizzes = [];
        $selected_module = null;
        
        // Check if a module has been selected
        if (isset($_GET['module_id']) && !empty($_GET['module_id'])) {
            $module_id = intval($_GET['module_id']);
            
            // Get the selected module details
            $stmt = $this->db->prepare("
                SELECT m.*, u.nom as enseignant_nom 
                FROM modules m
                JOIN utilisateurs u ON m.enseignant_id = u.id
                WHERE m.id = ?
            ");
            $stmt->execute([$module_id]);
            $selected_module = $stmt->fetch();
            
            if ($selected_module) {
                // Get quizzes for the selected module
                $stmt = $this->db->prepare("
                    SELECT e.*, m.nom as module_nom, u.nom as enseignant_nom
                    FROM evaluations e
                    JOIN modules m ON e.module_id = m.id
                    JOIN utilisateurs u ON m.enseignant_id = u.id
                    WHERE e.module_id = ?
                    ORDER BY e.id DESC
                ");
                $stmt->execute([$module_id]);
                $quizzes = $stmt->fetchAll();
                
                // Check which quizzes the student has already taken
                if (!empty($quizzes) && $_SESSION['user_role'] === 'etudiant') {
                    $quiz_ids = array_column($quizzes, 'id');
                    $placeholders = implode(',', array_fill(0, count($quiz_ids), '?'));
                    
                    $stmt = $this->db->prepare("
                        SELECT evaluation_id FROM resultats 
                        WHERE etudiant_id = ? AND evaluation_id IN ($placeholders)
                    ");
                    
                    $params = [$_SESSION['user_id']];
                    $params = array_merge($params, $quiz_ids);
                    
                    $stmt->execute($params);
                    $taken_quizzes = $stmt->fetchAll(PDO::FETCH_COLUMN);
                }
            }
        }

        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/evaluations/list_quizzes.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // After the listQuizzes method, add this new method
    
    // View a specific evaluation
    public function view() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
        
        $evaluation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Get evaluation details
        $stmt = $this->db->prepare("
            SELECT e.*, m.nom as module_nom, u.nom as enseignant_nom
            FROM evaluations e
            JOIN modules m ON e.module_id = m.id
            JOIN utilisateurs u ON m.enseignant_id = u.id
            WHERE e.id = ?
        ");
        $stmt->execute([$evaluation_id]);
        $evaluation = $stmt->fetch();
        
        if (!$evaluation) {
            header('Location: index.php?page=modules');
            exit;
        }
        
        // Check permissions - only admin, teacher of the module, or students can view
        if ($_SESSION['user_role'] !== 'admin' && 
            ($_SESSION['user_role'] !== 'enseignant' || !$this->isTeacherOfModule($_SESSION['user_id'], $evaluation['module_id'])) && 
            $_SESSION['user_role'] !== 'etudiant') {
            header('Location: index.php?page=home');
            exit;
        }
        
        // Get questions for this evaluation
        $stmt = $this->db->prepare("
            SELECT * FROM questions 
            WHERE evaluation_id = ?
            ORDER BY id ASC
        ");
        $stmt->execute([$evaluation_id]);
        $questions = $stmt->fetchAll();
        
        // Get options for each question
        foreach ($questions as &$question) {
            if ($question['type'] === 'qcm') {
                $stmt = $this->db->prepare("
                    SELECT * FROM options 
                    WHERE question_id = ?
                    ORDER BY id ASC
                ");
                $stmt->execute([$question['id']]);
                $question['options'] = $stmt->fetchAll();
            }
        }
        
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/evaluations/view.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }

    // Method to delete a quiz/evaluation
    public function delete() {
        // Check if user is logged in and is a teacher or admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'enseignant' && $_SESSION['user_role'] !== 'admin')) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
        
        $evaluation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Get evaluation details
        $stmt = $this->db->prepare("
            SELECT e.*, m.enseignant_id 
            FROM evaluations e
            JOIN modules m ON e.module_id = m.id
            WHERE e.id = ?
        ");
        $stmt->execute([$evaluation_id]);
        $evaluation = $stmt->fetch();
        
        if (!$evaluation) {
            $_SESSION['error'] = "Évaluation non trouvée.";
            header('Location: index.php?page=modules');
            exit;
        }
        
        // Check if user has permission to delete this evaluation
        // Only the teacher who created it or an admin can delete
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_id'] != $evaluation['enseignant_id']) {
            $_SESSION['error'] = "Vous n'avez pas la permission de supprimer cette évaluation.";
            header('Location: index.php?page=modules&action=view&id=' . $evaluation['module_id']);
            exit;
        }
        
        $error = null;
        $module_id = $evaluation['module_id'];
        
        // First include the header
        require_once VIEWS_PATH . '/templates/header.php';
        
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
                
                // Finally, delete the evaluation itself
                $stmt = $this->db->prepare("DELETE FROM evaluations WHERE id = ?");
                $stmt->execute([$evaluation_id]);
                
                $this->db->commit();
                
                $_SESSION['success'] = "L'évaluation a été supprimée avec succès.";
                header('Location: index.php?page=modules&action=view&id=' . $module_id);
                exit;
                
            } catch (PDOException $e) {
                $this->db->rollBack();
                $error = 'Erreur lors de la suppression de l\'évaluation: ' . $e->getMessage();
            }
        }
        
        // Then include the delete.php view
        require_once VIEWS_PATH . '/evaluations/delete.php';
        
        // Finally include the footer
        require_once VIEWS_PATH . '/templates/footer.php';
    } // End of delete method

    // Method for teachers and admins to edit quiz
    public function editQuiz() {
        // Check if user is logged in and is a teacher or admin
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'enseignant' && $_SESSION['user_role'] !== 'admin')) {
            header('Location: index.php?page=auth&action=login');
            exit;
        }
        
        $quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Get quiz details
        $stmt = $this->db->prepare("
            SELECT e.*, m.enseignant_id, m.nom as module_nom
            FROM evaluations e
            JOIN modules m ON e.module_id = m.id
            WHERE e.id = ?
        ");
        $stmt->execute([$quiz_id]);
        $quiz = $stmt->fetch();
        
        if (!$quiz) {
            $_SESSION['error'] = "Quiz non trouvé.";
            header('Location: index.php?page=evaluations&action=listQuizzes');
            exit;
        }
        
        // Check if user has permission to edit this quiz
        // Only the teacher who created it or an admin can edit
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_id'] != $quiz['enseignant_id']) {
            $_SESSION['error'] = "Vous n'avez pas la permission de modifier ce quiz.";
            header('Location: index.php?page=modules&action=view&id=' . $quiz['module_id']);
            exit;
        }
        
        // Get questions for this quiz
        $stmt = $this->db->prepare("
            SELECT * FROM questions 
            WHERE evaluation_id = ?
            ORDER BY id ASC
        ");
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll();
        
        // Get options for each question
        foreach ($questions as &$question) {
            $stmt = $this->db->prepare("
                SELECT * FROM options 
                WHERE question_id = ?
                ORDER BY id ASC
            ");
            $stmt->execute([$question['id']]);
            $question['options'] = $stmt->fetchAll();
        }
        
        $error = null;
        $success = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();
                
                // Update quiz basic info
                $stmt = $this->db->prepare("
                    UPDATE evaluations 
                    SET titre = ?, description = ?, duree = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['titre'],
                    $_POST['description'],
                    $_POST['duree'],
                    $quiz_id
                ]);
                
                // Process updated questions
                if (isset($_POST['questions']) && is_array($_POST['questions'])) {
                    foreach ($_POST['questions'] as $question_id => $question_data) {
                        // Update existing question
                        $stmt = $this->db->prepare("
                            UPDATE questions 
                            SET texte = ?, points = ?
                            WHERE id = ? AND evaluation_id = ?
                        ");
                        $stmt->execute([
                            $question_data['texte'],
                            $question_data['points'],
                            $question_id,
                            $quiz_id
                        ]);
                        
                        // Process options
                        if (isset($question_data['options']) && is_array($question_data['options'])) {
                            foreach ($question_data['options'] as $option_id => $option_data) {
                                $est_correcte = isset($option_data['est_correcte']) ? 1 : 0;
                                
                                // Update existing option
                                $stmt = $this->db->prepare("
                                    UPDATE options 
                                    SET texte = ?, est_correcte = ?
                                    WHERE id = ? AND question_id = ?
                                ");
                                $stmt->execute([
                                    $option_data['texte'],
                                    $est_correcte,
                                    $option_id,
                                    $question_id
                                ]);
                            }
                        }
                    }
                }
                
                $this->db->commit();
                $success = 'Quiz mis à jour avec succès!';
                
            } catch (PDOException $e) {
                $this->db->rollBack();
                $error = 'Erreur lors de la mise à jour du quiz: ' . $e->getMessage();
            }
        }
        
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/evaluations/edit_quiz.php'; // Vous devrez créer cette vue
        require_once VIEWS_PATH . '/templates/footer.php';
    }
} // End of EvaluationController class
