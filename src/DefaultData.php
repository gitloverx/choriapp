<?php
namespace App;
use PDO;
class DefaultData {
    public static function seedForUser(PDO $db, int $userId): void {
        $ing = [['Chorizo', 80, 'und'], ['Pan', 85, 'und'], ['Queso', 45, 'porc'], ['Papas', 60, 'porc']];
        $stmt = $db->prepare('INSERT INTO ingredients (user_id, name, stock, unit) VALUES (?, ?, ?)');
        foreach ($ing as [$n, $s, $u]) $stmt->execute([$userId, $n, $s, $u]);
        $prod = [['CLÁSICO', 'Chorizo + pan', 10000, 'fa-solid fa-hotdog'], ['GRATINADO', 'Con queso', 11000, 'fa-solid fa-cheese'], ['COMBO', 'Choripán + papas', 14000, 'fa-solid fa-utensils'], ['JUGOS', 'Jugo natural', 4000, 'fa-solid fa-glass-water'], ['GASEOSAS', 'Botella personal', 3500, 'fa-solid fa-bottle-water']];
        $stmt = $db->prepare('INSERT INTO products (user_id, name, description, price, icon) VALUES (?, ?, ?, ?, ?)');
        foreach ($prod as $p) $stmt->execute([$userId, ...$p]);
    }
}
