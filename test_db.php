<?php
require_once __DIR__ . '/src/bootstrap.php';
use App\Database;

echo "=== PRUEBA DE CONEXION A SUPABASE ===\n\n";

try {
    $db = Database::get();
    echo "✅ Conexión exitosa a PostgreSQL (Supabase).\n\n";

    // 1. Verificar usuarios
    $users = $db->query("SELECT id, email, business_name FROM users")->fetchAll();
    echo "👥 Usuarios encontrados: " . count($users) . "\n";
    foreach ($users as $u) {
        echo "   - ID: {$u['id']} | Email: {$u['email']} | Negocio: {$u['business_name']}\n";
    }

    // 2. Verificar productos
    $products = $db->query("SELECT id, user_id, name FROM products")->fetchAll();
    echo "\n📦 Productos en total en la BD: " . count($products) . "\n";
    
    if (count($products) == 0) {
        echo "⚠️  La tabla de productos está VACÍA.\n";
        echo "Inyectando productos al usuario más reciente...\n";
        
        // Buscar el último usuario registrado
        $lastUser = $db->query("SELECT id FROM users ORDER BY id DESC LIMIT 1")->fetch();
        if ($lastUser) {
            $uid = $lastUser['id'];
            $stmt = $db->prepare('INSERT INTO ingredients (user_id, name, stock, unit) VALUES (?, ?, ?, ?)');
            $stmt->execute([$uid, 'Chorizo', 50, 'und']);
            $stmt->execute([$uid, 'Pan', 50, 'und']);
            
            $stmt = $db->prepare('INSERT INTO products (user_id, name, description, price, icon) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$uid, 'CLÁSICO TEST', 'Chorizo + pan', 10000, 'fa-solid fa-hotdog']);
            $stmt->execute([$uid, 'GRATINADO TEST', 'Con queso', 11000, 'fa-solid fa-cheese']);
            
            echo "✅ ¡Se inyectaron 2 productos y 2 ingredientes al usuario ID: $uid!\n";
        }
    } else {
        echo "✅ Hay productos en la base de datos.\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR FATAL DE CONEXIÓN:\n";
    echo $e->getMessage() . "\n";
}
