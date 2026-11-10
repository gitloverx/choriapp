import { apiFetch, logout, formatMoney } from './api.js';

window.doLogout = logout;
let menuProducts = [], currentCart = [], orders = [], inventory = [];

const bName = localStorage.getItem('businessName') || 'Mi Negocio';
document.getElementById('biz-name').textContent = bName.toUpperCase();

async function init() {
    if(!localStorage.getItem('token')) return window.location.href = 'login.php';
    try {
        await loadData();
        // Si el menú está vacío, forzamos la inyección (Auto-reparación)
        if (menuProducts.length === 0) {
            console.log("Base de datos vacía. Inyectando datos...");
            await apiFetch('/seed', { method: 'POST' });
            await loadData(); 
        }
        switchRole('cajero');
    } catch (e) {
        alert("Error de conexión con el servidor: " + e.message);
    }
}

async function loadData() {
    const [p, o, i] = await Promise.all([
        apiFetch('/products'), 
        apiFetch('/orders'), 
        apiFetch('/inventory')
    ]);
    console.log("Datos recibidos del backend -> Productos:", p, "Pedidos:", o, "Inventario:", i);
    menuProducts = p; orders = o; inventory = i;
}
// ... (El resto de tu código de renderizado sigue igual) ...
