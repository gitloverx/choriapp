<?php
namespace App\Controllers;
use App\{Database, DefaultData, Jwt, Response};
class AuthController {
    public function register(array $params): void {
        $b = json_decode(file_get_contents('php://input'), true) ?? [];
        $email = trim((string)($b['email'] ?? ''));
        $role = ($email === getenv('SUPERADMIN_EMAIL')) ? 'admin' : 'user';
        $db = Database::get();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare('INSERT INTO users (email, password_hash, business_name, business_slug, role) VALUES (?, ?, ?, ?, ?) RETURNING id, email, business_name, role');
            $stmt->execute([$email, password_hash($b['password'], PASSWORD_BCRYPT), $b['business_name'], $b['business_slug'], $role]);
            $user = $stmt->fetch();
            DefaultData::seedForUser($db, $user['id']);
            $db->commit();
            Response::json(['message' => 'Usuario creado', 'user' => $user], 201);
        } catch (\PDOException $e) {
            if ($db->inTransaction()) $db->rollBack();
            // Si el error es 23505 es porque el correo ya existe
            if ($e->getCode() === '23505') {
                Response::json(['error' => 'Ese correo ya está registrado. Usa otro o inicia sesión.'], 400);
            }
            Response::json(['error' => 'Error BD', 'detalle_real' => $e->getMessage()], 400);
        }
    }
    public function login(array $params): void {
        $b = json_decode(file_get_contents('php://input'), true) ?? [];
        $stmt = Database::get()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([trim((string)($b['email'] ?? ''))]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($b['password'], $user['password_hash'])) Response::json(['error' => 'Credenciales inválidas'], 401);
        $token = Jwt::encode(['id' => $user['id'], 'role' => $user['role'], 'exp' => time() + 7200], getenv('JWT_SECRET'));
        Response::json(['token' => $token, 'business_name' => $user['business_name'], 'role' => $user['role']]);
    }
}
