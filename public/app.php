<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAN AL CHORI - App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-100 text-stone-800 font-sans min-h-screen p-4">
    <div id="test-view">
        <h2 class="text-lg font-bold mb-2">Estado del Sistema</h2>
        <pre id="status" class="bg-white p-4 rounded-lg shadow text-xs whitespace-pre-wrap border">Cargando datos...</pre>
    </div>

    <script type="module">
        import { apiFetch } from './js/api.js';
        
        async function test() {
            const el = document.getElementById('status');
            if(!localStorage.getItem('token')) {
                el.textContent = "❌ No hay token. Por favor ve a login.php";
                return;
            }
            try {
                const p = await apiFetch('/products');
                el.textContent = "✅ Conexión exitosa.\n\nProductos recibidos: " + p.length + "\n\n" + JSON.stringify(p, null, 2);
            } catch (e) {
                el.textContent = "❌ Error en la petición:\n" + e.message;
            }
        }
        test();
    </script>
</body>
</html>
