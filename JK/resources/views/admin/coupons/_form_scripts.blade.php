<script>
document.addEventListener('DOMContentLoaded', function () {
    const codeInput = document.getElementById('coupon_code');
    const nameInput = document.getElementById('coupon_name');
    const typeSelect = document.getElementById('discount_type');
    const valueInput = document.getElementById('discount_value');
    const minOrderInput = document.getElementById('min_order_amount');
    const maxDiscountWrap = document.getElementById('max_discount_wrap');
    const maxDiscountInput = document.getElementById('max_discount_amount');
    const usageLimitInput = document.getElementById('usage_limit');
    const startsAtInput = document.getElementById('starts_at');
    const expiresAtInput = document.getElementById('expires_at');
    const guestCheckbox = document.getElementById('guest_eligible');
    const discountPrefix = document.querySelector('.discount-prefix');

    const previewCode = document.getElementById('preview-code');
    const previewName = document.getElementById('preview-name');
    const previewDiscount = document.getElementById('preview-discount');
    const previewMinOrder = document.getElementById('preview-min-order');
    const previewAudience = document.getElementById('preview-audience');
    const previewUsage = document.getElementById('preview-usage');
    const previewValidity = document.getElementById('preview-validity');
    const selectedCountBadge = document.getElementById('customer-selected-count');

    const customerSearch = document.getElementById('customer-search');
    const customerItems = Array.from(document.querySelectorAll('.coupon-customer-item'));
    const customerCheckboxes = Array.from(document.querySelectorAll('.customer-checkbox'));
    const selectAllBtn = document.getElementById('customer-select-all');
    const clearAllBtn = document.getElementById('customer-clear-all');

    function formatCurrency(value) {
        if (!value || Number(value) <= 0) return null;
        return '₹' + Number(value).toLocaleString('en-IN');
    }

    function formatDateTime(value) {
        if (!value) return null;
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return null;
        return date.toLocaleString('en-IN', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    function updateDiscountTypeUi() {
        const isPercent = typeSelect.value === 'percent';
        if (discountPrefix) {
            discountPrefix.textContent = isPercent ? '%' : '₹';
        }
        if (maxDiscountWrap) {
            maxDiscountWrap.style.display = isPercent ? '' : 'none';
            if (!isPercent && maxDiscountInput) {
                maxDiscountInput.value = '';
            }
        }
        updatePreview();
    }

    function updateSelectedCount() {
        const count = customerCheckboxes.filter(cb => cb.checked).length;
        if (selectedCountBadge) {
            selectedCountBadge.textContent = count + ' selected';
        }
        customerItems.forEach(item => {
            const checkbox = item.querySelector('.customer-checkbox');
            item.classList.toggle('is-selected', checkbox && checkbox.checked);
        });
        updatePreview();
    }

    function updatePreview() {
        if (previewCode) {
            previewCode.textContent = (codeInput?.value || 'NEWCODE').toUpperCase();
        }
        if (previewName) {
            previewName.textContent = nameInput?.value || 'Coupon name';
        }

        const type = typeSelect?.value || 'percent';
        const value = valueInput?.value;
        if (previewDiscount) {
            if (!value) {
                previewDiscount.textContent = '—';
            } else if (type === 'percent') {
                previewDiscount.textContent = parseFloat(value) + '% off';
            } else {
                previewDiscount.textContent = formatCurrency(value) + ' off';
            }
        }

        if (previewMinOrder) {
            previewMinOrder.textContent = formatCurrency(minOrderInput?.value) || 'None';
        }

        if (previewAudience) {
            const selected = customerCheckboxes.filter(cb => cb.checked).length;
            const parts = [];
            if (guestCheckbox?.checked) parts.push('Guests');
            if (selected > 0) parts.push(selected + ' customer(s)');
            previewAudience.textContent = parts.length ? parts.join(' · ') : 'None configured';
        }

        if (previewUsage) {
            previewUsage.textContent = usageLimitInput?.value ? usageLimitInput.value + ' max' : 'Unlimited';
        }

        if (previewValidity) {
            const start = formatDateTime(startsAtInput?.value);
            const end = formatDateTime(expiresAtInput?.value);
            if (start && end) previewValidity.textContent = start + ' → ' + end;
            else if (start) previewValidity.textContent = 'From ' + start;
            else if (end) previewValidity.textContent = 'Until ' + end;
            else previewValidity.textContent = 'Always active';
        }
    }

    function filterCustomers() {
        const query = (customerSearch?.value || '').trim().toLowerCase();
        customerItems.forEach(item => {
            const haystack = item.dataset.search || '';
            item.classList.toggle('hidden-by-search', query !== '' && !haystack.includes(query));
        });
    }

    [codeInput, nameInput, valueInput, minOrderInput, usageLimitInput, startsAtInput, expiresAtInput].forEach(el => {
        el?.addEventListener('input', updatePreview);
    });
    typeSelect?.addEventListener('change', updateDiscountTypeUi);
    guestCheckbox?.addEventListener('change', updatePreview);
    customerSearch?.addEventListener('input', filterCustomers);

    customerCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));

    customerItems.forEach(item => {
        item.addEventListener('click', function (e) {
            if (e.target.matches('input[type="checkbox"]')) return;
            const checkbox = item.querySelector('.customer-checkbox');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                updateSelectedCount();
            }
        });
    });

    selectAllBtn?.addEventListener('click', function () {
        customerItems.forEach(item => {
            if (item.classList.contains('hidden-by-search')) return;
            const checkbox = item.querySelector('.customer-checkbox');
            if (checkbox) checkbox.checked = true;
        });
        updateSelectedCount();
    });

    clearAllBtn?.addEventListener('click', function () {
        customerCheckboxes.forEach(cb => { cb.checked = false; });
        updateSelectedCount();
    });

    updateDiscountTypeUi();
    updateSelectedCount();
    updatePreview();
});
</script>
