document.documentElement.classList.add('js');
document.getElementById('loader')?.remove();
/* FX scroll: sembunyikan via INLINE (bukan CSS) setelah paint, lalu reveal
   transisi inline. Tanpa JS = tampil. Gagal JS = tampil. Tak bisa hilang. */
(function(){
  var map={'fade-up':'translate3d(0,48px,0) scale(.985)','fade-down':'translate3d(0,-48px,0) scale(.985)','fade-left':'translate3d(-56px,0,0)','fade-right':'translate3d(56px,0,0)','zoom-in':'scale(.9)','zoom-out':'scale(1.08)','slide-left':'translate3d(-90px,0,0)','slide-right':'translate3d(90px,0,0)','flip':'perspective(1000px) rotateY(55deg)','bounce-in':'scale(.8)','rotate-in':'rotate(-7deg) scale(.92)','light-left':'translate3d(-120px,20px,0) skewX(-12deg)','light-right':'translate3d(120px,20px,0) skewX(12deg)','back-in':'scale(.7) translate3d(0,50px,0)','roll-in':'translate3d(-100px,0,0) rotate(-120deg)','elastic-in':'scale(.45)','swing-in':'rotate(-15deg) translate3d(0,20px,0)','blur-in':'scale(1.04)','pop-in':'scale(.2)','skew-in':'translate3d(0,36px,0) skewY(10deg)'};
  var els=[...document.querySelectorAll('.fx')];
  function play(el){
    if(el.dataset.fxDone||!el.isConnected)return;el.dataset.fxDone='1';
    var from=map[el.dataset.fxType]||map['fade-up'];
    el.style.opacity='1';el.style.transform='none';el.style.filter='none';
    if(el.animate)el.animate([{opacity:0,transform:from,filter:'blur(7px)'},{opacity:1,transform:'none',filter:'blur(0)'}],{duration:1050,easing:'cubic-bezier(.16,1,.3,1)',fill:'both'});
    [...el.querySelectorAll('[data-fx-stagger]')].forEach((item,i)=>setTimeout(()=>item.classList.add('fx-stagger-show'),100+i*85));
  }
  function prep(el){
    if(el.classList.contains('fx-none')){play(el);return}
    var t=(el.className.match(/fx-([a-z-]+)/)||[])[1]||'fade-up';
    // Stop the CSS fallback after the loader disappears so inline viewport
    // transitions are not overridden by animation-fill-mode.
    el.style.animation='none';
    el.dataset.fxType=t;el.style.transition='none';el.style.willChange='opacity,transform';el.style.opacity='0';el.style.filter='blur(7px)';el.style.transform=map[t]||map['fade-up'];
    var candidates=el.querySelectorAll('.grid > article,.grid > a,.grid > div,.card-hover');
    candidates.forEach((item,i)=>{if(i<12&&!item.closest('[data-slide]'))item.setAttribute('data-fx-stagger','')});
    void el.offsetWidth;
  }
  requestAnimationFrame(()=>requestAnimationFrame(()=>{
    els.forEach(prep);
    var io=('IntersectionObserver' in window)?new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){play(e.target);io.unobserve(e.target)}}),{threshold:.1,rootMargin:'0px 0px -6% 0px'}):null;
    els.forEach((el,i)=>{
      var r=el.getBoundingClientRect();
      if(r.top<innerHeight*.96&&r.bottom>0)setTimeout(()=>play(el),100+i*100);
      else if(io)io.observe(el);else play(el);
    });
  }));
})();
// Animate staggered content inside the landing hero.
(function(){
  var items=[...document.querySelectorAll('[data-fx-item]')];
  if(!items.length)return;
  requestAnimationFrame(()=>items.forEach(el=>{
    el.classList.add('fx-item');
    var delay=el.style.getPropertyValue('--fx-d')||'0s';el.style.setProperty('--fx-delay',delay);
  }));
})();
const mb=document.getElementById('mobBtn'),mm=document.getElementById('mobMenu');
mb?.addEventListener('click',()=>mm.classList.toggle('hidden'));
(function(){const d=document.querySelector('[data-clock-date]'),t=document.querySelector('[data-clock-time]');if(!d&&!t)return;const days=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];const months={Jan:'Jan',Feb:'Feb',Mar:'Mar',Apr:'Apr',May:'Mei',Jun:'Jun',Jul:'Jul',Aug:'Agu',Sep:'Sep',Oct:'Okt',Nov:'Nov',Dec:'Des'};const pad=n=>String(n).padStart(2,'0');function tick(){const n=new Date();if(t)t.textContent=pad(n.getHours())+':'+pad(n.getMinutes())+':'+pad(n.getSeconds());if(d){const parts=n.toDateString().split(' ');d.textContent=days[n.getDay()]+', '+parts[2]+' '+(months[parts[1]]||parts[1])+' '+parts[3]}}tick();setInterval(tick,1000)})();
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
document.querySelectorAll('[id^="cd-"]').forEach(el=>{
  const target=(el.dataset.target||'').trim();if(!target)return;
  const end=new Date(target).getTime();if(isNaN(end))return;
  const pad=n=>String(n).padStart(2,'0');
  const box=(v,l)=>'<div class="text-center"><div class="text-3xl font-extrabold bg-white/20 px-4 py-2 rounded-xl tabular-nums">'+v+'</div><div class="text-xs text-white/75 mt-1">'+l+'</div></div>';
  const tick=()=>{let t=end-Date.now();if(t<0)t=0;const d=Math.floor(t/864e5),h=Math.floor(t/36e5)%24,m=Math.floor(t/6e4)%60,s=Math.floor(t/1e3)%60;el.innerHTML=box(d,'Hari')+box(pad(h),'Jam')+box(pad(m),'Menit')+box(pad(s),'Detik')};
  tick();setInterval(tick,1000);
});
document.querySelectorAll('[data-vid-play]').forEach(b=>b.addEventListener('click',()=>{const f=document.getElementById(b.dataset.vidTarget),t=document.getElementById(b.dataset.vidLabel);if(f)f.src=b.dataset.vidPlay+(b.dataset.vidPlay.includes('?')?'&':'?')+'autoplay=1';if(t)t.textContent=b.dataset.vidTitle||'Video';f?.scrollIntoView({behavior:'smooth',block:'center'})}));
document.querySelectorAll('[data-carousel]').forEach(box=>{
  const slides=[...box.querySelectorAll('[data-slide]')];const dots=[...box.querySelectorAll('[data-dot]')];
  if(slides.length<2)return;let i=0,timer=null;
  const go=n=>{i=(n+slides.length)%slides.length;slides.forEach((s,k)=>{s.classList.toggle('opacity-100',k===i);s.classList.toggle('opacity-0',k!==i);s.classList.toggle('pointer-events-none',k!==i)});dots.forEach((d,k)=>{d.classList.toggle('bg-white',k===i);d.classList.toggle('bg-white/40',k!==i)})};
  const interval=Math.max(1000,parseInt(box.dataset.interval||'5000',10)||5000);const play=()=>{timer=setInterval(()=>go(i+1),interval)};const stop=()=>{clearInterval(timer)};
  box.querySelector('[data-prev]')?.addEventListener('click',()=>{stop();go(i-1);play()});
  box.querySelector('[data-next]')?.addEventListener('click',()=>{stop();go(i+1);play()});
  dots.forEach((d,k)=>d.addEventListener('click',()=>{stop();go(k);play()}));
  box.addEventListener('mouseenter',stop);box.addEventListener('mouseleave',play);play();
});
