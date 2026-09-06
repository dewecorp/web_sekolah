<?php $u = Auth::user(); ?>
<div id="adminTopbar" class="bg-emerald-700 text-white shadow sticky top-0 z-40"><div class="flex items-center justify-between px-4 py-2.5">
<div class="flex items-center gap-2"><button id="sideBtn" class="md:hidden w-9 h-9 border border-white/30 rounded-lg"><i class="fa fa-bars"></i></button><span class="font-bold"><i class="fa fa-graduation-cap mr-1"></i>SchoolCMS</span></div>
<div class="flex items-center gap-2 text-sm"><span class="hidden sm:block text-emerald-50"><i class="fa fa-user mr-1"></i><?= Helper::e($u['name'] ?? '') ?> (<?= Helper::e($u['role'] ?? '') ?>)</span>
<a href="<?= Helper::url() ?>" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 bg-white/15 border border-white/20 rounded-lg hover:bg-white/25"><i class="fa fa-globe mr-1"></i>Lihat Situs</a>
<form method="post" action="<?= Helper::url('admin/logout') ?>" data-confirm-logout><?= Security::csrfField() ?><button class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-500"><i class="fa fa-right-from-bracket mr-1"></i>Logout</button></form></div>
</div></div>
<script>document.getElementById('sideBtn')?.addEventListener('click',()=>document.getElementById('sidebar')?.classList.toggle('hidden'));</script>
