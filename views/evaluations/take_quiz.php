<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i><?php echo htmlspecialchars($quiz['titre']); ?></h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-book me-2"></i>Module:</strong> <?php echo htmlspecialchars($quiz['module_nom']); ?></p>
                                <p><strong><i class="fas fa-clock me-2"></i>Durée:</strong> <?php echo $quiz['duree']; ?> minutes</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-calendar-alt me-2"></i>Date limite:</strong> <?php echo date('d/m/Y H:i', strtotime($quiz['date_fin'])); ?></p>
                                <p><strong><i class="fas fa-info-circle me-2"></i>Instructions:</strong> Sélectionnez la bonne réponse pour chaque question.</p>
                            </div>
                        </div>
                    </div>
                    
                    <form method="post" id="quiz-form">
                        <?php $question_num = 1; ?>
                        <?php foreach ($questions as $question): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        Question <?php echo $question_num; ?> 
                                        <small class="text-muted">(<?php echo $question['points']; ?> points)</small>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="lead"><?php echo htmlspecialchars($question['texte']); ?></p>
                                    
                                    <div class="mt-3">
                                        <?php foreach ($question['options'] as $option): ?>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="answer[<?php echo $question['id']; ?>]" 
                                                    id="option_<?php echo $option['id']; ?>" value="<?php echo $option['id']; ?>" required>
                                                <label class="form-check-label" for="option_<?php echo $option['id']; ?>">
                                                    <?php echo htmlspecialchars($option['texte']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php $question_num++; ?>
                        <?php endforeach; ?>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Soumettre mes réponses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add a timer if needed
document.addEventListener('DOMContentLoaded', function() {
    // Set quiz duration in minutes
    const quizDuration = <?php echo $quiz['duree']; ?>;
    
    if (quizDuration > 0) {
        // Convert to seconds
        let timeLeft = quizDuration * 60;
        
        // Update timer every second
        const timerInterval = setInterval(function() {
            timeLeft--;
            
            // Format time as MM:SS
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const formattedTime = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Display timer
            document.title = `${formattedTime} - Quiz en cours`;
            
            // If time is up, submit the form
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                document.getElementById('quiz-form').submit();
            }
        }, 1000);
    }
});
</script>