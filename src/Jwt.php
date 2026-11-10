<?php
namespace App;
class Jwt {
    public static function encode(array $payload, string $secret): string {
        $h = self::b64(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $b = self::b64(json_encode($payload));
        $s = self::b64(hash_hmac('sha256', "$h.$b", $secret, true));
        return "$h.$b.$s";
    }
    public static function decode(string $token, string $secret): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$h, $p, $s] = $parts;
        if (!hash_equals(self::b64(hash_hmac('sha256', "$h.$p", $secret, true)), $s)) return null;
        $payload = json_decode(self::b64decode($p), true);
        if (isset($payload['exp']) && $payload['exp'] < time()) return null;
        return $payload;
    }
    private static function b64(string $d): string { return rtrim(strtr(base64_encode($d), '+/', '-_'), '='); }
    private static function b64decode(string $d): string { return base64_decode(strtr($d, '-_', '+/')); }
}
