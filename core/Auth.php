<?php
declare(strict_types=1);
final class Auth {
    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function check(): bool { return isset($_SESSION['user']); }
    public static function requireLogin(): void {
        if (!self::check()) { header('Location: ' . BASE_URL . '/admin/login'); exit; }
    }
    public static function requireRole(array $roles): void {
        self::requireLogin();
        if (!in_array($_SESSION['user']['role'] ?? '', $roles, true)) { http_response_code(403); require ROOT . '/templates/error/403.php'; exit; }
    }
    public static function login(PDO $db, string $id, string $pass, bool $remember = false): bool {
        $s = $db->prepare("SELECT u.*, r.name role FROM users u JOIN roles r ON r.id=u.role_id WHERE (u.username=? OR u.email=?) AND u.is_active=1 AND u.deleted_at IS NULL LIMIT 1");
        $s->execute([$id, $id]);
        $u = $s->fetch();
        if (!$u || !password_verify($pass, $u['password'])) return false;
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$u['id'],'name'=>$u['name'],'username'=>$u['username'],'role'=>$u['role'],'avatar'=>$u['avatar']];
        $db->prepare("UPDATE users SET last_login_at=NOW() WHERE id=?")->execute([$u['id']]);
        if ($remember) {
            $tok = bin2hex(random_bytes(32));
            $db->prepare("UPDATE users SET remember_token=? WHERE id=?")->execute([hash('sha256',$tok), $u['id']]);
            setcookie('remember', $u['id'] . ':' . $tok, time()+30*86400, '/', '', false, true);
        }
        self::log($db, 'login', 'auth', 'Login berhasil');
        return true;
    }
    public static function tryRemember(PDO $db): void {
        if (self::check() || empty($_COOKIE['remember']) || !str_contains($_COOKIE['remember'], ':')) return;
        [$id, $tok] = explode(':', $_COOKIE['remember'], 2);
        if (!ctype_digit($id) || $tok === '') return;
        $s = $db->prepare("SELECT u.*, r.name role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=? AND u.remember_token=? AND u.is_active=1 AND u.deleted_at IS NULL LIMIT 1");
        $s->execute([(int)$id, hash('sha256', $tok)]);
        $u = $s->fetch();
        if (!$u) return;
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$u['id'],'name'=>$u['name'],'username'=>$u['username'],'role'=>$u['role'],'avatar'=>$u['avatar']];
        $db->prepare("UPDATE users SET last_login_at=NOW() WHERE id=?")->execute([$u['id']]);
    }
    public static function logout(PDO $db): void {
        if (isset($_SESSION['user'])) { try { $db->prepare("UPDATE users SET remember_token=NULL WHERE id=?")->execute([$_SESSION['user']['id']]); } catch (Throwable) {} try { self::log($db, 'logout', 'auth', 'Logout'); } catch (Throwable) {} }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) { $p = session_get_cookie_params(); setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']); }
        setcookie('remember', '', time()-3600, '/');
        session_destroy();
    }
    public static function log(PDO $db, string $action, string $module, string $desc = ''): void {
        $s = $db->prepare("INSERT INTO activity_logs(user_id,action,module,description,ip) VALUES(?,?,?,?,?)");
        $s->execute([$_SESSION['user']['id'] ?? null, $action, $module, $desc, $_SERVER['REMOTE_ADDR'] ?? null]);
    }
}
