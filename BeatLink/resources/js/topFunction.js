document.addEventListener('DOMContentLoaded', () => {
    const mybutton = document.getElementById("myBtn");

    if (!mybutton) return;

    // Show/hide on scroll
    window.addEventListener("scroll", () => {
        if (window.scrollY > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    });

    // Scroll to top when clicked
    mybutton.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
