<section class="space-y-3">
    <button
        id="toggle-inventory-products"
        type="button"
        aria-expanded="false"
        aria-controls="inventory-products-panel"
        class="w-full rounded-xl border border-emerald-700 px-3 py-2.5 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-50"
    >
        Agregar productos del inventario
    </button>

    <div id="inventory-products-panel" hidden class="space-y-3 rounded-xl border border-gray-200 bg-gray-50 p-3">
        <div id="inventory-product-rows" class="space-y-3"></div>

        @if ($itemsInventario->contains(fn ($item) => $item->stock_actual > 0))
            <button
                id="add-inventory-product"
                type="button"
                class="rounded-lg bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-100 disabled:cursor-not-allowed disabled:opacity-50"
            >
                Añadir otro producto
            </button>
        @else
            <p id="no-inventory-stock" class="text-sm text-gray-600">No hay productos con stock disponible.</p>
        @endif

        <p id="inventory-stock-alert" role="alert" aria-live="assertive" hidden class="rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">
            No hay suficiente stock.
        </p>
    </div>

    <div>
        <label for="monto" class="mb-1 block text-xs font-bold uppercase text-gray-600">Monto total ($)</label>
        <input
            type="number"
            step="0.01"
            min="0"
            name="monto"
            id="monto"
            value="{{ old('monto', '0.00') }}"
            readonly
            required
            class="w-full rounded-xl border border-gray-200 bg-gray-100 px-3 py-2.5 text-sm font-bold text-gray-800"
        >
    </div>
</section>

<script>
    (() => {
    const initializeInventorySelector = () => {
    const inventoryItems = {{ Illuminate\Support\Js::from($itemsInventario) }};
    const initialProducts = {{ Illuminate\Support\Js::from(is_array(old('productos', [])) ? old('productos', []) : []) }};
    const productRows = document.getElementById('inventory-product-rows');
    const productsPanel = document.getElementById('inventory-products-panel');
    const toggleProductsButton = document.getElementById('toggle-inventory-products');
    const addProductButton = document.getElementById('add-inventory-product');
    const stockAlert = document.getElementById('inventory-stock-alert');
    const noInventoryStockMessage = document.getElementById('no-inventory-stock');
    const baseAmountInput = document.getElementById('monto_base');
    const totalAmountInput = document.getElementById('monto');
    const receiptForm = document.getElementById('receipt-form') ?? totalAmountInput.form;
    let productRowIndex = 0;

    function updateTotal() {
        const requestedQuantities = new Map();
        let productsTotalInCents = 0;

        productRows.querySelectorAll('[data-inventory-row]').forEach((row) => {
            const itemId = row.querySelector('[data-inventory-id]').value;
            const quantity = Number(row.querySelector('[data-inventory-quantity]').value);
            const item = inventoryItems.find((inventoryItem) => String(inventoryItem.id) === itemId);

            if (item && Number.isInteger(quantity) && quantity > 0) {
                requestedQuantities.set(itemId, (requestedQuantities.get(itemId) ?? 0) + quantity);
                productsTotalInCents += Math.round(Number(item.precio) * 100) * quantity;
            }
        });

        const hasInsufficientStock = [...requestedQuantities].some(([itemId, quantity]) => {
            const item = inventoryItems.find((inventoryItem) => String(inventoryItem.id) === itemId);

            return quantity > Number(item.stock_actual);
        });

        stockAlert.hidden = !hasInsufficientStock;

        productRows.querySelectorAll('[data-inventory-row]').forEach((row) => {
            const selectedItemId = row.querySelector('[data-inventory-id]').value;
            const selectedQuantity = Number(row.querySelector('[data-inventory-quantity]').value) || 0;
            const quantityFromOtherRows = new Map(requestedQuantities);

            if (selectedItemId) {
                quantityFromOtherRows.set(
                    selectedItemId,
                    (quantityFromOtherRows.get(selectedItemId) ?? 0) - selectedQuantity
                );
            }

            row.querySelectorAll('[data-inventory-id] option').forEach((option) => {
                const item = inventoryItems.find((inventoryItem) => String(inventoryItem.id) === option.value);

                if (item) {
                    const stockAvailableForRow = Number(item.stock_actual) -
                        (quantityFromOtherRows.get(String(item.id)) ?? 0);

                    option.disabled = Number(item.stock_actual) <= 0 ||
                        (option.value !== selectedItemId && stockAvailableForRow <= 0);
                }
            });

            const selectedItem = inventoryItems.find((item) => String(item.id) === selectedItemId);

            if (selectedItem) {
                const stockAvailableForRow = Number(selectedItem.stock_actual) -
                    (quantityFromOtherRows.get(selectedItemId) ?? 0);
                const quantityInput = row.querySelector('[data-inventory-quantity]');

                quantityInput.max = String(Math.max(stockAvailableForRow, 0));
                quantityInput.setCustomValidity(
                    selectedQuantity > stockAvailableForRow ? 'No hay suficiente stock.' : ''
                );
            }
        });

        const hasAvailableStock = inventoryItems.some((item) => {
            return Number(item.stock_actual) > (requestedQuantities.get(String(item.id)) ?? 0);
        });

        if (addProductButton) {
            addProductButton.disabled = !hasAvailableStock;
        }

        if (noInventoryStockMessage) {
            noInventoryStockMessage.hidden = hasAvailableStock;
        }

        const baseAmountInCents = Math.round((Number(baseAmountInput?.value) || 0) * 100);
        totalAmountInput.value = ((baseAmountInCents + productsTotalInCents) / 100).toFixed(2);
    }

    function addProductRow(product = {}) {
        const hasAvailableStock = inventoryItems.some((item) => {
            const selectedQuantity = [...productRows.querySelectorAll('[data-inventory-row]')]
                .filter((row) => row.querySelector('[data-inventory-id]').value === String(item.id))
                .reduce((total, row) => {
                    return total + (Number(row.querySelector('[data-inventory-quantity]').value) || 0);
                }, 0);

            return Number(item.stock_actual) > selectedQuantity;
        });

        if (!hasAvailableStock && !product.inventario_id) {
            updateTotal();

            return;
        }

        const row = document.createElement('div');
        row.dataset.inventoryRow = '';
        row.className = 'grid grid-cols-1 gap-2 sm:grid-cols-[minmax(0,1fr)_7rem_auto] sm:items-end';

        const productField = document.createElement('div');
        const productLabel = document.createElement('label');
        productLabel.className = 'mb-1 block text-xs font-semibold text-gray-600';
        productLabel.textContent = 'Producto';

        const productSelect = document.createElement('select');
        productSelect.name = `productos[${productRowIndex}][inventario_id]`;
        productSelect.dataset.inventoryId = '';
        productSelect.required = true;
        productSelect.className = 'w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800';

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Selecciona un producto';
        productSelect.append(placeholder);

        inventoryItems.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.nombre_item} — $${Number(item.precio).toFixed(2)} (stock: ${item.stock_actual})`;
            option.selected = String(product.inventario_id ?? '') === String(item.id);
            option.disabled = Number(item.stock_actual) <= 0;
            productSelect.append(option);
        });

        productLabel.htmlFor = `inventory-product-${productRowIndex}`;
        productSelect.id = productLabel.htmlFor;
        productField.append(productLabel, productSelect);

        const quantityField = document.createElement('div');
        const quantityLabel = document.createElement('label');
        quantityLabel.className = 'mb-1 block text-xs font-semibold text-gray-600';
        quantityLabel.textContent = 'Cantidad';

        const quantityInput = document.createElement('input');
        quantityInput.type = 'number';
        quantityInput.name = `productos[${productRowIndex}][cantidad]`;
        quantityInput.dataset.inventoryQuantity = '';
        quantityInput.min = '1';
        quantityInput.step = '1';
        quantityInput.value = product.cantidad ?? '1';
        quantityInput.required = true;
        quantityInput.className = 'w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800';
        quantityLabel.htmlFor = `inventory-quantity-${productRowIndex}`;
        quantityInput.id = quantityLabel.htmlFor;
        quantityField.append(quantityLabel, quantityInput);

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = 'Quitar';
        removeButton.className = 'rounded-lg px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50';
        removeButton.addEventListener('click', () => {
            row.remove();
            updateTotal();
        });

        productSelect.addEventListener('change', updateTotal);
        quantityInput.addEventListener('input', updateTotal);
        row.append(productField, quantityField, removeButton);
        productRows.append(row);
        productRowIndex += 1;
        updateTotal();
    }

    toggleProductsButton.addEventListener('click', () => {
        const isExpanded = toggleProductsButton.getAttribute('aria-expanded') === 'true';
        productsPanel.hidden = isExpanded;
        toggleProductsButton.setAttribute('aria-expanded', String(!isExpanded));

        if (!isExpanded && productRows.children.length === 0 && inventoryItems.length > 0) {
            addProductRow();
        }
    });

    if (addProductButton) {
        addProductButton.addEventListener('click', () => addProductRow());
    }

    baseAmountInput?.addEventListener('input', updateTotal);
    receiptForm.addEventListener('submit', (event) => {
        updateTotal();

        if (!stockAlert.hidden) {
            event.preventDefault();
            stockAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    if (initialProducts.length > 0) {
        productsPanel.hidden = false;
        toggleProductsButton.setAttribute('aria-expanded', 'true');
        initialProducts.forEach((product) => addProductRow(product));
    } else {
        updateTotal();
    }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeInventorySelector, { once: true });
    } else {
        initializeInventorySelector();
    }
    })();
</script>
