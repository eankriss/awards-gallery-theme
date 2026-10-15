(() => {
  document.querySelectorAll('[data-specification]').forEach((calculator) => {
    let pricing = [];
    try {
      pricing = JSON.parse(calculator.dataset.pricing || '[]');
    } catch (error) {
      return;
    }

    const size = calculator.querySelector('[data-spec-size]');
    const quantity = calculator.querySelector('[data-spec-quantity]');
    const total = calculator.querySelector('[data-spec-total]');
    if (!size || !quantity || !total || !pricing.length) return;

    const dropdown = calculator.querySelector('[data-spec-dropdown-toggle]');
    const menu = calculator.querySelector('#spec-size-menu');
    if (dropdown && menu) {
      const closeDropdown = () => {
        dropdown.setAttribute('aria-expanded', 'false');
        menu.hidden = true;
      };

      dropdown.addEventListener('click', () => {
        const open = dropdown.getAttribute('aria-expanded') === 'true';
        dropdown.setAttribute('aria-expanded', String(!open));
        menu.hidden = open;
      });
      menu.querySelectorAll('[data-spec-dropdown-option]').forEach((option) => {
        option.addEventListener('click', () => {
          size.value = option.dataset.value;
          dropdown.querySelector('[data-spec-dropdown-value]').textContent = option.dataset.label;
          menu.querySelectorAll('.is-selected').forEach((item) => item.classList.remove('is-selected'));
          option.classList.add('is-selected');
          closeDropdown();
          update();
        });
      });

      document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-specification] .ag-filter-field')) {
          closeDropdown();
        }
      });

      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeDropdown();
      });
    }

    calculator.querySelector('[data-spec-decrement]')?.addEventListener('click', () => {
      quantity.value = Math.max(1, (parseInt(quantity.value, 10) || 1) - 1);
      update();
    });
    calculator.querySelector('[data-spec-increment]')?.addEventListener('click', () => {
      quantity.value = (parseInt(quantity.value, 10) || 1) + 1;
      update();
    });

    const update = () => {
      const selected = pricing[Number(size.value)] || pricing[0];
      const count = Math.max(1, parseInt(quantity.value, 10) || 1);
      quantity.value = count;
      total.textContent = `₱${(Number(selected.price) * count).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    size.addEventListener('change', update);
    quantity.addEventListener('input', update);
    update();
  });
})();
