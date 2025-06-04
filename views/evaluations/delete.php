<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-trash me-2"></i>Supprimer l'évaluation</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Attention: Cette action est irréversible. Toutes les données associées à cette évaluation seront supprimées, y compris les questions, options, et résultats des étudiants.
                    </div>
                    
                    <p>Êtes-vous sûr de vouloir supprimer l'évaluation <strong><?php echo htmlspecialchars($evaluation['titre']); ?></strong>?</p>
                    
                    <form method="post">
                        <div class="d-flex justify-content-between">
                            <a href="index.php?page=modules&action=view&id=<?php echo $evaluation['module_id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Confirmer la suppression
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>