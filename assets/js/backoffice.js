document.addEventListener('DOMContentLoaded', () => {
    // Gestion des boutons toggle pour afficher/masquer les détails
    const toggleButtons = document.querySelectorAll('.btn-toggle');
    toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.target;
            const targetSection = document.getElementById(targetId);
            targetSection.classList.toggle('active');
            button.textContent = targetSection.classList.contains('active')
                ? 'Masquer détails'
                : 'Voir détails';
        });
    });
});