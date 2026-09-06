<?php declare(strict_types=1); Auth::requireRole(['administrator','editor']); $title='Kurikulum';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/curriculum')); exit; }
  foreach(['kurikulum_title','kurikulum_content','kurikulum_show'] as $k){
    if(!array_key_exists($k,$_POST['s']??[])) continue;
    $v=trim((string)$_POST['s'][$k]);
    if($k==='kurikulum_show') $v=$v==='1'?'1':'0';
    $db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)")->execute([$k,$v]);
  }
  Auth::log($db,'update','curriculum','Ubah kurikulum'); Session::flash('ok','Kurikulum disimpan.');
  header('Location: '.Helper::url('admin/curriculum')); exit;
}
$sets=[]; foreach($db->query("SELECT `key`,`value` FROM settings") as $r) $sets[$r['key']]=$r['value'];
require ROOT.'/templates/admin/header.php'; ?>
<div class="flex flex-wrap items-center gap-2 mb-4">
<h1 class="text-xl font-extrabold"><i class="fa fa-book-open text-emerald-600 mr-1"></i>Kurikulum</h1>
<span class="text-[11px] bg-slate-800 text-white px-2.5 py-0.5 rounded-full font-bold">public: /kurikulum</span>
<a href="<?= Helper::url('kurikulum') ?>" target="_blank" rel="noopener noreferrer" class="ml-auto text-sm px-3 py-1.5 border rounded-lg bg-white"><i class="fa fa-eye mr-1"></i>Lihat Public</a>
</div>
<form method="post" data-loading id="currForm" class="bg-white rounded-2xl border p-4 grid gap-2 text-sm max-w-4xl"><?= Security::csrfField() ?>
<label class="grid gap-1">Judul public<input name="s[kurikulum_title]" value="<?= Helper::e($sets['kurikulum_title']??'Kurikulum') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Isi<textarea name="s[kurikulum_content]" id="kurikulumContent" rows="10"><?= Helper::e($sets['kurikulum_content']??'<p>Kurikulum Merdeka dengan penguatan karakter, literasi, numerasi, dan keterampilan vokasi.</p><ul><li>Intrakurikuler</li><li>Projek Penguatan Profil Pelajar Pancasila</li><li>Ekstrakurikuler</li></ul>') ?></textarea><span id="editorWarn" class="hidden text-xs font-normal text-red-600">Editor gagal dimuat (CDN diblokir). Textarea biasa tetap bisa disimpan.</span></label>
<label class="grid gap-1 max-w-xs">Tampil di public<select name="s[kurikulum_show]" class="border rounded-lg p-2"><option value="1" <?= ($sets['kurikulum_show']??'1')==='1'?'selected':'' ?>>Tampilkan</option><option value="0" <?= ($sets['kurikulum_show']??'1')==='0'?'selected':'' ?>>Sembunyikan (404)</option></select></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button></div>
</form>
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mt-3 text-sm max-w-4xl"><b><i class="fa fa-list-ul text-amber-600 mr-1"></i>Menampilkan di menu public:</b> buka <a href="<?= Helper::url('admin/menus') ?>" class="text-emerald-700 font-bold">Menu Manager → Tautan Khusus</a> tambah URL <code class="font-mono bg-white px-1 rounded">/kurikulum</code>.</div>
<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@41.4.2/build/ckeditor.js"></script>
<script>
let currEditor=null;
(function(){
  const el=document.querySelector('#kurikulumContent');
  const warn=document.getElementById('editorWarn');
  if(!el)return;
  if(!window.ClassicEditor){ warn&&warn.classList.remove('hidden'); return; }
  ClassicEditor.create(el).then(e=>{currEditor=e}).catch(()=>warn&&warn.classList.remove('hidden'));
})();
document.getElementById('currForm').addEventListener('submit',()=>{ if(currEditor){ try{document.getElementById('kurikulumContent').value=currEditor.getData()}catch(_){} } });
</script>
<style>.ck-editor__editable{min-height:320px}.ck-content h1{font-size:1.6rem;font-weight:800}.ck-content h2{font-size:1.35rem;font-weight:800}.ck-content h3{font-size:1.15rem;font-weight:700}.ck-content table{width:100%}</style>
<?php require ROOT.'/templates/admin/footer.php'; ?>
