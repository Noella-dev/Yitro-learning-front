// Animation pour le canvas de la section hero
const canvas = document.getElementById('heros-animation');
const ctx = canvas.getContext('2d');

canvas.width = window.innerWidth;
canvas.height = canvas.parentElement.offsetHeight;

let particlesArray = [];

class Particle {
    constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 2 + 1;
        this.speedX = Math.random() * 1 - 0.5;
        this.speedY = Math.random() * 1 - 0.5;
    }
    update() {
        this.x += this.speedX;
        this.y += this.speedY;
        if (this.x < 0 || this.x > canvas.width) {
            this.speedX = -this.speedX;
        }
        if (this.y < 0 || this.y > canvas.height) {
            this.speedY = -this.speedY;
        }
    }
    draw() {
        ctx.fillStyle = 'rgba(255,255,255,0.8)';
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
    }
}

function init() {
    particlesArray = [];
    for (let i = 0; i < 100; i++) {
        particlesArray.push(new Particle());
    }
}

function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for (let i = 0; i < particlesArray.length; i++) {
        particlesArray[i].update();
        particlesArray[i].draw();
    }
    requestAnimationFrame(animate);
}

init();
animate();

window.addEventListener('resize', function() {
    canvas.width = window.innerWidth;
    canvas.height = canvas.parentElement.offsetHeight;
    init();
});

// Animation d'apparition au défilement
const fadeInElements = document.querySelectorAll('.fade-in');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

fadeInElements.forEach(element => {
    observer.observe(element);
});

// Mise en surbrillance des champs actifs et validation
const inputs = document.querySelectorAll('.form-control');
inputs.forEach(input => {
    input.addEventListener('focus', () => {
        input.parentElement.classList.add('active');
    });
    input.addEventListener('blur', () => {
        input.parentElement.classList.remove('active');
    });
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

// Animation du bouton de soumission
const submitButtons = document.querySelectorAll('.btn-primary');
submitButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
        const form = btn.closest('form');
        if (!form.checkValidity()) {
            e.preventDefault();
            btn.classList.add('shake');
            setTimeout(() => btn.classList.remove('shake'), 500);
        }
    });
});