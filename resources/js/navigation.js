/**
 * Hamburger menu voor mobiele navigatie.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const menuToggle = document.getElementById('menu-toggle');
        const mainNav = document.getElementById('main-nav');
        const navOverlay = document.getElementById('nav-overlay');

        if (!menuToggle || !mainNav) {
            return;
        }

        function closeMenu() {
            mainNav.classList.remove('is-open');
            menuToggle.classList.remove('is-active');
            menuToggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('menu-open');

            if (navOverlay) {
                navOverlay.hidden = true;
            }
        }

        function openMenu() {
            mainNav.classList.add('is-open');
            menuToggle.classList.add('is-active');
            menuToggle.setAttribute('aria-expanded', 'true');
            document.body.classList.add('menu-open');

            if (navOverlay) {
                navOverlay.hidden = false;
            }
        }

        menuToggle.addEventListener('click', function () {
            if (mainNav.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        if (navOverlay) {
            navOverlay.addEventListener('click', closeMenu);
        }

        mainNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 900) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });
    });
})();
