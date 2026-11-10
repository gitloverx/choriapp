<?php
namespace App\Controllers;
use App\{Database, Middleware, Response};
class StatsController {
    public function index(array $params): void {
        $u = Middleware::auth(); $db = Database::get();
        $stmt = $db->prepare('SELECT SUM(total) AS total_sales, COUNT(*) AS total_orders FROM orders WHERE user_id=?'); $stmt->execute([$u['id']]); $agg = $stmt->fetch();
        $stmt = $db->prepare('SELECT product_name, SUM(quantity) AS total_qty FROM order_items oi JOIN orders o ON oi.order_id=o.id WHERE o.user_id=? GROUP BY product_name ORDER BY total_qty DESC'); $stmt->execute([$u['id']]); $top = $stmt->fetchAll();
        $stmt = $db->prepare('SELECT DATE(created_at) AS date, SUM(total) AS total FROM orders WHERE user_id=? GROUP BY DATE(created_at)'); $stmt->execute([$u['id']]); $daily = $stmt->fetchAll();
        Response::json(['total_sales'=>(int)($agg['total_sales'] ?? 0), 'total_orders'=>(int)$agg['total_orders'], 'top_products'=>$top, 'daily_sales'=>$daily]);
    }
}
