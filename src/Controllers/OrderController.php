<?php
namespace App\Controllers;
use App\{Database, Middleware, Response};
class OrderController {
    public function index(array $params): void {
        $u = Middleware::auth(); $db = Database::get();
        $stmt = $db->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC'); $stmt->execute([$u['id']]); $o = $stmt->fetchAll();
        $stmt = $db->prepare('SELECT oi.order_id, oi.product_name AS name, oi.quantity AS qty, oi.price_at_sale AS price FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE o.user_id=?'); $stmt->execute([$u['id']]);
        $items = []; foreach ($stmt->fetchAll() as $it) $items[$it['order_id']][] = ['name'=>$it['name'],'qty'=>$it['qty'],'price'=>$it['price']];
        foreach ($o as &$x) $x['items'] = $items[$x['id']] ?? [];
        Response::json($o);
    }
    public function store(array $params): void {
        $u = Middleware::auth(); $b = json_decode(file_get_contents('php://input'), true); $db = Database::get();
        $db->beginTransaction();
        $stmt = $db->prepare('INSERT INTO orders (user_id, customer_name, total, status) VALUES (?, ?, ?, ?) RETURNING *'); $stmt->execute([$u['id'], $b['customer_name'] ?? 'Cliente', $b['total'], 'pendiente']); $ord = $stmt->fetch();
        if (!empty($b['items'])) { $is = $db->prepare('INSERT INTO order_items (order_id, product_name, quantity, price_at_sale) VALUES (?, ?, ?, ?)'); foreach ($b['items'] as $i) $is->execute([$ord['id'], $i['name'], $i['qty'], $i['price']]); }
        $db->commit(); Response::json($ord, 201);
    }
    public function update(array $params): void { $u = Middleware::auth(); $b = json_decode(file_get_contents('php://input'), true); Database::get()->prepare('UPDATE orders SET status=? WHERE id=? AND user_id=?')->execute([$b['status'], $params['id'], $u['id']]); Response::json(['message'=>'ok']); }
    public function destroy(array $params): void { $u = Middleware::auth(); Database::get()->prepare('DELETE FROM orders WHERE user_id=?')->execute([$u['id']]); Response::json(['message'=>'ok']); }
}
