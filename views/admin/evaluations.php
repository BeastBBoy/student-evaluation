<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i>Gestion des Évaluations</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($evaluations)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune évaluation disponible.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Module</th>
                                        <th>Enseignant</th>
                                        <th>Durée (min)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($evaluations as $evaluation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($evaluation['titre']); ?></td>
                                            <td><?php echo htmlspecialchars($evaluation['module_nom']); ?></td>
                                            <td><?php echo htmlspecialchars($evaluation['enseignant_nom']); ?></td>
                                            <td><?php echo htmlspecialchars($evaluation['duree']); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="index.php?page=evaluations&action=view&id=<?php echo $evaluation['id']; ?>" class="btn btn-sm btn-outline-primary" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="index.php?page=admin&action=editEvaluation&id=<?php echo $evaluation['id']; ?>" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="index.php?page=admin&action=deleteEvaluation&id=<?php echo $evaluation['id']; ?>" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
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