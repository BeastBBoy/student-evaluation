// Script personnalisé pour le site d'évaluations en ligne

// Activer les tooltips Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Fonction pour afficher un compte à rebours pour les évaluations
    window.startCountdown = function(elementId, duration) {
        const countdownElement = document.getElementById(elementId);
        if (!countdownElement) return;
        
        let timer = duration;
        const interval = setInterval(function() {
            const hours = Math.floor(timer / 3600);
            const minutes = Math.floor((timer % 3600) / 60);
            const seconds = timer % 60;
            
            countdownElement.textContent = 
                (hours < 10 ? "0" + hours : hours) + ":" +
                (minutes < 10 ? "0" + minutes : minutes) + ":" +
                (seconds < 10 ? "0" + seconds : seconds);
            
            if (--timer < 0) {
                clearInterval(interval);
                document.getElementById('test-form').submit();
            }
        }, 1000);
    };
});