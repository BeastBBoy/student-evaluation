<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i>Créer un Quiz</h4>
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
                    
                    <form method="post" id="quizForm">
                        <!-- Add this hidden field for the form token -->
                        <input type="hidden" name="form_token" value="<?php echo $form_token; ?>">
                        
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre du Quiz</label>
                            <input type="text" class="form-control" id="titre" name="titre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="duree" class="form-label">Durée (minutes)</label>
                                <input type="number" class="form-control" id="duree" name="duree" min="1" required>
                            </div>
                        </div>
                        
                        <hr>
                        <h5 class="mb-3">Questions</h5>
                        
                        <div id="questions-container">
                            <!-- Questions will be added here dynamically -->
                        </div>
                        
                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary" id="add-question">
                                <i class="fas fa-plus me-2"></i>Ajouter une question
                            </button>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Enregistrer le Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Question template (hidden) -->
<template id="question-template">
    <div class="question-item card mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Question <span class="question-number"></span></h6>
            <button type="button" class="btn btn-sm btn-danger remove-question">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Texte de la question</label>
                <textarea class="form-control" name="questions[INDEX][texte]" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Points</label>
                <input type="number" class="form-control" name="questions[INDEX][points]" min="1" value="1" required>
            </div>
            
            <h6 class="mb-3">Options</h6>
            <div class="options-container">
                <!-- Options will be added here dynamically -->
            </div>
            
            <div class="mb-3">
                <button type="button" class="btn btn-sm btn-secondary add-option">
                    <i class="fas fa-plus me-2"></i>Ajouter une option
                </button>
            </div>
        </div>
    </div>
</template>

<!-- Option template (hidden) -->
<template id="option-template">
    <div class="option-item mb-2 d-flex align-items-center">
        <div class="form-check me-2">
            <input class="form-check-input" type="checkbox" name="questions[QINDEX][options][OINDEX][est_correcte]">
            <label class="form-check-label">Correcte</label>
        </div>
        <input type="text" class="form-control me-2" name="questions[QINDEX][options][OINDEX][texte]" placeholder="Texte de l'option" required>
        <button type="button" class="btn btn-sm btn-danger remove-option">
            <i class="fas fa-times"></i>
        </button>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionsContainer = document.getElementById('questions-container');
        const addQuestionBtn = document.getElementById('add-question');
        const questionTemplate = document.getElementById('question-template');
        const optionTemplate = document.getElementById('option-template');
        const quizForm = document.getElementById('quizForm');
        const submitBtn = document.getElementById('submitBtn');
        
        let questionCount = 0;
        let formSubmitted = false;
        
        // Prevent duplicate submissions
        quizForm.addEventListener('submit', function(e) {
            if (formSubmitted) {
                e.preventDefault();
                return false;
            }
            
            // Validate form
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
            
            // Disable submit button and mark form as submitted
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement en cours...';
            formSubmitted = true;
        });
        
        // Function to validate the form
        function validateForm() {
            // Check if there's at least one question
            if (questionsContainer.children.length === 0) {
                alert('Veuillez ajouter au moins une question.');
                return false;
            }
            
            // Check if each question has at least two options
            const questions = questionsContainer.querySelectorAll('.question-item');
            for (let i = 0; i < questions.length; i++) {
                const options = questions[i].querySelectorAll('.option-item');
                if (options.length < 2) {
                    alert(`La question ${i+1} doit avoir au moins deux options.`);
                    return false;
                }
                
                // Check if at least one option is marked as correct
                let hasCorrectOption = false;
                options.forEach(option => {
                    if (option.querySelector('input[type="checkbox"]').checked) {
                        hasCorrectOption = true;
                    }
                });
                
                if (!hasCorrectOption) {
                    alert(`La question ${i+1} doit avoir au moins une option correcte.`);
                    return false;
                }
            }
            
            return true;
        }
        
        // Rest of the JavaScript remains unchanged
        
        // Add question
        addQuestionBtn.addEventListener('click', function() {
            addQuestion();
        });
        
        // Add first question by default
        addQuestion();
        
        function addQuestion() {
            const questionIndex = questionCount++;
            const questionNode = document.importNode(questionTemplate.content, true);
            
            // Update question number and indices
            questionNode.querySelector('.question-number').textContent = questionIndex + 1;
            questionNode.querySelectorAll('[name*="INDEX"]').forEach(el => {
                el.name = el.name.replace('INDEX', questionIndex);
            });
            
            // Add event listener to remove question button
            questionNode.querySelector('.remove-question').addEventListener('click', function() {
                this.closest('.question-item').remove();
                updateQuestionNumbers();
            });
            
            // Add event listener to add option button
            questionNode.querySelector('.add-option').addEventListener('click', function() {
                addOption(this.closest('.question-item'), questionIndex);
            });
            
            questionsContainer.appendChild(questionNode);
            
            // Add two options by default
            const questionItem = questionsContainer.lastElementChild;
            addOption(questionItem, questionIndex);
            addOption(questionItem, questionIndex);
        }
        
        function addOption(questionItem, questionIndex) {
            const optionsContainer = questionItem.querySelector('.options-container');
            const optionCount = optionsContainer.children.length;
            const optionNode = document.importNode(optionTemplate.content, true);
            
            // Update indices
            optionNode.querySelectorAll('[name*="QINDEX"]').forEach(el => {
                el.name = el.name.replace('QINDEX', questionIndex);
            });
            optionNode.querySelectorAll('[name*="OINDEX"]').forEach(el => {
                el.name = el.name.replace('OINDEX', optionCount);
            });
            
            // Add event listener to remove option button
            optionNode.querySelector('.remove-option').addEventListener('click', function() {
                this.closest('.option-item').remove();
            });
            
            optionsContainer.appendChild(optionNode);
        }
        
        function updateQuestionNumbers() {
            const questions = questionsContainer.querySelectorAll('.question-item');
            questions.forEach((question, index) => {
                question.querySelector('.question-number').textContent = index + 1;
            });
        }
    });
</script>