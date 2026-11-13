/* =========================================================
   CASA INTELIGENTE — script.js
   Interatividade: menu mobile, modo claro/escuro, scroll reveal,
   acordeão de segurança e botão "voltar ao topo".
   ========================================================= */

document.addEventListener('DOMContentLoaded', () => {

    /* -----------------------------------------------------
       1. Menu mobile
    ----------------------------------------------------- */
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');

    menuToggle.addEventListener('click', () => {
        menuToggle.classList.toggle('open');
        navLinks.classList.toggle('open');
    });

    // Fecha o menu ao clicar em um link (no mobile)
    navLinks.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            menuToggle.classList.remove('open');
            navLinks.classList.remove('open');
        });
    });

    /* -----------------------------------------------------
       2. Modo claro / escuro
       Persiste a escolha no localStorage.
    ----------------------------------------------------- */
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const body = document.body;

    function applyTheme(theme) {
        if (theme === 'light') {
            body.setAttribute('data-theme', 'light');
            themeIcon.textContent = '☀';
        } else {
            body.removeAttribute('data-theme');
            themeIcon.textContent = '☾';
        }
    }

    const savedTheme = localStorage.getItem('ci-theme');
    if (savedTheme) applyTheme(savedTheme);

    themeToggle.addEventListener('click', () => {
        const isLight = body.getAttribute('data-theme') === 'light';
        const newTheme = isLight ? 'dark' : 'light';
        applyTheme(newTheme);
        localStorage.setItem('ci-theme', newTheme);
    });

    /* -----------------------------------------------------
       3. Header com sombra ao scrollar
    ----------------------------------------------------- */
    const header = document.getElementById('header');
    window.addEventListener('scroll', () => {
        header.style.borderBottomColor = window.scrollY > 10
            ? 'var(--accent)'
            : 'var(--border)';
    });

    /* -----------------------------------------------------
       4. Botão "voltar ao topo"
    ----------------------------------------------------- */
    const backToTop = document.getElementById('backToTop');

    window.addEventListener('scroll', () => {
        backToTop.classList.toggle('visible', window.scrollY > 500);
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    /* -----------------------------------------------------
       5. Acordeão — Lógica de funcionamento e segurança
    ----------------------------------------------------- */
    const accordionItems = document.querySelectorAll('.accordion-item');

    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        header.addEventListener('click', () => {
            const isOpen = item.classList.contains('open');

            // Fecha os outros itens (comportamento de acordeão único)
            accordionItems.forEach(other => other.classList.remove('open'));

            if (!isOpen) item.classList.add('open');
        });
    });

    /* -----------------------------------------------------
       6. Scroll reveal — animação simples ao entrar na viewport
    ----------------------------------------------------- */
    const revealTargets = document.querySelectorAll(
        '.diagram-figure, .card, .hw-table, .accordion-item, .contato-info, .contato-form-wrapper'
    );
    revealTargets.forEach(el => el.classList.add('reveal'));

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealTargets.forEach(el => observer.observe(el));

    /* -----------------------------------------------------
       7. Marcação do link ativo no menu conforme a seção visível
    ----------------------------------------------------- */
    const sections = document.querySelectorAll('section[id]');
    const navAnchors = document.querySelectorAll('.nav-link');

    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navAnchors.forEach(a => {
                    a.classList.toggle('active', a.getAttribute('href') === `#${id}`);
                });
            }
        });
    }, { rootMargin: '-40% 0px -55% 0px' });

    sections.forEach(section => sectionObserver.observe(section));
});
