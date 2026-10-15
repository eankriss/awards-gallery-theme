/*
 * Award category page: submit the sort form on change, and load the next page of
 * products into the grid in place. The next-page URL is used only as an internal
 * fetch target; visitors stay on the current taxonomy page.
 */
(() => {
  document.querySelectorAll('[data-dropdown-toggle]').forEach((toggle) => {
    const menu = document.getElementById(toggle.getAttribute('aria-controls'));
    const native = toggle.closest('.ag-filter-field').querySelector('.ag-native-select');
    const close = () => { toggle.setAttribute('aria-expanded', 'false'); menu.hidden = true; };
    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') === 'true';
      document.querySelectorAll('[data-dropdown-toggle][aria-expanded="true"]').forEach((item) => item.click());
      toggle.setAttribute('aria-expanded', String(!open)); menu.hidden = open;
    });
    menu.querySelectorAll('[data-dropdown-option]').forEach((option) => option.addEventListener('click', () => {
      native.value = option.dataset.value;
      toggle.querySelector('[data-dropdown-value]').textContent = option.dataset.label;
      menu.querySelectorAll('.is-selected').forEach((item) => item.classList.remove('is-selected'));
      option.classList.add('is-selected'); close(); native.dispatchEvent(new Event('change', { bubbles: true }));
    }));
  });
  document.addEventListener('click', (event) => {
    if (!event.target.closest('.ag-filter-field')) document.querySelectorAll('[data-dropdown-toggle][aria-expanded="true"]').forEach((toggle) => toggle.click());
  });

  document.querySelectorAll('[data-browse-select]').forEach((select) => {
    select.addEventListener('change', () => {
      if (select.value) window.location.href = select.value;
    });
  });

  document.querySelectorAll('[data-sort-select]').forEach((select) => {
    select.addEventListener('change', () => select.form && select.form.submit());
  });
  document.querySelectorAll('[data-theme-select]').forEach((select) => {
    select.addEventListener('change', () => select.form && select.form.submit());
  });

  const grid = document.querySelector('[data-award-grid]');
  const wrap = document.querySelector('[data-load-more-wrap]');
  const button = wrap && wrap.querySelector('[data-load-more]');
  if (!grid || !button) return;

  const totalProducts = Number.parseInt(grid.dataset.totalProducts || '0', 10);

  const batchSize = () => {
    const width = window.innerWidth;
    if (width >= 1280) return 18; // desktop: 6 columns × 3 rows
    if (width >= 1024) return 15; // small desktop: 5 columns × 3 rows
    if (width >= 768) return 16;  // tablet: 4 columns × 4 rows
    return 18;                    // mobile / small tablet: 2–3 columns × 6 rows
  };

  const applyBatchVisibility = () => {
    const batch = batchSize();
    grid.querySelectorAll('[data-product-index]').forEach((card, index) => {
      card.hidden = index >= batch;
    });
  };

  applyBatchVisibility();
  window.addEventListener('resize', applyBatchVisibility, { passive: true });

  const removeButtonAtEnd = () => {
    const visibleCount = [...grid.children].filter((card) => !card.hidden).length;
    if (totalProducts && visibleCount >= totalProducts) {
      wrap.remove();
      return true;
    }
    return false;
  };

  if (removeButtonAtEnd()) return;

  const label = button.textContent;
  let loading = false;

  button.addEventListener('click', async (event) => {
    event.preventDefault();
    if (loading) return;
    loading = true;
    button.setAttribute('aria-busy', 'true');
    button.textContent = button.dataset.loading || label;

    try {
      const hiddenCards = [...grid.children].filter((card) => card.hidden);
      if (hiddenCards.length) {
        hiddenCards[0].hidden = false;
        if (removeButtonAtEnd()) return;
        button.textContent = label;
        button.removeAttribute('aria-busy');
        loading = false;
        return;
      }

      const response = await fetch(button.dataset.loadUrl, { credentials: 'same-origin' });
      if (!response.ok) throw new Error(String(response.status));

      const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
      const fragment = document.createDocumentFragment();
      const newCards = [];

      doc.querySelectorAll('[data-award-grid] > *').forEach((item) => {
        const card = document.importNode(item, true);
        // The scroll-reveal observer only saw the cards present at load, so show new ones directly.
        card.classList.add('is-in');
        newCards.push(card);
        fragment.appendChild(card);
      });
      grid.appendChild(fragment);
      newCards.forEach((card, index) => {
        card.hidden = index >= batchSize();
      });

      if (removeButtonAtEnd()) return;

      const next = doc.querySelector('[data-load-more]');
      if (next) {
        button.dataset.loadUrl = next.dataset.loadUrl;
        button.textContent = label;
        button.removeAttribute('aria-busy');
        loading = false;
      } else {
        wrap.remove();
      }
    } catch (error) {
      button.textContent = label;
      button.removeAttribute('aria-busy');
      button.setAttribute('aria-label', 'Unable to load more products. Please try again.');
      loading = false;
    }
  });
})();
