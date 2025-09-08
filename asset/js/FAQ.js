function toggleFAQ(element) {
    const question = element;
    const response = question.nextElementSibling;
    const icon = question.querySelector('i');

    // Toggle active class
    question.classList.toggle('active');
    response.classList.toggle('active');

    // Toggle display
    if (response.classList.contains('active')) {
        response.style.display = 'block';
    } else {
        response.style.display = 'none';
    }
}