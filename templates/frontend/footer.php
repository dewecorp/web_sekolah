</main>
<?php
$ft = Database::setting('footer_text',Database::setting('school_name',''));
$footerWidgets=[];
try{$footerWidgets=Database::conn()->query("SELECT * FROM widgets WHERE area='footer' AND is_active=1 ORDER BY sort_order,id")->fetchAll();}catch(Throwable){$footerWidgets=[];}
if(!$footerWidgets){
  try{$conn=Database::conn(); $ins=$conn->prepare("INSERT INTO widgets(area,type,title,content,sort_order,is_active) VALUES(?,?,?,?,?,?)");
  $ins->execute(['footer','about','Tentang Sekolah','',1,1]);
  $ins->execute(['footer','menu','Navigasi','',2,1]);
  $ins->execute(['footer','contact','Kontak','',3,1]);
  $ins->execute(['footer','social','Ikuti Kami','',4,1]);
  $footerWidgets=$conn->query("SELECT * FROM widgets WHERE area='footer' AND is_active=1 ORDER BY sort_order,id")->fetchAll();
  }catch(Throwable){} }
function renderFw($fw,$db){
  $fwc=trim((string)($fw['content']??'')); $t=$fw['type'];
  if($t==='about'){
    $logo=Database::setting('logo','');
    $logoUrl=$logo?(str_starts_with($logo,'http')?$logo:Helper::upload($logo)):Helper::dummy('school-logo',80,80);
    $school=Database::setting('school_name',''); $tagline=Database::setting('tagline',''); $addr=Database::setting('address','');
    $schoolLogo=Helper::upload(Database::setting('logo',''));
    echo '<span class="inline-flex w-11 h-11 rounded-xl bg-emerald-600 text-white items-center justify-center font-extrabold mb-3">';
    if(Database::setting('logo','')){echo '<img src="'.$schoolLogo.'" alt="" class="w-full h-full object-contain p-1">';}else{echo Helper::e(mb_substr($school,0,1));}
    echo '</span><h4 class="font-bold text-white mb-2">'.Helper::e($school).'</h4><p>'.Helper::e($school).' - '.Helper::e($tagline).'</p><p class="mt-2 text-slate-400">'.Helper::e($addr).'</p>';
    return;
  }
  if($t==='image'&&$fwc!==''){ $fwi=(preg_match('~^(?:https?:)?//~i',$fwc)||str_starts_with($fwc,'/'))?$fwc:Helper::upload($fwc); echo '<img src="'.Helper::e($fwi).'" alt="'.Helper::e($fw['title']??'').'" class="max-w-full max-h-36 rounded-xl object-contain">'; return; }
  if($t==='menu'){ $menuId=(int)$fwc; $menuItems=[]; try{ if($menuId>0){$mi=$db->prepare("SELECT * FROM menu_items WHERE menu_id=? AND is_active=1 ORDER BY sort_order");$mi->execute([$menuId]);$menuItems=$mi->fetchAll();} }catch(Throwable){} if(!$menuItems){try{$fm=$db->query("SELECT id FROM menus WHERE location='footer' LIMIT 1")->fetch();if($fm){$mi2=$db->prepare("SELECT * FROM menu_items WHERE menu_id=? AND is_active=1 ORDER BY sort_order");$mi2->execute([(int)$fm['id']]);$menuItems=$mi2->fetchAll();}}catch(Throwable){}} echo '<div class="grid gap-1.5">'; foreach($menuItems as $fi){[$fhref,$ftgt]=Helper::menuUrl($fi['url']); echo '<a href="'.Helper::e($fhref).'"'.($ftgt==='_blank'?' target="_blank" rel="noopener noreferrer"':'').' class="text-sm hover:text-white hover:translate-x-1 transition"><i class="fa fa-chevron-right text-emerald-500 mr-2"></i>'.Helper::e($fi['label']).'</a>'; } echo '</div>'; return; }
  if($t==='categories'){ try{$catsF=$db->query("SELECT name,slug FROM categories ORDER BY name")->fetchAll();}catch(Throwable){$catsF=[];} echo '<div class="grid gap-1.5">'; foreach($catsF as $cf) echo '<a href="'.Helper::url('berita?kategori='.Helper::e($cf['slug'])).'" class="text-sm hover:text-white hover:translate-x-1 transition"><i class="fa fa-tag text-emerald-500 mr-2"></i>'.Helper::e($cf['name']).'</a>'; echo '</div>'; return; }
  if($t==='links'){ $lnks=json_decode((string)$fwc,true)?:[]; echo '<div class="grid gap-1.5">'; foreach($lnks as $lk){ $lbl=trim($lk['label']??''); $href=trim($lk['url']??''); if($lbl&&$href) echo '<a href="'.Helper::url(ltrim($href,'/')).'" class="text-sm hover:text-white hover:translate-x-1 transition"><i class="fa fa-chevron-right text-emerald-500 mr-2"></i>'.Helper::e($lbl).'</a>'; } echo '</div>'; return; }
  if($t==='contact'){ echo '<div class="grid gap-1.5 text-sm"><p><i class="fa fa-location-dot text-emerald-500 w-5"></i>'.Helper::e(Database::setting('address','-')).'</p><p><i class="fa fa-phone text-emerald-500 w-5"></i>'.Helper::e(Database::setting('phone','-')).'</p><p><i class="fa fa-envelope text-emerald-500 w-5"></i>'.Helper::e(Database::setting('email','-')).'</p></div>'; return; }
  if($t==='social'){ echo '<div class="flex flex-wrap gap-2">'; foreach(['facebook'=>'fa-facebook-f','instagram'=>'fa-instagram','youtube'=>'fa-youtube','tiktok'=>'fa-tiktok'] as $fsn=>$fsi){$fsu=Database::setting($fsn,'');if(!$fsu)continue;echo '<a href="'.Helper::e($fsu).'" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-xl bg-white/10 grid place-items-center hover:bg-emerald-600" aria-label="'.ucfirst($fsn).'"><i class="fab '.$fsi.'"></i></a>';} echo '</div>'; return; }
  if($t==='html'){ echo '<div class="text-sm text-slate-300 leading-relaxed">'.$fwc.'</div>'; return; }
  if($fwc!=='') echo '<div class="text-sm text-slate-300 leading-relaxed">'.nl2br(Helper::e($fwc)).'</div>';
}
?>
<footer class="relative overflow-hidden bg-slate-950 text-slate-300 mt-auto w-full">
<div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-600 via-amber-500 to-emerald-600"></div>
<div class="absolute -top-32 -right-24 w-80 h-80 rounded-full bg-emerald-600/10 blur-3xl pointer-events-none"></div>
<div class="max-w-7xl mx-auto px-4 py-12 grid md:grid-cols-4 gap-8 text-sm">
<?php foreach($footerWidgets as $fw): ?>
<div><h4 class="font-bold text-white mb-3"><?= Helper::e($fw['title']?:'Informasi') ?></h4>
<?php renderFw($fw,$db); ?></div>
<?php endforeach; ?>
</div>
<div class="border-t border-white/10"><div class="max-w-7xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between text-xs gap-2">
<span>&copy; <?= date('Y') ?> <?= Helper::e($ft) ?>.</span>
<a href="<?= Helper::url('admin/login') ?>" target="_blank" rel="noopener noreferrer" class="hover:text-white">Login Admin</a>
</div></div>
</footer>
<button id="toTop" aria-label="Kembali ke atas" class="fixed bottom-5 right-5 z-50 w-11 h-11 rounded-full bg-emerald-600 text-white shadow-lg grid place-items-center opacity-0 invisible translate-y-3 transition-all duration-300 hover:bg-emerald-500"><i class="fa fa-arrow-up"></i></button>
<script src="<?= Helper::asset('js/app.js') ?>"></script>
</body></html>
