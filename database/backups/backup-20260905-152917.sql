-- SchoolCMS Backup 2026-09-05T15:29:17+07:00
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `achievements`;
CREATE TABLE `achievements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `year` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `achievements` (`id`,`title`,`description`,`year`,`level`,`image`,`is_active`,`created_at`) VALUES ('1','Juara 1 Olimpiade Sains Kabupaten','Tim IPA meraih juara 1.','2025','Kabupaten',NULL,'1','2026-09-05 06:20:17');
INSERT INTO `achievements` (`id`,`title`,`description`,`year`,`level`,`image`,`is_active`,`created_at`) VALUES ('2','Juara 2 Lomba Robotik','Tim robotik juara 2 provinsi.','2025','Provinsi',NULL,'1','2026-09-05 06:20:17');
INSERT INTO `achievements` (`id`,`title`,`description`,`year`,`level`,`image`,`is_active`,`created_at`) VALUES ('3','Juara 1 Olimpiade Sains Kabupaten','Tim IPA meraih juara 1.','2025','Kabupaten',NULL,'1','2026-09-05 06:22:10');
INSERT INTO `achievements` (`id`,`title`,`description`,`year`,`level`,`image`,`is_active`,`created_at`) VALUES ('4','Juara 2 Lomba Robotik','Tim robotik juara 2 provinsi.','2025','Provinsi',NULL,'1','2026-09-05 06:22:10');

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_date` (`created_at`),
  CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('1','4','login','auth','Login berhasil','::1','2026-09-05 06:23:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('2','4','update','sections','Ubah section homepage','::1','2026-09-05 06:28:24');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('3','4','backup','db','Backup database','::1','2026-09-05 06:32:29');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('4','4','update','sections','Ubah hero','::1','2026-09-05 06:42:27');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('5','4','create','sections','Quick add hero','::1','2026-09-05 06:43:02');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('6','4','delete','sections','Hapus section','::1','2026-09-05 06:43:12');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('7','4','update','sections','Reorder via drag-drop','::1','2026-09-05 06:43:36');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('8','4','update','sections','Ubah statistik','::1','2026-09-05 06:44:41');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('9','4','update','sections','Ubah statistik','::1','2026-09-05 06:45:43');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('10','4','create','sections','Quick add guru','::1','2026-09-05 07:32:12');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('11','4','update','sections','Reorder via drag-drop','::1','2026-09-05 07:32:25');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('12','4','update','sections','Reorder via drag-drop','::1','2026-09-05 07:32:25');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('13','4','update','sections','Ubah guru-1788568332','::1','2026-09-05 07:33:17');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('14','4','update','sections','Reorder via drag-drop','::1','2026-09-05 07:33:46');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('15','4','update','sections','Reorder via drag-drop','::1','2026-09-05 07:33:46');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('16','4','update','sections','Ubah guru-1788568332','::1','2026-09-05 07:34:10');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('17','4','update','sections','Ubah guru-1788568332','::1','2026-09-05 07:34:39');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('18','4','delete','teachers','Hapus guru','::1','2026-09-05 07:38:16');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('19','4','update','sections','Ubah hero','::1','2026-09-05 07:48:32');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('20','4','update','sections','Ubah sambutan','::1','2026-09-05 07:49:03');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('21','4','update','sections','Ubah hero','::1','2026-09-05 07:49:39');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('22','4','update','sections','Ubah hero','::1','2026-09-05 07:54:58');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('23','4','update','sections','Ubah hero','::1','2026-09-05 07:55:13');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('24','4','update','sections','Ubah hero','::1','2026-09-05 07:55:29');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('25','4','update','sections','Ubah hero','::1','2026-09-05 08:19:24');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('26','4','delete','teachers','Hapus guru','::1','2026-09-05 08:22:19');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('27','4','delete','teachers','Hapus guru','::1','2026-09-05 08:22:27');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('28','4','update','sections','Ubah hero','::1','2026-09-05 08:58:35');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('29','4','create','pages','Tambah Sejarah Madrasah','::1','2026-09-05 14:32:44');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('30','4','create','menus','Tambah 1 laman ke menu','::1','2026-09-05 14:39:53');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('31','4','update','menus','Reorder drag-drop','::1','2026-09-05 14:42:21');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('32','4','update','menus','Reorder drag-drop','::1','2026-09-05 14:42:25');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('33','4','update','menus','Reorder drag-drop','::1','2026-09-05 14:42:29');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('34','4','update','menus','Reorder drag-drop','::1','2026-09-05 14:42:54');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('35','4','update','menus','Reorder drag-drop','::1','2026-09-05 14:42:59');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('36','4','update','menus','Reorder drag-drop','::1','2026-09-05 14:44:30');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('37','4','update','menus','Reorder drag-drop','::1','2026-09-05 14:44:45');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('38','4','update','menus','Ubah Profil Madrasah','::1','2026-09-05 14:48:33');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('39','4','update','menus','Ubah Sejarah Madrasah','::1','2026-09-05 14:48:49');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('40','4','update','menus','Reorder drag-drop','::1','2026-09-05 14:50:17');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('41','4','update','menus','Reorder drag-drop','::1','2026-09-05 14:50:21');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('42','4','update','menus','Ubah menu','::1','2026-09-05 14:51:23');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('43','4','update','menus','Ubah menu','::1','2026-09-05 14:51:58');

DROP TABLE IF EXISTS `agenda`;
CREATE TABLE `agenda` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date NOT NULL,
  `start_time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_date` (`event_date`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `agenda` (`id`,`title`,`event_date`,`start_time`,`end_time`,`location`,`description`,`status`,`created_at`,`updated_at`) VALUES ('1','Rapat Orang Tua Siswa','2026-09-08','09:00',NULL,'Aula Sekolah','Sosialisasi program semester baru.','published','2026-09-05 06:20:17','2026-09-05 06:20:17');
INSERT INTO `agenda` (`id`,`title`,`event_date`,`start_time`,`end_time`,`location`,`description`,`status`,`created_at`,`updated_at`) VALUES ('2','Ujian Tengah Semester','2026-09-15','07:30',NULL,'Ruang Kelas','UTS semester genap.','published','2026-09-05 06:20:17','2026-09-05 06:20:17');
INSERT INTO `agenda` (`id`,`title`,`event_date`,`start_time`,`end_time`,`location`,`description`,`status`,`created_at`,`updated_at`) VALUES ('3','Rapat Orang Tua Siswa','2026-09-08','09:00',NULL,'Aula Sekolah','Sosialisasi program semester baru.','published','2026-09-05 06:22:10','2026-09-05 06:22:10');
INSERT INTO `agenda` (`id`,`title`,`event_date`,`start_time`,`end_time`,`location`,`description`,`status`,`created_at`,`updated_at`) VALUES ('4','Ujian Tengah Semester','2026-09-15','07:30',NULL,'Ruang Kelas','UTS semester genap.','published','2026-09-05 06:22:10','2026-09-05 06:22:10');

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `published_at` datetime DEFAULT NULL,
  `author_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status_date` (`status`,`published_at`),
  KEY `fk_ann_author` (`author_id`),
  CONSTRAINT `fk_ann_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `announcements` (`id`,`title`,`content`,`attachment`,`status`,`published_at`,`author_id`,`created_at`,`updated_at`) VALUES ('1','Jadwal Libur Semester','Libur semester dimulai tanggal 20 Desember. Masuk kembali 3 Januari.',NULL,'published','2026-09-05 06:20:17',NULL,'2026-09-05 06:20:17','2026-09-05 06:20:17');
INSERT INTO `announcements` (`id`,`title`,`content`,`attachment`,`status`,`published_at`,`author_id`,`created_at`,`updated_at`) VALUES ('2','Pembayaran SPP','Pembayaran SPP bulan berjalan paling lambat tanggal 10.',NULL,'published','2026-09-05 06:20:17',NULL,'2026-09-05 06:20:17','2026-09-05 06:20:17');
INSERT INTO `announcements` (`id`,`title`,`content`,`attachment`,`status`,`published_at`,`author_id`,`created_at`,`updated_at`) VALUES ('3','Jadwal Libur Semester','Libur semester dimulai tanggal 20 Desember. Masuk kembali 3 Januari.',NULL,'published','2026-09-05 06:22:10',NULL,'2026-09-05 06:22:10','2026-09-05 06:22:10');
INSERT INTO `announcements` (`id`,`title`,`content`,`attachment`,`status`,`published_at`,`author_id`,`created_at`,`updated_at`) VALUES ('4','Pembayaran SPP','Pembayaran SPP bulan berjalan paling lambat tanggal 10.',NULL,'published','2026-09-05 06:22:10',NULL,'2026-09-05 06:22:10','2026-09-05 06:22:10');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `categories` (`id`,`name`,`slug`,`description`,`created_at`,`updated_at`) VALUES ('1','Berita Sekolah','berita-sekolah',NULL,'2026-09-05 06:06:26','2026-09-05 06:06:26');
INSERT INTO `categories` (`id`,`name`,`slug`,`description`,`created_at`,`updated_at`) VALUES ('2','Prestasi','prestasi',NULL,'2026-09-05 06:06:26','2026-09-05 06:06:26');
INSERT INTO `categories` (`id`,`name`,`slug`,`description`,`created_at`,`updated_at`) VALUES ('3','Pengumuman','pengumuman',NULL,'2026-09-05 06:06:26','2026-09-05 06:06:26');

DROP TABLE IF EXISTS `extracurriculars`;
CREATE TABLE `extracurriculars` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `coach` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `schedule` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `extracurriculars` (`id`,`name`,`description`,`coach`,`schedule`,`image`,`is_active`,`sort_order`) VALUES ('1','Pramuka','Kepramukaan dan kepemimpinan.','Pak Budi','Jumat 15.00',NULL,'1','0');
INSERT INTO `extracurriculars` (`id`,`name`,`description`,`coach`,`schedule`,`image`,`is_active`,`sort_order`) VALUES ('2','Futsal','Olahraga futsal.','Pak Andi','Rabu 15.30',NULL,'1','0');
INSERT INTO `extracurriculars` (`id`,`name`,`description`,`coach`,`schedule`,`image`,`is_active`,`sort_order`) VALUES ('3','Pramuka','Kepramukaan dan kepemimpinan.','Pak Budi','Jumat 15.00',NULL,'1','0');
INSERT INTO `extracurriculars` (`id`,`name`,`description`,`coach`,`schedule`,`image`,`is_active`,`sort_order`) VALUES ('4','Futsal','Olahraga futsal.','Pak Andi','Rabu 15.30',NULL,'1','0');

DROP TABLE IF EXISTS `galleries`;
CREATE TABLE `galleries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `gallery_images`;
CREATE TABLE `gallery_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gallery_id` int unsigned NOT NULL,
  `filepath` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gallery` (`gallery_id`),
  CONSTRAINT `fk_gimg_gallery` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `homepage_sections`;
CREATE TABLE `homepage_sections` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `section_key` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'custom',
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btn_text` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btn_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btn2_text` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btn2_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `bg` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `padding` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lg',
  `align` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'left',
  `items_limit` int NOT NULL DEFAULT '3',
  `effect` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fade-up',
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_key` (`section_key`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `homepage_sections` (`id`,`section_key`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`) VALUES ('1','hero','Selamat Datang di Website Kami','Unggul, Berkarakter, Siap Kerja','1','1','hero','','','','','','','default','emerald-soft','lg','left','3','fade-up');
INSERT INTO `homepage_sections` (`id`,`section_key`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`) VALUES ('2','sambutan','Sambutan Kepala Madrasah','','1','2','sambutan','','','','','','','default','white','lg','left','3','fade-left');
INSERT INTO `homepage_sections` (`id`,`section_key`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`) VALUES ('3','statistik','Statistik Sekolah','','1','3','statistik','','','','','','','bordered','gradient-emerald','lg','left','3','fade-up');
INSERT INTO `homepage_sections` (`id`,`section_key`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`) VALUES ('4','berita','Berita Terbaru','Kabar terkini sekolah','1','5','berita',NULL,NULL,NULL,NULL,NULL,NULL,'default','white','lg','left','3','fade-right');
INSERT INTO `homepage_sections` (`id`,`section_key`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`) VALUES ('5','agenda','Agenda Terdekat','','1','6','agenda',NULL,NULL,NULL,NULL,NULL,NULL,'default','white','lg','left','3','fade-up');
INSERT INTO `homepage_sections` (`id`,`section_key`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`) VALUES ('6','galeri','Galeri','','1','7','galeri',NULL,NULL,NULL,NULL,NULL,NULL,'default','white','lg','left','3','fade-up');
INSERT INTO `homepage_sections` (`id`,`section_key`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`) VALUES ('7','cta','Siap Bergabung?','PPDB Tahun Ajaran Baru','1','8','cta',NULL,NULL,NULL,NULL,NULL,NULL,'default','white','lg','left','3','fade-up');
INSERT INTO `homepage_sections` (`id`,`section_key`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`) VALUES ('23','guru-1788568332','Pendidik dan Tenaga Kependidikan','','1','4','guru','','','','','','','default','white','lg','left','3','fade-up');

DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_time` (`ip`,`attempted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filepath` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_bytes` int unsigned NOT NULL,
  `alt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mime` (`mime`),
  KEY `fk_media_user` (`uploaded_by`),
  CONSTRAINT `fk_media_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `mega_menus`;
CREATE TABLE `mega_menus` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_item_id` int unsigned DEFAULT NULL,
  `columns_json` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_mm_item` (`menu_item_id`),
  CONSTRAINT `fk_mm_item` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int unsigned NOT NULL,
  `parent_id` int unsigned DEFAULT NULL,
  `label` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '_self',
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `kind` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'link',
  `mega_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_menu_parent` (`menu_id`,`parent_id`,`sort_order`),
  KEY `fk_mi_parent` (`parent_id`),
  CONSTRAINT `fk_mi_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mi_parent` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('3','1',NULL,'Beranda','/','_self','1','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('4','1',NULL,'Profil','/profil','_self','2','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('5','1',NULL,'Berita','/berita','_self','3','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('6','1',NULL,'Galeri','/galeri','_self','4','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('7','1',NULL,'Guru','/guru','_self','5','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('8','1',NULL,'Kontak','/kontak','_self','6','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('9','1','4','Sejarah Madrasah','/sejarah-madrasah','_self','1','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('10','1','4','Visi Misi','/visi-misi','_self','2','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('11','2',NULL,'Profil','/profil','_self','1','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('12','2',NULL,'Berita','/berita','_self','2','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('13','2',NULL,'Galeri','/galeri','_self','3','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('14','2',NULL,'Kontak','/kontak','_self','4','1','link',NULL);

DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `menus` (`id`,`name`,`location`,`created_at`) VALUES ('1','Utama','primary','2026-09-05 06:06:26');
INSERT INTO `menus` (`id`,`name`,`location`,`created_at`) VALUES ('2','Menu Footer','footer','2026-09-05 06:06:26');

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `seo_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug_status` (`slug`,`status`),
  KEY `fk_pages_author` (`author_id`),
  CONSTRAINT `fk_pages_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `pages` (`id`,`title`,`slug`,`content`,`featured_image`,`status`,`seo_title`,`seo_description`,`author_id`,`created_at`,`updated_at`,`deleted_at`) VALUES ('1','Sejarah Madrasah','sejarah-madrasah','<p>Sejarah ……</p>','','published','','','4','2026-09-05 14:32:44','2026-09-05 14:32:44',NULL);

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `author_id` int unsigned DEFAULT NULL,
  `views` int unsigned DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_status_date` (`status`,`published_at`),
  KEY `fk_posts_cat` (`category_id`),
  KEY `fk_posts_author` (`author_id`),
  CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_posts_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `posts` (`id`,`category_id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`status`,`author_id`,`views`,`published_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('1','1','PPDB Tahun Ajaran Baru Telah Dibuka','ppdb-tahun-ajaran-baru','Penerimaan peserta didik baru tahun ajaran baru resmi dibuka.','<p>Penerimaan Peserta Didik Baru (PPDB) tahun ajaran baru telah dibuka. Calon siswa dapat mendaftar melalui sekretariat sekolah atau kontak panitia.</p><ul><li>Gelombang 1: Juni</li><li>Gelombang 2: Juli</li></ul>',NULL,'published',NULL,'0','2026-09-05 06:20:17','2026-09-05 06:20:17','2026-09-05 06:20:17',NULL);
INSERT INTO `posts` (`id`,`category_id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`status`,`author_id`,`views`,`published_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('2','1','Siswa Raih Juara Olimpiade Sains','siswa-juara-olimpiade','Tim olimpiade sekolah meraih juara tingkat kabupaten.','<p>Tim olimpiade sains sekolah berhasil meraih juara 1 tingkat kabupaten. Prestasi ini hasil pembinaan intensif dan kerja keras siswa.</p>',NULL,'published',NULL,'0','2026-09-05 06:20:17','2026-09-05 06:20:17','2026-09-05 06:20:17',NULL);
INSERT INTO `posts` (`id`,`category_id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`status`,`author_id`,`views`,`published_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('3','2','Upacara Bendera dan Pembinaan Karakter','upacara-bendera','Upacara rutin Senin dengan pembinaan karakter disiplin.','<p>Upacara bendera rutin dilaksanakan setiap Senin pagi sebagai sarana pembinaan karakter disiplin dan nasionalisme.</p>',NULL,'published',NULL,'1','2026-09-05 06:20:17','2026-09-05 06:20:17','2026-09-05 06:23:16',NULL);

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `roles` (`id`,`name`,`description`,`created_at`,`updated_at`) VALUES ('1','administrator','Akses penuh','2026-09-05 06:06:25','2026-09-05 06:06:25');
INSERT INTO `roles` (`id`,`name`,`description`,`created_at`,`updated_at`) VALUES ('2','editor','Kelola konten','2026-09-05 06:06:25','2026-09-05 06:06:25');
INSERT INTO `roles` (`id`,`name`,`description`,`created_at`,`updated_at`) VALUES ('3','author','Buat konten sendiri','2026-09-05 06:06:25','2026-09-05 06:06:25');

DROP TABLE IF EXISTS `school_profile`;
CREATE TABLE `school_profile` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `principal_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Kepala Sekolah',
  `principal_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_greeting` text COLLATE utf8mb4_unicode_ci,
  `history` text COLLATE utf8mb4_unicode_ci,
  `vision` text COLLATE utf8mb4_unicode_ci,
  `mission` text COLLATE utf8mb4_unicode_ci,
  `org_chart` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_students` int DEFAULT '0',
  `total_teachers` int DEFAULT '0',
  `total_extracurricular` int DEFAULT '0',
  `years_established` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `school_profile` (`id`,`principal_name`,`principal_title`,`principal_photo`,`principal_greeting`,`history`,`vision`,`mission`,`org_chart`,`total_students`,`total_teachers`,`total_extracurricular`,`years_established`,`updated_at`) VALUES ('1','Drs. H. Ahmad Suryana, M.Pd','Kepala Sekolah',NULL,'Selamat datang di SMK Nusantara. Kami berkomitmen mencetak lulusan unggul, berkarakter, dan siap kerja.',NULL,'Menjadi sekolah unggul berstandar nasional tahun 2030.','1. Menyelenggarakan pembelajaran berkualitas.\n2. Membentuk karakter disiplin dan religius.\n3. Menjalin kemitraan industri.',NULL,'520','38','12','25','2026-09-05 06:06:26');
INSERT INTO `school_profile` (`id`,`principal_name`,`principal_title`,`principal_photo`,`principal_greeting`,`history`,`vision`,`mission`,`org_chart`,`total_students`,`total_teachers`,`total_extracurricular`,`years_established`,`updated_at`) VALUES ('2','Drs. H. Ahmad Suryana, M.Pd','Kepala Sekolah',NULL,'Selamat datang di SMK Nusantara. Kami berkomitmen mencetak lulusan unggul, berkarakter, dan siap kerja.',NULL,'Menjadi sekolah unggul berstandar nasional tahun 2030.','1. Menyelenggarakan pembelajaran berkualitas.\n2. Membentuk karakter disiplin dan religius.\n3. Menjalin kemitraan industri.',NULL,'520','38','12','25','2026-09-05 06:20:17');
INSERT INTO `school_profile` (`id`,`principal_name`,`principal_title`,`principal_photo`,`principal_greeting`,`history`,`vision`,`mission`,`org_chart`,`total_students`,`total_teachers`,`total_extracurricular`,`years_established`,`updated_at`) VALUES ('3','Drs. H. Ahmad Suryana, M.Pd','Kepala Sekolah',NULL,'Selamat datang di SMK Nusantara. Kami berkomitmen mencetak lulusan unggul, berkarakter, dan siap kerja.',NULL,'Menjadi sekolah unggul berstandar nasional tahun 2030.','1. Menyelenggarakan pembelajaran berkualitas.\n2. Membentuk karakter disiplin dan religius.\n3. Menjalin kemitraan industri.',NULL,'520','38','12','25','2026-09-05 06:22:10');

DROP TABLE IF EXISTS `seo_settings`;
CREATE TABLE `seo_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('1','school_name','SMK Nusantara','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('2','tagline','Unggul, Berkarakter, Siap Kerja','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('3','address','Jl. Pendidikan No. 123, Jakarta','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('4','phone','(021) 1234567','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('5','email','info@smknusantara.sch.id','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('6','footer_text','SMK Nusantara','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('7','powered_by','Powered by SchoolCMS','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('8','homepage_title','SMK Nusantara - Unggul dan Berkarakter','2026-09-05 06:06:26');

DROP TABLE IF EXISTS `sliders`;
CREATE TABLE `sliders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `heading` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subheading` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cta_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta2_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta2_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active_order` (`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `sliders` (`id`,`heading`,`subheading`,`image`,`cta_text`,`cta_url`,`cta2_text`,`cta2_url`,`sort_order`,`is_active`,`created_at`) VALUES ('1','Selamat Datang di SMK Nusantara','Unggul, Berkarakter, Siap Kerja','','Jelajahi Sekolah','/profil','Lihat Berita','/berita','1','1','2026-09-05 06:20:17');
INSERT INTO `sliders` (`id`,`heading`,`subheading`,`image`,`cta_text`,`cta_url`,`cta2_text`,`cta2_url`,`sort_order`,`is_active`,`created_at`) VALUES ('2','Selamat Datang di SMK Nusantara','Unggul, Berkarakter, Siap Kerja','','Jelajahi Sekolah','/profil','Lihat Berita','/berita','1','1','2026-09-05 06:22:10');

DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Guru',
  `type` enum('guru','tendik') COLLATE utf8mb4_unicode_ci DEFAULT 'guru',
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `education` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active_order` (`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `teachers` (`id`,`name`,`nip`,`position`,`type`,`photo`,`education`,`subject`,`description`,`is_active`,`sort_order`,`created_at`,`updated_at`) VALUES ('4','Drs. H. Ahmad Suryana, M.Pd','19650101199001','Kepala Sekolah','tendik',NULL,'S2 Manajemen Pendidikan','-',NULL,'1','1','2026-09-05 06:22:10','2026-09-05 06:22:10');
INSERT INTO `teachers` (`id`,`name`,`nip`,`position`,`type`,`photo`,`education`,`subject`,`description`,`is_active`,`sort_order`,`created_at`,`updated_at`) VALUES ('5','Siti Rahma, S.Pd','19820304200601','Waka Kurikulum','guru',NULL,'S1 Pendidikan Matematika','Matematika',NULL,'1','2','2026-09-05 06:22:10','2026-09-05 06:22:10');
INSERT INTO `teachers` (`id`,`name`,`nip`,`position`,`type`,`photo`,`education`,`subject`,`description`,`is_active`,`sort_order`,`created_at`,`updated_at`) VALUES ('6','Budi Santoso, S.Kom','19870510201001','Guru Produktif','guru',NULL,'S1 Teknik Informatika','Informatika',NULL,'1','3','2026-09-05 06:22:10','2026-09-05 06:22:10');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int unsigned NOT NULL DEFAULT '3',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role` (`role_id`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `users` (`id`,`role_id`,`name`,`username`,`email`,`password`,`avatar`,`is_active`,`remember_token`,`last_login_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('4','1','Administrator','admin','admin@sekolah.sch.id','$2y$10$C1oEysEHyd3zwgeCe7AeleKht016Y2clXUDLkeyTD50JDR/4qL77y',NULL,'1',NULL,'2026-09-05 06:23:55','2026-09-05 06:22:21','2026-09-05 06:23:55',NULL);

DROP TABLE IF EXISTS `widgets`;
CREATE TABLE `widgets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `area` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_area` (`area`,`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
