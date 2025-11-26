/* ========================================
   LibreNews - Main JavaScript
   Versão Otimizada v2.0
   ======================================== */

(function () {
    "use strict";

    /* ========================================
       SPINNER / LOADING
       ======================================== */
    const spinner = document.getElementById('spinner');
    if (spinner) {
        setTimeout(() => {
            spinner.classList.remove('show');
        }, 500);
    }

    /* ========================================
       FIXED NAVBAR ON SCROLL
       ======================================== */
    const stickyNav = document.querySelector('.sticky-top');
    if (stickyNav) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                stickyNav.style.top = '0px';
                stickyNav.classList.add('shadow-sm');
            } else {
                stickyNav.style.top = '-200px';
                stickyNav.classList.remove('shadow-sm');
            }
        });
    }

    /* ========================================
       BOTÃO VOLTAR AO TOPO
       ======================================== */
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        // Mostrar/Esconder botão baseado no scroll
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        // Ação de clique - scroll suave para o topo
        backToTop.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /* ========================================
       CAROUSEL HORIZONTAL (CSS PURO)
       ======================================== */
    function initCarousel(carouselSelector) {
        const carousel = document.querySelector(carouselSelector);
        if (!carousel) return;

        const items = carousel.querySelectorAll('.latest-news-item, .whats-item');
        if (items.length === 0) return;

        // Criar botões de navegação
        const navContainer = document.createElement('div');
        navContainer.style.cssText = 'position: relative; margin-top: 20px; text-align: right;';
        
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '<i class="bi bi-arrow-left"></i>';
        prevBtn.className = 'btn btn-primary me-2';
        prevBtn.onclick = () => scrollCarousel('prev');
        
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '<i class="bi bi-arrow-right"></i>';
        nextBtn.className = 'btn btn-primary';
        nextBtn.onclick = () => scrollCarousel('next');
        
        navContainer.appendChild(prevBtn);
        navContainer.appendChild(nextBtn);
        carousel.parentElement.insertBefore(navContainer, carousel);

        // Função de scroll do carousel
        function scrollCarousel(direction) {
            const itemWidth = items[0].offsetWidth + 24; // width + gap
            const scrollAmount = direction === 'next' ? itemWidth : -itemWidth;
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }

        // Touch swipe para mobile
        let startX = 0;
        carousel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });

        carousel.addEventListener('touchend', (e) => {
            const endX = e.changedTouches[0].clientX;
            const diff = startX - endX;

            if (Math.abs(diff) > 50) {
                scrollCarousel(diff > 0 ? 'next' : 'prev');
            }
        });

        // Autoplay (5 segundos)
        let autoplayInterval = setInterval(() => {
            const maxScroll = carousel.scrollWidth - carousel.clientWidth;
            if (carousel.scrollLeft >= maxScroll) {
                carousel.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                scrollCarousel('next');
            }
        }, 5000);

        // Pausar autoplay ao passar o mouse
        carousel.addEventListener('mouseenter', () => clearInterval(autoplayInterval));
        
        carousel.addEventListener('mouseleave', () => {
            autoplayInterval = setInterval(() => {
                const maxScroll = carousel.scrollWidth - carousel.clientWidth;
                if (carousel.scrollLeft >= maxScroll) {
                    carousel.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    scrollCarousel('next');
                }
            }, 5000);
        });
    }

    // Inicializar carousels na página
    initCarousel('.latest-news-carousel');
    initCarousel('.whats-carousel');

    /* ========================================
       NAVBAR SCROLL EFFECT
       ======================================== */
    const navbarWrapper = document.querySelector('.navbar-modern-wrapper-full');
    if (navbarWrapper) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbarWrapper.classList.add('scrolled');
            } else {
                navbarWrapper.classList.remove('scrolled');
            }
        });
    }

    /* ========================================
       SMOOTH SCROLL PARA ÂNCORAS
       ======================================== */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    /* ========================================
       VALIDAÇÃO DE FORMULÁRIO DE CONTATO
       ======================================== */
    const contactForm = document.querySelector('form[action=""]:not(#formCandidatura)');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Obter valores dos campos
            const name = this.querySelector('[name="name"]').value.trim();
            const email = this.querySelector('[name="email"]').value.trim();
            const subject = this.querySelector('[name="subject"]').value.trim();
            const message = this.querySelector('textarea').value.trim();

            // Validar campos obrigatórios
            if (!name || !email || !subject || !message) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }

            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Por favor, insira um email válido.');
                return;
            }

            // Envio bem-sucedido (aqui você adicionaria a lógica real de envio)
            alert('Mensagem enviada com sucesso! Entraremos em contato em breve.');
            this.reset();
        });
    }

    /* ========================================
       NEWSLETTER SUBSCRIPTION
       ======================================== */
    const newsletterForms = document.querySelectorAll('form input[type="email"][placeholder*="e-mail"]');
    newsletterForms.forEach(form => {
        const submitBtn = form.parentElement.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const email = form.value.trim();
                
                // Validar se email foi preenchido
                if (!email) {
                    alert('Por favor, insira seu email.');
                    return;
                }

                // Validar formato de email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert('Por favor, insira um email válido.');
                    return;
                }

                // Inscrição bem-sucedida (aqui você adicionaria a lógica real de inscrição)
                alert('Obrigado por se inscrever! Você receberá nossas atualizações semanais.');
                form.value = '';
            });
        }
    });

    /* ========================================
       LAZY LOADING DE IMAGENS
       ======================================== */
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));

    /* ========================================
       CONSOLE MESSAGE
       ======================================== */
    console.log('%c🚀 LibreNews v2.0', 'color: #4FC3F7; font-size: 20px; font-weight: bold;');
    console.log('%cPortal de Notícias Tech Otimizado', 'color: #81C784; font-size: 14px;');

})();