document.addEventListener('DOMContentLoaded', () => {
    // Gestion du mode sombre/clair
    const toggleThemeBtn = document.querySelector('.btn-toggle-theme');
    toggleThemeBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        toggleThemeBtn.textContent = document.body.classList.contains('dark-mode')
            ? 'Passer au mode clair'
            : 'Passer au mode sombre';
    });
});