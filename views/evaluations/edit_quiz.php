<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Modifier le Quiz</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" id="quizForm">
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre du Quiz</label>
                            <input type="text" class="form-control" id="titre" name="titre" value="<?php echo htmlspecialchars($quiz['titre']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($quiz['description']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="duree" class="form-label">Durée (minutes)</label>
                            <input type="number" class="form-control" id="duree" name="duree" value="<?php echo htmlspecialchars($quiz['duree']); ?>" required>
                        </div>
                        
                        <h5 class="mt-4 mb-3">Questions</h5>
                        
                        <div id="questions-container">
                            <?php foreach ($questions as $question): ?>
                                <div class="card mb-3 question-card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Question</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Texte de la question</label>
                                            <input type="text" class="form-control" name="questions[<?php echo $question['id']; ?>][texte]" value="<?php echo htmlspecialchars($question['texte']); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Points</label>
                                            <input type="number" class="form-control" name="questions[<?php echo $question['id']; ?>][points]" value="<?php echo htmlspecialchars($question['points']); ?>" required>
                                        </div>
                                        
                                        <div class="options-container">
                                            <label class="form-label">Options</label>
                                            <?php foreach ($question['options'] as $option): ?>
                                                <div class="input-group mb-2 option-input">
                                                    <div class="input-group-text">
                                                        <input type="checkbox" name="questions[<?php echo $question['id']; ?>][options][<?php echo $option['id']; ?>][est_correcte]" <?php echo $option['est_correcte'] ? 'checked' : ''; ?>>
                                                    </div>
                                                    <input type="text" class="form-control" name="questions[<?php echo $question['id']; ?>][options][<?php echo $option['id']; ?>][texte]" value="<?php echo htmlspecialchars($option['texte']); ?>" placeholder="Option" required>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="index.php?page=modules&action=view&id=<?php echo $quiz['module_id']; ?>" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>