// Mobile Menu Toggle
const menu = document.querySelector('#menu-bar');
const navbar = document.querySelector('.navbar');
let isMenuOpen = false;

const toggleMenu = (e) => {
    if (e) e.preventDefault();
    isMenuOpen = !isMenuOpen;
    menu.classList.toggle('fa-times', isMenuOpen);
    navbar.classList.toggle('active', isMenuOpen);
    document.body.classList.toggle('no-scroll', isMenuOpen);
};

const closeMenu = () => {
    isMenuOpen = false;
    menu.classList.remove('fa-times');
    navbar.classList.remove('active');
    document.body.classList.remove('no-scroll');
};

menu.addEventListener('click', toggleMenu);

// City Selector Logic (Drawer Style)
let cityDrawerOpen = false;

function toggleCitySelector(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const drawer = document.querySelector('.city-drawer');
    const arrow = document.querySelector('.arrow-icon');
    
    cityDrawerOpen = !cityDrawerOpen;
    drawer.classList.toggle('active', cityDrawerOpen);
    
    if (arrow) {
        arrow.style.transform = cityDrawerOpen ? 'rotate(180deg)' : 'rotate(0deg)';
    }
}

// Handle outside clicks for closing elements
document.addEventListener('click', (e) => {
    // Close Navbar
    if (isMenuOpen && !navbar.contains(e.target) && e.target !== menu) {
        closeMenu();
    }
    
    // Close City Drawer
    if (cityDrawerOpen && !e.target.closest('.city-selector-container')) {
        toggleCitySelector();
    }
});

// Initialize on Load
document.addEventListener('DOMContentLoaded', () => {
    // Other initializations can go here
});

// Smooth Scroll for Navigation
document.querySelectorAll('.navbar a').forEach(link => {
    link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        if (href.startsWith('#')) {
            e.preventDefault();
            closeMenu();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
});