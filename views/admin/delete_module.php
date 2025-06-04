<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Supprimer le Module</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="alert alert-warning">
                        <p><strong>Attention :</strong> Vous êtes sur le point de supprimer le module "<?php echo htmlspecialchars($module['nom']); ?>".</p>
                        <p>Cette action supprimera également toutes les évaluations, questions, options et résultats associés à ce module.</p>
                        <p>Cette action est irréversible. Êtes-vous sûr de vouloir continuer ?</p>
                    </div>
                    
                    <form method="post">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?page=admin&action=modules" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>