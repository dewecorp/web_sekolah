<?php declare(strict_types=1); Auth::requireRole(['administrator','editor']); $title='Kurikulum';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!Security::verifyCsrf($_POST['csrf']??null)){ Session::flash('err','CSRF tidak valid.'); header('Location: '.Helper::url('admin/curriculum')); exit; }
  foreach(['kurikulum_title','kurikulum_content','kurikulum_comps','kurikulum_show'] as $k){
    if(!array_key_exists($k,$_POST['s']??[])) continue;
    $v=trim((string)$_POST['s'][$k]);
    if($k==='kurikulum_show') $v=$v==='1'?'1':'0';
    if($k==='kurikulum_comps'&&strip_tags($v)===$v&&$v!==''){
      $lis=''; foreach(array_filter(array_map('trim',explode("\n",$v))) as $line){ $lis.='<li>'.htmlspecialchars($line,ENT_QUOTES,'UTF-8').'</li>'; }
      if($lis!=='')$v='<ul>'.$lis.'</ul>';
    }
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
<form method="post" data-loading id="currForm" class="bg-white rounded-2xl border p-4 grid gap-2 text-sm w-full max-w-none"><?= Security::csrfField() ?>
<label class="grid gap-1">Judul public<input name="s[kurikulum_title]" value="<?= Helper::e($sets['kurikulum_title']??'Kurikulum') ?>" class="border rounded-lg p-2"></label>
<label class="grid gap-1">Isi<textarea name="s[kurikulum_content]" id="kurikulumContent" rows="10"><?= Helper::e(trim((string)($sets['kurikulum_content']??''))!==''?$sets['kurikulum_content']:'<p>Kurikulum Merdeka dengan penguatan karakter, literasi, numerasi, dan keterampilan vokasi.</p><ul><li>Intrakurikuler</li><li>Projek Penguatan Profil Pelajar Pancasila</li><li>Ekstrakurikuler</li></ul>') ?></textarea><span id="editorWarn" class="hidden text-xs font-normal text-red-600">Editor gagal dimuat (CDN diblokir). Textarea biasa tetap bisa disimpan.</span></label>
<label class="grid gap-1">Komponen utama<textarea name="s[kurikulum_comps]" id="kurikulumComps" rows="6"><?= Helper::e(trim((string)($sets['kurikulum_comps']??''))!==''?$sets['kurikulum_comps']:'<ul><li><strong>Intrakurikuler</strong> — pembelajaran tatap muka sesuai CP & TP.</li><li><strong>Projek P5</strong> — penguatan profil pelajar Pancasila lintas mapel.</li><li><strong>Ekstrakurikuler</strong> — minat, bakat, dan karakter di luar jam wajib.</li></ul>') ?></textarea></label>

<label class="grid gap-1 max-w-xs">Tampil di public<select name="s[kurikulum_show]" class="border rounded-lg p-2"><option value="1" <?= ($sets['kurikulum_show']??'1')==='1'?'selected':'' ?>>Tampilkan</option><option value="0" <?= ($sets['kurikulum_show']??'1')==='0'?'selected':'' ?>>Sembunyikan (404)</option></select></label>
<div class="flex justify-center"><button class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-8 py-2 font-bold w-full sm:w-auto sm:min-w-[200px]"><i class="fa fa-floppy-disk mr-1"></i>Simpan</button></div>
</form>
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mt-3 text-sm w-full"><b><i class="fa fa-list-ul text-amber-600 mr-1"></i>Menampilkan di menu public:</b> buka <a href="<?= Helper::url('admin/menus') ?>" class="text-emerald-700 font-bold">Menu Manager → Tautan Khusus</a> tambah URL <code class="font-mono bg-white px-1 rounded">/kurikulum</code>.</div>
<script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js"></script>
<script>
let currEditor=null,compsEditor=null;
(function(){
  const el=document.querySelector('#kurikulumContent'),el2=document.querySelector('#kurikulumComps'),warn=document.getElementById('editorWarn');
  if(!el&&!el2)return;
  const boot=()=>{
    if(!window.tinymce||!window.RichEditorCreate){ warn&&warn.classList.remove('hidden'); return; }
    const ps=[];
    if(el)ps.push(window.RichEditorCreate(el,{height:420}).then(e=>{currEditor=e}).catch(()=>warn&&warn.classList.remove('hidden')));
    if(el2)ps.push(window.RichEditorCreate(el2,{height:260}).then(e=>{compsEditor=e}).catch(()=>{}));
    Promise.all(ps).then(()=>warn&&warn.classList.add('hidden'));
  };
  boot();
})();
document.getElementById('currForm').addEventListener('submit',()=>{ if(currEditor){ try{document.getElementById('kurikulumContent').value=currEditor.getData()}catch(_){} } if(compsEditor){ try{document.getElementById('kurikulumComps').value=compsEditor.getData()}catch(_){} } });
</script>
<style>#currForm .tox-tinymce{min-height:520px}</style>
<?php require ROOT.'/templates/admin/footer.php'; ?>




