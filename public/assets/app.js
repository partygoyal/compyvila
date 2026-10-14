(() => {
  document.querySelectorAll('[data-blog-slider]').forEach(slider => {
    const slides = [...slider.querySelectorAll('[data-hero-slide]')];
    if (slides.length < 2) return;
    slider.dataset.heroEnhanced = 'true';
    const dots = [...slider.querySelectorAll('[data-hero-dot]')];
    const counter = slider.querySelector('[data-hero-counter]');
    const play = slider.querySelector('[data-hero-play]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let index = 0;
    let paused = reducedMotion.matches;
    let hovered = false;
    let focused = false;
    let inView = true;
    let timer;
    const schedule = () => {
      clearTimeout(timer);
      if (!paused && !hovered && !focused && inView && !document.hidden) timer = setTimeout(() => show(index + 1, false), 7000);
    };
    const show = (next, manual = true) => {
      index = (next + slides.length) % slides.length;
      slides.forEach((slide, position) => { slide.hidden = position !== index; });
      dots.forEach((dot, position) => dot.setAttribute('aria-current', String(position === index)));
      counter.setAttribute('aria-live', manual ? 'polite' : 'off');
      const total = document.createElement('span');
      total.textContent = ` / ${String(slides.length).padStart(2, '0')}`;
      counter.replaceChildren(document.createTextNode(String(index + 1).padStart(2, '0')), total);
      schedule();
    };
    const updatePlay = () => {
      play.textContent = paused ? 'Play ▷' : 'Pause Ⅱ';
      play.setAttribute('aria-label', paused ? 'Play slideshow' : 'Pause slideshow');
      schedule();
    };
    slider.querySelector('[data-hero-prev]').addEventListener('click', () => show(index - 1));
    slider.querySelector('[data-hero-next]').addEventListener('click', () => show(index + 1));
    dots.forEach((dot, position) => dot.addEventListener('click', () => show(position)));
    play.addEventListener('click', () => { paused = !paused; updatePlay(); });
    slider.addEventListener('keydown', event => {
      if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') { event.preventDefault(); show(index + (event.key === 'ArrowRight' ? 1 : -1)); }
    });
    slider.addEventListener('pointerenter', event => { if (event.pointerType === 'mouse') { hovered = true; schedule(); } });
    slider.addEventListener('pointerleave', () => { hovered = false; schedule(); });
    slider.addEventListener('focusin', () => { focused = true; schedule(); });
    slider.addEventListener('focusout', event => { focused = slider.contains(event.relatedTarget); schedule(); });
    let touch;
    slider.addEventListener('touchstart', event => {
      touch = event.touches.length === 1 ? { x: event.touches[0].clientX, y: event.touches[0].clientY } : null;
    }, { passive: true });
    slider.addEventListener('touchend', event => {
      if (!touch || !event.changedTouches.length) return;
      const dx = event.changedTouches[0].clientX - touch.x;
      const dy = event.changedTouches[0].clientY - touch.y;
      touch = null;
      if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.5) { event.preventDefault(); show(index + (dx < 0 ? 1 : -1)); }
    }, { passive: false });
    slider.addEventListener('touchcancel', () => { touch = null; });
    document.addEventListener('visibilitychange', schedule);
    reducedMotion.addEventListener('change', event => { if (event.matches) { paused = true; updatePlay(); } });
    if ('IntersectionObserver' in window) new IntersectionObserver(([entry]) => { inView = entry.isIntersecting; schedule(); }).observe(slider);
    updatePlay();
  });
  const toggle = document.querySelector('.mobile-toggle');
  toggle?.addEventListener('click', () => {
    const open = toggle.getAttribute('aria-expanded') !== 'true';
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    document.querySelector('#navigation').classList.toggle('is-open', open);
  });
  let saved = [];
  try { const value = JSON.parse(localStorage.getItem('compyvila-reading-list') || '[]'); if (Array.isArray(value)) saved = value.filter(x => typeof x === 'string'); } catch {}
  const toast = message => { const box = document.querySelector('.toast'); if (!box) return; box.textContent = message; box.classList.add('visible'); setTimeout(() => box.classList.remove('visible'), 3000); };
  const updateButtons = () => document.querySelectorAll('[data-save]').forEach(button => {
    const active = saved.includes(button.dataset.save);
    button.setAttribute('aria-pressed', String(active));
    button.textContent = active ? '✓ Saved' : '＋ Save';
  });
  const renderSaved = () => {
    const target = document.querySelector('.saved-stories');
    const template = document.querySelector('#saved-card-template');
    if (!target || !template) return;
    target.replaceChildren();
    template.content.querySelectorAll('[data-saved-slug]').forEach(card => { if (saved.includes(card.dataset.savedSlug)) target.append(card.cloneNode(true)); });
    document.querySelector('.saved-empty').hidden = target.children.length > 0;
  };
  document.addEventListener('click', event => {
    const button = event.target.closest('[data-save]');
    if (button) {
      const slug = button.dataset.save;
      const next = saved.includes(slug) ? saved.filter(x => x !== slug) : [...saved, slug];
      try { localStorage.setItem('compyvila-reading-list', JSON.stringify(next)); saved = next; toast(saved.includes(slug) ? 'Added to your reading list.' : 'Removed from your reading list.'); renderSaved(); updateButtons(); } catch { toast('Browser storage is unavailable. Your reading list could not be saved.'); }
    }
    const share = event.target.closest('.share-button');
    if (share) {
      const url = location.href;
      if (navigator.clipboard?.writeText) navigator.clipboard.writeText(url).then(() => toast('Article link copied.')).catch(() => toast('Copy the address from your browser to share.'));
      else toast('Copy the address from your browser to share.');
    }
  });
  renderSaved(); updateButtons();
  document.querySelectorAll('[data-confirm]').forEach(form => form.addEventListener('submit', event => { if (!confirm(form.dataset.confirm)) event.preventDefault(); }));
})();
