<?php
namespace App\Controllers;
use App\{Database, Middleware, Response};
class InventoryController {
    public function index(array $params): void { $u = Middleware::auth(); $stmt = Database::get()->prepare('SELECT * FROM ingredients WHERE user_id=? ORDER BY id DESC'); $stmt->execute([$u['id']]); Response::json($stmt->fetchAll()); }
    public function store(array $params): void { $u = Middleware::auth(); $b = json_decode(file_get_contents('php://input'), true); $stmt = Database::get()->prepare('INSERT INTO ingredients (user_id, name, stock, unit) VALUES (?, ?, ?, ?) RETURNING *'); $stmt->execute([$u['id'], $b['name'], $b['stock'], $b['unit']]); Response::json($stmt->fetch(), 201); }
    public function update(array $params): void { $u = Middleware::auth(); $b = json_decode(file_get_contents('php://input'), true); $stmt = Database::get()->prepare('UPDATE ingredients SET stock=? WHERE id=? AND user_id=? RETURNING *'); $stmt->execute([$b['stock'], $params['id'], $u['id']]); Response::json($stmt->fetch() ?: []); }
}
