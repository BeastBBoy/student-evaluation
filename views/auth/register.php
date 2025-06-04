<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!-- Page d'inscription -->
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Inscription</h4>
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
                
                <form method="post" action="index.php?page=auth&action=register">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom complet</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Role selection -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Je suis un</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                            <select class="form-select" id="role" name="role" required onchange="toggleStudentFields()">
                                <option value="etudiant">Étudiant</option>
                                <option value="enseignant">Enseignant</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Student fields -->
                    <div id="student-fields">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="promotion" class="form-label">Promotion</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                    <select class="form-select" id="promotion" name="promotion">
                                        <option value="" selected disabled>Choisir une promotion</option>
                                        <option value="L1">Licence 1</option>
                                        <option value="L2">Licence 2</option>
                                        <option value="L3">Licence 3</option>
                                        <option value="M1">Master 1</option>
                                        <option value="M2">Master 2</option>
                                        <option value="D">Doctorat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="groupe" class="form-label">Groupe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    <input type="text" class="form-control" id="groupe" name="groupe">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>S'inscrire
                        </button>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <p>Vous avez déjà un compte ? <a href="index.php?page=auth&action=login">Connectez-vous</a></p>
                </div>
                
                <script>
                    function toggleStudentFields() {
                        const role = document.getElementById('role').value;
                        const studentFields = document.getElementById('student-fields');
                        const promotionField = document.getElementById('promotion');
                        const groupeField = document.getElementById('groupe');
                        
                        if (role === 'etudiant') {
                            studentFields.style.display = 'block';
                            promotionField.setAttribute('required', '');
                            groupeField.setAttribute('required', '');
                        } else {
                            studentFields.style.display = 'none';
                            promotionField.removeAttribute('required');
                            groupeField.removeAttribute('required');
                        }
                    }
                    
                    // Initialize the form when the page loads
                    document.addEventListener('DOMContentLoaded', toggleStudentFields);
                </script>
            </div>
        </div>
    </div>
</div>