<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../src/bootstrap.php';
use App\Database;

try {
    $db = Database::get();
    $users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $prods = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    
    echo json_encode([
        "status" => "OK",
        "mensaje" => "Render se conectó a Supabase correctamente.",
        "usuarios" => $users,
        "productos_totales" => $prods
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(["status" => "ERROR", "mensaje" => $e->getMessage()]);
}
