<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiPuestico - Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-100 text-stone-800 font-sans min-h-screen">
<div class="max-w-5xl mx-auto p-4 space-y-4">
    <header class="bg-white rounded-2xl p-4 shadow-xs border border-stone-200 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="bg-stone-900 text-white w-9 h-9 rounded-xl flex items-center justify-center"><i class="fa-solid fa-shield-halved"></i></div>
            <div><h1 class="font-black text-sm text-stone-900">SUPER ADMIN</h1><p class="text-[10px] text-stone-400 uppercase tracking-wider font-bold">Panel Global</p></div>
        </div>
        <button onclick="window.location.href='login.php'" class="text-stone-400 hover:text-rose-600 p-2 text-xs"><i class="fa-solid fa-right-from-bracket"></i> Salir</button>
    </header>
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white p-3.5 rounded-2xl shadow-xs border border-stone-200"><p class="text-[10px] uppercase font-bold text-stone-400">Usuarios</p><h3 id="kpi-users" class="text-xl font-black text-stone-900 mt-1">—</h3></div>
        <div class="bg-white p-3.5 rounded-2xl shadow-xs border border-stone-200"><p class="text-[10px] uppercase font-bold text-stone-400">Pedidos</p><h3 id="kpi-orders" class="text-xl font-black text-stone-900 mt-1">—</h3></div>
        <div class="bg-white p-3.5 rounded-2xl shadow-xs border border-stone-200"><p class="text-[10px] uppercase font-bold text-stone-400">Ventas globales</p><h3 id="kpi-sales" class="text-xl font-black text-stone-900 mt-1">—</h3></div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-stone-200 space-y-2">
        <p class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Usuarios registrados</p>
        <div id="users-list" class="space-y-2 max-h-96 overflow-y-auto"></div>
    </div>
</div>
<script type="module">
    import { apiFetch } from './js/api.js';
    
    // BARRERA DE SEGURIDAD: Si no es admin, lo devuelve al login
    const role = localStorage.getItem('role');
    if (!localStorage.getItem('token') || role !== 'admin') {
        window.location.href = 'login.php';
    }

    async function loadData() {
        try {
            const data = await apiFetch('/superadmin');
            document.getElementById('kpi-users').textContent = data.kpis.users;
            document.getElementById('kpi-orders').textContent = data.kpis.orders;
            document.getElementById('kpi-sales').textContent = '$' + Number(data.kpis.sales).toLocaleString('es-CO');
            document.getElementById('users-list').innerHTML = data.users.map(u => `
                <div class="flex justify-between items-center bg-stone-50 p-2.5 rounded-xl border border-stone-100 text-xs">
                    <div><div class="font-bold">${u.business_name || 'Sin nombre'}</div><div class="text-[10px] text-stone-500">${u.email} · ${u.role}</div></div>
                    <div class="text-right"><div class="font-bold text-amber-600">$${Number(u.total_sales).toLocaleString('es-CO')}</div><div class="text-[10px]">${u.total_orders} pedidos</div></div>
                </div>
            `).join('');
        } catch(e) { alert(e.message); }
    }
    loadData();
</script>
</body>
</html>
