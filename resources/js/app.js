// Shiksha Link – app.js (light theme only, minimal)
document.addEventListener('DOMContentLoaded', () => {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ── Mobile nav drawer ────────────────────────────────────────────
    const drawerOpen    = document.getElementById('nav-menu-open');
    const drawerClose   = document.getElementById('nav-menu-close');
    const drawer        = document.getElementById('mobile-nav');
    const drawerOverlay = document.getElementById('mobile-nav-overlay');

    function openNav() {
        if (!drawer) return;
        drawer.classList.remove('-translate-x-full');
        drawer.setAttribute('aria-hidden', 'false');
        drawerOverlay?.classList.remove('opacity-0', 'pointer-events-none');
        document.body.style.overflow = 'hidden';
    }
    function closeNav() {
        if (!drawer) return;
        drawer.classList.add('-translate-x-full');
        drawer.setAttribute('aria-hidden', 'true');
        drawerOverlay?.classList.add('opacity-0', 'pointer-events-none');
        document.body.style.overflow = '';
    }
    drawerOpen?.addEventListener('click', openNav);
    drawerClose?.addEventListener('click', closeNav);
    drawerOverlay?.addEventListener('click', closeNav);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeNav(); });

    // ── Count-up ─────────────────────────────────────────────────────
    function runCount(el) {
        const target = Number(el.dataset.count || 0);
        if (reduce || !target) { el.textContent = target.toLocaleString(); return; }
        const duration = 900;
        const start = performance.now();
        const easeOut = t => 1 - Math.pow(1 - t, 3);
        const step = now => {
            const p = Math.min(1, (now - start) / duration);
            el.textContent = Math.round(easeOut(p) * target).toLocaleString();
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }
    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { runCount(e.target); obs.unobserve(e.target); } });
        }, { threshold: 0.5 });
        document.querySelectorAll('.count-up[data-count]').forEach(el => obs.observe(el));
    } else {
        document.querySelectorAll('.count-up[data-count]').forEach(runCount);
    }

    // ── Back to top ──────────────────────────────────────────────────
    const backToTop = document.getElementById('back-to-top');
    if (backToTop) {
        const toggle = () => {
            backToTop.style.opacity = window.scrollY > 400 ? '1' : '0';
            backToTop.style.pointerEvents = window.scrollY > 400 ? 'auto' : 'none';
        };
        window.addEventListener('scroll', toggle, { passive: true }); toggle();
        backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' }));
    }

    // ── Tabbed search (hero) ─────────────────────────────────────────
    document.querySelectorAll('[data-search-tab]').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('[data-search-tab]').forEach(t => {
                t.classList.toggle('tab-active', t === tab);
                t.classList.toggle('tab-inactive', t !== tab);
            });
            const type = tab.dataset.searchTab;
            const hiddenInput = document.getElementById('search-type');
            if (hiddenInput) hiddenInput.value = type === 'all' ? '' : type;
        });
    });
});
