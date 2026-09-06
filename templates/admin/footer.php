</main>
</div>
<script>
document.querySelectorAll('[data-confirm]')?.forEach(f=>{f.addEventListener('submit',e=>{e.preventDefault();Swal.fire({title:'Apakah Anda yakin?',text:'Data yang dihapus tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)f.submit()})})});
document.querySelectorAll('[data-confirm-logout]')?.forEach(f=>{f.addEventListener('submit',e=>{e.preventDefault();Swal.fire({title:'Logout?',text:'Keluar dari dashboard?',icon:'question',showCancelButton:true,confirmButtonText:'Ya, Logout',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)f.submit()})})});
document.querySelectorAll('form[data-loading]')?.forEach(f=>{f.addEventListener('submit',()=>{const b=f.querySelector('[type=submit]');if(b){b.disabled=true;b.dataset.t=b.innerHTML;b.innerHTML='Menyimpan...'}})});
const _ok="<?= addslashes((string)(Session::flash('ok') ?? '')) ?>",_er="<?= addslashes((string)(Session::flash('err') ?? '')) ?>";
if(_ok){Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:2600,timerProgressBar:true,didOpen:t=>{t.addEventListener('mouseenter',Swal.stopTimer);t.addEventListener('mouseleave',Swal.resumeTimer)}}).fire({icon:'success',title:_ok});}if(_er){Swal.fire('Terjadi Kesalahan',_er,'error');}
</script>
</body></html>
