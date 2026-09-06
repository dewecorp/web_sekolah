document.documentElement.classList.add('js');
document.getElementById('loader')?.remove();
/* FX scroll: sembunyikan via INLINE (bukan CSS) setelah paint, lalu reveal
   transisi inline. Tanpa JS = tampil. Gagal JS = tampil. Tak bisa hilang. */
(function(){
  var map={'fade-up':'translateY(56px)','fade-down':'translateY(-56px)','fade-left':'translateX(-64px)','fade-right':'translateX(64px)','zoom-in':'scale(.88)','zoom-out':'scale(1.14)','slide-left':'translateX(-110px)','slide-right':'translateX(110px)','flip':'perspective(900px) rotateY(65deg)','bounce-in':'scale(.75)','rotate-in':'rotate(-9deg) scale(.88)'};
  var els=[...document.querySelectorAll('.fx')];
  function play(el){
    if(el.dataset.fxDone||!el.isConnected)return;el.dataset.fxDone='1';
    el.style.opacity='1';el.style.transform='none';
  }
  function prep(el){
    if(el.classList.contains('fx-none')){play(el);return}
    var t=(el.className.match(/fx-([a-z-]+)/)||[])[1]||'fade-up';
    el.style.transition='opacity 1s ease-out,transform 1.1s cubic-bezier(.16,1,.3,1)';
    el.style.opacity='0';el.style.transform=map[t]||map['fade-up'];
    void el.offsetWidth;
  }
  requestAnimationFrame(()=>requestAnimationFrame(()=>{
    els.forEach(prep);
    var io=('IntersectionObserver' in window)?new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){play(e.target);io.unobserve(e.target)}}),{threshold:.08,rootMargin:'0px 0px -8% 0px'}):null;
    if(matchMedia('(prefers-reduced-motion: reduce)').matches){els.forEach(play);return}
    els.forEach((el,i)=>{
      var r=el.getBoundingClientRect();
      if(r.top<innerHeight*.95&&r.bottom>0)setTimeout(()=>play(el),150+i*130);
      else if(io)io.observe(el);else play(el);
    });
  }));
})();
const mb=document.getElementById('mobBtn'),mm=document.getElementById('mobMenu');
mb?.addEventListener('click',()=>mm.classList.toggle('hidden'));
const dbtn=document.getElementById('darkBtn');
function paint(){if(dbtn)dbtn.textContent=document.documentElement.classList.contains('dark')?'☀':'🌙'}
dbtn?.addEventListener('click',()=>{document.documentElement.classList.toggle('dark');localStorage.theme=document.documentElement.classList.contains('dark')?'dark':'light';paint()});paint();
window.addEventListener('scroll',()=>{document.getElementById('mainNav')?.classList.toggle('scrolled',scrollY>10)});
const toTop=document.getElementById('toTop');
window.addEventListener('scroll',()=>{const s=scrollY>400;toTop?.classList.toggle('opacity-0',!s);toTop?.classList.toggle('invisible',!s);toTop?.classList.toggle('translate-y-3',!s)},{passive:true});
toTop?.addEventListener('click',()=>window.scrollTo({top:0,behavior:'smooth'}));
const io=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('show');io.unobserve(e.target)}}),{threshold:.12});
document.querySelectorAll('.reveal').forEach(el=>io.observe(el));
document.querySelectorAll('[data-count]').forEach(el=>{
  const t=parseInt(el.dataset.count||'0',10),suf=el.dataset.suffix||'';
  let c=0;const step=Math.max(1,Math.ceil(t/60));
  const iv=setInterval(()=>{c+=step;if(c>=t){c=t;clearInterval(iv)}el.textContent=c+suf},30);
});
document.querySelectorAll('[data-confirm]').forEach(f=>{f.addEventListener('submit',e=>{e.preventDefault();Swal.fire({title:'Apakah Anda yakin?',text:'Data yang dihapus tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal'}).then(r=>{if(r.isConfirmed)f.submit()})})});
document.querySelectorAll('[data-lightbox]').forEach(img=>{img.addEventListener('click',()=>{Swal.fire({imageUrl:img.src,imageAlt:img.alt||'',showConfirmButton:false,showCloseButton:true,width:800})})});
document.querySelectorAll('[data-carousel]').forEach(box=>{
  const slides=[...box.querySelectorAll('[data-slide]')];const dots=[...box.querySelectorAll('[data-dot]')];
  if(slides.length<2)return;let i=0,timer=null;
  const go=n=>{i=(n+slides.length)%slides.length;slides.forEach((s,k)=>{s.classList.toggle('opacity-100',k===i);s.classList.toggle('opacity-0',k!==i);s.classList.toggle('pointer-events-none',k!==i)});dots.forEach((d,k)=>{d.classList.toggle('bg-white',k===i);d.classList.toggle('bg-white/40',k!==i)})};
  const play=()=>{timer=setInterval(()=>go(i+1),5000)};const stop=()=>{clearInterval(timer)};
  box.querySelector('[data-prev]')?.addEventListener('click',()=>{stop();go(i-1);play()});
  box.querySelector('[data-next]')?.addEventListener('click',()=>{stop();go(i+1);play()});
  dots.forEach((d,k)=>d.addEventListener('click',()=>{stop();go(k);play()}));
  box.addEventListener('mouseenter',stop);box.addEventListener('mouseleave',play);play();
});
