document.addEventListener('DOMContentLoaded', () => {
    // Gestion du menu mobile
    const mobileMenu = document.getElementById('mobile-menu');
    const navList = document.querySelector('.nav-list');
    if (mobileMenu && navList) {
        mobileMenu.addEventListener('click', () => {
            navList.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });
    }

    // Validation du formulaire de candidature (devenir-formateur.html)
    const form = document.querySelector('.application-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const expertise = document.getElementById('expertise').value.trim();
            const message = document.getElementById('message').value.trim();

            if (name && email && expertise && message) {
                alert('Candidature envoyée ! Merci de nous avoir contactés.');
                form.reset();
            } else {
                alert('Veuillez remplir tous les champs.');
            }
        });
    }

    // Filtrage des cours (catalogue.html)
    const filters = document.querySelectorAll('.filters select, #sort-filter');
    const courseCards = document.querySelectorAll('.course-card');
    if (filters.length && courseCards.length) {
        function filterCourses() {
            const theme = document.getElementById('theme-filter')?.value;
            const level = document.getElementById('level-filter')?.value;
            const duration = document.getElementById('duration-filter')?.value;
            const price = document.getElementById('price-filter')?.value;
            const certificate = document.getElementById('certificate-filter')?.value;

            courseCards.forEach(card => {
                const matchesTheme = !theme || card.dataset.theme === theme;
                const matchesLevel = !level || card.dataset.level === level;
                const matchesDuration = !duration || card.dataset.duration === duration;
                const matchesPrice = !price || card.dataset.price === price;
                const matchesCertificate = !certificate || card.dataset.certificate === certificate;

                card.style.display = matchesTheme && matchesLevel && matchesDuration && matchesPrice && matchesCertificate ? 'block' : 'none';
            });
        }
        filters.forEach(filter => filter.addEventListener('change', filterCourses));
    }

    // Recherche forum (communaute.html)
    const forumSearch = document.querySelector('.forum-search input');
    const topicCards = document.querySelectorAll('.topic-card');
    if (forumSearch && topicCards.length) {
        forumSearch.addEventListener('input', () => {
            const query = forumSearch.value.toLowerCase();
            topicCards.forEach(card => {
                const title = card.querySelector('h4').textContent.toLowerCase();
                const content = card.querySelector('p').textContent.toLowerCase();
                card.style.display = title.includes(query) || content.includes(query) ? 'block' : 'none';
            });
        });
    }
});