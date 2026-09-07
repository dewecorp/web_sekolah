<?php
declare(strict_types=1);
if (Auth::check()) { header('Location: '.BASE_URL.'/admin'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if (!Security::loginAllowed($db, $ip)) $err = 'Terlalu banyak percobaan. Tunggu 10 menit.';
    elseif (!Security::verifyCsrf($_POST['csrf'] ?? null)) $err = 'CSRF tidak valid.';
    else {
        $id = trim($_POST['id'] ?? ''); $pw = $_POST['password'] ?? '';
        if (Auth::login($db, $id, $pw, !empty($_POST['remember']))) {
            Security::clearAttempts($db, $ip);
            header('Location: '.BASE_URL.'/admin'); exit;
        } else { Security::logAttempt($db, $ip, $id); $err = 'Username/email atau password salah.'; }
    }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><title>Login Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>body{font-family:"Plus Jakarta Sans",system-ui,sans-serif}h1{font-family:Fraunces,serif;letter-spacing:-.02em}</style></head>
<body class="bg-slate-900 min-h-screen grid place-items-center p-4">
<form method="post" data-loading class="bg-white rounded-2xl p-8 w-full max-w-sm grid gap-3">
<h1 class="font-extrabold text-xl">Login Admin</h1>
<?= Security::csrfField() ?>
<label class="text-sm grid gap-1">Username / Email<input name="id" required class="border rounded-lg p-2"></label>
<label class="text-sm grid gap-1">Password<input name="password" type="password" required class="border rounded-lg p-2"></label>
<label class="text-sm flex gap-2 items-center"><input type="checkbox" name="remember" value="1"> Remember Me</label>
<button class="bg-emerald-600 text-white rounded-lg p-2 font-bold">Login</button>
<a href="<?= Helper::url() ?>" class="text-center text-sm text-emerald-600">← Beranda</a>
</form>
<?php if($err): ?><script>Swal.fire('Login Gagal','<?= addslashes($err) ?>','error')</script><?php endif; ?>
</body></html>

