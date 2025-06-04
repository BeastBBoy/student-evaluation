<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i><?php echo htmlspecialchars($evaluation['titre']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5>Description</h5>
                            <p><?php echo nl2br(htmlspecialchars($evaluation['description'])); ?></p>
                            
                            <h5>Module</h5>
                            <p><i class="fas fa-book me-2"></i><?php echo htmlspecialchars($evaluation['module_nom']); ?></p>
                            
                            <h5>Enseignant</h5>
                            <p><i class="fas fa-user me-2"></i><?php echo htmlspecialchars($evaluation['enseignant_nom']); ?></p>
                            
                            <h5>Durée</h5>
                            <p><i class="fas fa-clock me-2"></i><?php echo htmlspecialchars($evaluation['duree']); ?> minutes</p>
                        </div>
                        <div class="col-md-4">
                            <!-- Administration and results buttons removed -->
                        </div>
                    </div>
                    
                    <h5 class="mb-3">Questions</h5>
                    <?php if (empty($questions)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune question disponible pour cette évaluation.
                        </div>
                    <?php else: ?>
                        <div class="accordion" id="questionsAccordion">
                            <?php foreach ($questions as $index => $question): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                                            <strong>Question <?php echo $index + 1; ?>:</strong> <?php echo htmlspecialchars($question['texte']); ?> (<?php echo $question['points']; ?> points)
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#questionsAccordion">
                                        <div class="accordion-body">
                                            <?php if ($question['type'] === 'qcm'): ?>
                                                <h6>Options:</h6>
                                                <ul class="list-group">
                                                    <?php foreach ($question['options'] as $option): ?>
                                                        <li class="list-group-item <?php echo $option['est_correcte'] ? 'list-group-item-success' : ''; ?>">
                                                            <?php echo htmlspecialchars($option['texte']); ?>
                                                            <?php if ($option['est_correcte']): ?>
                                                                <span class="badge bg-success float-end">Correcte</span>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-muted">Question à réponse libre</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>