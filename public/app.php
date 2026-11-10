<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PAN AL CHORI - App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>tailwind.config={theme:{extend:{colors:{brand:{600:'#d4a017'}},fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>body{-webkit-tap-highlight-color:transparent;touch-action:manipulation} @keyframes urgentPulse{0%,100%{box-shadow:0 0 0 0 rgba(225,29,72,.7)}50%{box-shadow:0 0 0 4px rgba(225,29,72,0)}} .urgent-pulse{animation:urgentPulse 1.5s infinite}</style>
</head>
<body class="bg-stone-100 text-stone-800 font-sans min-h-screen flex flex-col pb-20 select-none">

<div id="main-app" class="flex-1 flex flex-col">
    <header class="bg-white border-b border-stone-200 sticky top-0 z-40 px-4 py-3 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="bg-amber-500 text-white w-8 h-8 rounded-xl flex items-center justify-center text-sm"><i class="fa-solid fa-hotdog"></i></div>
            <div>
                <h1 class="font-black text-xs tracking-tight text-stone-900 leading-none">PAN AL CHORI</h1>
                <span id="active-role-label" class="text-[10px] text-stone-400 font-semibold uppercase tracking-wider">Caja</span>
            </div>
        </div>
        <button onclick="doLogout()" class="text-stone-400 hover:text-rose-600 p-2 text-xs rounded-xl"><i class="fa-solid fa-right-from-bracket"></i></button>
    </header>

    <main class="flex-1 max-w-md w-full mx-auto p-3 space-y-3">
        <!-- CAJERO -->
        <div id="view-cajero" class="space-y-3">
            <div class="bg-white rounded-2xl p-2.5 shadow-xs border border-stone-200 flex gap-2 items-center">
                <i class="fa-solid fa-user text-stone-400 pl-2 text-xs"></i>
                <input id="customer-name" type="text" placeholder="Nombre cliente..." class="w-full bg-transparent text-sm focus:outline-none font-medium">
            </div>
            <div class="space-y-2">
                <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Menú Rápido</span>
                <div id="menu-grid" class="grid grid-cols-1 gap-2"></div>
            </div>
            <div class="bg-white rounded-2xl p-3.5 shadow-sm border border-stone-200 space-y-3">
                <div class="flex justify-between items-center border-b border-stone-100 pb-2">
                    <span class="font-bold text-xs text-stone-900 flex items-center gap-1.5 uppercase tracking-wide"><i class="fa-solid fa-receipt text-amber-600"></i> Pedido Actual</span>
                    <button onclick="clearCart()" class="text-[11px] text-stone-400 hover:text-rose-600 font-medium">Vaciar</button>
                </div>
                <div id="cart-items" class="divide-y divide-stone-100 max-h-36 overflow-y-auto space-y-1 pr-1 text-xs">
                    <p class="text-stone-400 text-xs py-2 text-center">Sin items en el pedido</p>
                </div>
                <div class="border-t border-stone-100 pt-2 flex justify-between items-center">
                    <span class="text-[11px] text-stone-500 uppercase font-bold">Total:</span>
                    <span id="cart-total" class="text-amber-600 font-black text-base">$0</span>
                </div>
                <button onclick="submitOrder()" id="btn-submit" class="w-full bg-stone-900 hover:bg-stone-800 text-white font-bold py-3 rounded-xl text-xs shadow-xs active:scale-[0.98] transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check text-xs"></i> Enviar a Cocina
                </button>
            </div>
        </div>

        <!-- COCINERO -->
        <div id="view-cocinero" class="hidden space-y-3">
            <div class="flex justify-between items-center px-1">
                <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Cocina (En Vivo)</span>
                <span id="kitchen-count" class="bg-amber-100 text-amber-800 text-[11px] px-2 py-0.5 rounded-full font-bold">0 pendientes</span>
            </div>
            <div id="kitchen-orders-list" class="space-y-2.5"></div>
        </div>

        <!-- INVENTARIO -->
        <div id="view-inventario" class="hidden space-y-3">
            <div class="flex justify-between items-center px-1">
                <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Inventario</span>
            </div>
            <div id="inventory-list" class="space-y-2"></div>
        </div>

        <!-- ADMIN -->
        <div id="view-admin" class="hidden space-y-3">
            <div class="grid grid-cols-2 gap-2.5">
                <div class="bg-white p-3 rounded-2xl shadow-xs border border-stone-200"><span class="text-[10px] uppercase font-bold text-stone-400">Ventas Totales</span><h3 id="stat-sales" class="text-base font-black text-stone-900 mt-1">$0</h3></div>
                <div class="bg-white p-3 rounded-2xl shadow-xs border border-stone-200"><span class="text-[10px] uppercase font-bold text-stone-400">Total Pedidos</span><h3 id="stat-orders" class="text-base font-black text-stone-900 mt-1">0</h3></div>
            </div>
        </div>
    </main>

    <nav class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-stone-200 py-2 px-3 flex justify-around items-center z-40 max-w-md mx-auto">
        <button onclick="switchRole('cajero')" id="nav-btn-cajero" class="flex flex-col items-center gap-1 text-amber-600 transition"><i class="fa-solid fa-cash-register text-base"></i><span class="text-[10px] font-bold">Caja</span></button>
        <button onclick="switchRole('cocinero')" id="nav-btn-cocinero" class="flex flex-col items-center gap-1 text-stone-400 hover:text-stone-700 transition"><i class="fa-solid fa-fire-burner text-base"></i><span class="text-[10px] font-bold">Cocina</span></button>
        <button onclick="switchRole('inventario')" id="nav-btn-inventario" class="flex flex-col items-center gap-1 text-stone-400 hover:text-stone-700 transition"><i class="fa-solid fa-boxes-stacked text-base"></i><span class="text-[10px] font-bold">Inventario</span></button>
        <button onclick="switchRole('admin')" id="nav-btn-admin" class="flex flex-col items-center gap-1 text-stone-400 hover:text-stone-700 transition"><i class="fa-solid fa-chart-pie text-base"></i><span class="text-[10px] font-bold">Admin</span></button>
    </nav>
</div>

<script type="module" src="js/app.js?v=2"></script>
</body>
</html>
