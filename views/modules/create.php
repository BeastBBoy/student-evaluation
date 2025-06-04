<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Créer un nouveau module</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" id="moduleForm">
                        <input type="hidden" name="form_submitted" value="1">
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du module</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Créer le module
                            </button>
                            <a href="index.php?page=modules" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent duplicate form submissions
    const form = document.getElementById('moduleForm');
    const submitBtn = document.getElementById('submitBtn');
    let formSubmitted = false;
    
    form.addEventListener('submit', function(e) {
        if (formSubmitted) {
            e.preventDefault();
            return false;
        }
        
        // Disable the submit button and change its text
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement en cours...';
        formSubmitted = true;
    });
});
</script>