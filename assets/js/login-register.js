document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.auth-tabs .tab');
    const forms = document.querySelectorAll('.auth-form');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Retirer la classe active de tous les onglets
            tabs.forEach(t => t.classList.remove('active'));
            // Ajouter la classe active à l'onglet cliqué
            tab.classList.add('active');

            // Masquer tous les formulaires
            forms.forEach(form => form.classList.remove('active'));
            // Afficher le formulaire correspondant
            const targetForm = document.getElementById(tab.dataset.tab);
            targetForm.classList.add('active');
        });
    });
});