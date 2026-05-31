// Mobile Menu Toggle for Floating Pill Header
const menu = document.querySelector('#menu-bar');
const navLinks = document.querySelector('.nav-links');
const header = document.querySelector('#main-header');
let isMenuOpen = false;

if (menu) {
    menu.addEventListener('click', () => {
        isMenuOpen = !isMenuOpen;
        menu.classList.toggle('fa-times', isMenuOpen);
        navLinks.classList.toggle('active', isMenuOpen);
        
        // Mobile style for nav links when active
        if (isMenuOpen) {
            navLinks.style.display = 'flex';
            navLinks.style.flexDirection = 'column';
            navLinks.style.position = 'absolute';
            navLinks.style.top = '70px';
            navLinks.style.left = '0';
            navLinks.style.width = '100%';
            navLinks.style.background = 'white';
            navLinks.style.padding = '20px';
            navLinks.style.borderRadius = '20px';
            navLinks.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
        } else {
            navLinks.style.display = '';
        }
    });
}

// Close menu on link click
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        isMenuOpen = false;
        if (menu) menu.classList.remove('fa-times');
        navLinks.classList.remove('active');
        if (window.innerWidth <= 768) {
            navLinks.style.display = '';
        }
    });
});

// Scroll Effects
window.addEventListener('scroll', () => {
    if (header) {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
});

// Intersection Observer for Reveal Animations
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal').forEach(el => {
    revealObserver.observe(el);
});