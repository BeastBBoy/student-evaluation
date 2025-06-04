<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression</h4>
                </div>
                <div class="card-body">
                    <p class="lead">Êtes-vous sûr de vouloir supprimer le module suivant ?</p>
                    
                    <div class="alert alert-warning">
                        <p><strong>Nom:</strong> <?php echo htmlspecialchars($module['nom']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($module['description']); ?></p>
                    </div>
                    
                    <p class="text-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Attention:</strong> Cette action est irréversible et supprimera également toutes les évaluations, questions et résultats associés à ce module.
                    </p>
                    
                    <form method="post">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Confirmer la suppression
                            </button>
                            <a href="index.php?page=modules&action=view&id=<?php echo $module['id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>