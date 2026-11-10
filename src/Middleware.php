<?php
namespace App;
class Middleware {
    public static function auth(): array {
        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $token = explode(' ', $hdr)[1] ?? '';
        $payload = Jwt::decode($token, getenv('JWT_SECRET'));
        if (!$payload) Response::json(['error' => 'Token inválido'], 401);
        return $payload;
    }
    public static function admin(): array {
        $u = self::auth();
        if ($u['role'] !== 'admin') Response::json(['error' => 'No eres Admin'], 403);
        return $u;
    }
}
