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
                <h1 id="biz-name" class="font-black text-xs tracking-tight text-stone-900 leading-none">PAN AL CHORI</h1>
                <span id="active-role-label" class="text-[10px] text-stone-400 font-semibold uppercase tracking-wider">Caja</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <!-- PUNTITO DE SINCRONIZACIÓN -->
            <span id="sync-indicator" class="flex h-2 w-2 relative" title="Conectando...">
                <span class="relative inline-flex rounded-full h-2 w-2 bg-stone-400"></span>
            </span>
            <button onclick="doLogout()" class="text-stone-400 hover:text-rose-600 p-2 text-xs rounded-xl"><i class="fa-solid fa-right-from-bracket"></i></button>
        </div>
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
                <button onclick="openIngredientModal()" class="bg-stone-900 hover:bg-stone-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg"><i class="fa-solid fa-plus"></i> Nuevo ingrediente</button>
            </div>
            <div id="inventory-list" class="space-y-2"></div>
        </div>

        <!-- ADMIN -->
        <div id="view-admin" class="hidden space-y-3">
            <div class="grid grid-cols-2 gap-2.5">
                <div class="bg-white p-3 rounded-2xl shadow-xs border border-stone-200"><span class="text-[10px] uppercase font-bold text-stone-400">Ventas Totales</span><h3 id="stat-sales" class="text-base font-black text-stone-900 mt-1">$0</h3></div>
                <div class="bg-white p-3 rounded-2xl shadow-xs border border-stone-200"><span class="text-[10px] uppercase font-bold text-stone-400">Total Pedidos</span><h3 id="stat-orders" class="text-base font-black text-stone-900 mt-1">0</h3></div>
            </div>
            
            <div class="bg-white rounded-2xl p-3.5 shadow-xs border border-stone-200 space-y-3">
                <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Estadísticas de Ventas</span>
                <div>
                    <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider mb-1"><i class="fa-solid fa-arrow-trend-up"></i> Más vendidos</p>
                    <div id="stats-top-products" class="space-y-1"></div>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-rose-600 uppercase tracking-wider mb-1"><i class="fa-solid fa-arrow-trend-down"></i> Menos vendidos</p>
                    <div id="stats-bottom-products" class="space-y-1"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-3.5 shadow-xs border border-stone-200 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Productos</span>
                    <button onclick="openProductModal()" class="bg-stone-900 hover:bg-stone-800 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg"><i class="fa-solid fa-plus"></i> Nuevo producto</button>
                </div>
                <div id="admin-menu-list" class="space-y-2 max-h-96 overflow-y-auto"></div>
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

<!-- MODAL INGREDIENTE -->
<div id="ingredient-modal" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm z-50 hidden overflow-y-auto">
    <div class="min-h-full flex items-start justify-center p-3 py-6">
        <div class="bg-white rounded-2xl p-4 max-w-md w-full shadow-xl space-y-3 border border-stone-100">
            <div class="flex justify-between items-center border-b border-stone-100 pb-2">
                <h3 class="font-bold text-stone-900 text-sm">Nuevo Ingrediente</h3>
                <button onclick="document.getElementById('ingredient-modal').classList.add('hidden')" class="text-stone-400 hover:text-rose-600 p-1 text-sm"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div><label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Nombre</label><input id="ingredient-modal-name" type="text" placeholder="Ej: Chorizo" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-3 py-2 text-sm font-medium focus:outline-none focus:border-amber-600"></div>
            <div class="grid grid-cols-2 gap-2">
                <div><label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Stock</label><input id="ingredient-modal-stock" type="number" value="0" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-3 py-2 text-sm font-bold text-center focus:outline-none"></div>
                <div><label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Unidad</label><select id="ingredient-modal-unit" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-3 py-2 text-sm font-medium focus:outline-none"><option value="und">unidades</option><option value="porc">porciones</option><option value="kg">kg</option><option value="g">g</option><option value="L">L</option><option value="ml">ml</option><option value="paq">paquete</option></select></div>
            </div>
            <div class="flex gap-2 pt-1">
                <button onclick="saveIngredient()" class="w-full bg-stone-900 hover:bg-stone-800 text-white font-bold py-2.5 rounded-xl text-xs">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PRODUCTO -->
<div id="product-modal" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm z-50 hidden overflow-y-auto">
    <div class="min-h-full flex items-start justify-center p-3 py-6">
        <div class="bg-white rounded-2xl p-4 max-w-md w-full shadow-xl space-y-3 border border-stone-100">
            <div class="flex justify-between items-center border-b border-stone-100 pb-2">
                <h3 class="font-bold text-stone-900 text-sm">Nuevo Producto</h3>
                <button onclick="document.getElementById('product-modal').classList.add('hidden')" class="text-stone-400 hover:text-rose-600 p-1 text-sm"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div><label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Nombre</label><input id="product-modal-name" type="text" placeholder="Ej: ESPECIAL" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-3 py-2 text-sm font-bold uppercase focus:outline-none focus:border-amber-600"></div>
            <div><label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Descripción</label><input id="product-modal-desc" type="text" placeholder="Ej: Chorizo + queso" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-3 py-2 text-sm focus:outline-none"></div>
            <div class="grid grid-cols-2 gap-2">
                <div><label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Precio</label><input id="product-modal-price" type="number" value="10000" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-3 py-2 text-sm font-bold text-center focus:outline-none"></div>
                <div>
                    <label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Icono</label>
                    <select id="product-modal-icon" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-2 py-2 text-sm font-medium focus:outline-none">
                        <option value="fa-solid fa-hotdog">Perro Caliente</option>
                        <option value="fa-solid fa-burger">Hamburguesa</option>
                        <option value="fa-solid fa-pizza-slice">Pizza</option>
                        <option value="fa-solid fa-taco">Taco</option>
                        <option value="fa-solid fa-burrito">Burrito</option>
                        <option value="fa-solid fa-drumstick-bite">Pollo / Alitas</option>
                        <option value="fa-solid fa-fish">Pescado</option>
                        <option value="fa-solid fa-bacon">Tocino</option>
                        <option value="fa-solid fa-egg">Huevos</option>
                        <option value="fa-solid fa-cheese">Queso</option>
                        <option value="fa-solid fa-bread-slice">Pan</option>
                        <option value="fa-solid fa-wheat-awn">Arepa / Masa</option>
                        <option value="fa-solid fa-bowl-food">Tazón / Ensalada</option>
                        <option value="fa-solid fa-bowl-rice">Arroz / Combo</option>
                        <option value="fa-solid fa-utensils">Cubiertos / Combo</option>
                        <option value="fa-solid fa-chopsticks">Fideos / Ramen</option>
                        <option value="fa-solid fa-pepper-hot">Picante / Salsas</option>
                        <option value="fa-solid fa-carrot">Verduras</option>
                        <option value="fa-solid fa-apple-whole">Frutas</option>
                        <option value="fa-solid fa-lemon">Cítricos / Limón</option>
                        <option value="fa-solid fa-ice-cream">Helado</option>
                        <option value="fa-solid fa-cookie">Galletas</option>
                        <option value="fa-solid fa-cake-candles">Pastel / Postre</option>
                        <option value="fa-solid fa-candy-cane">Dulces</option>
                        <option value="fa-solid fa-mug-hot">Café / Té</option>
                        <option value="fa-solid fa-mug-saucer">Bebida Caliente</option>
                        <option value="fa-solid fa-glass-water">Vaso de Agua / Jugo</option>
                        <option value="fa-solid fa-bottle-water">Botella / Gaseosa</option>
                        <option value="fa-solid fa-beer-mug-empty">Cerveza</option>
                        <option value="fa-solid fa-wine-glass">Vino</option>
                        <option value="fa-solid fa-martini-glass-citrus">Cóctel</option>
                        <option value="fa-solid fa-box">Caja / Para Llevar</option>
                        <option value="fa-solid fa-shopping-bag">Bolsa</option>
                        <option value="fa-solid fa-cubes-stacked">Adicional / Extras</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold text-stone-400 uppercase tracking-wider">Receta (Ingredientes)</label>
                <div id="product-modal-recipe" class="space-y-1.5 bg-stone-50 rounded-xl p-2 border border-stone-100 max-h-52 overflow-y-auto mt-1"></div>
            </div>
            <div class="flex gap-2 pt-1">
                <button onclick="saveProduct()" class="w-full bg-stone-900 hover:bg-stone-800 text-white font-bold py-2.5 rounded-xl text-xs">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script type="module" src="js/app.js?v=<?php echo time(); ?>
</body>
</html>
