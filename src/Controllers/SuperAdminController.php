<?php
namespace App\Controllers;
use App\{Database, Middleware, Response};
class SuperAdminController {
    public function index(array $params): void {
        Middleware::admin(); $db = Database::get();
        $users = $db->query('SELECT u.id, u.email, u.business_name, u.role, u.created_at, (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS total_orders, (SELECT COALESCE(SUM(total), 0) FROM orders o WHERE o.user_id = u.id) AS total_sales FROM users u ORDER BY total_sales DESC')->fetchAll();
        Response::json(['kpis'=>['users'=>(int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn(), 'orders'=>(int)$db->query('SELECT COUNT(*) FROM orders')->fetchColumn(), 'sales'=>(int)$db->query('SELECT COALESCE(SUM(total),0) FROM orders')->fetchColumn()], 'users'=>$users]);
    }
    public function promote(array $params): void {
        Middleware::admin(); $stmt = Database::get()->prepare("UPDATE users SET role='admin' WHERE id=? RETURNING id, business_name, role"); $stmt->execute([$params['id']]); Response::json(['message'=>'ok', 'user'=>$stmt->fetch()]);
    }
}
