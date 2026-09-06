<?php
$metaTitle = 'Kontak - ' . Database::setting('school_name','Sekolah');
require ROOT."/templates/frontend/header.php";
$addr = Database::setting("address","");
$phone = Database::setting("phone","");
$email = Database::setting("email","");
$maps = Database::setting("maps_embed","");
$days = Database::setting("service_days","Senin - Jumat");
$open = Database::setting("service_open","07:00");
$close = Database::setting("service_close","15:30");
$fmt = fn($t) => str_replace(':', '.', substr(trim($t), 0, 5));
$hours = trim($days) !== '' ? $days . ', ' . $fmt($open) . ' - ' . $fmt($close) . ' WIB' : Database::setting("service_hours","Senin - Jumat, 07.00 - 15.30 WIB");
$wa = preg_replace('/\D+/', '', $phone);
?>
<div class="max-w-7xl mx-auto px-4 py-10">
<div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 text-white p-7 md:p-12 shadow-xl">
<span class="absolute -right-16 -top-20 w-64 h-64 rounded-full border-[28px] border-white/10"></span>
<span class="absolute -left-20 -bottom-24 w-72 h-72 rounded-full border-[36px] border-white/10"></span>
<div class="relative max-w-3xl">
<span class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-3 py-1 text-[11px] font-bold uppercase tracking-[.16em]"><i class="fa fa-headset text-amber-300"></i>Hubungi Kami</span>
<h1 class="text-3xl md:text-5xl font-extrabold leading-tight mt-4">Kontak <?= Helper::e(Database::setting('school_name','Sekolah')) ?></h1>
<p class="text-white/80 text-sm md:text-base max-w-2xl mt-4">Silakan hubungi kami melalui alamat, telepon, email, atau formulir pesan. Kami merespons pada jam kerja sekolah.</p>
<div class="mt-5 flex flex-wrap gap-2">
<?php if($phone): ?><a href="tel:<?= Helper::e($phone) ?>" class="inline-flex items-center gap-2 bg-white text-emerald-700 px-4 py-2.5 rounded-xl font-bold text-sm"><i class="fa fa-phone"></i><?= Helper::e($phone) ?></a><?php endif; ?>
<?php if($wa): ?><a href="https://wa.me/<?= Helper::e($wa) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 border border-white/40 px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-white/10"><i class="fab fa-whatsapp"></i>WhatsApp</a><?php endif; ?>
<?php if($email): ?><a href="mailto:<?= Helper::e($email) ?>" class="inline-flex items-center gap-2 border border-white/40 px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-white/10"><i class="fa fa-envelope"></i>Email</a><?php endif; ?>
</div>
</div>
</div>
<div class="grid lg:grid-cols-5 gap-4 mt-4 w-full">
<div class="lg:col-span-2 grid gap-4">
<div class="grid sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-4">
<div class="rounded-2xl border bg-white dark:bg-slate-800 p-5 card-hover group transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-emerald-300 hover:bg-emerald-50/50 dark:hover:bg-slate-700"><span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-200 grid place-items-center transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-6"><i class="fa fa-location-dot"></i></span><p class="font-bold mt-3 text-sm">Alamat</p><p class="text-sm text-slate-500 mt-1"><?= $addr ? Helper::e($addr) : '-' ?></p><?php if($addr): ?><a href="https://www.google.com/maps/search/<?= urlencode($addr) ?>" target="_blank" rel="noopener" class="text-emerald-600 text-sm font-bold mt-2 inline-block transition-transform duration-300 group-hover:translate-x-1">Lihat peta &rarr;</a><?php endif; ?></div>
<div class="rounded-2xl border bg-white dark:bg-slate-800 p-5 card-hover group transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-sky-300 hover:bg-sky-50/50 dark:hover:bg-slate-700"><span class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900 text-sky-700 dark:text-sky-200 grid place-items-center transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-6"><i class="fa fa-phone"></i></span><p class="font-bold mt-3 text-sm">Telepon / WA</p><p class="text-sm text-slate-500 mt-1"><?= $phone ? Helper::e($phone) : '-' ?></p><?php if($wa): ?><a href="https://wa.me/<?= Helper::e($wa) ?>" target="_blank" rel="noopener" class="text-emerald-600 text-sm font-bold mt-2 inline-block transition-transform duration-300 group-hover:translate-x-1">Chat sekarang &rarr;</a><?php endif; ?></div>
<div class="rounded-2xl border bg-white dark:bg-slate-800 p-5 card-hover group transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-amber-300 hover:bg-amber-50/50 dark:hover:bg-slate-700"><span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-200 grid place-items-center transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-6"><i class="fa fa-envelope"></i></span><p class="font-bold mt-3 text-sm">Email</p><p class="text-sm text-slate-500 mt-1 break-all"><?= $email ? Helper::e($email) : '-' ?></p><?php if($email): ?><a href="mailto:<?= Helper::e($email) ?>" class="text-emerald-600 text-sm font-bold mt-2 inline-block transition-transform duration-300 group-hover:translate-x-1">Kirim email &rarr;</a><?php endif; ?></div>
<div class="rounded-2xl border bg-white dark:bg-slate-800 p-5 card-hover group transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-violet-300 hover:bg-violet-50/50 dark:hover:bg-slate-700"><span class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-900 text-violet-700 dark:text-violet-200 grid place-items-center transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-6"><i class="fa fa-clock"></i></span><p class="font-bold mt-3 text-sm">Jam Layanan</p><p class="text-sm text-slate-500 mt-1"><?= nl2br(Helper::e($hours)) ?></p></div>
</div>
<?php if($maps): ?><div class="rounded-2xl overflow-hidden border bg-white dark:bg-slate-800 min-h-[240px]"><?= $maps ?></div><?php endif; ?>
</div>
<form class="lg:col-span-3 rounded-2xl border bg-white dark:bg-slate-800 p-5 md:p-7 grid gap-3 content-start" onsubmit="Swal.fire('Berhasil!','Pesan terkirim.','success');return false">
<div><h2 class="font-extrabold text-lg">Kirim Pesan</h2><p class="text-sm text-slate-500">Isi formulir, pesan diteruskan ke email sekolah.</p></div>
<div class="grid sm:grid-cols-2 gap-3"><label class="grid gap-1 text-sm font-semibold">Nama<input required placeholder="Nama lengkap" class="border rounded-xl p-2.5 font-normal"></label><label class="grid gap-1 text-sm font-semibold">Email<input required type="email" placeholder="nama@email.com" class="border rounded-xl p-2.5 font-normal"></label></div>
<label class="grid gap-1 text-sm font-semibold">Subjek<input placeholder="Contoh: Info PPDB" class="border rounded-xl p-2.5 font-normal"></label>
<label class="grid gap-1 text-sm font-semibold">Pesan<textarea required rows="5" placeholder="Tulis pesan Anda..." class="border rounded-xl p-2.5 font-normal"></textarea></label>
<button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl py-3 font-bold"><i class="fa fa-paper-plane mr-1"></i>Kirim Pesan</button>
</form>
</div>
</div>
<?php require ROOT."/templates/frontend/footer.php"; ?>
