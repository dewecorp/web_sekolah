<?php
declare(strict_types=1);
final class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
            session_start();
        }
    }
    public static function flash(string $k, $v = null) {
        if ($v !== null) { $_SESSION['_f'][$k] = $v; return; }
        $v = $_SESSION['_f'][$k] ?? null;
        unset($_SESSION['_f'][$k]);
        return $v;
    }
}
