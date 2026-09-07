<?php $ftName = $brandName ?? Database::setting('school_name',''); $ftText = Database::setting('footer_text',$ftName); ?>
<div aria-hidden="true" class="h-6 shrink-0"></div>
<footer class="max-w-full w-full border-t border-slate-200 bg-white/80 backdrop-blur px-4 md:px-6 py-3.5 mt-auto flex flex-col md:flex-row items-center gap-1.5 text-xs text-slate-500">
<span>&copy; <?= date('Y') ?> <?= Helper::e($ftText) ?>.</span>
<span class="md:ml-auto flex items-center gap-2"><a href="<?= Helper::url() ?>" target="_blank" rel="noopener noreferrer" class="text-emerald-700 font-bold hover:underline"><i class="fa fa-globe mr-1"></i>Lihat Situs</a><span class="text-slate-300">|</span><a href="<?= Helper::url('admin') ?>" class="hover:text-slate-700">Dashboard Admin</a></span>
</footer>
</main>
</div>
<script>
(function(){
  function closeAll(except){document.querySelectorAll('.cselect.open').forEach(x=>{if(x!==except)x.classList.remove('open')})}
  function build(sel){
    if(sel.dataset.cselect)return; sel.dataset.cselect='1';
    const wrap=document.createElement('div'); wrap.className='cselect';
    sel.insertAdjacentElement('afterend',wrap); wrap.appendChild(sel);
    sel.style.display='none';
    const btn=document.createElement('button'); btn.type='button'; btn.className='cselect-btn';
    const list=document.createElement('div'); list.className='cselect-list'; list.hidden=true;
    const paint=()=>{const o=sel.selectedOptions[0]; btn.innerHTML='<span>'+(o?o.textContent:'—')+'</span><i class="fa fa-chevron-down text-xs text-slate-400"></i>'; [...list.children].forEach(b=>b.setAttribute('aria-selected',b.dataset.v===sel.value?'true':'false'))};
    [...sel.options].forEach(o=>{
      const b=document.createElement('button'); b.type='button'; b.className='cselect-opt'; b.dataset.v=o.value; b.textContent=o.textContent;
      b.addEventListener('click',()=>{sel.value=o.value; sel.dispatchEvent(new Event('change',{bubbles:true})); paint(); wrap.classList.remove('open'); list.hidden=true});
      list.appendChild(b);
    });
    btn.addEventListener('click',e=>{e.stopPropagation(); const was=wrap.classList.contains('open'); closeAll();
      if(!was){
        wrap.classList.add('open'); list.hidden=false;
        const r=btn.getBoundingClientRect(),h=list.offsetHeight||200;
        const openUp=r.bottom+h>window.innerHeight-16&&r.top-h>16;
        list.style.top=openUp?'auto':''; list.style.bottom=openUp?'calc(100% + 6px)':'';
      }else{wrap.classList.remove('open'); list.hidden=true}});
    document.addEventListener('click',e=>{if(!wrap.contains(e.target)){wrap.classList.remove('open'); list.hidden=true}});
    document.addEventListener('keydown',e=>{if(e.key==='Escape'){wrap.classList.remove('open'); list.hidden=true}});
    wrap.appendChild(btn); wrap.appendChild(list); paint();
    sel._cpaint=paint;
    sel._csync=paint;
    window.__refreshSelects=window.__refreshSelects||{};
    if(sel.id)window.__refreshSelects[sel.id]=paint;
    sel.addEventListener('change',paint);
    new MutationObserver(paint).observe(sel,{attributes:true,childList:true,subtree:true});
  }
  function init(){document.querySelectorAll('select:not(.swal2-select)').forEach(sel=>{ if(sel.closest('.swal2-container'))return; build(sel) })}
  init(); new MutationObserver(init).observe(document.body,{childList:true,subtree:true});
})();
document.querySelectorAll('[data-confirm]')?.forEach(f=>{f.addEventListener('submit',e=>{e.preventDefault();Swal.fire({title:'Apakah Anda yakin?',text:'Data yang dihapus tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)f.submit()})})});
document.querySelectorAll('[data-confirm-logout]')?.forEach(f=>{f.addEventListener('submit',e=>{e.preventDefault();Swal.fire({title:'Logout?',text:'Keluar dari dashboard?',icon:'question',showCancelButton:true,confirmButtonText:'Ya, Logout',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)f.submit()})})});
document.querySelectorAll('form[data-loading]')?.forEach(f=>{f.addEventListener('submit',()=>{const b=f.querySelector('[type=submit]');if(b){b.disabled=true;b.dataset.t=b.innerHTML;b.innerHTML='Menyimpan...'}})});
(function(){const el=document.querySelector('[data-clock-admin]');if(!el)return;const pad=n=>String(n).padStart(2,'0');const tick=()=>{const n=new Date();el.textContent=n.toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'short',year:'numeric'})+' • '+pad(n.getHours())+':'+pad(n.getMinutes())+':'+pad(n.getSeconds())};tick();setInterval(tick,1000)})();
window.CKUploadAdapter=(function(){
  function UploadAdapter(loader,url,csrf){this.loader=loader;this.url=url;this.csrf=csrf}
  UploadAdapter.prototype.upload=function(){
    return this.loader.file.then(file=>new Promise((resolve,reject)=>{
      const fd=new FormData();fd.append('csrf',this.csrf);fd.append('act','ckeditor');fd.append('upload',file,file.name);
      fetch(this.url,{method:'POST',body:fd}).then(r=>r.json()).then(j=>{
        if(j&&j.uploaded&&j.url)resolve({default:j.url});
        else reject(j&&(j.msg||j.message||j.error&&j.error.message)||'Upload gagal');
      }).catch(reject);
    }));
  };
  UploadAdapter.prototype.abort=function(){};
  return function(editor){
    const url=document.body.dataset.ckUpload||'',csrf=document.body.dataset.csrf||'';
    editor.plugins.get('FileRepository').createUploadAdapter=loader=>new UploadAdapter(loader,url,csrf);
  };
})();
window.CKEditorConfig=(function(){
  const up=window.CKUploadAdapter;
  return {
    toolbar:{items:['heading','|','bold','italic','underline','strikethrough','subscript','superscript','removeFormat','|','fontSize','fontFamily','fontColor','fontBackgroundColor','highlight','|','alignment','bulletedList','numberedList','todoList','outdent','indent','|','link','blockQuote','insertTable','imageUpload','mediaEmbed','code','codeBlock','htmlEmbed','horizontalLine','|','undo','redo']},
    heading:{options:[{model:'paragraph',title:'Paragraph',class:'ck-heading_paragraph'},{model:'heading1',view:'h1',title:'Heading 1',class:'ck-heading_heading1'},{model:'heading2',view:'h2',title:'Heading 2',class:'ck-heading_heading2'},{model:'heading3',view:'h3',title:'Heading 3',class:'ck-heading_heading3'}]},
    fontSize:{options:[9,11,12,14,16,18,20,22,24,28,32,36]},
    image:{toolbar:['imageTextAlternative','toggleImageCaption','imageStyle:inline','imageStyle:block','imageStyle:side','linkImage']},
    table:{contentToolbar:['tableColumn','tableRow','mergeTableCells','tableCellProperties','tableProperties','toggleTableCaption']},
    extraPlugins:up?[up]:[]
  };
})();
window.CKCreate=function(el){
  if(!window.ClassicEditor)return Promise.reject(new Error('no editor'));
  return window.ClassicEditor.create(el,window.CKEditorConfig);
};
document.body.dataset.ckUpload=<?= json_encode(Helper::url('admin/media'),JSON_UNESCAPED_SLASHES) ?>;
document.body.dataset.csrf=<?= json_encode(Security::csrfToken()) ?>;
const _ok=<?= json_encode((string)(Session::flash('ok') ?? ''), JSON_UNESCAPED_UNICODE) ?>,_warn=<?= json_encode((string)(Session::flash('warn') ?? ''), JSON_UNESCAPED_UNICODE) ?>,_er=<?= json_encode((string)(Session::flash('err') ?? ''), JSON_UNESCAPED_UNICODE) ?>;
const _toast=(icon,title,color)=>Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:2600,timerProgressBar:true,didOpen:t=>{t.style.borderLeft='5px solid '+color;t.addEventListener('mouseenter',Swal.stopTimer);t.addEventListener('mouseleave',Swal.resumeTimer)}}).fire({icon,title});
if(_ok)_toast('success',_ok,'#10b981');
if(_warn)_toast('warning',_warn,'#f59e0b');
if(_er)Swal.fire({title:'Gagal',text:_er,icon:'error',confirmButtonColor:'#dc2626'});
</script>
</body></html>
