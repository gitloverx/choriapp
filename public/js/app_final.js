import { apiFetch, logout, formatMoney } from './api.js';

window.doLogout = logout;
let menuProducts = [], currentCart = [], orders = [], inventory = [];

const bName = localStorage.getItem('businessName') || 'Mi Negocio';
const bTitle = document.getElementById('biz-name');
if(bTitle) bTitle.textContent = bName.toUpperCase();

async function init() {
    if(!localStorage.getItem('token')) return window.location.href = 'login.php';
    try {
        await loadData();
        switchRole('cajero');
    } catch (e) {
        const dot = document.getElementById('sync-indicator');
        if (dot) {
            dot.title = 'Error de conexión';
            dot.innerHTML = '<span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>';
        }
        alert("Error de conexión: " + e.message);
    }
}

async function loadData() {
    const [p, o, i] = await Promise.all([
        apiFetch('/products'), 
        apiFetch('/orders'), 
        apiFetch('/inventory')
    ]);
    menuProducts = p; orders = o; inventory = i;

    const dot = document.getElementById('sync-indicator');
    if (dot) {
        dot.title = 'Conectado a la nube';
        dot.innerHTML = '<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>';
    }
}

window.switchRole = (role) => {
    ['cajero', 'cocinero', 'inventario', 'admin'].forEach(r => {
        document.getElementById(`view-${r}`).classList.add('hidden');
        document.getElementById(`nav-btn-${r}`).className = "flex flex-col items-center gap-1 text-stone-400 hover:text-stone-700 transition";
    });
    document.getElementById(`view-${role}`).classList.remove('hidden');
    document.getElementById(`nav-btn-${role}`).className = "flex flex-col items-center gap-1 text-amber-600 transition";
    document.getElementById('active-role-label').textContent = role.toUpperCase();
    
    if(role === 'cajero') renderMenu();
    if(role === 'cocinero') renderKitchen();
    if(role === 'inventario') renderInventory();
    if(role === 'admin') renderAdmin();
};

// --- CAJERO ---
function renderMenu() {
    const grid = document.getElementById('menu-grid');
    if (!grid) return;

    grid.innerHTML = menuProducts.map(p => {
        const inCart = currentCart.find(c => c.id === p.id);
        const qtyBadge = inCart ? `<span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full mr-2">${inCart.qty}</span>` : '';

        return `
        <div class="bg-white p-2.5 rounded-2xl border border-stone-200 flex justify-between items-center mb-2 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-stone-100 flex items-center justify-center text-xs text-stone-600"><i class="${p.icon || 'fa-solid fa-utensils'}"></i></div>
                <div>
                    <h4 class="font-bold text-xs text-stone-900">${p.name}</h4>
                    <p class="text-[10px] text-stone-400">${formatMoney(p.price)}</p>
                </div>
            </div>
            <div class="flex items-center">
                ${qtyBadge}
                <button onclick="addToCart(${p.id})" class="w-8 h-8 bg-amber-500 hover:bg-amber-600 text-white rounded-lg flex items-center justify-center shadow-sm active:scale-95 transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                </button>
            </div>
        </div>
        `;
    }).join('');
}

window.addToCart = (id) => {
    const prod = menuProducts.find(p => p.id === id);
    const ex = currentCart.find(c => c.id === id);
    if(ex) ex.qty++; 
    else currentCart.push({ id: prod.id, name: prod.name, price: prod.price, qty: 1 });
    renderCart();
    renderMenu();
};

window.updateCartQty = (id, delta) => {
    const it = currentCart.find(c => c.id === id);
    if(it) {
        it.qty += delta;
        if(it.qty <= 0) currentCart = currentCart.filter(c => c.id !== id);
    }
    renderCart();
    renderMenu();
};

window.clearCart = () => { currentCart = []; renderCart(); renderMenu(); };

function renderCart() {
    const c = document.getElementById('cart-items');
    if(!c) return;
    
    if(!currentCart.length) { 
        c.innerHTML = '<p class="text-stone-400 text-xs py-2 text-center">Sin items en el pedido</p>'; 
        document.getElementById('cart-total').textContent = '$0'; 
        return; 
    }
    
    let total = 0;
    c.innerHTML = currentCart.map(it => {
        total += it.price * it.qty;
        return `
        <div class="flex justify-between items-center py-2 border-b border-stone-100 last:border-0">
            <div class="flex-1 min-w-0 pr-2">
                <p class="font-bold text-xs text-stone-800 truncate">${it.name}</p>
                <p class="text-[10px] text-stone-500">${formatMoney(it.price)} c/u</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="updateCartQty(${it.id}, -1)" class="w-6 h-6 bg-stone-100 hover:bg-stone-200 rounded text-stone-600 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-minus text-[10px]"></i>
                </button>
                <span class="text-xs font-bold w-4 text-center">${it.qty}</span>
                <button onclick="updateCartQty(${it.id}, 1)" class="w-6 h-6 bg-stone-100 hover:bg-stone-200 rounded text-stone-600 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                </button>
                <span class="font-black text-xs text-amber-600 w-14 text-right ml-1">${formatMoney(it.price * it.qty)}</span>
            </div>
        </div>`;
    }).join('');
    
    document.getElementById('cart-total').textContent = formatMoney(total);
}

window.submitOrder = async () => {
    if(!currentCart.length) return alert('Carrito vacío');
    const customer = document.getElementById('customer-name').value || 'Cliente';
    const total = currentCart.reduce((s, i) => s + (i.price * i.qty), 0);
    try {
        await apiFetch('/orders', { method: 'POST', body: JSON.stringify({ customer_name: customer, total, items: currentCart }) });
        alert('¡Pedido enviado a cocina!');
        clearCart(); await loadData(); renderMenu();
    } catch(e) { alert(e.message); }
};

// --- COCINA ---
function renderKitchen() {
    const pending = orders.filter(o => o.status === 'pendiente');
    document.getElementById('kitchen-count').textContent = `${pending.length} pendientes`;
    document.getElementById('kitchen-orders-list').innerHTML = pending.map(o => `
        <div class="bg-white rounded-2xl p-3 shadow-sm border-l-4 border-amber-500 urgent-pulse mb-2">
            <div class="flex justify-between items-center mb-2">
                <b class="text-sm">#${o.id} - ${o.customer_name}</b>
                <button onclick="markDone(${o.id})" class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg font-bold text-xs">Listo</button>
            </div>
            <ul class="text-xs space-y-1">${(o.items||[]).map(i => `<li>${i.qty}x ${i.name}</li>`).join('')}</ul>
        </div>
    `).join('') || '<p class="text-stone-400 text-xs text-center">No hay pedidos pendientes</p>';
}

window.markDone = async (id) => {
    await apiFetch(`/orders/${id}`, { method: 'PUT', body: JSON.stringify({ status: 'listo' }) });
    await loadData(); renderKitchen();
};

// --- INVENTARIO ---
function renderInventory() {
    document.getElementById('inventory-list').innerHTML = inventory.map(i => `
        <div class="bg-white rounded-xl p-3 flex justify-between items-center border border-stone-200 mb-2">
            <div><b class="text-sm">${i.name}</b><p class="text-[10px] text-stone-500">Stock: ${i.stock} ${i.unit}</p></div>
        </div>
    `).join('');
}

window.openIngredientModal = () => {
    document.getElementById('ingredient-modal-name').value = '';
    document.getElementById('ingredient-modal-stock').value = 0;
    document.getElementById('ingredient-modal').classList.remove('hidden');
};

window.saveIngredient = async () => {
    const name = document.getElementById('ingredient-modal-name').value;
    const stock = parseInt(document.getElementById('ingredient-modal-stock').value);
    const unit = document.getElementById('ingredient-modal-unit').value;
    if(!name) return alert('Escribe un nombre');
    
    try {
        await apiFetch('/inventory', { method: 'POST', body: JSON.stringify({ name, stock, unit }) });
        document.getElementById('ingredient-modal').classList.add('hidden');
        await loadData(); renderInventory();
    } catch(e) { alert(e.message); }
};

// --- ADMIN ---
async function renderAdmin() {
    try {
        const stats = await apiFetch('/stats');
        document.getElementById('stat-sales').textContent = formatMoney(stats.total_sales);
        document.getElementById('stat-orders').textContent = stats.total_orders;
        
        const tops = stats.top_products || [];
        const top3 = tops.slice(0, 3);
        const bot3 = tops.slice(-3).reverse();

        document.getElementById('stats-top-products').innerHTML = top3.length ? top3.map((p, i) => `
            <div class="flex justify-between items-center bg-emerald-50 p-2 rounded-lg text-xs border border-emerald-100">
                <span class="font-semibold text-emerald-800">${i+1}. ${p.product_name}</span>
                <span class="font-bold text-emerald-700">${p.total_qty} und</span>
            </div>`).join('') : '<p class="text-stone-400 text-xs py-1 text-center">Sin ventas aún.</p>';

        document.getElementById('stats-bottom-products').innerHTML = bot3.length ? bot3.map((p, i) => `
            <div class="flex justify-between items-center bg-rose-50 p-2 rounded-lg text-xs border border-rose-100">
                <span class="font-semibold text-rose-800">${i+1}. ${p.product_name}</span>
                <span class="font-bold text-rose-700">${p.total_qty} und</span>
            </div>`).join('') : '<p class="text-stone-400 text-xs py-1 text-center">Sin ventas aún.</p>';
            
        document.getElementById('admin-menu-list').innerHTML = menuProducts.map(p => `
            <div class="bg-stone-50 p-2.5 rounded-xl text-xs border border-stone-100 mb-1">
                <div class="flex justify-between items-center gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-stone-200 text-stone-700 flex items-center justify-center text-[11px] shrink-0"><i class="${p.icon}"></i></div>
                        <div class="min-w-0"><p class="font-bold text-stone-900 truncate">${p.name}</p></div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0"><span class="font-bold text-amber-600">${formatMoney(p.price)}</span></div>
                </div>
            </div>
        `).join('');
    } catch(e) { console.error(e); }
}

window.openProductModal = () => {
    document.getElementById('product-modal-name').value = '';
    document.getElementById('product-modal-desc').value = '';
    document.getElementById('product-modal-price').value = 10000;
    
    document.getElementById('product-modal-recipe').innerHTML = inventory.map(ing => `
        <div class="flex justify-between items-center bg-white p-2 rounded-lg border border-stone-200">
            <span class="text-xs font-semibold text-stone-700 truncate">${ing.name}</span>
            <div class="flex items-center gap-1">
                <input type="number" min="0" value="0" data-ingid="${ing.id}" class="recipe-qty w-16 text-center text-xs font-bold bg-stone-50 border border-stone-200 rounded-lg py-1">
                <span class="text-[10px] text-stone-400 w-8">${ing.unit}</span>
            </div>
        </div>
    `).join('');

    document.getElementById('product-modal').classList.remove('hidden');
};

window.saveProduct = async () => {
    const name = document.getElementById('product-modal-name').value.toUpperCase();
    const desc = document.getElementById('product-modal-desc').value;
    const price = parseInt(document.getElementById('product-modal-price').value);
    const icon = document.getElementById('product-modal-icon').value;
    
    if(!name || price <= 0) return alert('Nombre y precio válido son obligatorios');

    const recipe = [];
    document.querySelectorAll('.recipe-qty').forEach(input => {
        const qty = parseInt(input.value);
        if(qty > 0) recipe.push({ ingredient_id: parseInt(input.dataset.ingid), qty: qty });
    });

    try {
        await apiFetch('/products', { method: 'POST', body: JSON.stringify({ name, description: desc, price, icon, recipe }) });
        document.getElementById('product-modal').classList.add('hidden');
        await loadData(); renderAdmin();
        alert('Producto creado exitosamente');
    } catch(e) { alert(e.message); }
};

init();
