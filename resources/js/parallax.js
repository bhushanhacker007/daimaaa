/**
 * Parallax scrolling, scroll-triggered reveals, and counter animations.
 * Runs only on pages that contain the relevant data attributes.
 */

document.addEventListener('DOMContentLoaded', () => {
    initParallax();
    initScrollReveal();
    initCounterAnimation();
});

function initParallax() {
    const parallaxEls = document.querySelectorAll('[data-parallax]');
    if (!parallaxEls.length) return;

    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                const scrollY = window.scrollY;
                parallaxEls.forEach(el => {
                    const speed = parseFloat(el.dataset.parallax) || 0.3;
                    const rect = el.getBoundingClientRect();
                    const inView = rect.top < window.innerHeight && rect.bottom > 0;
                    if (inView) {
                        const offset = (scrollY - el.offsetTop) * speed;
                        el.style.transform = `translate3d(0, ${offset}px, 0)`;
                    }
                });
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });
}

function initScrollReveal() {
    const revealEls = document.querySelectorAll('[data-reveal]');
    if (!revealEls.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = parseInt(entry.target.dataset.revealDelay) || 0;
                setTimeout(() => {
                    entry.target.classList.add('revealed');
                }, delay);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    revealEls.forEach(el => observer.observe(el));
}

function initCounterAnimation() {
    const counters = document.querySelectorAll('[data-count-to]');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(el => observer.observe(el));
}

function animateCounter(el) {
    const target = parseInt(el.dataset.countTo);
    const suffix = el.dataset.countSuffix || '';
    const duration = 1800;
    const start = performance.now();

    function update(now) {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(eased * target);
        el.textContent = current.toLocaleString('en-IN') + suffix;
        if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
}
