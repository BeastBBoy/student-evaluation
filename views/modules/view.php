<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-book me-2"></i><?php echo htmlspecialchars($module['nom']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5>Description</h5>
                            <p><?php echo nl2br(htmlspecialchars($module['description'])); ?></p>
                            
                            <h5>Enseignant</h5>
                            <p><i class="fas fa-user me-2"></i><?php echo htmlspecialchars($module['enseignant_nom']); ?></p>
                        </div>
                        <div class="col-md-4">
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $module['enseignant_id']): ?>
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="index.php?page=modules&action=edit&id=<?php echo $module['id']; ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-edit me-2"></i>Modifier le module
                                            </a>
                                            <!-- Removed the 'Créer une évaluation' button -->
                                            <a href="index.php?page=evaluations&action=createQuiz&module_id=<?php echo $module['id']; ?>" class="btn btn-outline-info">
                                                <i class="fas fa-question-circle me-2"></i>Créer un quiz
                                            </a>
                                            <a href="index.php?page=modules&action=delete&id=<?php echo $module['id']; ?>" class="btn btn-outline-danger">
                                                <i class="fas fa-trash me-2"></i>Supprimer le module
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h5 class="mb-3">Évaluations</h5>
                    <?php if (empty($evaluations)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune évaluation disponible pour ce module.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Type</th>
                                        <th>Durée</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($evaluations as $evaluation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($evaluation['titre']); ?></td>
                                            <td>
                                                <?php if (isset($evaluation['type']) && $evaluation['type'] === 'quiz'): ?>
                                                    <span class="badge bg-info">Quiz</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">Évaluation</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo isset($evaluation['duree']) ? htmlspecialchars($evaluation['duree']) . ' minutes' : 'Non spécifiée'; ?></td>
                                            <td>
                                                <!-- In the actions column of the evaluations table -->
                                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'etudiant'): ?>
                                                    <a href="index.php?page=evaluations&action=take&id=<?php echo $evaluation['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit me-1"></i>Passer
                                                    </a>
                                                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $module['enseignant_id']): ?>
                                                    <div class="btn-group">
                                                        <a href="index.php?page=evaluations&action=view&id=<?php echo $evaluation['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="index.php?page=evaluations&action=results&id=<?php echo $evaluation['id']; ?>" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-chart-bar"></i>
                                                        </a>
                                                        <a href="index.php?page=evaluations&action=delete&id=<?php echo $evaluation['id']; ?>" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>