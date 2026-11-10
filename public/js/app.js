import { apiFetch, logout, formatMoney } from './api.js';

window.doLogout = logout;
let menuProducts = [], currentCart = [], orders = [], inventory = [];

const bName = localStorage.getItem('businessName') || 'Mi Negocio';
document.getElementById('biz-name').textContent = bName.toUpperCase();

async function init() {
    if(!localStorage.getItem('token')) return window.location.href = 'login.php';
    try {
        await loadData();
        // AUTO-REPARACIÓN: Si el menú está vacío, forzamos la inyección de datos
        if (menuProducts.length === 0) {
            console.log("Menú vacío. Inyectando datos por defecto...");
            await apiFetch('/seed', { method: 'POST' });
            await loadData(); // Volver a cargar
        }
        switchRole('cajero');
    } catch (e) {
        alert("Error cargando datos: " + e.message);
    }
}

async function loadData() {
    const [p, o, i] = await Promise.all([
        apiFetch('/products'), 
        apiFetch('/orders'), 
        apiFetch('/inventory')
    ]);
    menuProducts = p; orders = o; inventory = i;
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

function renderMenu() {
    document.getElementById('menu-grid').innerHTML = menuProducts.map(p => `
        <div onclick="addToCart(${p.id})" class="bg-white p-2.5 rounded-2xl border border-stone-200 active:scale-[0.99] cursor-pointer flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-stone-100 flex items-center justify-center text-xs"><i class="${p.icon || 'fa-solid fa-utensils'}"></i></div>
                <div><h4 class="font-bold text-xs">${p.name}</h4><p class="text-[10px] text-stone-400">${p.description||''}</p></div>
            </div>
            <div class="text-right"><span class="font-black text-xs text-amber-600">${formatMoney(p.price)}</span></div>
        </div>
    `).join('');
}

window.addToCart = (id) => {
    const prod = menuProducts.find(p => p.id === id);
    const ex = currentCart.find(c => c.id === id);
    if(ex) ex.qty++; else currentCart.push({ id: prod.id, name: prod.name, price: prod.price, qty: 1 });
    renderCart();
};

window.clearCart = () => { currentCart = []; renderCart(); };

function renderCart() {
    const c = document.getElementById('cart-items');
    if(!currentCart.length) { c.innerHTML = '<p class="text-stone-400 text-xs text-center py-2">Sin items</p>'; document.getElementById('cart-total').textContent = '$0'; return; }
    let total = 0;
    c.innerHTML = currentCart.map(it => {
        total += it.price * it.qty;
        return `<div class="flex justify-between py-1"><span>${it.name} (x${it.qty})</span><span>${formatMoney(it.price * it.qty)}</span></div>`;
    }).join('');
    document.getElementById('cart-total').textContent = formatMoney(total);
}

window.submitOrder = async () => {
    if(!currentCart.length) return alert('Carrito vacío');
    const customer = document.getElementById('customer-name').value || 'Cliente';
    const total = currentCart.reduce((s, i) => s + (i.price * i.qty), 0);
    try {
        await apiFetch('/orders', { method: 'POST', body: JSON.stringify({ customer_name: customer, total, items: currentCart }) });
        alert('¡Pedido enviado!');
        clearCart(); await loadData(); renderMenu();
    } catch(e) { alert(e.message); }
};

function renderKitchen() {
    const pending = orders.filter(o => o.status === 'pendiente');
    document.getElementById('kitchen-count').textContent = `${pending.length} pendientes`;
    document.getElementById('kitchen-orders-list').innerHTML = pending.map(o => `
        <div class="bg-white rounded-2xl p-3 shadow-sm border-l-4 border-amber-500 urgent-pulse">
            <div class="flex justify-between items-center mb-2">
                <b class="text-sm">#${o.id} - ${o.customer_name}</b>
                <button onclick="markDone(${o.id})" class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg font-bold text-xs">Listo</button>
            </div>
            <ul class="text-xs space-y-1">${(o.items||[]).map(i => `<li>${i.qty}x ${i.name}</li>`).join('')}</ul>
        </div>
    `).join('') || '<p class="text-stone-400 text-xs text-center">No hay pedidos</p>';
}

window.markDone = async (id) => {
    await apiFetch(`/orders/${id}`, { method: 'PUT', body: JSON.stringify({ status: 'listo' }) });
    await loadData(); renderKitchen();
};

function renderInventory() {
    document.getElementById('inventory-list').innerHTML = inventory.map(i => `
        <div class="bg-white rounded-xl p-3 flex justify-between items-center border border-stone-200">
            <div><b class="text-sm">${i.name}</b><p class="text-[10px] text-stone-500">Stock: ${i.stock} ${i.unit}</p></div>
        </div>
    `).join('');
}

async function renderAdmin() {
    try {
        const stats = await apiFetch('/stats');
        document.getElementById('stat-sales').textContent = formatMoney(stats.total_sales);
        document.getElementById('stat-orders').textContent = stats.total_orders;
    } catch(e) { console.error(e); }
}

init();
