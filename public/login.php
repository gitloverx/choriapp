<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiPuestico - Acceso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-100 text-stone-800 font-sans min-h-screen flex flex-col">
    <main class="flex-1 flex flex-col items-center justify-center px-6 py-10">
        <div class="w-20 h-20 bg-gradient-to-br from-amber-400 to-amber-600 text-white rounded-3xl flex items-center justify-center text-4xl mb-5 shadow-2xl">
            <i class="fa-solid fa-shop"></i>
        </div>
        <h1 class="font-black text-3xl text-stone-900 tracking-tight text-center">MiPuestico</h1>
        <p class="text-sm text-stone-500 text-center mt-2 max-w-xs">El sistema para tu puesto de comida.</p>

        <div class="w-full max-w-sm mt-8 bg-white p-6 rounded-3xl shadow-xl border border-stone-200">
            <div class="flex border-b border-stone-200 mb-4">
                <button id="tab-login" onclick="switchTab('login')" class="flex-1 py-2 text-sm font-bold text-amber-600 border-b-2 border-amber-600">Iniciar Sesión</button>
                <button id="tab-register" onclick="switchTab('register')" class="flex-1 py-2 text-sm font-bold text-stone-400 hover:text-stone-600">Crear Cuenta</button>
            </div>

            <div id="form-login" class="space-y-3">
                <input id="login-email" type="email" placeholder="Correo electrónico" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-600">
                <input id="login-password" type="password" placeholder="Contraseña" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-600">
                <button onclick="doLogin()" id="btn-login" class="w-full bg-stone-900 hover:bg-stone-800 text-white font-bold py-3.5 rounded-xl text-sm shadow-md transition">Entrar</button>
            </div>

            <div id="form-register" class="hidden space-y-3">
                <input id="reg-business" type="text" placeholder="Nombre de tu puesto" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-600">
                <input id="reg-email" type="email" placeholder="Correo electrónico" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-600">
                <input id="reg-password" type="password" placeholder="Contraseña (mín. 8 caracteres)" class="w-full bg-stone-100 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-amber-600">
                <button onclick="doRegister()" id="btn-register" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3.5 rounded-xl text-sm shadow-md transition">Crear mi Tienda</button>
            </div>

            <p id="msg" class="text-[11px] text-rose-600 text-center font-bold mt-3"></p>
        </div>
    </main>

    <script type="module">
        import { apiFetch, setToken } from './js/api.js?v=2';

        // Limpiar sesión vieja al cargar la página de login
        localStorage.clear();

        window.switchTab = (tab) => {
            document.getElementById('form-login').classList.toggle('hidden', tab !== 'login');
            document.getElementById('form-register').classList.toggle('hidden', tab !== 'register');
            document.getElementById('tab-login').className = tab === 'login' ? 'flex-1 py-2 text-sm font-bold text-amber-600 border-b-2 border-amber-600' : 'flex-1 py-2 text-sm font-bold text-stone-400 hover:text-stone-600';
            document.getElementById('tab-register').className = tab === 'register' ? 'flex-1 py-2 text-sm font-bold text-amber-600 border-b-2 border-amber-600' : 'flex-1 py-2 text-sm font-bold text-stone-400 hover:text-stone-600';
            document.getElementById('msg').textContent = '';
        };

        window.doLogin = async () => {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const btn = document.getElementById('btn-login');
            btn.disabled = true; btn.textContent = 'Conectando...';
            try {
                const res = await apiFetch('/login', { method: 'POST', body: JSON.stringify({ email, password }) });
                setToken(res.token);
                localStorage.setItem('role', res.role);
                localStorage.setItem('businessName', res.business_name || 'Mi Negocio');
                
                // Redirección basada en el ROL
                if (res.role === 'admin') {
                    window.location.href = 'admin.php';
                } else {
                    window.location.href = 'app.php';
                }
            } catch (e) {
                document.getElementById('msg').textContent = e.message;
                btn.disabled = false; btn.textContent = 'Entrar';
            }
        };

        window.doRegister = async () => {
            const business_name = document.getElementById('reg-business').value;
            const email = document.getElementById('reg-email').value;
            const password = document.getElementById('reg-password').value;
            const btn = document.getElementById('btn-register');
            if(!business_name || !email || !password) return document.getElementById('msg').textContent = 'Llena todos los campos';
            
            const business_slug = business_name.toLowerCase().replace(/[^a-z0-9]+/g, '-') + '-' + Math.floor(Math.random()*1000);

            btn.disabled = true; btn.textContent = 'Creando...';
            try {
                await apiFetch('/register', { method: 'POST', body: JSON.stringify({ email, password, business_name, business_slug }) });
                document.getElementById('msg').style.color = '#059669';
                document.getElementById('msg').textContent = '¡Tienda creada! Iniciando sesión...';
                setTimeout(() => doLogin(), 1000);
            } catch (e) {
                document.getElementById('msg').textContent = e.message;
                btn.disabled = false; btn.textContent = 'Crear mi Tienda';
            }
        };
    </script>
</body>
</html>
