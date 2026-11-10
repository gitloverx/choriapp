<?php
namespace App\Controllers;
use App\{Database, Middleware, Response};
class ProductController {
    public function index(array $params): void {
        $u = Middleware::auth(); $db = Database::get();
        $stmt = $db->prepare('SELECT * FROM products WHERE user_id = ? ORDER BY id DESC'); $stmt->execute([$u['id']]); $p = $stmt->fetchAll();
        $stmt = $db->prepare('SELECT r.product_id, r.ingredient_id, r.quantity AS qty, i.name, i.unit FROM recipes r JOIN ingredients i ON i.id = r.ingredient_id JOIN products p ON p.id = r.product_id WHERE p.user_id = ?'); $stmt->execute([$u['id']]);
        $r = []; foreach ($stmt->fetchAll() as $row) $r[$row['product_id']][] = ['ingredient_id'=>$row['ingredient_id'],'qty'=>$row['qty'],'name'=>$row['name'],'unit'=>$row['unit']];
        foreach ($p as &$x) $x['recipe'] = $r[$x['id']] ?? [];
        Response::json($p);
    }
    public function store(array $params): void {
        $u = Middleware::auth(); $b = json_decode(file_get_contents('php://input'), true); $db = Database::get();
        $db->beginTransaction();
        $stmt = $db->prepare('INSERT INTO products (user_id, name, description, price, icon) VALUES (?, ?, ?, ?, ?) RETURNING *'); $stmt->execute([$u['id'], $b['name'], $b['description'], $b['price'], $b['icon']]); $prod = $stmt->fetch();
        if (!empty($b['recipe'])) { $rs = $db->prepare('INSERT INTO recipes (product_id, ingredient_id, quantity) VALUES (?, ?, ?)'); foreach ($b['recipe'] as $i) $rs->execute([$prod['id'], $i['ingredient_id'], $i['qty']]); }
        $db->commit(); Response::json($prod, 201);
    }
    public function update(array $params): void {
        $u = Middleware::auth(); $b = json_decode(file_get_contents('php://input'), true); $db = Database::get();
        $db->beginTransaction();
        $db->prepare('UPDATE products SET name=?, description=?, price=?, icon=? WHERE id=? AND user_id=?')->execute([$b['name'], $b['description'], $b['price'], $b['icon'], $params['id'], $u['id']]);
        $db->prepare('DELETE FROM recipes WHERE product_id=?')->execute([$params['id']]);
        if (!empty($b['recipe'])) { $rs = $db->prepare('INSERT INTO recipes (product_id, ingredient_id, quantity) VALUES (?, ?, ?)'); foreach ($b['recipe'] as $i) $rs->execute([$params['id'], $i['ingredient_id'], $i['qty']]); }
        $db->commit(); Response::json(['message'=>'ok']);
    }
    public function destroy(array $params): void { Middleware::auth(); Database::get()->prepare('DELETE FROM products WHERE id=? AND user_id=?')->execute([$params['id'], Middleware::auth()['id']]); Response::json(['message'=>'ok']); }
}
