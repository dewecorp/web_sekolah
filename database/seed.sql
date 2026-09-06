-- Seed default CMS
INSERT INTO roles(name,description) VALUES
('administrator','Akses penuh'),('editor','Kelola konten'),('author','Buat konten sendiri')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- admin / admin123
INSERT INTO users(role_id,name,username,email,password,is_active) VALUES
(1,'Administrator','admin','admin@sekolah.sch.id','$2y$12$5PziR448WkwizQx4BEavK.KzxvqDn3ZrvPGhWuAuBkWJM700h0qQq',1)
ON DUPLICATE KEY UPDATE email=VALUES(email);

INSERT INTO settings(`key`,`value`) VALUES
('school_name','SMK Nusantara'),('tagline','Unggul, Berkarakter, Siap Kerja'),
('address','Jl. Pendidikan No. 123, Jakarta'),('phone','(021) 1234567'),
('email','info@smknusantara.sch.id'),('footer_text','SMK Nusantara'),
('powered_by','Powered by SchoolCMS'),('homepage_title','SMK Nusantara - Unggul dan Berkarakter')
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

INSERT INTO school_profile(principal_name,principal_title,principal_greeting,vision,mission,total_students,total_teachers,total_extracurricular,years_established) VALUES
('Drs. H. Ahmad Suryana, M.Pd','Kepala Sekolah','Selamat datang di SMK Nusantara. Kami berkomitmen mencetak lulusan unggul, berkarakter, dan siap kerja.','Menjadi sekolah unggul berstandar nasional tahun 2030.','1. Menyelenggarakan pembelajaran berkualitas.\n2. Membentuk karakter disiplin dan religius.\n3. Menjalin kemitraan industri.',520,38,12,25)
ON DUPLICATE KEY UPDATE principal_name=VALUES(principal_name);

INSERT INTO categories(name,slug) VALUES
('Berita Sekolah','berita-sekolah'),('Prestasi','prestasi'),('Pengumuman','pengumuman')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO homepage_sections(section_key,title,subtitle,sort_order) VALUES
('hero','Selamat Datang','Unggul, Berkarakter, Siap Kerja',1),
('sambutan','Sambutan Kepala Sekolah','',2),
('statistik','Statistik Sekolah','',3),
('berita','Berita Terbaru','Kabar terkini sekolah',4),
('agenda','Agenda Terdekat','',5),
('galeri','Galeri','',6),
('cta','Siap Bergabung?','PPDB Tahun Ajaran Baru',7)
ON DUPLICATE KEY UPDATE title=VALUES(title);

INSERT INTO menus(name,location) VALUES ('Menu Utama','primary'),('Menu Footer','footer')
ON DUPLICATE KEY UPDATE location=VALUES(location);

-- Menu default sinkron landing <-> manager (aman di-rerun, cegah duplikat via URL)
INSERT INTO menu_items(menu_id,parent_id,label,url,target,sort_order,is_active)
SELECT m.id,NULL,x.label,x.url,'_self',x.o,1 FROM menus m
JOIN (SELECT 'Beranda' label,'/' url,1 o UNION SELECT 'Profil','/profil',2 UNION SELECT 'Berita','/berita',3 UNION SELECT 'Galeri','/galeri',4 UNION SELECT 'Guru','/guru',5 UNION SELECT 'Kontak','/kontak',6) x
WHERE m.location='primary' AND NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_id=m.id AND url=x.url);
INSERT INTO menu_items(menu_id,parent_id,label,url,target,sort_order,is_active)
SELECT m.id,NULL,x.label,x.url,'_self',x.o,1 FROM menus m
JOIN (SELECT 'Profil' label,'/profil' url,1 o UNION SELECT 'Berita','/berita',2 UNION SELECT 'Galeri','/galeri',3 UNION SELECT 'Kontak','/kontak',4) x
WHERE m.location='footer' AND NOT EXISTS (SELECT 1 FROM menu_items WHERE menu_id=m.id AND url=x.url);

-- Demo data (aman dihapus untuk produksi)
INSERT INTO posts(category_id,title,slug,excerpt,content,status,author_id,published_at) VALUES
(1,'PPDB Tahun Ajaran Baru Telah Dibuka','ppdb-tahun-ajaran-baru','Penerimaan peserta didik baru tahun ajaran baru resmi dibuka.','<p>Penerimaan Peserta Didik Baru (PPDB) tahun ajaran baru telah dibuka. Calon siswa dapat mendaftar melalui sekretariat sekolah atau kontak panitia.</p><ul><li>Gelombang 1: Juni</li><li>Gelombang 2: Juli</li></ul>', 'published',1,NOW()),
(1,'Siswa Raih Juara Olimpiade Sains','siswa-juara-olimpiade','Tim olimpiade sekolah meraih juara tingkat kabupaten.','<p>Tim olimpiade sains sekolah berhasil meraih juara 1 tingkat kabupaten. Prestasi ini hasil pembinaan intensif dan kerja keras siswa.</p>','published',1,NOW()),
(2,'Upacara Bendera dan Pembinaan Karakter','upacara-bendera','Upacara rutin Senin dengan pembinaan karakter disiplin.','<p>Upacara bendera rutin dilaksanakan setiap Senin pagi sebagai sarana pembinaan karakter disiplin dan nasionalisme.</p>','published',1,NOW())
ON DUPLICATE KEY UPDATE title=VALUES(title);

INSERT INTO teachers(name,nip,position,type,education,subject,is_active,sort_order) VALUES
('Drs. H. Ahmad Suryana, M.Pd','19650101199001','Kepala Sekolah','tendik','S2 Manajemen Pendidikan','-',1,1),
('Siti Rahma, S.Pd','19820304200601','Waka Kurikulum','guru','S1 Pendidikan Matematika','Matematika',1,2),
('Budi Santoso, S.Kom','19870510201001','Guru Produktif','guru','S1 Teknik Informatika','Informatika',1,3)
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO agenda(title,event_date,start_time,location,description,status) VALUES
('Rapat Orang Tua Siswa',CURDATE()+INTERVAL 3 DAY,'09:00','Aula Sekolah','Sosialisasi program semester baru.', 'published'),
('Ujian Tengah Semester',CURDATE()+INTERVAL 10 DAY,'07:30','Ruang Kelas','UTS semester genap.', 'published')
ON DUPLICATE KEY UPDATE title=VALUES(title);

INSERT INTO announcements(title,content,status,published_at,author_id) VALUES
('Jadwal Libur Semester','Libur semester dimulai tanggal 20 Desember. Masuk kembali 3 Januari.', 'published',NOW(),1),
('Pembayaran SPP','Pembayaran SPP bulan berjalan paling lambat tanggal 10.', 'published',NOW(),1)
ON DUPLICATE KEY UPDATE title=VALUES(title);

INSERT INTO section_slides(section_id,heading,subheading,image,cta_text,cta_url,cta2_text,cta2_url,sort_order,is_active)
SELECT h.id,'Selamat Datang di SMK Nusantara','Unggul, Berkarakter, Siap Kerja','', 'Jelajahi Sekolah','/profil','Lihat Berita','/berita',1,1 FROM homepage_sections h
WHERE h.section_key='hero' AND NOT EXISTS (SELECT 1 FROM section_slides WHERE section_id=h.id);

INSERT INTO extracurriculars(name,description,coach,schedule) VALUES
('Pramuka','Kepramukaan dan kepemimpinan.','Pak Budi','Jumat 15.00'),
('Futsal','Olahraga futsal.','Pak Andi','Rabu 15.30')
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO achievements(title,description,year,level) VALUES
('Juara 1 Olimpiade Sains Kabupaten','Tim IPA meraih juara 1.','2025','Kabupaten'),
('Juara 2 Lomba Robotik','Tim robotik juara 2 provinsi.','2025','Provinsi')
ON DUPLICATE KEY UPDATE title=VALUES(title);
