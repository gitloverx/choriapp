<?php
namespace App;
use PDO;
class Database {
    private static ?PDO $pdo = null;
    public static function get(): PDO {
        if (self::$pdo instanceof PDO) return self::$pdo;
        $url = getenv('DATABASE_URL') ?: '';
        $p = parse_url($url);
        $dsn = "pgsql:host={$p['host']};port={$p['port']};dbname=".ltrim($p['path'], '/').";sslmode=require";
        self::$pdo = new PDO($dsn, urldecode($p['user']), urldecode($p['pass']), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        return self::$pdo;
    }
}
