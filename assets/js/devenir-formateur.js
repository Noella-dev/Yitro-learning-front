 // Animation d'apparition au défilement
 const fadeInElements = document.querySelectorAll('.fade-in');

 const observer = new IntersectionObserver((entries) => {
     entries.forEach(entry => {
         if (entry.isIntersecting) {
             entry.target.classList.add('visible');
             observer.unobserve(entry.target);
         }
     });
 }, {
     threshold: 0.1
 });

 fadeInElements.forEach(element => {
     observer.observe(element);
 });

 // Effet de survol sur les cartes de témoignage
 const testimonialCards = document.querySelectorAll('.testimonial-card');
 testimonialCards.forEach(card => {
     card.addEventListener('mouseenter', () => {
         card.style.transform = 'scale(1.05)';
     });
     card.addEventListener('mouseleave', () => {
         card.style.transform = 'scale(1)';
     });
 });