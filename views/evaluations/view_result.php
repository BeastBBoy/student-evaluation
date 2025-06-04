<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Résultat du Quiz</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2><?php echo htmlspecialchars($quiz['titre']); ?></h2>
                        <p class="text-muted"><?php echo htmlspecialchars($quiz['module_nom']); ?></p>
                        
                        <div class="mt-4">
                            <div class="display-1 <?php echo $result['score'] >= 50 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo number_format($result['score'], 1); ?>%
                            </div>
                            <p class="lead">
                                <?php if ($result['score'] >= 80): ?>
                                    <span class="badge bg-success">Excellent!</span>
                                <?php elseif ($result['score'] >= 60): ?>
                                    <span class="badge bg-primary">Très bien!</span>
                                <?php elseif ($result['score'] >= 50): ?>
                                    <span class="badge bg-warning text-dark">Passable</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">À améliorer</span>
                                <?php endif; ?>
                            </p>
                            <p>Date de soumission: <?php echo date('d/m/Y H:i', strtotime($result['date_soumission'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h4>Détails des réponses</h4>
                        
                        <?php $question_num = 1; ?>
                        <?php foreach ($questions as $question): ?>
                            <div class="card mb-3 <?php echo $question['is_correct'] ? 'border-success' : 'border-danger'; ?>">
                                <div class="card-header <?php echo $question['is_correct'] ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                                    <h5 class="mb-0">
                                        Question <?php echo $question_num; ?> 
                                        <small>(<?php echo $question['points']; ?> points)</small>
                                        <?php if ($question['is_correct']): ?>
                                            <i class="fas fa-check-circle float-end"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times-circle float-end"></i>
                                        <?php endif; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="lead"><?php echo htmlspecialchars($question['texte']); ?></p>
                                    
                                    <div class="mt-3">
                                        <?php foreach ($question['options'] as $option): ?>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" 
                                                    <?php echo $option['id'] == $question['user_answer'] ? 'checked' : ''; ?> 
                                                    disabled>
                                                <label class="form-check-label <?php 
                                                    if ($option['est_correcte']) echo 'text-success fw-bold';
                                                    else if ($option['id'] == $question['user_answer']) echo 'text-danger';
                                                ?>">
                                                    <?php echo htmlspecialchars($option['texte']); ?>
                                                    <?php if ($option['est_correcte']): ?>
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    <?php elseif ($option['id'] == $question['user_answer']): ?>
                                                        <i class="fas fa-times-circle text-danger"></i>
                                                    <?php endif; ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php $question_num++; ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="<?php echo $_SESSION['user_role'] === 'admin' ? 'index.php?page=admin&action=results' : 'index.php?page=evaluations&action=listQuizzes'; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-arrow-left me-2"></i>Retour
                                </a>
                            </div>
                            <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'teacher')): ?>
                            <div class="col-md-6">
                                <a href="index.php?page=evaluations&action=create&module_id=<?php echo isset($quiz['module_id']) ? $quiz['module_id'] : ''; ?>" class="btn btn-success w-100">
                                    <i class="fas fa-plus me-2"></i>Créer une nouvelle évaluation
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>