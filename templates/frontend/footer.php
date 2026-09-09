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
    if(Database::setting('logo','')){echo '<img src="'.$schoolLogo.'" alt="" class="h-14 w-auto object-contain mb-3" style="filter:drop-shadow(0 0 1px #fff) drop-shadow(0 0 6px rgba(255,255,255,.9)) drop-shadow(0 2px 8px rgba(255,255,255,.45))">';}
    echo '<h4 class="font-bold text-white mb-2">'.Helper::e($school).'</h4><p>'.Helper::e($tagline).'</p><p class="mt-2 text-slate-400">'.Helper::e($addr).'</p>';
    return;
  }
  if($t==='image'&&$fwc!==''){ $fwi=(preg_match('~^(?:https?:)?//~i',$fwc)||str_starts_with($fwc,'/'))?$fwc:Helper::upload($fwc); echo '<img src="'.Helper::e($fwi).'" alt="'.Helper::e($fw['title']??'').'" class="max-w-full max-h-36 rounded-xl object-contain">'; return; }
  if($t==='menu'){ $menuId=(int)$fwc; $menuItems=[]; try{ if($menuId>0){$mi=$db->prepare("SELECT * FROM menu_items WHERE menu_id=? AND is_active=1 ORDER BY sort_order");$mi->execute([$menuId]);$menuItems=$mi->fetchAll();} }catch(Throwable){} if(!$menuItems){try{$fm=$db->query("SELECT id FROM menus WHERE location='footer' LIMIT 1")->fetch();if($fm){$mi2=$db->prepare("SELECT * FROM menu_items WHERE menu_id=? AND is_active=1 ORDER BY sort_order");$mi2->execute([(int)$fm['id']]);$menuItems=$mi2->fetchAll();}}catch(Throwable){}} echo '<div class="grid gap-1.5">'; foreach($menuItems as $fi){[$fhref,$ftgt]=Helper::menuUrl($fi['url']); echo '<a href="'.Helper::e($fhref).'"'.($ftgt==='_blank'?' target="_blank" rel="noopener noreferrer"':'').' class="text-sm hover:text-white hover:translate-x-1 transition"><i class="fa fa-chevron-right text-emerald-500 mr-2"></i>'.Helper::e($fi['label']).'</a>'; } echo '</div>'; return; }
  if($t==='categories'){ try{$catsF=$db->query("SELECT name,slug FROM categories ORDER BY name")->fetchAll();}catch(Throwable){$catsF=[];} echo '<div class="grid gap-1.5">'; foreach($catsF as $cf) echo '<a href="'.Helper::url('berita?kategori='.Helper::e($cf['slug'])).'" class="text-sm hover:text-white hover:translate-x-1 transition"><i class="fa fa-tag text-emerald-500 mr-2"></i>'.Helper::e($cf['name']).'</a>'; echo '</div>'; return; }
  if($t==='links'){ $lnks=json_decode((string)$fwc,true)?:[]; if(!$lnks&&$fwc!==''){ $lnks=array_map(fn($l)=>['label'=>trim(explode('|',$l)[0]??''),'url'=>trim(explode('|',$l)[1]??''),'target'=>trim(explode('|',$l)[2]??'')],array_filter(array_map('trim',explode("\n",$fwc)))); } echo '<div class="grid gap-1.5">'; foreach($lnks as $lk){ $lbl=trim($lk['label']??''); $href=trim($lk['url']??''); if(!$lbl||!$href) continue; [$fhref,$auto]=Helper::menuUrl($href); $tgt=(($lk['target']??'')==='_blank'||$auto==='_blank')?' target="_blank" rel="noopener noreferrer"':''; echo '<a href="'.Helper::e($fhref).'"'.$tgt.' class="text-sm hover:text-white hover:translate-x-1 transition"><i class="fa fa-chevron-right text-emerald-500 mr-2"></i>'.Helper::e($lbl).'</a>'; } echo '</div>'; return; }
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
<div id="annModal" class="hidden fixed inset-0 z-[70] overflow-y-auto"><div class="fixed inset-0 bg-slate-950/60" data-ann-close></div><div class="relative min-h-full flex items-start justify-center p-4"><div class="relative w-full bg-white dark:bg-slate-800 rounded-3xl shadow-2xl my-6 overflow-hidden" style="max-width:380px!important"><div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-4 flex items-center gap-3"><span class="w-9 h-9 rounded-xl bg-white/20 grid place-items-center text-white shrink-0"><i class="fa fa-bullhorn text-sm"></i></span><div class="min-w-0 flex-1"><h3 id="annMTitle" class="font-extrabold text-white text-sm leading-snug break-words"></h3><p id="annMDate" class="text-[11px] text-white/80 mt-0.5"></p></div><button data-ann-close class="w-8 h-8 rounded-lg bg-white/20 grid place-items-center text-white hover:bg-white/30 shrink-0"><i class="fa fa-xmark text-sm"></i></button></div><div class="p-5"><div id="annMBody" class="ann-content text-[15px] leading-[1.8] font-medium text-slate-700 dark:text-slate-200"></div><div id="annMFile" class="mt-4 hidden"><a id="annMLink" href="#" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded-xl shadow"><i class="fa fa-download"></i>Unduh Lampiran</a></div></div></div></div></div>
<script>
document.querySelectorAll('[data-ann]').forEach(b=>b.addEventListener('click',()=>{
  let d={}; try{d=JSON.parse(atob(b.dataset.ann))}catch(e){try{d=JSON.parse(b.dataset.ann)}catch(e2){}}
  document.getElementById('annMTitle').textContent=d.title||'Pengumuman';
  document.getElementById('annMDate').textContent=d.date||'';
  const body=document.getElementById('annMBody');
  const c=(d.content||'').trim();
  body.innerHTML=(c.startsWith('<')||c.includes('</'))?c:c.replace(/\n/g,'<br>');
  const fw=document.getElementById('annMFile');
  if(d.file){fw.classList.remove('hidden');document.getElementById('annMLink').href=d.file}else{fw.classList.add('hidden')}
  const m=document.getElementById('annModal');m.classList.remove('hidden');document.body.style.overflow='hidden';
}));
const annClose=()=>{document.getElementById('annModal').classList.add('hidden');document.body.style.overflow=''};
document.querySelectorAll('[data-ann-close]').forEach(b=>b.addEventListener('click',annClose));
document.getElementById('annModal').addEventListener('mousedown',e=>{if(!e.target.closest('.relative.w-full'))annClose()});
document.addEventListener('keydown',e=>{if(e.key==='Escape'){document.getElementById('annModal')?.classList.add('hidden');document.body.style.overflow=''}});
</script>
<script src="<?= Helper::asset('js/app.js') ?>"></script>
</body></html>
