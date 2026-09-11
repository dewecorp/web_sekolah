<?php
$metaTitle = 'Kontak - ' . Database::setting('school_name','Sekolah');
try{$DB->exec("CREATE TABLE IF NOT EXISTS contact_messages(id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(150) NOT NULL,email VARCHAR(190) NOT NULL,subject VARCHAR(190) NOT NULL DEFAULT '',message MEDIUMTEXT NOT NULL,is_read TINYINT(1) NOT NULL DEFAULT 0,created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,KEY idx_read_created (is_read,created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");}catch(Throwable $e){ error_log('inbox-migrate: '.$e->getMessage()); }
$contactOk=''; $contactErr='';
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['contact_send']??'')==='1'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ $contactErr='Sesi kedaluwarsa. Muat ulang halaman.'; }
  else{
    $nm=trim((string)($_POST['name']??'')); $em=trim((string)($_POST['email']??'')); $sj=trim((string)($_POST['subject']??'')); $ps=trim((string)($_POST['message']??''));
    if(mb_strlen($nm)<2){ $contactErr='Nama minimal 2 huruf.'; }
    elseif(!filter_var($em,FILTER_VALIDATE_EMAIL)||mb_strlen($em)>190){ $contactErr='Email tidak valid.'; }
    elseif(mb_strlen($ps)<5){ $contactErr='Pesan minimal 5 huruf.'; }
    else{
      try{ $DB->prepare("INSERT INTO contact_messages(name,email,subject,message) VALUES(?,?,?,?)")->execute([mb_substr($nm,0,150),$em,mb_substr($sj,0,190),$ps]); $contactOk='Pesan terkirim. Terima kasih!'; $_POST=[]; }
      catch(Throwable $e){ error_log('inbox-insert: '.$e->getMessage()); $contactErr='Gagal menyimpan pesan. '.$e->getMessage(); }
    }
  }
}
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
<?php $hal=Database::setting('hero_align','center'); $haC=$hal==='left'?'text-left':($hal==='right'?'text-right':'text-center'); $hjC=$hal==='left'?'justify-start':($hal==='right'?'justify-end':'justify-center'); $hmC=$hal==='left'?'mr-auto':($hal==='right'?'ml-auto':'mx-auto'); ?>
<div class="relative max-w-3xl <?= $hmC ?> <?= $haC ?>">
<span class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-3 py-1 text-[11px] font-bold uppercase tracking-[.16em]"><i class="fa fa-headset text-white"></i>Hubungi Kami</span>
<h1 class="text-3xl md:text-5xl font-extrabold leading-tight mt-4 <?= $haC ?>">Kontak <?= Helper::e(Database::setting('school_name','Sekolah')) ?></h1>
<?php $contactDesc=Database::setting('contact_desc',''); if($contactDesc!==''): ?><p class="text-white/80 text-sm md:text-base max-w-2xl mt-4 <?= $hmC ?> <?= $haC ?>"><?= nl2br(Helper::e($contactDesc)) ?></p><?php endif; ?>
<div class="mt-5 flex flex-wrap gap-2 <?= $hjC ?> <?= $haC ?>">
<span class="inline-flex items-center gap-2 bg-white text-slate-900 px-4 py-2.5 rounded-xl font-bold text-sm"><i class="fa fa-calendar-day text-emerald-600"></i><?= Helper::pageDate('settings') ?></span>
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
<form method="post" class="lg:col-span-3 rounded-2xl border bg-white dark:bg-slate-800 p-5 md:p-7 grid gap-3 content-start"><?= Security::csrfField() ?><input type="hidden" name="contact_send" value="1">
<div><h2 class="font-extrabold text-lg">Kirim Pesan</h2><p class="text-sm text-slate-500">Isi formulir, pesan diteruskan ke email sekolah.</p></div>
<?php if($contactOk!==''): ?><script>document.addEventListener('DOMContentLoaded',()=>{ if(window.Swal)Swal.fire({title:'Berhasil!',text:<?= json_encode($contactOk,JSON_UNESCAPED_UNICODE) ?>,icon:'success',confirmButtonColor:'#059669',timer:2600,timerProgressBar:true,showConfirmButton:false}); });</script><?php endif; ?>
<?php if($contactErr!==''): ?><script>document.addEventListener('DOMContentLoaded',()=>{ if(window.Swal)Swal.fire({title:'Gagal',text:<?= json_encode($contactErr,JSON_UNESCAPED_UNICODE) ?>,icon:'error',confirmButtonColor:'#dc2626',timer:3500,timerProgressBar:true,showConfirmButton:false}); });</script><?php endif; ?>
<div class="grid sm:grid-cols-2 gap-3"><label class="grid gap-1 text-sm font-semibold">Nama<input name="name" required maxlength="150" value="<?= Helper::e($_POST['name']??'') ?>" placeholder="Nama lengkap" class="border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 font-normal bg-white dark:bg-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition placeholder:text-slate-400"></label><label class="grid gap-1 text-sm font-semibold">Email<input name="email" required type="email" maxlength="190" value="<?= Helper::e($_POST['email']??'') ?>" placeholder="nama@email.com" class="border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 font-normal bg-white dark:bg-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition placeholder:text-slate-400"></label></div>
<label class="grid gap-1 text-sm font-semibold">Subjek<input name="subject" maxlength="190" value="<?= Helper::e($_POST['subject']??'') ?>" placeholder="Contoh: Info PPDB" class="border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 font-normal bg-white dark:bg-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition placeholder:text-slate-400"></label>
<label class="grid gap-1 text-sm font-semibold">Pesan<textarea name="message" required rows="5" placeholder="Tulis pesan Anda..." class="border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 font-normal bg-white dark:bg-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition placeholder:text-slate-400 resize-y"><?= Helper::e($_POST['message']??'') ?></textarea></label>
<button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl py-3 font-bold transition hover:-translate-y-0.5 hover:shadow-xl hover:brightness-105 active:translate-y-0 active:scale-[.99]"><i class="fa fa-paper-plane mr-1"></i>Kirim Pesan</button>
</form>
</div>
</div>
<?php require ROOT."/templates/frontend/footer.php"; ?>

