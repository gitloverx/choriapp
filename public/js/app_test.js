import { apiFetch, logout, formatMoney } from './api.js';

window.doLogout = logout;
let menuProducts = [], currentCart = [], orders = [], inventory = [];

async function initApp() {
    // 1. Verificar token
    if(!localStorage.getItem('token')) {
        window.location.href = 'login.php';
        return;
    }

    try {
        // 2. Pedir datos
        const p = await apiFetch('/products');
        const o = await apiFetch('/orders');
        const i = await apiFetch('/inventory');
        
        menuProducts = p; 
        orders = o; 
        inventory = i;

        // 3. Cambiar puntito a verde
        const dot = document.getElementById('sync-indicator');
        if (dot) dot.innerHTML = '<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>';

        // 4. Renderizar
        renderMenu();
        
    } catch (error) {
        // SI HAY CUALQUIER ERROR, LO MOSTRAMOS EN PANTALLA
        alert("TEST FALLIDO:\n" + error.message);
        console.error(error);
    }
}

function renderMenu() {
    const grid = document.getElementById('menu-grid');
    if (!grid) return alert('ERROR: No se encontró el div menu-grid');

    if (menuProducts.length === 0) {
        grid.innerHTML = '<p class="text-rose-500 text-xs text-center">No hay productos en la BD.</p>';
        return;
    }

    grid.innerHTML = menuProducts.map(p => `
        <div class="bg-white p-2.5 rounded-2xl border border-stone-200 mb-2 shadow-sm flex justify-between items-center">
            <div>
                <h4 class="font-bold text-xs">${p.name}</h4>
                <p class="text-[10px] text-stone-400">${formatMoney(p.price)}</p>
            </div>
            <button class="bg-amber-500 text-white w-8 h-8 rounded-lg"><i class="fa-solid fa-plus"></i></button>
        </div>
    `).join('');
}

// Iniciar
initApp();
