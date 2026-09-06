<?php
declare(strict_types=1);
final class Database {
    private static ?PDO $pdo = null;
    public static function conn(): PDO {
        if (self::$pdo) return self::$pdo;
        $c = require dirname(__DIR__) . '/config/database.php';
        $dsn = "mysql:host={$c['host']};dbname={$c['name']};charset={$c['charset']}";
        self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return self::$pdo;
    }
    public static function setting(string $key, string $def = ''): string {
        try {
            $s = self::conn()->prepare('SELECT `value` FROM settings WHERE `key`=?');
            $s->execute([$key]);
            return (string)($s->fetchColumn() ?? $def);
        } catch (Throwable) { return $def; }
    }
}
