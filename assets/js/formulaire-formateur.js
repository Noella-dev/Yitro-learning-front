// Ajout d'un effet de surbrillance pour les champs actifs
document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.yitro-input, .yitro-select, .yitro-textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('active');
        });
        input.addEventListener('blur', () => {
            input.parentElement.classList.remove('active');
        });
    });

    // Validation visuelle en temps réel pour les champs requis
    const requiredInputs = document.querySelectorAll('[required]');
    requiredInputs.forEach(input => {
        input.addEventListener('input', () => {
            if (input.validity.valid) {
                input.classList.add('valid');
                input.classList.remove('invalid');
            } else {
                input.classList.add('invalid');
                input.classList.remove('valid');
            }
        });
    });

    // Animation douce pour le bouton de soumission
    const submitBtn = document.querySelector('.yitro-submit-btn');
    submitBtn.addEventListener('click', (e) => {
        if (!document.getElementById('formulaireYitro').checkValidity()) {
            e.preventDefault();
            submitBtn.classList.add('shake');
            setTimeout(() => submitBtn.classList.remove('shake'), 500);
        }
    });
});