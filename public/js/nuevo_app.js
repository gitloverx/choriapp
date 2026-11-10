import { apiFetch, formatMoney } from './api.js';

let menuProducts = [], cart = [], orders = [], inventory = [];
const token = localStorage.getItem('token');

if(!token) window.location.href = 'login.php';

function conectar() {
    const dot = document.getElementById('sync-indicator');
    if (dot) dot.innerHTML = '<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>';
}

function error(msg) {
    const dot = document.getElementById('sync-indicator');
    if (dot) dot.innerHTML = '<span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>';
    alert("❌ Error: " + msg);
}

async function cargar() {
    try {
        menuProducts = await apiFetch('/products');
        orders = await apiFetch('/orders');
        inventory = await apiFetch('/inventory');
        conectar();
        switchRole('cajero');
    } catch(e) {
        error(e.message);
    }
}

window.switchRole = (role) => {
    ['cajero', 'cocinero', 'inventario', 'admin'].forEach(r => {
        document.getElementById(`view-${r}`).classList.add('hidden');
        document.getElementById(`nav-btn-${r}`).className = "flex flex-col items-center gap-1 text-stone-400 transition";
    });
    document.getElementById(`view-${role}`).classList.remove('hidden');
    document.getElementById(`nav-btn-${role}`).className = "flex flex-col items-center gap-1 text-amber-600 transition";
    document.getElementById('active-role-label').textContent = role.toUpperCase();
    
    if(role === 'cajero') pintarMenu();
    if(role === 'admin') pintarAdmin();
    if(role === 'inventario') pintarInventario();
};

function pintarMenu() {
    const grid = document.getElementById('menu-grid');
    if(!grid) return;
    grid.innerHTML = menuProducts.map(p => `
        <div class="bg-white p-2.5 rounded-2xl border border-stone-200 flex justify-between items-center mb-2 shadow-sm">
            <div>
                <h4 class="font-bold text-xs">${p.name}</h4>
                <p class="text-[10px] text-stone-400">${formatMoney(p.price)}</p>
            </div>
            <button onclick="addCart(${p.id})" class="w-8 h-8 bg-amber-500 text-white rounded-lg flex items-center justify-center"><i class="fa-solid fa-plus"></i></button>
        </div>
    `).join('');
}

window.addCart = (id) => {
    const p = menuProducts.find(x => x.id === id);
    const ex = cart.find(x => x.id === id);
    if(ex) ex.qty++; else cart.push({ id: p.id, name: p.name, price: p.price, qty: 1 });
    pintarCarrito();
};

window.clearCart = () => { cart = []; pintarCarrito(); };

function pintarCarrito() {
    const c = document.getElementById('cart-items');
    if(!cart.length) {
        c.innerHTML = '<p class="text-stone-400 text-xs py-2 text-center">Sin items</p>';
        document.getElementById('cart-total').textContent = '$0';
        return;
    }
    let total = 0;
    c.innerHTML = cart.map(it => {
        total += it.price * it.qty;
        return `<div class="flex justify-between py-1 border-b"><span class="text-xs font-bold">${it.qty}x ${it.name}</span><span class="text-xs text-amber-600 font-bold">${formatMoney(it.price * it.qty)}</span></div>`;
    }).join('');
    document.getElementById('cart-total').textContent = formatMoney(total);
}

window.submitOrder = async () => {
    if(!cart.length) return alert('Vacío');
    const total = cart.reduce((s, i) => s + (i.price * i.qty), 0);
    const cliente = document.getElementById('customer-name').value || 'Cliente';
    try {
        await apiFetch('/orders', { method: 'POST', body: JSON.stringify({ customer_name: cliente, total, items: cart }) });
        alert('Pedido enviado');
        clearCart();
    } catch(e) { alert(e.message); }
};

function pintarAdmin() {
    const list = document.getElementById('admin-menu-list');
    if(!list) return;
    list.innerHTML = menuProducts.map(p => `
        <div class="bg-stone-50 p-2 rounded border mb-1 flex justify-between">
            <span class="font-bold text-xs">${p.name}</span>
            <span class="text-xs text-amber-600">${formatMoney(p.price)}</span>
        </div>
    `).join('');
}

function pintarInventario() {
    const list = document.getElementById('inventory-list');
    if(!list) return;
    list.innerHTML = inventory.map(i => `
        <div class="bg-white p-2 rounded border mb-1 flex justify-between">
            <span class="font-bold text-xs">${i.name}</span>
            <span class="text-xs">${i.stock} ${i.unit}</span>
        </div>
    `).join('');
}

cargar();
