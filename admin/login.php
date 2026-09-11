<?php
declare(strict_types=1);
if (Auth::check()) { header('Location: '.BASE_URL.'/admin'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $id = trim($_POST['id'] ?? ''); $pw = $_POST['password'] ?? '';
    if (!Security::loginAllowed($db, $ip, $id)) {
        $remaining = Security::remainingLockoutSeconds($db, $ip, $id);
        $err = 'Terlalu banyak percobaan gagal. Silakan tunggu ' . Helper::humanDuration(max($remaining, 60)) . ' sebelum mencoba lagi.';
    } elseif (!Security::verifyCsrf($_POST['csrf'] ?? null)) $err = 'CSRF tidak valid.';
    else {
        if (Auth::login($db, $id, $pw, !empty($_POST['remember']))) {
            Security::clearAttempts($db, $ip, $id);
            header('Location: '.BASE_URL.'/admin'); exit;
        } else {
            Security::logAttempt($db, $ip, $id);
            $err = 'Username/email atau password salah.';
        }
    }
}
?>
<?php $school=Database::setting('school_name','Sekolah'); $logo=Database::setting('logo',''); $loginFav=Database::setting('favicon','')?:$logo; ?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Admin - <?= Helper::e($school) ?></title>
<?php if($loginFav!==''): ?><link rel="icon" href="<?= Helper::e(Helper::upload($loginFav)) ?>"><?php endif; ?>
<script src="https://cdn.tailwindcss.com"></script><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>:root{--font-body:"Plus Jakarta Sans",system-ui,-apple-system,"Segoe UI",sans-serif}html,body,input,button{font-family:var(--font-body)!important}h1{letter-spacing:-.02em}</style></head>
<body class="min-h-screen grid place-items-center p-4 bg-slate-950">
<div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_15%,rgba(16,185,129,.25),transparent_45%),radial-gradient(circle_at_85%_85%,rgba(139,92,246,.18),transparent_50%)]"></div>
<div class="relative w-full max-w-sm">
<div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden border">
<div class="bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600 p-7 text-white text-center">
<?php if($logo): ?><img src="<?= Helper::upload($logo) ?>" alt="" class="h-16 w-auto object-contain mx-auto" style="filter:drop-shadow(0 0 1px #fff) drop-shadow(0 0 10px rgba(255,255,255,.95)) drop-shadow(0 4px 16px rgba(255,255,255,.5))"><?php else: ?><span class="w-14 h-14 rounded-2xl bg-white grid place-items-center mx-auto shadow font-extrabold text-emerald-700 text-xl"><?= Helper::e(mb_substr($school,0,1)) ?></span><?php endif; ?>
<h1 class="font-extrabold text-2xl mt-3">Masuk Admin</h1><p class="text-white/80 text-xs mt-1"><?= Helper::e($school) ?></p>
</div>
<form method="post" data-loading class="p-6 grid gap-3 bg-white">
<h2 class="font-bold text-sm">Login Admin</h2>
<?= Security::csrfField() ?>
<label class="text-xs font-bold grid gap-1">Username / Email<input name="id" required placeholder="admin / email" class="border rounded-xl p-2.5 font-normal"></label>
<label class="text-xs font-bold grid gap-1">Password<div class="relative"><input id="pw" name="password" type="password" required placeholder="••••••••" class="border rounded-xl p-2.5 pr-9 w-full font-normal"><button type="button" onclick="var i=document.getElementById('pw');i.type=i.type==='password'?'text':'password';this.firstElementChild.classList.toggle('fa-eye');this.firstElementChild.classList.toggle('fa-eye-slash')" class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 grid place-items-center text-slate-400 hover:text-slate-600"><i class="fa fa-eye text-xs"></i></button></div></label>
<label class="text-xs flex gap-2 items-center font-medium"><input type="checkbox" name="remember" value="1"> Remember Me</label>
<button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl py-2.5 font-bold shadow">Masuk</button>
<a href="<?= Helper::url() ?>" class="text-center text-xs font-bold text-emerald-600 hover:underline"><i class="fa fa-arrow-left mr-1"></i>Beranda</a>
</form>
</div></div>
<?php if($err): ?><script>Swal.fire('Login Gagal','<?= addslashes($err) ?>','error')</script><?php endif; ?>
</body></html>




