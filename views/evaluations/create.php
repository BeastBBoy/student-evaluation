<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Créer une Nouvelle Évaluation</h4>
        </div>
        <div class="card-body">
            <form action="index.php?page=evaluations&action=store" method="POST" id="evaluationForm">
                <input type="hidden" name="module_id" value="<?php echo isset($module_id) ? htmlspecialchars($module_id) : ''; ?>">
                <input type="hidden" name="form_submitted" value="1">
                
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre de l'évaluation</label>
                    <input type="text" class="form-control" id="titre" name="titre" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="duree" class="form-label">Durée (minutes)</label>
                        <input type="number" class="form-control" id="duree" name="duree" min="5" value="30" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tentatives_max" class="form-label">Tentatives maximales</label>
                        <input type="number" class="form-control" id="tentatives_max" name="tentatives_max" min="1" value="1" required>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="est_actif" name="est_actif" value="1" checked>
                    <label class="form-check-label" for="est_actif">Activer l'évaluation</label>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="index.php?page=modules&action=view&id=<?php echo isset($module_id) ? htmlspecialchars($module_id) : ''; ?>" class="btn btn-secondary me-md-2">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Enregistrer et continuer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent duplicate form submissions
    const form = document.getElementById('evaluationForm');
    const submitBtn = document.getElementById('submitBtn');
    let formSubmitted = false;
    
    form.addEventListener('submit', function(e) {
        if (formSubmitted) {
            e.preventDefault();
            return false;
        }
        
        // Disable the submit button and change its text
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement en cours...';
        formSubmitted = true;
    });
});
</script>