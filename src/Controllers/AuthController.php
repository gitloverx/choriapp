<?php
namespace App\Controllers;
use App\{Database, DefaultData, Jwt, Response};
class AuthController {
    public function register(array $params): void {
        $b = json_decode(file_get_contents('php://input'), true) ?? [];
        $role = ($b['email'] === getenv('SUPERADMIN_EMAIL')) ? 'admin' : 'user';
        $db = Database::get();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare('INSERT INTO users (email, password_hash, business_name, business_slug, role) VALUES (?, ?, ?, ?, ?) RETURNING id, email, business_name, role');
            $stmt->execute([$b['email'], password_hash($b['password'], PASSWORD_BCRYPT), $b['business_name'], $b['business_slug'], $role]);
            $user = $stmt->fetch();
            DefaultData::seedForUser($db, $user['id']);
            $db->commit();
            Response::json(['message' => 'Usuario creado', 'user' => $user], 201);
        } catch (\PDOException $e) {
            if ($db->inTransaction()) $db->rollBack();
            Response::json(['error' => $e->getCode() === '23505' ? 'Correo ya registrado' : 'Error BD'], 400);
        }
    }
    public function login(array $params): void {
        $b = json_decode(file_get_contents('php://input'), true) ?? [];
        $stmt = Database::get()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$b['email']]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($b['password'], $user['password_hash'])) Response::json(['error' => 'Credenciales inválidas'], 401);
        $token = Jwt::encode(['id' => $user['id'], 'role' => $user['role'], 'exp' => time() + 7200], getenv('JWT_SECRET'));
        Response::json(['token' => $token, 'business_name' => $user['business_name'], 'role' => $user['role']]);
    }
}
