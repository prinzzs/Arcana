// O mesmo menu em todas as páginas, com navegação disponível sem JavaScript.
(() => {
    const header = document.querySelector('.site-toolbar');
    const toggle = document.querySelector('.nav-toggle');
    const menu = document.getElementById('site-navigation');
    if (!header || !toggle || !menu) return;
    document.body.classList.add('nav-enhanced');
    toggle.hidden = false;

    function setOpen(open) {
        toggle.setAttribute('aria-expanded', String(open));
        toggle.querySelector('span').textContent = open ? '−' : '+';
        menu.classList.toggle('is-open', open);
    }
    toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    menu.addEventListener('click', event => {
        if (event.target.closest('a')) setOpen(false);
    });
    document.addEventListener('click', event => {
        if (!header.contains(event.target)) setOpen(false);
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            toggle.focus();
        }
    });
    window.matchMedia('(max-width: 760px)').addEventListener('change', () => setOpen(false));
})();
