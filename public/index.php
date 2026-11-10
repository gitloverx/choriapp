<?php
require_once __DIR__ . '/../src/bootstrap.php';
use App\Router;
use App\Controllers\{AuthController, ProductController, InventoryController, OrderController, StatsController, SuperAdminController};
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;
$r = new Router();
$r->add('POST', '/api/register', [AuthController::class, 'register']); $r->add('POST', '/api/login', [AuthController::class, 'login']);
$r->add('GET', '/api/superadmin', [SuperAdminController::class, 'index']); $r->add('PUT', '/api/superadmin/promote/:id', [SuperAdminController::class, 'promote']);
$r->add('GET', '/api/products', [ProductController::class, 'index']); $r->add('POST', '/api/products', [ProductController::class, 'store']); $r->add('PUT', '/api/products/:id', [ProductController::class, 'update']); $r->add('DELETE', '/api/products/:id', [ProductController::class, 'destroy']);
$r->add('GET', '/api/inventory', [InventoryController::class, 'index']); $r->add('POST', '/api/inventory', [InventoryController::class, 'store']); $r->add('PUT', '/api/inventory/:id', [InventoryController::class, 'update']);
$r->add('GET', '/api/orders', [OrderController::class, 'index']); $r->add('POST', '/api/orders', [OrderController::class, 'store']); $r->add('PUT', '/api/orders/:id', [OrderController::class, 'update']); $r->add('DELETE', '/api/orders', [OrderController::class, 'destroy']);
$r->add('GET', '/api/stats', [StatsController::class, 'index']);
$r->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
