-- SchoolCMS Backup 2026-09-10T17:43:42+07:00
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
) ENGINE=InnoDB AUTO_INCREMENT=723 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('623','4','update','widgets','Ubah widget Agenda Bulan (agenda/sidebar)','::1','2026-09-09 18:55:17');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('624','4','update','widgets','Ubah widget Pengumuman Bulan (announcements/sidebar)','::1','2026-09-09 18:55:25');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('625','4','delete','sections','Hapus section','::1','2026-09-10 06:09:59');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('626','4','delete','sections','Hapus section','::1','2026-09-10 06:10:07');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('627','4','create','sections','Quick add sambutan','::1','2026-09-10 06:10:09');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('628','4','delete','sections','Hapus section','::1','2026-09-10 06:10:15');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('629','4','delete','sections','Hapus section','::1','2026-09-10 07:12:38');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('630','4','update','sections','Reorder via drag-drop','::1','2026-09-10 07:12:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('631','4','update','sections','Reorder via drag-drop','::1','2026-09-10 07:12:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('632','4','update','sections','Reorder via drag-drop','::1','2026-09-10 07:13:04');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('633','4','update','sections','Reorder via drag-drop','::1','2026-09-10 07:13:04');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('634','4','update','sections','Reorder via drag-drop','::1','2026-09-10 07:13:08');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('635','4','update','sections','Reorder via drag-drop','::1','2026-09-10 07:13:08');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('636','4','update','sections','Ubah berita-kat-1','::1','2026-09-10 07:13:28');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('637','4','update','sections','Ubah berita-kat-3','::1','2026-09-10 07:13:35');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('638','4','update','sections','Ubah berita-kat-2','::1','2026-09-10 07:13:45');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('639','4','update','sections','Ubah berita-kat-9','::1','2026-09-10 07:13:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('640','4','update','sections','Ubah berita-kat-8','::1','2026-09-10 07:13:59');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('641','4','update','sections','Reorder via drag-drop','::1','2026-09-10 07:15:26');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('642','4','update','sections','Reorder via drag-drop','::1','2026-09-10 07:15:26');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('643','4','update','sections','Ubah berita-kat-2','::1','2026-09-10 07:18:02');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('644','4','update','sections','Ubah berita-kat-1','::1','2026-09-10 07:18:47');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('645','4','update','settings','Ubah pengaturan tampilan','::1','2026-09-10 07:23:07');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('646','4','create','media','Upload CKEditor 20260910-080755-3c3462d31829.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('647','4','create','media','Upload CKEditor 20260910-080755-9433c3700600.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('648','4','create','media','Upload CKEditor 20260910-080755-63ae2742871c.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('649','4','create','media','Upload CKEditor 20260910-080755-3e1e2d7c7923.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('650','4','create','media','Upload CKEditor 20260910-080755-63263dbf9d5f.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('651','4','create','media','Upload CKEditor 20260910-080755-583fc4f24c83.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('652','4','create','media','Upload CKEditor 20260910-080755-cf26818e5037.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('653','4','create','media','Upload CKEditor 20260910-080755-035f152d0a8a.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('654','4','create','media','Upload CKEditor 20260910-080755-85ab063ec94a.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('655','4','create','media','Upload CKEditor 20260910-080755-e8ee3d91966a.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('656','4','create','media','Upload CKEditor 20260910-080755-228819aed452.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('657','4','create','media','Upload CKEditor 20260910-080755-65f1877f9e51.jpg','::1','2026-09-10 08:07:55');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('658','4','create','media','Upload CKEditor 20260910-080755-680d129bc1e1.jpg','::1','2026-09-10 08:07:56');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('659','4','create','posts','Tambah Pendidikan di Era Perubahan: Membangun Generasi yang Cerdas, Berkarakter, dan Adaptif','::1','2026-09-10 08:09:17');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('660','4','update','posts','Ubah Pendidikan di Era Perubahan: Membangun Generasi yang Cerdas, Berkarakter, dan Adaptif','::1','2026-09-10 08:10:01');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('661','4','update','posts','Ubah Pendidikan di Era Perubahan: Membangun Generasi yang Cerdas, Berkarakter, dan Adaptif','::1','2026-09-10 08:10:38');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('662','4','update','posts','Ubah Pendidikan di Era Perubahan: Membangun Generasi yang Cerdas, Berkarakter, dan Adaptif','::1','2026-09-10 08:12:43');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('663','4','delete','media','Hapus massal 11 media','::1','2026-09-10 08:14:49');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('664','4','update','sections','Ubah berita-kat-9','::1','2026-09-10 08:18:02');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('665','4','update','sections','Ubah berita-kat-9','::1','2026-09-10 08:18:26');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('666','4','update','sections','Ubah berita-kat-9','::1','2026-09-10 08:19:29');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('667','4','delete','categories','Hapus kategori','::1','2026-09-10 08:25:32');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('668','4','delete','sections','Hapus section','::1','2026-09-10 08:25:45');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('669','4','create','posts','Tambah OMI 2026 Jepara: Ajang Mengasah Talenta dan Semangat Berprestasi Siswa Madrasah','::1','2026-09-10 08:29:13');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('670','4','update','posts','Ubah OMI 2026 Jepara: Ajang Mengasah Talenta dan Semangat Berprestasi Siswa Madrasah','::1','2026-09-10 08:29:21');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('671','4','update','posts','Ubah OMI 2026 Jepara: Ajang Mengasah Talenta dan Semangat Berprestasi Siswa Madrasah','::1','2026-09-10 08:31:23');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('672','4','update','sections','Ubah berita-kat-9','::1','2026-09-10 08:33:56');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('673','4','update','sections','Ubah berita','::1','2026-09-10 08:37:04');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('674','4','create','gallery','Album Lainnya','::1','2026-09-10 08:37:53');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('675','4','delete','menus','Hapus item','::1','2026-09-10 08:40:20');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('676','4','update','settings','Ubah identitas sekolah','::1','2026-09-10 08:43:29');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('677','4','update','settings','Ubah identitas sekolah','::1','2026-09-10 08:44:54');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('678','4','update','posts','Ubah Upacara Bendera dan Pembinaan Karakter','::1','2026-09-10 08:46:54');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('679','4','update','posts','Ubah Upacara Bendera sebagai Sarana Pembinaan Karakter Siswa','::1','2026-09-10 08:53:26');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('680','4','update','posts','Ubah Upacara Bendera sebagai Sarana Pembinaan Karakter Siswa','::1','2026-09-10 08:55:06');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('681','4','update','curriculum','Ubah kurikulum','::1','2026-09-10 08:58:02');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('682','4','update','curriculum','Ubah kurikulum','::1','2026-09-10 08:58:23');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('683','4','update','settings','Ubah identitas sekolah','::1','2026-09-10 09:01:30');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('684','4','update','sections','Ubah berita-kat-1','::1','2026-09-10 10:21:45');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('685','4','update','sections','Ubah berita-kat-3','::1','2026-09-10 10:24:00');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('686','4','update','sections','Ubah berita-kat-2','::1','2026-09-10 11:25:51');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('687','4','update','sections','Ubah berita-kat-2','::1','2026-09-10 11:26:26');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('688','4','update','sections','Ubah berita-kat-2','::1','2026-09-10 11:27:05');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('689','4','update','sections','Ubah berita-kat-2','::1','2026-09-10 11:27:30');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('690','4','update','settings','Ubah identitas sekolah','::1','2026-09-10 11:37:01');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('691','4','update','sections','Ubah sambutan','::1','2026-09-10 11:37:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('692','4','update','settings','Ubah identitas sekolah','::1','2026-09-10 11:40:30');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('693','4','update','sections','Ubah sambutan','::1','2026-09-10 11:50:20');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('694','4','update','sections','Ubah sambutan','::1','2026-09-10 11:50:35');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('695','4','update','sections','Ubah sambutan','::1','2026-09-10 11:50:57');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('696','4','update','sections','Ubah sambutan','::1','2026-09-10 11:51:17');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('697','4','update','sections','Ubah sambutan','::1','2026-09-10 11:52:01');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('698','4','update','sections','Ubah sambutan','::1','2026-09-10 11:52:21');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('699','4','update','sections','Ubah sambutan','::1','2026-09-10 11:53:19');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('700','4','update','sections','Ubah sambutan','::1','2026-09-10 11:59:32');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('701','4','update','sections','Ubah sambutan','::1','2026-09-10 11:59:45');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('702','4','update','sections','Ubah sambutan','::1','2026-09-10 12:00:03');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('703','4','update','sections','Ubah sambutan','::1','2026-09-10 12:03:45');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('704','4','update','sections','Ubah cta','::1','2026-09-10 12:06:56');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('705','4','update','sections','Ubah cta','::1','2026-09-10 12:12:19');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('706','4','update','sections','Ubah cta','::1','2026-09-10 12:17:56');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('707','4','update','sections','Ubah cta','::1','2026-09-10 12:18:20');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('708','4','update','sections','Ubah cta','::1','2026-09-10 12:18:37');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('709','4','update','sections','Ubah cta','::1','2026-09-10 12:37:34');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('710','4','update','sections','Ubah cta','::1','2026-09-10 12:38:47');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('711','4','update','sections','Ubah cta','::1','2026-09-10 12:43:25');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('712','4','update','sections','Ubah cta','::1','2026-09-10 12:43:52');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('713','4','update','sections','Ubah cta','::1','2026-09-10 12:44:14');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('714','4','update','sections','Ubah cta','::1','2026-09-10 12:44:38');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('715','4','update','sections','Ubah cta','::1','2026-09-10 12:48:42');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('716','4','update','sections','Ubah statistik','::1','2026-09-10 12:54:47');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('717','4','update','sections','Ubah statistik','::1','2026-09-10 12:55:44');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('718','4','update','sections','Ubah statistik','::1','2026-09-10 12:56:32');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('719','4','update','sections','Ubah guru-1788568332','::1','2026-09-10 12:57:31');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('720','4','update','sections','Ubah berita','::1','2026-09-10 12:58:03');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('721','4','update','sections','Ubah pengumuman','::1','2026-09-10 13:02:15');
INSERT INTO `activity_logs` (`id`,`user_id`,`action`,`module`,`description`,`ip`,`created_at`) VALUES ('722','4','update','sections','Ubah pengumuman','::1','2026-09-10 13:03:02');

DROP TABLE IF EXISTS `agenda`;
CREATE TABLE `agenda` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
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
INSERT INTO `agenda` (`id`,`title`,`event_date`,`end_date`,`start_time`,`end_time`,`location`,`description`,`status`,`created_at`,`updated_at`) VALUES ('1','Rapat Orang Tua Siswa','2026-09-08',NULL,'09:00',NULL,'Aula Sekolah','Sosialisasi program semester baru.','published','2026-09-05 06:20:17','2026-09-05 06:20:17');
INSERT INTO `agenda` (`id`,`title`,`event_date`,`end_date`,`start_time`,`end_time`,`location`,`description`,`status`,`created_at`,`updated_at`) VALUES ('2','Ujian Tengah Semester Gasal','2026-09-28','2026-10-06','07:30','11:00','Ruang Kelas','UTS Semester Gasal','published','2026-09-05 06:20:17','2026-09-07 11:12:01');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `announcements` (`id`,`title`,`content`,`attachment`,`status`,`published_at`,`author_id`,`created_at`,`updated_at`) VALUES ('3','Jadwal Libur Semester','Libur semester dimulai tanggal 20 Desember. Masuk kembali 3 Januari.',NULL,'published','2026-09-05 06:22:10',NULL,'2026-09-05 06:22:10','2026-09-05 06:22:10');
INSERT INTO `announcements` (`id`,`title`,`content`,`attachment`,`status`,`published_at`,`author_id`,`created_at`,`updated_at`) VALUES ('4','Pembayaran SPP','Pembayaran SPP tiap bulan berjalan paling lambat tanggal 10.','','published','2026-09-05 06:22:00',NULL,'2026-09-05 06:22:10','2026-09-07 11:06:00');
INSERT INTO `announcements` (`id`,`title`,`content`,`attachment`,`status`,`published_at`,`author_id`,`created_at`,`updated_at`) VALUES ('5','Pelaksanaan Asesmen Sumatif Tengah Semster','<p>Pelaksanaan Asesmen Sumatif Tengah Semester Gasal pada tanggal 28 September 2026 - 6 Oktober 2026. Para siswa dihimbau belajarnya ditingkatkan.</p>','','published','2026-09-09 07:54:18','4','2026-09-09 07:54:18','2026-09-09 07:54:18');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grid_style` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cards-3',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `categories` (`id`,`name`,`slug`,`description`,`grid_style`,`created_at`,`updated_at`) VALUES ('1','Berita Madrasah','berita-madrasah','','cards-4','2026-09-05 06:06:26','2026-09-07 10:08:27');
INSERT INTO `categories` (`id`,`name`,`slug`,`description`,`grid_style`,`created_at`,`updated_at`) VALUES ('2','Kegiatan Madrasah','kegiatan-madrasah','','magazine','2026-09-05 06:06:26','2026-09-07 10:08:01');
INSERT INTO `categories` (`id`,`name`,`slug`,`description`,`grid_style`,`created_at`,`updated_at`) VALUES ('3','Berita Pendidikan','berita-pendidikan','','cards-4','2026-09-05 06:06:26','2026-09-07 10:07:39');
INSERT INTO `categories` (`id`,`name`,`slug`,`description`,`grid_style`,`created_at`,`updated_at`) VALUES ('9','Artikel','artikel','','overlay','2026-09-07 10:21:54','2026-09-10 11:19:35');

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `subject` varchar(190) NOT NULL DEFAULT '',
  `message` mediumtext NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_read_created` (`is_read`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
INSERT INTO `contact_messages` (`id`,`name`,`email`,`subject`,`message`,`is_read`,`created_at`) VALUES ('2','Nur Huda','ibnuhasan3@gmail.com','test','test kirim pesan','1','2026-09-10 13:48:57');
INSERT INTO `contact_messages` (`id`,`name`,`email`,`subject`,`message`,`is_read`,`created_at`) VALUES ('3','Nur Huda','ibnuhasan3@gmail.com','test','test lagi untuk uji coba','1','2026-09-10 13:53:49');

DROP TABLE IF EXISTS `extracurriculars`;
CREATE TABLE `extracurriculars` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `coach` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `day` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` time DEFAULT NULL,
  `schedule` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `extracurriculars` (`id`,`name`,`description`,`coach`,`day`,`time`,`schedule`,`image`,`is_active`,`sort_order`) VALUES ('2','Pencak Silat','Bela diri Pencak silat Pagar Nusa','Siti Rahma, S.Pd','Sabtu','15:30:00','Halaman sekolah',NULL,'1','0');
INSERT INTO `extracurriculars` (`id`,`name`,`description`,`coach`,`day`,`time`,`schedule`,`image`,`is_active`,`sort_order`) VALUES ('3','Pramuka','Kepramukaan dan kepemimpinan.','Nur Huda, S.Pd.I','Jumat','14:00:00','Kelas dan Halaman',NULL,'1','0');
INSERT INTO `extracurriculars` (`id`,`name`,`description`,`coach`,`day`,`time`,`schedule`,`image`,`is_active`,`sort_order`) VALUES ('4','Futsal','Olahraga futsal.','Budi Santoso, S.Kom','Rabu','15:00:00','Lapangan',NULL,'1','0');
INSERT INTO `extracurriculars` (`id`,`name`,`description`,`coach`,`day`,`time`,`schedule`,`image`,`is_active`,`sort_order`) VALUES ('5','Rebana','Seni Islami Rebana','Budi Santoso, S.Kom','Senin','15:30:00','Aula Sekolah',NULL,'1','0');

DROP TABLE IF EXISTS `facilities`;
CREATE TABLE `facilities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cond` enum('baik','rusak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `facilities` (`id`,`name`,`qty`,`unit`,`cond`,`sort_order`,`created_at`) VALUES ('1','Tanah','2000','m2','baik','0','2026-09-08 19:45:01');
INSERT INTO `facilities` (`id`,`name`,`qty`,`unit`,`cond`,`sort_order`,`created_at`) VALUES ('2','Ruang Kelas','6','ruang','baik','0','2026-09-08 19:45:30');
INSERT INTO `facilities` (`id`,`name`,`qty`,`unit`,`cond`,`sort_order`,`created_at`) VALUES ('3','Ruang Kepala','1','ruang','baik','0','2026-09-08 19:45:49');
INSERT INTO `facilities` (`id`,`name`,`qty`,`unit`,`cond`,`sort_order`,`created_at`) VALUES ('4','Ruang Guru','1','ruang','baik','0','2026-09-08 19:46:02');
INSERT INTO `facilities` (`id`,`name`,`qty`,`unit`,`cond`,`sort_order`,`created_at`) VALUES ('5','Aula','1','ruang','baik','0','2026-09-08 19:50:40');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `galleries` (`id`,`title`,`slug`,`description`,`cover_image`,`status`,`created_at`,`updated_at`) VALUES ('1','Kemah','kemah','',NULL,'published','2026-09-05 15:32:45','2026-09-05 15:32:45');
INSERT INTO `galleries` (`id`,`title`,`slug`,`description`,`cover_image`,`status`,`created_at`,`updated_at`) VALUES ('2','Upacara','upacara','',NULL,'published','2026-09-05 15:33:37','2026-09-05 15:33:37');
INSERT INTO `galleries` (`id`,`title`,`slug`,`description`,`cover_image`,`status`,`created_at`,`updated_at`) VALUES ('3','Lainnya','lainnya','',NULL,'published','2026-09-10 08:37:53','2026-09-10 08:37:53');

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `gallery_images` (`id`,`gallery_id`,`filepath`,`caption`,`sort_order`,`created_at`) VALUES ('1','1','assets/uploads/20260905-153254-d75276d096df.jpeg','','0','2026-09-05 15:32:54');
INSERT INTO `gallery_images` (`id`,`gallery_id`,`filepath`,`caption`,`sort_order`,`created_at`) VALUES ('2','1','assets/uploads/20260905-153303-c7d5462ed403.jpeg','','0','2026-09-05 15:33:03');
INSERT INTO `gallery_images` (`id`,`gallery_id`,`filepath`,`caption`,`sort_order`,`created_at`) VALUES ('3','2','assets/uploads/20260905-153348-46df86d9dfc2.jpg','','0','2026-09-05 15:33:48');
INSERT INTO `gallery_images` (`id`,`gallery_id`,`filepath`,`caption`,`sort_order`,`created_at`) VALUES ('4','2','assets/uploads/20260905-153357-3a216e198d06.jpg','','0','2026-09-05 15:33:57');
INSERT INTO `gallery_images` (`id`,`gallery_id`,`filepath`,`caption`,`sort_order`,`created_at`) VALUES ('5','3','assets/uploads/20260910-083802-9d069419f15c.png','','0','2026-09-10 08:38:02');
INSERT INTO `gallery_images` (`id`,`gallery_id`,`filepath`,`caption`,`sort_order`,`created_at`) VALUES ('6','3','assets/uploads/20260910-083809-49a81687bb35.png','','0','2026-09-10 08:38:09');

DROP TABLE IF EXISTS `homepage_sections`;
CREATE TABLE `homepage_sections` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `section_key` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'home',
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
  `grid` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cards-3',
  `btn_target` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self',
  `btn2_target` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self',
  `buttons_json` mediumtext COLLATE utf8mb4_unicode_ci,
  `img_fx` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `img_size` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'full',
  `img_cw` int DEFAULT NULL,
  `img_ch` int DEFAULT NULL,
  `img_hover` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_key` (`section_key`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('1','hero','home','SMK Nusantara Jepara','Unggul, Berkarakter, Siap Kerja','1','2','hero','','','','','','','default','emerald-soft','lg','center','3','fade-up','cards-3','_self','_self',NULL,'kenburns','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('2','sambutan','home','Sambutan Kepala Madrasah','','1','3','sambutan','','','','','','','default','white','lg','center','3','zoom-out','right','_self','_self','{\"wtext\":\"quote\"}','none','rounded',NULL,NULL,'zoom');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('3','statistik','home','Statistik Sekolah','','1','4','statistik','','','','','','','glass','slate','lg','center','3','fade-up','cards-3','_self','_self',NULL,'none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('4','berita','home','Berita Terbaru','Kabar Terkini Sekolah','1','6','berita','','','','','','','glass','slate','lg','center','4','zoom-out','cards-4','_self','_self','{\"news_cat\":\"\",\"car\":{\"limit\":5,\"delay\":5000,\"mh\":200,\"dh\":270,\"auto\":0,\"dots\":0}}','none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('5','agenda','home','Agenda Kegiatan','','1','9','agenda','','','','','','','default','slate','lg','center','3','fade-up','cards-3','_self','_self',NULL,'none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('6','galeri','home','Galeri','','1','11','galeri','','','','','','','default','white','lg','center','4','fade-up','cards-3','_self','_self',NULL,'marquee','normal|lg',NULL,NULL,'bubble');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('7','cta','home','Ayo Bergabung','Penerimaan Murid Baru Madrasah','1','13','cta','','','Daftar Sekarang','#','','','default','slate','lg','center','3','fade-down','cards-3','_blank','_self','{\"0\":{\"text\":\"Daftar Sekarang\",\"url\":\"#\",\"target\":\"_blank\"},\"cta_bg\":\"20260910-121219-b78a8ab7e8ae.png\",\"cta_style\":\"gradient\",\"cta_btn\":\"emerald\",\"cta_btn_hover\":\"glow\",\"cta_align\":\"center\"}','none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('23','guru-1788568332','home','Pendidik dan Tenaga Kependidikan','','1','5','guru','','','','','','','glass','slate','lg','center','4','fade-up','cards-4','_self','_self',NULL,'none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('24','pengumuman','home','Pengumuman','Info resmi sekolah','1','10','pengumuman','','','','','','','emerald-soft','slate','lg','center','3','fade-up','cards-3','_self','_self',NULL,'none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('27','ekskul-1788608564','home','Ekstrakurikuler','','1','7','ekskul','','','','','','','card','emerald-soft','lg','center','4','fade-up','cards-4','_self','_self',NULL,'none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('28','prestasi-1788608613','home','Prestasi Siswa','','1','8','prestasi','','','','','','','gradient','white','lg','center','3','fade-up','cards-3','_self','_self',NULL,'none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('29','video-1788694973','home','Video Kegiatan','','1','12','video','https://www.youtube.com/watch?v=J8XyEjwDQ8k','','','','','','card','white','lg','left','3','fade-up','cards-3','_self','_self',NULL,'none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('37','berita-carousel','berita','Sorotan Berita','Kabar utama sekolah','1','1','carousel-berita',NULL,NULL,NULL,NULL,NULL,NULL,'card','white','lg','left','5','fade-up','cards-3','_self','_self',NULL,'none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('39','berita-kat-9','berita','Artikel','','1','5','kategori-berita','','','','','','','card','white','lg','left','4','fade-up','overlay','_self','_self','{\"news_cat\":\"9\",\"car\":{\"limit\":5,\"delay\":5000,\"mh\":200,\"dh\":270,\"auto\":0,\"dots\":0}}','none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('40','berita-kat-1','berita','Berita Madrasah','','1','2','kategori-berita','','','','','','','card','white','lg','left','4','fade-up','featured','_self','_self','{\"news_cat\":\"1\",\"car\":{\"limit\":5,\"delay\":5000,\"mh\":200,\"dh\":270,\"auto\":0,\"dots\":0}}','none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('41','berita-kat-3','berita','Berita Pendidikan','','1','3','kategori-berita','','','','','','','card','white','lg','left','4','fade-up','horizontal','_self','_self','{\"news_cat\":\"3\",\"car\":{\"limit\":5,\"delay\":5000,\"mh\":200,\"dh\":270,\"auto\":0,\"dots\":0}}','none','full',NULL,NULL,'none');
INSERT INTO `homepage_sections` (`id`,`section_key`,`page`,`title`,`subtitle`,`is_active`,`sort_order`,`type`,`content`,`image`,`btn_text`,`btn_url`,`btn2_text`,`btn2_url`,`style`,`bg`,`padding`,`align`,`items_limit`,`effect`,`grid`,`btn_target`,`btn2_target`,`buttons_json`,`img_fx`,`img_size`,`img_cw`,`img_ch`,`img_hover`) VALUES ('42','berita-kat-2','berita','Kegiatan Madrasah','','1','4','kategori-berita','','','','','','','card','white','lg','left','4','fade-up','timeline','_self','_self','{\"news_cat\":\"2\",\"car\":{\"limit\":5,\"delay\":5000,\"mh\":200,\"dh\":270,\"auto\":0,\"dots\":0}}','none','full',NULL,NULL,'none');

DROP TABLE IF EXISTS `learning_facilities`;
CREATE TABLE `learning_facilities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `good_qty` int DEFAULT '0',
  `mid_qty` int DEFAULT '0',
  `bad_qty` int DEFAULT '0',
  `total` int DEFAULT '0',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `learning_facilities` (`id`,`name`,`good_qty`,`mid_qty`,`bad_qty`,`total`,`sort_order`,`created_at`) VALUES ('1','Meja SIswa','200','0','0','200','0','2026-09-09 05:36:13');
INSERT INTO `learning_facilities` (`id`,`name`,`good_qty`,`mid_qty`,`bad_qty`,`total`,`sort_order`,`created_at`) VALUES ('2','Kursi Siswa','200','0','0','200','0','2026-09-09 06:38:54');

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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('1','20260905-153222-d1e59c4e1a69.jpeg','assets/uploads/20260905-153222-d1e59c4e1a69.jpeg','image/jpeg','jpeg','212825',NULL,'4','2026-09-05 15:32:22');
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('2','20260905-153315-144d23e6d15f.jpeg','assets/uploads/20260905-153315-144d23e6d15f.jpeg','image/jpeg','jpeg','195531',NULL,'4','2026-09-05 15:33:15');
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('3','20260905-155317-3f7826727455.jpg','assets/uploads/20260905-155317-3f7826727455.jpg','image/jpeg','jpg','52060',NULL,'4','2026-09-05 15:53:17');
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('4','20260907-095722-5aa39bf8ff0a.jpeg','assets/uploads/20260907-095722-5aa39bf8ff0a.jpeg','image/jpeg','jpeg','212825',NULL,'4','2026-09-07 09:57:22');
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('5','20260907-102017-15252d5d5526.jpeg','assets/uploads/20260907-102017-15252d5d5526.jpeg','image/jpeg','jpeg','30181',NULL,'4','2026-09-07 10:20:17');
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('6','20260907-102018-337d768fff5e.jpeg','assets/uploads/20260907-102018-337d768fff5e.jpeg','image/jpeg','jpeg','36733',NULL,'4','2026-09-07 10:20:18');
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('7','20260907-102018-cdcfa809ec19.jpeg','assets/uploads/20260907-102018-cdcfa809ec19.jpeg','image/jpeg','jpeg','26634',NULL,'4','2026-09-07 10:20:18');
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('8','20260907-155359-a0104d2abd77.jpg','assets/uploads/20260907-155359-a0104d2abd77.jpg','image/jpeg','jpg','81087',NULL,'4','2026-09-07 15:53:59');
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('9','20260909-102338-535c0f265914.jpg','assets/uploads/20260909-102338-535c0f265914.jpg','image/jpeg','jpg','81087',NULL,'4','2026-09-09 10:23:38');
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('10','20260910-080755-3c3462d31829.jpg','assets/uploads/20260910-080755-3c3462d31829.jpg','image/jpeg','jpg','36692',NULL,'4','2026-09-10 08:07:55');
INSERT INTO `media` (`id`,`filename`,`filepath`,`mime`,`extension`,`size_bytes`,`alt`,`uploaded_by`,`created_at`) VALUES ('20','20260910-080755-228819aed452.jpg','assets/uploads/20260910-080755-228819aed452.jpg','image/jpeg','jpg','52207',NULL,'4','2026-09-10 08:07:55');

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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('3','1',NULL,'Beranda','/','_self','1','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('4','1',NULL,'Profil','/profil','_self','2','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('5','1',NULL,'Berita','/berita','_self','6','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('6','1',NULL,'Galeri','/galeri','_self','7','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('7','1','25','Data Pendidik dan Tenaga Kependidikan','/guru','_self','1','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('8','1',NULL,'Kontak','/kontak','_self','10','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('9','1','4','Sejarah Madrasah','/sejarah-madrasah','_self','1','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('10','1','4','Visi, Misi dan Tujuan','/visi-misi','_self','2','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('11','2',NULL,'Profil','/profil','_self','1','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('12','2',NULL,'Berita','/berita','_self','2','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('13','2',NULL,'Galeri','/galeri','_self','3','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('14','2',NULL,'Kontak','/kontak','_self','5','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('15','1',NULL,'PPDB','#','_blank','8','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('16','1','4','Struktur Pimpinan','/struktur-organisasi','_self','3','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('17','1','4','Kurikulum','/kurikulum','_self','4','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('19','2',NULL,'Indeks Berita','/indeks-berita','_self','4','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('20','1',NULL,'Akademik','#','_self','4','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('21','1','20','Tata Tertib Siswa','/tata-tertib-siswa','_self','3','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('22','1','20','Tata Tertib Guru','/tata-tertib-guru','_self','4','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('23','1','4','Peta Lokasi','/peta-lokasi','_self','5','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('25','1',NULL,'Data Sekolah','#','_self','3','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('27','1','20','Ekstrakurikuler','/ekstrakurikuler','_self','1','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('28','1','25','Prestasi Siswa','/prestasi','_self','3','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('29','1',NULL,'Sarana Prasarana','#','_self','5','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('31','1','29','Sarana Pembelajaran','/sarana-pembelajaran','_self','2','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('32','1','20','Kegiatan Pembiasaan','/kegiatan-pembiasaan','_self','2','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('33','1','25','Data Siswa','/siswa','_self','2','1','link',NULL);
INSERT INTO `menu_items` (`id`,`menu_id`,`parent_id`,`label`,`url`,`target`,`sort_order`,`is_active`,`kind`,`mega_id`) VALUES ('34','1','29','Sarana Infrastruktur','/sarana','_self','1','1','link',NULL);

DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `pages` (`id`,`title`,`slug`,`content`,`featured_image`,`status`,`seo_title`,`seo_description`,`author_id`,`created_at`,`updated_at`,`deleted_at`) VALUES ('1','Sejarah Madrasah','sejarah-madrasah','<p>Sejarah ……</p>','','published','','','4','2026-09-05 14:32:44','2026-09-05 14:32:44',NULL);
INSERT INTO `pages` (`id`,`title`,`slug`,`content`,`featured_image`,`status`,`seo_title`,`seo_description`,`author_id`,`created_at`,`updated_at`,`deleted_at`) VALUES ('2','Tata Tertib Siswa','tata-tertib-siswa','<p>Tata Tertib Siswa</p>','','published','','','4','2026-09-06 15:11:07','2026-09-06 15:11:07',NULL);
INSERT INTO `pages` (`id`,`title`,`slug`,`content`,`featured_image`,`status`,`seo_title`,`seo_description`,`author_id`,`created_at`,`updated_at`,`deleted_at`) VALUES ('3','Tata Tertib Guru','tata-tertib-guru','<p>Tata Tertib Guru</p>','','published','','','4','2026-09-06 15:12:51','2026-09-06 15:12:51',NULL);
INSERT INTO `pages` (`id`,`title`,`slug`,`content`,`featured_image`,`status`,`seo_title`,`seo_description`,`author_id`,`created_at`,`updated_at`,`deleted_at`) VALUES ('4','Peta Lokasi','peta-lokasi','<p><iframe src=\"https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d31701.558736665538!2d110.657536!3d-6.684672!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2s!5e0!3m2!1sid!2sid!4v1788840045217!5m2!1sid!2sid\" width=\"600\" height=\"550\" style=\"border: 0;\" allowfullscreen=\"allowfullscreen\" loading=\"lazy\" referrerpolicy=\"strict-origin-when-cross-origin\"></iframe></p>','','published','','','4','2026-09-08 11:01:07','2026-09-08 11:09:20',NULL);
INSERT INTO `pages` (`id`,`title`,`slug`,`content`,`featured_image`,`status`,`seo_title`,`seo_description`,`author_id`,`created_at`,`updated_at`,`deleted_at`) VALUES ('5','Data Siswa','data-siswa','<p style=\"text-align: center;\"><strong>Data SIswa SMK Nusantara Jepara</strong></p>\r\n<p style=\"text-align: center;\"><strong>Tahun Ajaran 2026/2027</strong></p>\r\n<table style=\"border-collapse: collapse; width: 65.3266%; height: 184.375px;\" border=\"1\"><colgroup><col style=\"width: 10.1075%;\"><col style=\"width: 19.5699%;\"><col style=\"width: 16.129%;\"><col style=\"width: 16.5591%;\"><col style=\"width: 37.6344%;\"></colgroup>\r\n<tbody>\r\n<tr style=\"height: 36.875px;\">\r\n<td style=\"text-align: center;\">NO</td>\r\n<td style=\"text-align: center;\">KELAS</td>\r\n<td style=\"text-align: center;\">PUTRA</td>\r\n<td style=\"text-align: center;\">PUTRI</td>\r\n<td style=\"text-align: center;\">TOTAL</td>\r\n</tr>\r\n<tr style=\"height: 36.875px;\">\r\n<td style=\"text-align: center;\">1</td>\r\n<td style=\"text-align: center;\">10</td>\r\n<td style=\"text-align: center;\">50</td>\r\n<td style=\"text-align: center;\">50</td>\r\n<td style=\"text-align: center;\">100</td>\r\n</tr>\r\n<tr style=\"height: 36.875px;\">\r\n<td style=\"text-align: center;\">2</td>\r\n<td style=\"text-align: center;\">11</td>\r\n<td style=\"text-align: center;\">30</td>\r\n<td style=\"text-align: center;\">20</td>\r\n<td style=\"text-align: center;\">50</td>\r\n</tr>\r\n<tr style=\"height: 36.875px;\">\r\n<td style=\"text-align: center;\">3</td>\r\n<td style=\"text-align: center;\">12</td>\r\n<td style=\"text-align: center;\">55</td>\r\n<td style=\"text-align: center;\">40</td>\r\n<td style=\"text-align: center;\">95</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>','','published','','','4','2026-09-08 11:19:57','2026-09-08 19:20:42','2026-09-08 19:20:42');
INSERT INTO `pages` (`id`,`title`,`slug`,`content`,`featured_image`,`status`,`seo_title`,`seo_description`,`author_id`,`created_at`,`updated_at`,`deleted_at`) VALUES ('6','Sarana Infrastruktur','sarana-infrastruktur','<p style=\"text-align: center;\"><strong>Sarana Infrastruktur</strong></p>\r\n<p style=\"text-align: center;\"><strong>SMK Nusantara Jepara</strong></p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p>&nbsp;</p>','','published','','','4','2026-09-08 11:35:33','2026-09-08 11:35:33',NULL);
INSERT INTO `pages` (`id`,`title`,`slug`,`content`,`featured_image`,`status`,`seo_title`,`seo_description`,`author_id`,`created_at`,`updated_at`,`deleted_at`) VALUES ('7','Sarana Pembelajaran','sarana-pembelajaran','<p style=\"text-align: center;\"><strong>SARANA PEMBELAJARAN</strong></p>\r\n<p style=\"text-align: center;\"><strong>SMK NUSANTARA JEPARA</strong></p>','','published','','','4','2026-09-08 11:36:04','2026-09-08 13:37:38',NULL);
INSERT INTO `pages` (`id`,`title`,`slug`,`content`,`featured_image`,`status`,`seo_title`,`seo_description`,`author_id`,`created_at`,`updated_at`,`deleted_at`) VALUES ('8','Kegiatan Pembiasaan','kegiatan-pembiasaan','<p style=\"text-align: center;\"><strong>KEGIATAN PEMBIASAAN</strong></p>\r\n<p style=\"text-align: center;\"><strong>SMK NUSANTARA JEPARA</strong></p>\r\n<ol>\r\n<li>nnnnn</li>\r\n<li>mmmmm</li>\r\n<li>lllll</li>\r\n</ol>','','published','','','4','2026-09-08 11:39:38','2026-09-09 07:31:12',NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `posts` (`id`,`category_id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`status`,`author_id`,`views`,`published_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('1','1','PPDB Tahun Ajaran Baru Telah Dibuka','ppdb-tahun-ajaran-baru','Penerimaan peserta didik baru tahun ajaran baru resmi dibuka.','<p>Penerimaan Peserta Didik Baru (PPDB) tahun ajaran baru telah dibuka. Calon siswa dapat mendaftar melalui sekretariat sekolah atau kontak panitia.</p><ul><li>Gelombang 1: Juni</li><li>Gelombang 2: Juli</li></ul>',NULL,'published',NULL,'4','2026-09-05 06:20:17','2026-09-05 06:20:17','2026-09-06 16:00:32',NULL);
INSERT INTO `posts` (`id`,`category_id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`status`,`author_id`,`views`,`published_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('2','2','Siswa Raih Juara Olimpiade Sains','siswa-juara-olimpiade','Tim olimpiade sekolah meraih juara tingkat kabupaten.','<p>Tim olimpiade sains sekolah berhasil meraih juara 1 tingkat kabupaten. Prestasi ini hasil pembinaan intensif dan kerja keras siswa.</p>','','published',NULL,'3','2026-09-05 06:20:00','2026-09-05 06:20:17','2026-09-10 08:26:03',NULL);
INSERT INTO `posts` (`id`,`category_id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`status`,`author_id`,`views`,`published_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('3','9','Upacara Bendera sebagai Sarana Pembinaan Karakter Siswa','upacara-bendera-sebagai-sarana-pembinaan-karakter-siswa','Upacara rutin Senin dengan pembinaan karakter disiplin.','<p class=\"FirstParagraph\"><span lang=\"EN-US\">Upacara bendera merupakan salah satu kegiatan rutin yang memiliki peran penting dalam membentuk karakter peserta didik. Lebih dari sekadar kegiatan seremonial, upacara bendera menjadi momentum untuk menanamkan nilai-nilai kedisiplinan, tanggung jawab, nasionalisme, serta sikap saling menghormati dalam kehidupan sehari-hari.</span></p>\r\n<p class=\"MsoBodyText\"><span lang=\"EN-US\">Melalui pelaksanaan upacara yang tertib dan khidmat, siswa diajak untuk memahami pentingnya menghargai simbol-simbol negara sekaligus menumbuhkan rasa cinta terhadap tanah air. Sikap berdiri tegap, mengikuti rangkaian kegiatan dengan tertib, serta mendengarkan amanat pembina upacara merupakan bentuk sederhana dari pembiasaan karakter positif.</span></p>\r\n<h2><strong><span lang=\"EN-US\">Menanamkan Kedisiplinan</span></strong></h2>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: menanamkan-kedisiplinan;\"><span lang=\"EN-US\">Salah satu nilai utama yang dapat dibentuk melalui upacara bendera adalah kedisiplinan. Siswa dituntut hadir tepat waktu, mengenakan seragam sesuai ketentuan, mengikuti barisan dengan rapi, dan menaati seluruh tata tertib selama kegiatan berlangsung.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: menanamkan-kedisiplinan;\"><span lang=\"EN-US\">Kebiasaan tersebut diharapkan tidak hanya diterapkan ketika mengikuti upacara, tetapi juga terbawa dalam kegiatan pembelajaran dan kehidupan sehari-hari. Dengan demikian, kedisiplinan menjadi bagian dari budaya positif di lingkungan sekolah.</span></span></p>\r\n<h2><strong><span lang=\"EN-US\">Membangun Rasa Tanggung Jawab</span></strong></h2>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: membangun-rasa-tanggung-jawab;\"><span lang=\"EN-US\">Upacara bendera juga memberikan kesempatan kepada siswa untuk belajar bertanggung jawab. Petugas upacara, misalnya, harus mempersiapkan diri dan menjalankan tugas masing-masing dengan sebaik-baiknya.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: membangun-rasa-tanggung-jawab;\"><span lang=\"EN-US\">Pengalaman tersebut mengajarkan bahwa setiap tugas memiliki peran penting dan harus dilaksanakan dengan sungguh-sungguh. Nilai tanggung jawab inilah yang kemudian dapat diterapkan dalam kegiatan akademik, organisasi, maupun kehidupan bermasyarakat.</span></span></p>\r\n<h2><strong><span lang=\"EN-US\">Menumbuhkan Nasionalisme</span></strong></h2>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: menumbuhkan-nasionalisme;\"><span lang=\"EN-US\">Pengibaran Sang Merah Putih dan penghormatan terhadap bendera menjadi bagian penting dalam menumbuhkan rasa nasionalisme. Siswa diajak untuk mengenal dan menghargai perjuangan para pahlawan serta memahami bahwa generasi muda memiliki tanggung jawab untuk menjaga persatuan dan membangun bangsa.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: menumbuhkan-nasionalisme;\"><span lang=\"EN-US\">Pesan-pesan yang disampaikan dalam amanat pembina upacara juga dapat menjadi sarana untuk menguatkan semangat belajar, kepedulian terhadap sesama, serta kecintaan terhadap lingkungan sekolah dan bangsa Indonesia.</span></span></p>\r\n<h2><strong><span lang=\"EN-US\">Membentuk Sikap Saling Menghormati</span></strong></h2>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: membentuk-sikap-saling-menghormati;\"><span lang=\"EN-US\">Dalam pelaksanaan upacara, seluruh warga sekolah berkumpul dan mengikuti kegiatan secara bersama-sama. Kondisi ini menjadi kesempatan untuk menanamkan sikap saling menghormati, baik kepada guru, tenaga kependidikan, sesama siswa, maupun seluruh warga sekolah.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: membentuk-sikap-saling-menghormati;\"><span lang=\"EN-US\">Sikap menghargai orang lain merupakan salah satu fondasi penting dalam menciptakan lingkungan pendidikan yang aman, nyaman, dan harmonis.</span></span></p>\r\n<h2><strong><span lang=\"EN-US\">Upacara sebagai Pembiasaan Karakter Positif</span></strong></h2>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: X3d79eeaa2d01b13490416eeef41580b957c01a5;\"><span lang=\"EN-US\">Pembinaan karakter tidak cukup dilakukan melalui teori di dalam kelas. Nilai-nilai karakter perlu dibangun melalui keteladanan dan pembiasaan yang dilakukan secara konsisten. Dalam hal ini, upacara bendera menjadi salah satu kegiatan yang dapat mendukung proses tersebut.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: X3d79eeaa2d01b13490416eeef41580b957c01a5;\"><span lang=\"EN-US\">Dengan pelaksanaan yang teratur dan penuh makna, upacara bendera dapat menjadi sarana pendidikan karakter yang sederhana tetapi memiliki dampak positif. Setiap rangkaian kegiatan mengandung pembelajaran tentang disiplin, tanggung jawab, nasionalisme, kerja sama, kepemimpinan, dan penghormatan terhadap sesama.</span></span></p>\r\n<h2><strong><span lang=\"EN-US\">Mewujudkan Generasi Berkarakter</span></strong></h2>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: mewujudkan-generasi-berkarakter;\"><span lang=\"EN-US\">Melalui berbagai kegiatan pembiasaan, termasuk upacara bendera, sekolah berupaya membentuk peserta didik yang tidak hanya memiliki kemampuan akademik, tetapi juga memiliki karakter yang kuat. Generasi yang disiplin, bertanggung jawab, berintegritas, memiliki rasa nasionalisme, dan mampu menghargai orang lain merupakan bagian penting dari tujuan pendidikan.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: mewujudkan-generasi-berkarakter;\"><span lang=\"EN-US\">Oleh karena itu, marilah kita menjadikan upacara bendera bukan sekadar rutinitas mingguan, melainkan sebagai kesempatan untuk terus belajar dan memperkuat karakter. Dengan semangat kebersamaan dan keteladanan seluruh warga sekolah, diharapkan lahir generasi muda yang berprestasi, berakhlak, dan siap memberikan kontribusi positif bagi masyarakat, bangsa, dan negara.</span></span></p>','20260910-085326-836628fd47b9.png','published',NULL,'14','2026-09-05 06:20:00','2026-09-05 06:20:17','2026-09-10 08:55:09',NULL);
INSERT INTO `posts` (`id`,`category_id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`status`,`author_id`,`views`,`published_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('5','3','Pelaksanaan Olimpiade Madrasah 2026 Kian Kompetitif, Peserta Melonjak','pelaksanaan-olimpiade-madrasah-2026-kian-kompetitif-peserta-melonjak','','<p style=\"text-align: justify;\">Jakarta (ANTARA) - Kementerian Agama melaporkan pelaksanaan Olimpiade Madrasah Indonesia (OMI) 2026 makin kompetitif yang ditandai dengan lonjakan partisipasi peserta yakni 230.123 siswa tercatat mendaftar OMI 2026 atau naik 12,7 persen dibanding OMI 2025 dengan 204.222 pendaftar.</p>\r\n<p style=\"text-align: justify;\">&ldquo;Bertambahnya peserta berarti semakin banyak potensi dan talenta anak bangsa yang berhasil kita identifikasi dan berikan ruang untuk berkembang,&rdquo; ujar Dirjen Pendis Kemenah Amien Suyitno dalam keterangannya di Jakarta, Minggu.</p>\r\n<p style=\"text-align: justify;\">Dari jumlah tersebut, sebanyak 229.593 peserta dinyatakan lolos verifikasi. Jumlah peserta yang lolos verifikasi juga naik dibanding OMI 2025 dengan 202.117 peserta.</p>\r\n<p style=\"text-align: justify;\">Suyitno mengatakan OMI tidak semata-mata dipandang sebagai kompetisi untuk menentukan pemenang. Lebih dari itu, OMI menjadi bagian dari ekosistem pembinaan prestasi peserta didik yang perlu dikembangkan secara berkelanjutan.</p>\r\n<p style=\"text-align: justify;\">&ldquo;Kompetisi adalah salah satu pintu masuk. Yang lebih penting adalah bagaimana kita menemukan anak-anak yang memiliki potensi, kemudian memberikan pembinaan yang tepat agar potensi tersebut berkembang menjadi prestasi,&rdquo; kata dia.</p>\r\n<p style=\"text-align: justify;\">Salah satu perkembangan paling menonjol dalam OMI 2026 adalah meningkatnya partisipasi murid sekolah umum. Laporan pendaftar OMI 2026 mencatat 13.271 murid sekolah umum dari jenjang SD, SMP, dan SMA ikut berpartisipasi. Jumlah tersebut meningkat dari 9.612 peserta sekolah umum pada OMI 2025, atau bertambah 3.659 peserta (38,1 persen).</p>\r\n<p style=\"text-align: justify;\">Direktur Kurikulum, Sarana, Kelembagaan, dan Kesiswaan (KSKK) Madrasah Nyayu Khodijah mengatakan peningkatan tersebut menjadi indikator bahwa OMI semakin dikenal sebagai ajang kompetisi yang terbuka dan inklusif.</p>\r\n<p style=\"text-align: justify;\">&ldquo;OMI terus berkembang menjadi ruang kompetisi yang inklusif. Peserta tidak hanya berasal dari madrasah, tetapi juga dari sekolah umum. Ini menunjukkan bahwa semangat untuk berprestasi dan mengembangkan potensi tidak mengenal batas satuan pendidikan,&rdquo; ujar Nyayu Khodijah.</p>\r\n<p style=\"text-align: justify;\">Ia menambahkan keterlibatan peserta sekolah umum menjadi bagian penting dalam memperluas ekosistem kompetisi dan pembinaan prestasi peserta didik.</p>\r\n<p style=\"text-align: justify;\">Ketua panitia OMI 2026 Solla Taufiq mengatakan OMI 2026 juga mencatat jangkauan partisipasi murid yang sangat luas. Sebanyak 34 provinsi berpartisipasi dengan total peserta 230.123 orang.</p>\r\n<p style=\"text-align: justify;\">Data laporan juga menunjukkan keterlibatan lebih dari 33 ribu lembaga pendidikan. Tiga provinsi dengan jumlah peserta terbanyak adalah Jawa Timur (41.685), Jawa Barat (32.446), dan Jawa Tengah (30.518).</p>\r\n<p style=\"text-align: justify;\">Selain OMI Bidang Sains, Olimpiade juga membuka cabang Riset. Tahun ini, jumlah mencatat 9.130 pendaftar. Dari jumlah tersebut, sebanyak 8.100 peserta telah mengajukan pendaftaran, sementara 1.030 lainnya masih berstatus draft atau tidak menyempurnakan proses submit pendaftaran.</p>\r\n<p style=\"text-align: justify;\">&ldquo;Angka ini menunjukkan tingginya minat siswa madrasah untuk mengembangkan kemampuan riset dan menuangkan gagasan ilmiah melalui ajang kompetisi tingkat nasional,&rdquo; ujar.</p>\r\n<p style=\"text-align: justify;\">Berdasarkan jenjang pendidikan, kata Solla, Madrasah Aliyah (MA) menyumbang 4.862 pendaftar, sedangkan Madrasah Tsanawiyah (MTs) sebanyak 4.268 pendaftar.</p>\r\n<p style=\"text-align: justify;\">Adapun berdasarkan bidang riset, Integrasi Keislaman dan Keilmuan (Ekoteologi) menjadi bidang paling diminati dengan 3.553 pendaftar, diikuti Sustainable Development Goals(SDGs) sebanyak 3.384 pendaftar, dan Transformasi Digital untuk Pembangunan Nasional sebanyak 2.193 pendaftar.</p>\r\n<p style=\"text-align: justify;\">&nbsp;</p>','20260907-101218-c53059975fd7.jpg','published','4','2','2026-09-07 10:11:00','2026-09-07 10:12:18','2026-09-07 14:06:08',NULL);
INSERT INTO `posts` (`id`,`category_id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`status`,`author_id`,`views`,`published_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('6','3','MTsN 1 Kota Bekasi Diduga Pungli Siswa Baru, Komite Sekolah Buka Suara','mtsn-1-kota-bekasi-diduga-pungli-siswa-baru-komite-sekolah-buka-suara','','<p style=\"text-align: justify;\">Dugaan pungli ini ramai dibahas di media sosial. Dalam unggahan itu orang tua siswa mengadukan dimintai sumbangan sebesar Rp 2,5 juta dan iuran bulanan sebesar Rp 200 ribu.</p>\r\n<p style=\"text-align: justify;\">Permintaan itu disampaikan saat pertemuan orang tua siswa. Mereka juga diminta menandatangani surat pernyataan terkait pemberian sumbangan itu dan ditandatangani di atas meterai.</p>\r\n<p style=\"text-align: justify;\">Tudingan itu dibantah oleh Komite MTsN 1 Kota Bekasi. Sumbangan dihimpun berdasarkan Peraturan Menteri Agama (PMA) Nomor 16 Tahun 2020 tentang Komite Madrasah.</p>\r\n<p style=\"text-align: justify;\">Ketua Komite MTsN 1 Kota Bekasi, Wida Esakelana, mengatakan besaran sumbangan tersebut merupakan hasil musyawarah dengan orang tua siswa.</p>\r\n<p style=\"text-align: justify;\">\"Kami sudah melaksanakan kesepakatan bersama wali murid sesuai PMA Nomor 16 Tahun 2020. Nominal Rp 2,5 juta dan iuran bulanan Rp 200 ribu itu sifatnya sumbangan sukarela, tidak dipaksakan. Faktanya, yang membayar hanya sekitar 50 sampai 60 persen, bahkan ada yang tidak membayar dan tidak pernah diberi sanksi,\" kata Wida, Selasa (7/7).</p>\r\n<p style=\"text-align: justify;\">Menurut Wida, sumbangan Rp 2,5 juta hanya berlaku bagi siswa baru dan digunakan untuk pengadaan bangku dan meja, pemasangan CCTV, pengecatan ruang kelas, plafon, serta kegiatan OSIS.&nbsp;</p>\r\n<p style=\"text-align: justify;\">Adapun iuran bulanan dipakai membiayai honor guru honorer, guru tahfiz, petugas kebersihan, petugas keamanan, 23 pelatih ekstrakurikuler dari luar sekolah, biaya listrik ruang kelas ber-AC, aplikasi pembelajaran, hosting, dan kebutuhan operasional lainnya.</p>\r\n<p style=\"text-align: justify;\">Ia mengatakan madrasah negeri memiliki keterbatasan anggaran dibanding sekolah negeri di bawah pemerintah daerah.</p>\r\n<figure class=\"image align-center\" style=\"display: table; float: none; margin-left: auto; margin-right: auto;\"><img style=\"aspect-ratio: 602 / 453; display: block; float: none; max-width: 100%; height: auto;\" src=\"http://localhost/web_sekolah/assets/uploads/20260907-102017-15252d5d5526.jpeg\" alt=\"Ketua Komite MTsN 1 Kota Bekasi, Wida Esakelana. Foto: kumparan\" width=\"602\" height=\"453\"></figure>\r\n<p style=\"text-align: justify;\">\"Masyarakat menganggap semua sekolah negeri sama, padahal madrasah hanya mengandalkan anggaran DIPA dari Kementerian Agama. Karena itu kami masih membutuhkan partisipasi masyarakat untuk memenuhi kebutuhan yang belum terbiayai pemerintah,\" ujarnya.</p>\r\n<p style=\"text-align: justify;\">Wida juga memastikan dana tidak dikelola pihak sekolah. Seluruh pembayaran dilakukan melalui&nbsp;</p>\r\n<p style=\"text-align: justify;\">\"Sekolah tidak memegang rekening maupun uang komite. Semua dikelola komite,\" katanya.</p>\r\n<p style=\"text-align: justify;\">Hal yang sama juga disampaikan guru IPS MTsN 1 Kota Bekasi, Sukra. Ia menyebut keberadaan komite menjadi penopang sejumlah program yang belum dapat dibiayai pemerintah.&nbsp;</p>\r\n<p style=\"text-align: justify;\">Menurut Sukra, dana komite digunakan untuk menunjang kenyamanan belajar, termasuk perawatan ruang kelas ber-AC dan seluruh kegiatan ekstrakurikuler.</p>\r\n<figure class=\"image align-center\" style=\"display: table; float: none; margin-left: auto; margin-right: auto;\"><img style=\"aspect-ratio: 602 / 453; display: block; float: none; max-width: 100%; height: auto;\" src=\"http://localhost/web_sekolah/assets/uploads/20260907-102018-337d768fff5e.jpeg\" alt=\"Guru IPS MTsN 1 Kota Bekasi, Sukra. Foto: kumparan\" width=\"602\" height=\"453\"></figure>\r\n<p style=\"text-align: justify;\">\"Kalau dibandingkan SMP negeri, mereka mendapat dukungan anggaran dari pemerintah daerah dan pusat. Kami hanya mengandalkan anggaran dari Kementerian Agama. Karena itu komite sangat dibutuhkan untuk menunjang kegiatan belajar siswa,\" ujar Sukra.</p>\r\n<p style=\"text-align: justify;\">Ia menegaskan pihak sekolah tidak menentukan besaran sumbangan maupun mengelola dana komite.</p>\r\n<p style=\"text-align: justify;\">\"Semua diputuskan melalui musyawarah antara komite dan orang tua siswa. Sekolah tidak ikut campur dan tidak memegang dana tersebut,\" katanya.</p>\r\n<p style=\"text-align: justify;\"><strong>Orang Tua Siswa Kelas VIII Akui Ada Sumbangan</strong></p>\r\n<p style=\"text-align: justify;\">Permintaan uang sumbangan tersebut juga diakui oleh orang tua siswa kelas VIII, Mamik. Ia mengatakan sumbangan diminta saat anaknya baru pertama kali diterima di MTsN 1 Kota Bekasi.</p>\r\n<p style=\"text-align: justify;\">\"Kalau saya dulu malah lebih ramai. Kami sampai mengadu ke Ombudsman. Tetapi setelah anak saya sekolah di sini, saya melihat uang yang dihimpun melalui komite memang kembali untuk kebutuhan siswa,\" ujarnya.</p>\r\n<p style=\"text-align: justify;\">Menurut Mamik, saat anaknya masuk dua tahun lalu, dirinya dikenai kontribusi sekitar Rp 2,8 juta yang diperuntukkan bagi pengadaan meja, kursi, serta perbaikan fasilitas sekolah.</p>\r\n<p style=\"text-align: justify;\">\"Faktanya setiap siswa mendapatkan meja dan kursi baru. Ruang kelas dicat, dipasang AC, dan kondisinya jauh lebih nyaman. Untuk kegiatan ekstrakurikuler maupun mengikuti berbagai perlombaan juga tidak dipungut biaya lagi karena sudah ditanggung dari dana komite,\" katanya.</p>\r\n<p style=\"text-align: justify;\">Ia menambahkan, di masanya, pembayaran kontribusi tersebut tidak harus sekaligus. Orang tua diberi kesempatan mencicil sesuai kemampuan. Bahkan bagi keluarga yang benar-benar mengalami kesulitan ekonomi, menurutnya tersedia mekanisme pengajuan keringanan kepada komite sekolah.</p>\r\n<p style=\"text-align: justify;\">Untuk iuran bulanan, Mamik mengaku hingga kini masih membayar Rp 220 ribu setiap bulan sejak anaknya duduk di kelas VII. Selama itu pula, ia mengaku tidak pernah dimintai pungutan lain di luar ketentuan yang telah disepakati.</p>\r\n<p style=\"text-align: justify;\"><strong>Ortu Siswa Dipersilakan Mengadu ke DPRD</strong></p>\r\n<figure class=\"image align-center\" style=\"display: table; float: none; margin-left: auto; margin-right: auto;\"><img style=\"aspect-ratio: 602 / 460; display: block; float: none; max-width: 100%; height: auto;\" src=\"http://localhost/web_sekolah/assets/uploads/20260907-102018-cdcfa809ec19.jpeg\" alt=\"Wakil Ketua Komisi IV DPRD Kota Bekasi, Ahmadi. Foto: Dok. Istimewa\" width=\"602\" height=\"460\"></figure>\r\n<p style=\"text-align: justify;\">Wakil Ketua Komisi IV DPRD Kota Bekasi, Ahmadi. Foto: Dok. Istimewa</p>\r\n<p style=\"text-align: justify;\">Dugaan pungli ini turut disoroti oleh DPRD Kota Bekasi. Wakil Ketua Komisi IV DPRD Kota Bekasi, Ahmadi, menegaskan komite sekolah memang memiliki kewenangan sebagaimana diatur dalam Permendikbud Nomor 75 Tahun 2016, namun, pelaksanaannya tidak boleh menimbulkan kesan memaksa atau membebani orang tua siswa.</p>\r\n<p style=\"text-align: justify;\">\"Kalau masyarakat merasa keberatan atau ada dugaan pelanggaran aturan, silakan mengadu ke DPRD. Kami akan mempelajari setiap laporan, apakah masuk kategori pungutan liar atau memang sesuai ketentuan,\" kata Ahmadi kepada wartawan, Senin (6/7).</p>\r\n<p style=\"text-align: justify;\">Menurutnya, persoalan biaya pendidikan tidak boleh menjadi penghalang bagi anak untuk memperoleh hak belajar.</p>\r\n<p style=\"text-align: justify;\">\"Jangan sampai niat anak bersekolah justru terhambat karena persoalan biaya. Pendidikan harus tetap bisa diakses semua kalangan,\" tegasnya.</p>\r\n<p style=\"text-align: justify;\">Ahmadi juga mempertanyakan urgensi adanya iuran bulanan di sekolah negeri. Berdasarkan pemahamannya, operasional sekolah pada dasarnya telah ditopang melalui dana Bantuan Operasional Sekolah (BOS) dari pemerintah pusat serta BOS Daerah (Bosda).</p>\r\n<p style=\"text-align: justify;\">\"Kalau memang sudah ada BOS dan Bosda, tentu perlu dijelaskan secara terbuka peruntukan iuran tersebut agar tidak menimbulkan pertanyaan di masyarakat,\" ujarnya.</p>','20260907-102040-8894f9b34bc0.jpg','published','4','11','2026-09-07 10:18:00','2026-09-07 10:20:40','2026-09-10 07:23:13',NULL);
INSERT INTO `posts` (`id`,`category_id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`status`,`author_id`,`views`,`published_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('7','9','Pendidikan di Era Perubahan: Membangun Generasi yang Cerdas, Berkarakter, dan Adaptif','pendidikan-di-era-perubahan-membangun-generasi-yang-cerdas-berkarakter-dan-adaptif','','<p class=\"MsoNormal\"><strong>Pendidikan Bukan Sekadar Mengejar Nilai</strong></p>\r\n<p class=\"MsoNormal\">Pendidikan memiliki peran yang sangat penting dalam menentukan masa depan seorang anak. Melalui pendidikan, anak tidak hanya memperoleh pengetahuan, tetapi juga belajar mengenal dirinya, memahami lingkungan, membangun hubungan dengan orang lain, serta mempersiapkan diri menghadapi berbagai perubahan kehidupan.</p>\r\n<p class=\"MsoNormal\">Di tengah perkembangan teknologi dan perubahan sosial yang berlangsung begitu cepat, dunia pendidikan juga menghadapi tantangan yang semakin kompleks. Anak-anak saat ini tumbuh dalam lingkungan yang sangat berbeda dibandingkan generasi sebelumnya. Informasi dapat diperoleh dengan mudah melalui internet, sementara teknologi digital telah menjadi bagian dari kehidupan sehari-hari.</p>\r\n<p class=\"MsoNormal\">Dalam kondisi tersebut, sekolah tidak cukup hanya mengajarkan materi pelajaran. Sekolah perlu menjadi tempat bagi anak untuk <strong>berpikir, bertanya, mencoba, bekerja sama, berkreasi, dan membangun karakter positif</strong>.</p>\r\n<p class=\"MsoNormal\">&nbsp;</p>\r\n<p class=\"MsoNormal\"><strong>Guru Memiliki Peran Penting</strong></p>\r\n<p class=\"MsoNormal\"><span style=\"mso-no-proof: yes;\"><br><!--[endif]--></span></p>\r\n<p class=\"MsoNormal\"><span style=\"mso-no-proof: yes;\"><!-- [if gte vml 1]><v:shape\r\n id=\"Picture_x0020_33\" o:spid=\"_x0000_i1037\" type=\"#_x0000_t75\" alt=\"Image\"\r\n style=\'width:451.5pt;height:300pt;visibility:visible;mso-wrap-style:square\'>\r\n <v:imagedata src=\"file:///C:/Users/Administrator/AppData/Local/Temp/msohtmlclip1/01/clip_image003.jpg\"\r\n  o:title=\"Image\"/>\r\n</v:shape><![endif]--><!-- [if !vml]--><img class=\"align-center\" style=\"display: block; float: none; margin-left: auto; margin-right: auto;\" src=\"http://localhost/web_sekolah/assets/uploads/20260910-080755-3c3462d31829.jpg\" alt=\"Image\" width=\"602\" height=\"400\"><!--[endif]--></span></p>\r\n<p class=\"MsoNormal\">Guru merupakan salah satu bagian penting dalam proses pendidikan. Guru bukan hanya seseorang yang menyampaikan materi pelajaran, tetapi juga menjadi pembimbing dan teladan bagi peserta didik.</p>\r\n<p class=\"MsoNormal\">Cara guru berbicara, bersikap, menyelesaikan masalah, menghargai perbedaan, dan memperlakukan peserta didik dapat menjadi pembelajaran yang jauh lebih bermakna daripada sekadar teori di dalam buku.</p>\r\n<p class=\"MsoNormal\">Pembelajaran yang baik juga perlu memberikan ruang kepada siswa untuk aktif. Anak tidak hanya mendengarkan penjelasan guru, tetapi diberikan kesempatan untuk berdiskusi, melakukan percobaan, menyampaikan pendapat, memecahkan masalah, dan menghasilkan sebuah karya.</p>\r\n<p class=\"MsoNormal\">Penelitian dan kajian pendidikan juga menunjukkan bahwa pengelolaan kelas merupakan salah satu faktor yang berkaitan dengan keberhasilan belajar siswa. Lingkungan belajar yang baik membantu peserta didik mengikuti pembelajaran secara lebih optimal. (<a title=\"PERAN GURU DALAM PENGELOLAAN KELAS TERHADAP HASIL BELAJAR SISWA SEKOLAH DASAR | Pendas : Jurnal Ilmiah Pendidikan Dasar\" href=\"https://journal.unpas.ac.id/index.php/pendas/article/view/4050?utm_source=chatgpt.com\">Journal Universitas Pasundan</a>)</p>\r\n<p class=\"MsoNormal\">&nbsp;</p>\r\n<p class=\"MsoNormal\"><strong>Pendidikan Karakter Tidak Kalah Penting</strong></p>\r\n<p class=\"MsoNormal\"><span style=\"mso-no-proof: yes;\"><!-- [if gte vml 1]><v:shape\r\n id=\"Picture_x0020_24\" o:spid=\"_x0000_i1028\" type=\"#_x0000_t75\" alt=\"Image\"\r\n style=\'width:451.5pt;height:282pt;visibility:visible;mso-wrap-style:square\'>\r\n <v:imagedata src=\"file:///C:/Users/Administrator/AppData/Local/Temp/msohtmlclip1/01/clip_image021.jpg\"\r\n  o:title=\"Image\"/>\r\n</v:shape><![endif]--><!-- [if !vml]--><img class=\"align-center\" style=\"display: block; float: none; margin-left: auto; margin-right: auto;\" src=\"http://localhost/web_sekolah/assets/uploads/20260910-080755-228819aed452.jpg\" alt=\"Image\" width=\"602\" height=\"376\" border=\"0\"><!--[endif]--></span></p>\r\n<p class=\"MsoNormal\">Keberhasilan pendidikan tidak seharusnya hanya diukur dari nilai ujian. Anak yang memperoleh nilai tinggi tetapi tidak memiliki kejujuran, tanggung jawab, kepedulian, dan kemampuan bekerja sama tentu belum dapat dikatakan memperoleh pendidikan secara utuh.</p>\r\n<p class=\"MsoNormal\">Karakter perlu dibangun melalui kebiasaan sehari-hari.</p>\r\n<p class=\"MsoNormal\">Datang tepat waktu, menjaga kebersihan, menghormati guru dan teman, mengerjakan tugas dengan jujur, berani mengakui kesalahan, membantu orang lain, serta menjaga lingkungan merupakan contoh sederhana pendidikan karakter yang dapat diterapkan di sekolah.</p>\r\n<p class=\"MsoNormal\">Nilai-nilai tersebut akan lebih mudah tertanam apabila dilakukan secara konsisten. Karena itu, pendidikan karakter bukan hanya tanggung jawab guru, tetapi membutuhkan kerja sama antara <strong>sekolah, keluarga, dan masyarakat</strong>.</p>\r\n<p class=\"MsoNormal\">&nbsp;</p>\r\n<p class=\"MsoNormal\"><strong>Belajar Harus Bermakna dan Menyenangkan</strong></p>\r\n<p class=\"MsoNormal\">Pembelajaran yang efektif tidak selalu harus berlangsung dengan metode ceramah. Anak-anak membutuhkan pengalaman belajar yang membuat mereka memahami mengapa suatu pengetahuan penting dan bagaimana pengetahuan tersebut dapat digunakan dalam kehidupan.</p>\r\n<p class=\"MsoNormal\">Pembelajaran dapat dilakukan melalui permainan edukatif, proyek, diskusi kelompok, eksperimen sederhana, kegiatan di lingkungan sekolah, maupun pemanfaatan teknologi secara bijak.</p>\r\n<p class=\"MsoNormal\">Pendekatan pembelajaran yang bermakna juga menjadi perhatian dalam perkembangan pendidikan saat ini. Kemendikdasmen pada 2026 menekankan pentingnya pembelajaran yang <strong>mindful, meaningful, dan joyful</strong>, yaitu pembelajaran yang dilakukan dengan kesadaran, memiliki makna, dan memberikan pengalaman belajar yang menyenangkan. (<a title=\"Masa Depan Pendidikan Ditentukan di Kelas Awal SD | Pusat Standar &amp; Kebijakan Pendidikan\" href=\"https://pskp.kemendikdasmen.go.id/gagasan/detail/masa-depan-pendidikan-ditentukan-di-kelas-awal-sd?utm_source=chatgpt.com\">Pusat Standar &amp; Kebijakan Pendidikan</a>)</p>\r\n<p class=\"MsoNormal\">Dengan demikian, sekolah diharapkan tidak menjadi tempat yang membuat anak takut belajar. Sebaliknya, sekolah harus menjadi tempat yang membuat anak memiliki rasa ingin tahu dan berani mencoba.</p>\r\n<p class=\"MsoNormal\"><strong>Teknologi Harus Menjadi Sarana, Bukan Tujuan</strong></p>\r\n<p class=\"MsoNormal\">Perkembangan teknologi memberikan peluang besar bagi dunia pendidikan. Guru dapat memanfaatkan berbagai sumber digital untuk memperkaya pembelajaran. Siswa juga dapat memperoleh informasi dari berbagai sumber dalam waktu yang sangat cepat.</p>\r\n<p class=\"MsoNormal\">Namun, kemudahan tersebut harus diimbangi dengan kemampuan memilih dan menyaring informasi.</p>\r\n<p class=\"MsoNormal\">Anak perlu dibimbing agar tidak sekadar menjadi pengguna teknologi, tetapi mampu menggunakan teknologi secara <strong>cerdas, aman, kreatif, dan bertanggung jawab</strong>.</p>\r\n<p class=\"MsoNormal\">Penggunaan teknologi juga tidak boleh menghilangkan interaksi sosial. Berdiskusi dengan teman, membaca buku, melakukan kegiatan bersama, bermain, berolahraga, serta berinteraksi langsung dengan guru dan keluarga tetap memiliki peran penting dalam perkembangan anak.</p>\r\n<p class=\"MsoNormal\"><strong>Pendidikan Adalah Tanggung Jawab Bersama</strong></p>\r\n<p class=\"MsoNormal\">Pendidikan tidak dapat dibebankan hanya kepada sekolah. Waktu anak berada di sekolah terbatas, sedangkan sebagian besar kehidupannya berlangsung di rumah dan lingkungan masyarakat.</p>\r\n<p class=\"MsoNormal\">Karena itu, keberhasilan pendidikan membutuhkan hubungan yang baik antara sekolah dan orang tua.</p>\r\n<p class=\"MsoNormal\">Orang tua dapat mendukung pendidikan anak dengan membangun kebiasaan membaca, mendampingi belajar, membatasi penggunaan gawai secara sehat, memberikan teladan yang baik, serta memberikan apresiasi terhadap proses dan usaha anak.</p>\r\n<p class=\"MsoNormal\">Sementara itu, sekolah perlu menciptakan lingkungan yang aman, nyaman, disiplin, inklusif, dan mendorong setiap anak untuk berkembang sesuai potensinya.</p>\r\n<p class=\"MsoNormal\"><strong>Membentuk Generasi untuk Masa Depan</strong></p>\r\n<p class=\"MsoNormal\">Pendidikan dasar memiliki posisi yang sangat penting karena menjadi salah satu fondasi perkembangan anak. Kajian Kemendikdasmen menekankan bahwa penguatan kemampuan dasar, khususnya literasi dan numerasi, sangat penting bagi perjalanan pendidikan anak selanjutnya. (<a title=\"Masa Depan Pendidikan Ditentukan di Kelas Awal SD | Pusat Standar &amp; Kebijakan Pendidikan\" href=\"https://pskp.kemendikdasmen.go.id/gagasan/detail/masa-depan-pendidikan-ditentukan-di-kelas-awal-sd?utm_source=chatgpt.com\">Pusat Standar &amp; Kebijakan Pendidikan</a>)</p>\r\n<p class=\"MsoNormal\">Namun, fondasi tersebut tidak hanya berupa kemampuan membaca, menulis, dan berhitung. Anak juga perlu memiliki rasa ingin tahu, kemampuan berpikir kritis, kreativitas, komunikasi, kerja sama, kemandirian, serta karakter yang baik.</p>\r\n<p class=\"MsoNormal\">Pada akhirnya, pendidikan bukan sekadar tentang <strong>berapa nilai yang diperoleh seorang anak</strong>, tetapi tentang <strong>menjadi pribadi seperti apa anak tersebut setelah menjalani proses pendidikan</strong>.</p>\r\n<p class=\"MsoNormal\">Sekolah yang baik bukan hanya menghasilkan siswa yang mampu menjawab soal, tetapi juga mampu menghadapi kehidupan.</p>\r\n<p class=\"MsoNormal\"><strong>Penutup</strong></p>\r\n<p class=\"MsoNormal\">Pendidikan adalah investasi jangka panjang. Hasilnya mungkin tidak selalu terlihat hari ini, tetapi akan menentukan wajah masyarakat di masa depan.</p>\r\n<p class=\"MsoNormal\">Karena itu, mari membangun pendidikan yang tidak hanya mencerdaskan pikiran, tetapi juga membentuk karakter, menumbuhkan kepedulian, mengembangkan kreativitas, dan mempersiapkan anak menghadapi perubahan zaman.</p>\r\n<p class=\"MsoNormal\"><strong>Anak-anak hari ini adalah generasi yang akan menentukan masa depan bangsa. Memberikan pendidikan terbaik kepada mereka berarti sedang mempersiapkan masa depan yang lebih baik bagi Indonesia.</strong></p>\r\n<div class=\"MsoNormal\" style=\"text-align: center;\" align=\"center\"><hr align=\"center\" size=\"2\" width=\"100%\"></div>\r\n<p class=\"MsoNormal\">&nbsp;</p>\r\n<p class=\"MsoNormal\"><strong>Sumber rujukan:</strong></p>\r\n<p class=\"MsoNormal\">Artikel ini disusun dengan mengacu pada beberapa publikasi pendidikan, termasuk kajian Kemendikdasmen mengenai penguatan pendidikan dasar dan pembelajaran mendalam serta kajian tentang pengelolaan kelas dan kualitas pembelajaran. (<a title=\"Masa Depan Pendidikan Ditentukan di Kelas Awal SD | Pusat Standar &amp; Kebijakan Pendidikan\" href=\"https://pskp.kemendikdasmen.go.id/gagasan/detail/masa-depan-pendidikan-ditentukan-di-kelas-awal-sd?utm_source=chatgpt.com\">Pusat Standar &amp; Kebijakan Pendidikan</a>)</p>','20260910-080917-b74de26f8b45.png','published','4','7','2026-09-10 08:06:00','2026-09-10 08:09:17','2026-09-10 08:21:30',NULL);
INSERT INTO `posts` (`id`,`category_id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`status`,`author_id`,`views`,`published_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('8','1','OMI 2026 Jepara: Ajang Mengasah Talenta dan Semangat Berprestasi Siswa Madrasah','omi-2026-jepara-ajang-mengasah-talenta-dan-semangat-berprestasi-siswa-madrasah','','<p class=\"FirstParagraph\"><strong><span lang=\"EN-US\">Jepara, 10 September 2026</span></strong><span lang=\"EN-US\"> &mdash; Olimpiade Madrasah Indonesia (OMI) Tahun 2026 tingkat Kabupaten Jepara telah berlangsung pada 7&ndash;9 September 2026. Kegiatan ini menjadi salah satu wadah bagi peserta didik untuk mengembangkan kemampuan akademik, mengasah talenta, serta membangun semangat berkompetisi secara sehat.</span></p>\r\n<p class=\"MsoBodyText\"><span lang=\"EN-US\">OMI 2026 tingkat Kabupaten Jepara diikuti oleh <strong>1.327 peserta</strong> dari berbagai jenjang pendidikan, mulai dari MI/SD, MTs/SMP, hingga MA/SMA. Pelaksanaan kompetisi tersebar di lima titik lokasi, yaitu MAN 1 Jepara, MAN 2 Jepara, MTs Darul Ulum Purwogondo, MA Matholibul Huda Mlonggo, dan MTs Safinatul Huda Karimunjawa.</span></p>\r\n<h2><strong><span lang=\"EN-US\">Mengembangkan Talenta Peserta Didik</span></strong></h2>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: mengembangkan-talenta-peserta-didik;\"><span lang=\"EN-US\">OMI bukan sekadar kompetisi untuk menentukan siapa yang menjadi juara. Ajang ini menjadi bagian dari upaya pengembangan talenta peserta didik madrasah agar mereka memiliki kesempatan untuk menunjukkan kemampuan terbaiknya.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: mengembangkan-talenta-peserta-didik;\"><span lang=\"EN-US\">Pada OMI 2026, Kementerian Agama mengusung tema <strong>&ldquo;Islam, Sains, dan Ekoteologi: Menumbuhkan Talenta serta Menggerakkan Inovasi Madrasah untuk Indonesia Maju.&rdquo;</strong> Tema tersebut mencerminkan upaya untuk memadukan kemampuan akademik dengan nilai keislaman, inovasi, karakter, serta kepedulian terhadap lingkungan.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: mengembangkan-talenta-peserta-didik;\"><span lang=\"EN-US\">Secara nasional, OMI 2026 diikuti lebih dari <strong>229 ribu peserta</strong> yang mengikuti kompetisi secara serentak di berbagai daerah Indonesia. Pelaksanaan dilakukan berbasis komputer atau Computer-Based Test (CBT) dengan ribuan titik lokasi.</span></span></p>\r\n<h2><strong><span lang=\"EN-US\">Pengalaman Berharga bagi Siswa</span></strong></h2>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: pengalaman-berharga-bagi-siswa;\"><span lang=\"EN-US\">Bagi peserta didik, mengikuti OMI bukan hanya tentang memperoleh medali atau menjadi juara. Proses mengikuti kompetisi memberikan pengalaman berharga dalam membangun keberanian, kedisiplinan, ketelitian, tanggung jawab, dan kemampuan menghadapi tantangan.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: pengalaman-berharga-bagi-siswa;\"><span lang=\"EN-US\">Setiap peserta telah melalui proses persiapan sebelum mengikuti kompetisi. Belajar lebih giat, berlatih mengerjakan soal, mengikuti simulasi, serta mendapatkan bimbingan dari guru menjadi bagian penting dalam mempersiapkan diri menghadapi OMI.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: pengalaman-berharga-bagi-siswa;\"><span lang=\"EN-US\">Pengalaman tersebut diharapkan dapat menjadi motivasi bagi siswa untuk terus belajar dan mengembangkan potensi yang dimiliki.</span></span></p>\r\n<h2><strong><span lang=\"EN-US\">Menumbuhkan Budaya Prestasi di Madrasah</span></strong></h2>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: menumbuhkan-budaya-prestasi-di-madrasah;\"><span lang=\"EN-US\">Keikutsertaan siswa dalam kompetisi akademik seperti OMI juga menjadi bagian dari upaya membangun budaya prestasi di lingkungan madrasah.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: menumbuhkan-budaya-prestasi-di-madrasah;\"><span lang=\"EN-US\">Prestasi tidak selalu dimulai dari sebuah kemenangan. Prestasi dapat dimulai dari keberanian seorang siswa untuk mencoba, kemauan untuk belajar, kesungguhan dalam berlatih, serta kemampuan untuk menerima hasil dengan sikap sportif.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: menumbuhkan-budaya-prestasi-di-madrasah;\"><span lang=\"EN-US\">Karena itu, siswa yang telah mengikuti OMI patut mendapatkan apresiasi atas usaha dan perjuangan mereka. Bagi yang berhasil meraih prestasi, pencapaian tersebut tentu menjadi kebanggaan. Sementara bagi yang belum berhasil, pengalaman mengikuti kompetisi dapat menjadi bekal untuk memperbaiki diri dan mempersiapkan diri menghadapi kesempatan berikutnya.</span></span></p>\r\n<h2><strong><span lang=\"EN-US\">Madrasah Terus Mendorong Siswa Berkembang</span></strong></h2>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: X6cd54e055fb3d67c26485f85605f546800ea5af;\"><span lang=\"EN-US\">OMI menjadi salah satu momentum penting untuk menunjukkan bahwa peserta didik madrasah memiliki potensi besar dalam bidang sains, akademik, riset, dan inovasi.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: X6cd54e055fb3d67c26485f85605f546800ea5af;\"><span lang=\"EN-US\">Melalui pembinaan yang berkelanjutan, dukungan guru, orang tua, serta lingkungan madrasah yang positif, potensi tersebut dapat terus dikembangkan sehingga siswa tidak hanya mampu berprestasi di tingkat Kabupaten Jepara, tetapi juga memiliki kesempatan untuk melangkah ke tingkat provinsi hingga nasional.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: X6cd54e055fb3d67c26485f85605f546800ea5af;\"><span lang=\"EN-US\">Pelaksanaan OMI 2026 tingkat Kabupaten/Kota sendiri merupakan bagian dari rangkaian kompetisi yang berjenjang. Untuk bidang sains, peserta yang berhasil melaju akan mengikuti tahapan berikutnya sesuai jadwal yang telah ditetapkan.</span></span></p>\r\n<h3><strong><span style=\"mso-bookmark: X6cd54e055fb3d67c26485f85605f546800ea5af;\"><span lang=\"EN-US\">Terus Belajar, Terus Berprestasi</span></span></strong></h3>\r\n<p class=\"FirstParagraph\"><span style=\"mso-bookmark: terus-belajar-terus-berprestasi;\"><span lang=\"EN-US\">OMI 2026 telah memberikan pengalaman dan pelajaran berharga bagi seluruh peserta. Menang atau belum menang bukanlah akhir dari perjalanan.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: terus-belajar-terus-berprestasi;\"><span lang=\"EN-US\">Yang terpenting adalah terus belajar, berani mencoba, menjaga sportivitas, dan tidak berhenti mengembangkan kemampuan.</span></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: terus-belajar-terus-berprestasi;\"><strong><span lang=\"EN-US\">Selamat kepada seluruh peserta OMI 2026. Teruslah belajar, berkarya, dan berprestasi. Dari madrasah, lahir generasi yang cerdas, berkarakter, inovatif, dan siap memberikan kontribusi bagi Indonesia.</span></strong></span></p>\r\n<p class=\"MsoBodyText\"><span style=\"mso-bookmark: terus-belajar-terus-berprestasi;\"><em><span lang=\"EN-US\">Semoga perjuangan dan pengalaman dalam OMI 2026 menjadi langkah awal menuju prestasi-prestasi berikutnya.</span></em></span></p>','20260910-082913-a327cbe04374.png','published','4','2','2026-09-10 08:28:00','2026-09-10 08:29:13','2026-09-10 08:31:26',NULL);

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
INSERT INTO `school_profile` (`id`,`principal_name`,`principal_title`,`principal_photo`,`principal_greeting`,`history`,`vision`,`mission`,`org_chart`,`total_students`,`total_teachers`,`total_extracurricular`,`years_established`,`updated_at`) VALUES ('1','Drs. H. Ahmad Suryana, M.Pd','Kepala Sekolah','20260908-093227-f27683f8b910.jpg','<p class=\"FirstParagraph\" style=\"text-align: justify;\"><strong><span lang=\"EN-US\">Assalamu&rsquo;alaikum warahmatullahi wabarakatuh</span></strong></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span lang=\"EN-US\">Selamat datang di website resmi&nbsp;<strong>MI Sultan Fattah Sukosono</strong>.</span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span lang=\"EN-US\">Website ini kami hadirkan sebagai salah satu sarana untuk memberikan informasi, membangun komunikasi, serta memperkenalkan berbagai kegiatan dan perkembangan madrasah kepada seluruh masyarakat. Melalui website ini, kami berharap orang tua, peserta didik, alumni, dan masyarakat dapat memperoleh informasi madrasah secara mudah, terbuka, dan aktual.</span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span lang=\"EN-US\">Kami menyadari bahwa kemajuan madrasah tidak dapat dibangun oleh satu pihak saja. Diperlukan kerja sama dan sinergi antara&nbsp;<strong>guru, tenaga kependidikan, peserta didik, orang tua, yayasan, alumni, dan masyarakat</strong>.&nbsp;</span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span lang=\"EN-US\">Kami juga membuka diri terhadap kritik, saran, dan masukan yang membangun. Setiap masukan merupakan bagian penting dalam proses evaluasi dan perbaikan madrasah agar dapat memberikan yang terbaik bagi peserta didik.</span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span lang=\"EN-US\">Akhirnya, kami mengucapkan terima kasih kepada seluruh pihak yang telah memberikan dukungan dan kepercayaan kepada MI Sultan Fattah Sukosono. Semoga setiap ikhtiar yang kita lakukan dalam mendidik dan membimbing generasi penerus bangsa mendapatkan ridha dan keberkahan dari Allah SWT.</span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><strong><span lang=\"EN-US\">Wassalamu&rsquo;alaikum warahmatullahi wabarakatuh.</span></strong></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><strong><span lang=\"EN-US\">Kepala Madrasah</span></strong><span lang=\"EN-US\"><br><strong>MI Sultan Fattah Sukosono</strong></span></p>','','Menjadi sekolah unggul berstandar nasional tahun 2030.','1. Menyelenggarakan pembelajaran berkualitas.\r\n2. Membentuk karakter disiplin dan religius.\r\n3. Menjalin kemitraan industri.',NULL,'520','38','12','25','2026-09-10 11:40:30');
INSERT INTO `school_profile` (`id`,`principal_name`,`principal_title`,`principal_photo`,`principal_greeting`,`history`,`vision`,`mission`,`org_chart`,`total_students`,`total_teachers`,`total_extracurricular`,`years_established`,`updated_at`) VALUES ('2','Drs. H. Ahmad Suryana, M.Pd','Kepala Sekolah',NULL,'Selamat datang di SMK Nusantara. Kami berkomitmen mencetak lulusan unggul, berkarakter, dan siap kerja.',NULL,'Menjadi sekolah unggul berstandar nasional tahun 2030.','1. Menyelenggarakan pembelajaran berkualitas.\n2. Membentuk karakter disiplin dan religius.\n3. Menjalin kemitraan industri.',NULL,'520','38','12','25','2026-09-05 06:20:17');
INSERT INTO `school_profile` (`id`,`principal_name`,`principal_title`,`principal_photo`,`principal_greeting`,`history`,`vision`,`mission`,`org_chart`,`total_students`,`total_teachers`,`total_extracurricular`,`years_established`,`updated_at`) VALUES ('3','Drs. H. Ahmad Suryana, M.Pd','Kepala Sekolah',NULL,'Selamat datang di SMK Nusantara. Kami berkomitmen mencetak lulusan unggul, berkarakter, dan siap kerja.',NULL,'Menjadi sekolah unggul berstandar nasional tahun 2030.','1. Menyelenggarakan pembelajaran berkualitas.\n2. Membentuk karakter disiplin dan religius.\n3. Menjalin kemitraan industri.',NULL,'520','38','12','25','2026-09-05 06:22:10');

DROP TABLE IF EXISTS `section_slides`;
CREATE TABLE `section_slides` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int unsigned NOT NULL,
  `heading` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subheading` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta2_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cta2_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sec` (`section_id`,`is_active`,`sort_order`),
  CONSTRAINT `fk_slide_sec` FOREIGN KEY (`section_id`) REFERENCES `homepage_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `section_slides` (`id`,`section_id`,`heading`,`subheading`,`image`,`cta_text`,`cta_url`,`cta2_text`,`cta2_url`,`sort_order`,`is_active`,`created_at`) VALUES ('3','29','Kereta tebu blusukan melewati Jembatan Darurat jalur Rel Portable‼️','','https://www.youtube.com/embed/J8XyEjwDQ8k',NULL,NULL,NULL,NULL,'1','1','2026-09-07 06:22:48');
INSERT INTO `section_slides` (`id`,`section_id`,`heading`,`subheading`,`image`,`cta_text`,`cta_url`,`cta2_text`,`cta2_url`,`sort_order`,`is_active`,`created_at`) VALUES ('4','29','KOTA KECIL YANG TERLUPAKAN‼️Padahal Jadi Kota Bisnis & Sentra Ekonomi','','https://www.youtube.com/embed/p9Xt9xpVQLs',NULL,NULL,NULL,NULL,'2','1','2026-09-07 07:44:37');

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
) ENGINE=InnoDB AUTO_INCREMENT=521 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('1','school_name','SMK Nusantara Jepara','2026-09-07 19:06:56');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('2','tagline','Unggul, Berkarakter, Siap Kerja','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('3','address','Jl. Pendidikan No. 123, Jakarta','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('4','phone','(021) 1234567','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('5','email','info@smknusantara.sch.id','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('6','footer_text','SMK Nusantara Jepara','2026-09-07 19:06:39');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('7','powered_by','Powered by SchoolCMS','2026-09-05 06:06:26');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('8','homepage_title','SMK Nusantara Jepara | Unggul dan Berkarakter','2026-09-07 19:07:24');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('26','news_empty_text','Belum ada berita pada kategori ini.','2026-09-06 09:58:44');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('27','share_heading','Bagikan berita ini :','2026-09-10 07:23:07');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('28','share_description','Sebarkan informasi kepada keluarga dan teman.','2026-09-06 09:58:44');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('29','news_carousel_mobile_height','200','2026-09-06 09:58:44');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('30','news_carousel_desktop_height','470','2026-09-06 10:35:11');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('31','news_carousel_speed','5000','2026-09-06 09:58:44');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('32','news_carousel_limit','5','2026-09-06 09:58:44');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('33','news_carousel_style','rounded','2026-09-06 09:58:44');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('178','facebook','#','2026-09-06 12:24:31');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('179','instagram','#','2026-09-06 12:24:31');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('180','youtube','#','2026-09-06 12:24:31');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('181','tiktok','#','2026-09-06 12:24:31');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('182','service_days','Senin - Jumat','2026-09-06 10:42:34');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('183','service_open','07:00','2026-09-06 10:42:34');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('184','service_close','15:30','2026-09-06 10:42:34');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('185','meta_title','','2026-09-06 10:42:34');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('186','meta_description','','2026-09-06 10:42:34');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('187','meta_keywords','','2026-09-06 10:42:34');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('188','og_image','','2026-09-06 10:42:34');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('189','canonical_url','','2026-09-06 10:42:34');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('190','struktur_title','Struktur Pimpinan','2026-09-08 16:00:20');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('191','struktur_desc','','2026-09-06 11:17:44');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('192','struktur_show','1','2026-09-06 11:17:44');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('193','kurikulum_title','Kurikulum Sekolah','2026-09-07 12:10:49');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('194','kurikulum_content','<p class=\"FirstParagraph\" style=\"text-align: justify;\"><span lang=\"EN-US\">Sekolah kami menggunakan <strong>Kurikulum Merdeka</strong> sebagai pedoman dalam penyelenggaraan pendidikan. Kurikulum ini memberikan ruang yang lebih luas bagi sekolah, guru, dan peserta didik untuk menciptakan proses pembelajaran yang bermakna, kontekstual, dan sesuai dengan kebutuhan serta potensi setiap peserta didik.</span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span lang=\"EN-US\">Kurikulum Merdeka dirancang untuk mendorong pembelajaran yang berpusat pada peserta didik. Dalam prosesnya, guru berperan sebagai fasilitator yang mendampingi dan mengarahkan siswa agar dapat mengembangkan kemampuan, minat, bakat, kreativitas, serta karakter secara optimal.</span></p>\r\n<h2 style=\"text-align: justify;\"><strong><span lang=\"EN-US\">Pembelajaran yang Berpusat pada Peserta Didik</span></strong></h2>\r\n<p class=\"FirstParagraph\" style=\"text-align: justify;\"><span style=\"mso-bookmark: Xaf1d7e5f34939990d6dbf45be8508c5b940ccbf;\"><span lang=\"EN-US\">Melalui Kurikulum Merdeka, pembelajaran tidak hanya berorientasi pada pencapaian akademik, tetapi juga pada pengembangan kompetensi dan karakter. Peserta didik diberikan kesempatan untuk aktif bertanya, berdiskusi, mengeksplorasi berbagai sumber belajar, memecahkan masalah, serta mengembangkan ide dan kreativitas.</span></span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span style=\"mso-bookmark: Xaf1d7e5f34939990d6dbf45be8508c5b940ccbf;\"><span lang=\"EN-US\">Pembelajaran disesuaikan dengan karakteristik dan kebutuhan siswa sehingga diharapkan dapat menciptakan pengalaman belajar yang menyenangkan sekaligus mendorong siswa menjadi pembelajar sepanjang hayat.</span></span></p>\r\n<h2 style=\"text-align: justify;\"><strong><span lang=\"EN-US\">Penguatan Karakter dan Profil Pelajar Pancasila</span></strong></h2>\r\n<p class=\"FirstParagraph\" style=\"text-align: justify;\"><span style=\"mso-bookmark: X934648a3a7b89ae27157e99b0c2daf1a578f4b7;\"><span lang=\"EN-US\">Salah satu bagian penting dalam implementasi Kurikulum Merdeka adalah penguatan karakter peserta didik. Nilai-nilai Pancasila ditanamkan melalui kegiatan pembelajaran maupun berbagai aktivitas di lingkungan sekolah.</span></span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span style=\"mso-bookmark: X934648a3a7b89ae27157e99b0c2daf1a578f4b7;\"><span lang=\"EN-US\">Peserta didik diarahkan untuk menjadi pribadi yang beriman dan berakhlak mulia, mandiri, mampu bekerja sama, bernalar kritis, kreatif, serta memiliki kepedulian terhadap lingkungan dan masyarakat.</span></span></p>\r\n<h2 style=\"text-align: justify;\"><strong><span lang=\"EN-US\">Pembelajaran yang Kontekstual dan Bermakna</span></strong></h2>\r\n<p class=\"FirstParagraph\" style=\"text-align: justify;\"><span style=\"mso-bookmark: X5c864dba9c16f391af9941f9219fff2698ef5ac;\"><span lang=\"EN-US\">Kurikulum Merdeka memberikan kesempatan kepada sekolah untuk mengembangkan pembelajaran sesuai dengan kondisi dan lingkungan satuan pendidikan. Oleh karena itu, proses pembelajaran dapat dikaitkan dengan kehidupan nyata sehingga materi yang dipelajari menjadi lebih relevan dan mudah dipahami oleh peserta didik.</span></span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span style=\"mso-bookmark: X5c864dba9c16f391af9941f9219fff2698ef5ac;\"><span lang=\"EN-US\">Melalui pendekatan tersebut, siswa tidak hanya memahami konsep secara teori, tetapi juga belajar menerapkannya dalam kehidupan sehari-hari.</span></span></p>\r\n<h2 style=\"text-align: justify;\"><strong><span lang=\"EN-US\">Mendorong Potensi dan Kreativitas Siswa</span></strong></h2>\r\n<p class=\"FirstParagraph\" style=\"text-align: justify;\"><span style=\"mso-bookmark: mendorong-potensi-dan-kreativitas-siswa;\"><span lang=\"EN-US\">Setiap peserta didik memiliki keunikan, minat, dan potensi yang berbeda. Kurikulum Merdeka memberikan ruang bagi siswa untuk berkembang sesuai dengan kemampuan dan karakteristiknya.</span></span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span style=\"mso-bookmark: mendorong-potensi-dan-kreativitas-siswa;\"><span lang=\"EN-US\">Sekolah terus berupaya menghadirkan berbagai kegiatan pembelajaran dan pengembangan diri yang dapat membantu siswa menemukan potensi terbaiknya, baik dalam bidang akademik maupun nonakademik.</span></span></p>\r\n<h2 style=\"text-align: justify;\"><strong><span lang=\"EN-US\">Komitmen Sekolah</span></strong></h2>\r\n<p class=\"FirstParagraph\" style=\"text-align: justify;\"><span style=\"mso-bookmark: komitmen-sekolah;\"><span lang=\"EN-US\">Implementasi Kurikulum Merdeka merupakan bagian dari komitmen sekolah untuk menghadirkan pendidikan yang relevan dengan perkembangan zaman sekaligus tetap berlandaskan nilai-nilai karakter dan Pancasila.</span></span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span style=\"mso-bookmark: komitmen-sekolah;\"><span lang=\"EN-US\">Dengan dukungan seluruh warga sekolah, kami berupaya menciptakan lingkungan belajar yang aman, nyaman, inklusif, dan mendorong setiap peserta didik untuk tumbuh menjadi pribadi yang kompeten, berkarakter, mandiri, serta siap menghadapi tantangan masa depan.</span></span></p>\r\n<p class=\"MsoBodyText\" style=\"text-align: justify;\"><span style=\"mso-bookmark: komitmen-sekolah;\"><strong><span lang=\"EN-US\">Kurikulum Merdeka bukan sekadar perubahan kurikulum, tetapi sebuah upaya untuk memberikan ruang kepada setiap peserta didik agar dapat belajar, berkembang, dan menemukan potensi terbaik dalam dirinya.</span></strong></span></p>','2026-09-10 08:58:23');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('195','kurikulum_show','1','2026-09-06 11:18:07');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('222','vm_title','Visi, Misi dan Tujuan','2026-09-07 13:58:57');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('223','vm_show','1','2026-09-06 13:21:07');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('224','vision_content','<p>Menjadi sekolah unggul berstandar nasional&nbsp;</p>','2026-09-06 15:50:34');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('225','mission_content','<ol>\r\n<li>Menyelenggarakan pembelajaran berkualitas.&nbsp;</li>\r\n<li>Membentuk karakter disiplin dan religius.&nbsp;</li>\r\n<li>Menjalin kemitraan industri.</li>\r\n</ol>','2026-09-08 09:31:44');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('226','goals_content','<ol>\r\n<li>Menyelenggarakan pembelajaran berkualitas.&nbsp;</li>\r\n<li>Membentuk karakter disiplin dan religius.&nbsp;</li>\r\n<li>Menjalin kemitraan industri.</li>\r\n</ol>','2026-09-08 16:30:21');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('249','kurikulum_comps','<ol>\r\n<li>Intrakurikuler &mdash; pembelajaran tatap muka sesuai CP &amp; TP.</li>\r\n<li>Kokurikuler &mdash; penguatan dimensi profil lulusan lintas mapel.</li>\r\n<li>Ekstrakurikuler &mdash; minat, bakat, dan karakter di luar jam wajib.</li>\r\n</ol>','2026-09-08 09:28:51');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('250','kurikulum_help','Hubungi bagian kurikulum / TU pada jam layanan untuk jadwal, pembagian kelas, dan kegiatan projek.','2026-09-07 11:40:02');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('283','hero_align','center','2026-09-07 19:03:44');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('394','logo','20260908-093430-469633ae6315.png','2026-09-08 09:34:30');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('395','guru_title','Pendidik dan Tendik','2026-09-08 16:03:49');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('396','guru_desc','','2026-09-08 15:47:37');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('397','guru_show','1','2026-09-08 15:47:37');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('398','guru_cols','4','2026-09-08 15:47:37');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('399','ekskul_title','Ekstrakurikuler','2026-09-08 15:55:06');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('400','ekskul_desc','','2026-09-08 15:55:06');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('401','ekskul_show','1','2026-09-08 15:55:06');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('402','ekskul_cols','4','2026-09-08 15:55:06');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('403','prestasi_title','Prestasi Sekolah','2026-09-08 15:55:38');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('404','prestasi_desc','','2026-09-08 15:55:38');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('405','prestasi_show','1','2026-09-08 15:55:38');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('406','prestasi_cols','4','2026-09-08 15:55:38');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('410','struktur_cols','4','2026-09-08 16:00:20');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('420','siswa_title','Data Siswa','2026-09-08 19:21:15');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('421','siswa_desc','','2026-09-08 19:21:15');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('422','siswa_show','1','2026-09-08 19:21:15');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('423','siswa_cols','3','2026-09-08 19:21:15');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('424','learn_title','Sarana Belajar','2026-09-08 20:05:12');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('425','learn_desc','','2026-09-08 20:05:12');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('426','learn_show','1','2026-09-08 20:05:12');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('427','sarana_title','Sarana  Infrastruktur','2026-09-09 05:54:03');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('428','sarana_desc','','2026-09-09 05:54:03');
INSERT INTO `settings` (`id`,`key`,`value`,`updated_at`) VALUES ('429','sarana_show','1','2026-09-09 05:54:03');

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

DROP TABLE IF EXISTS `statistics`;
CREATE TABLE `statistics` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` int NOT NULL DEFAULT '0',
  `suffix` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `icon` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'fa-chart-simple',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `gradient` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT 'from-emerald-500 to-teal-600',
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active_order` (`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `statistics` (`id`,`name`,`value`,`suffix`,`icon`,`description`,`gradient`,`sort_order`,`is_active`,`created_at`,`updated_at`) VALUES ('1','Siswa Aktif','520','+','fa-users','Peserta didik tahun ini','from-emerald-500 to-teal-600','1','1','2026-09-06 14:11:31','2026-09-06 14:11:31');
INSERT INTO `statistics` (`id`,`name`,`value`,`suffix`,`icon`,`description`,`gradient`,`sort_order`,`is_active`,`created_at`,`updated_at`) VALUES ('2','Guru & Tendik','38','','fa-chalkboard-user','Pendidik profesional','from-sky-500 to-indigo-600','2','1','2026-09-06 14:11:31','2026-09-06 14:11:31');
INSERT INTO `statistics` (`id`,`name`,`value`,`suffix`,`icon`,`description`,`gradient`,`sort_order`,`is_active`,`created_at`,`updated_at`) VALUES ('3','Ekstrakurikuler','4','','fa-futbol','Minat & bakat','from-amber-500 to-orange-600','3','1','2026-09-06 14:11:31','2026-09-06 14:27:13');
INSERT INTO `statistics` (`id`,`name`,`value`,`suffix`,`icon`,`description`,`gradient`,`sort_order`,`is_active`,`created_at`,`updated_at`) VALUES ('4','Rombel','6','','fa-building-columns','Rombongan Belajar','from-violet-500 to-fuchsia-600','4','1','2026-09-06 14:11:31','2026-09-07 10:28:13');

DROP TABLE IF EXISTS `structures`;
CREATE TABLE `structures` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` int unsigned DEFAULT NULL,
  `position` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`sort_order`),
  KEY `fk_struct_teacher` (`teacher_id`),
  CONSTRAINT `fk_struct_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `structures` (`id`,`teacher_id`,`position`,`sort_order`,`created_at`,`updated_at`) VALUES ('1','4','Kepala Madrasah','0','2026-09-06 11:16:53','2026-09-06 11:17:33');
INSERT INTO `structures` (`id`,`teacher_id`,`position`,`sort_order`,`created_at`,`updated_at`) VALUES ('2','5','Waka Kurikulum','0','2026-09-06 11:17:01','2026-09-06 11:17:01');
INSERT INTO `structures` (`id`,`teacher_id`,`position`,`sort_order`,`created_at`,`updated_at`) VALUES ('3','6','Waka Kesiswaan','0','2026-09-06 11:17:39','2026-09-08 12:32:19');
INSERT INTO `structures` (`id`,`teacher_id`,`position`,`sort_order`,`created_at`,`updated_at`) VALUES ('4','7','Bendahara','0','2026-09-08 15:51:25','2026-09-08 15:51:25');

DROP TABLE IF EXISTS `student_classes`;
CREATE TABLE `student_classes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `n_l` int NOT NULL DEFAULT '0',
  `n_p` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `student_classes` (`id`,`name`,`sort_order`,`created_at`,`n_l`,`n_p`) VALUES ('1','X','0','2026-09-08 19:10:49','35','45');
INSERT INTO `student_classes` (`id`,`name`,`sort_order`,`created_at`,`n_l`,`n_p`) VALUES ('2','XI','0','2026-09-08 19:10:57','45','55');
INSERT INTO `student_classes` (`id`,`name`,`sort_order`,`created_at`,`n_l`,`n_p`) VALUES ('3','XII','0','2026-09-08 19:11:11','38','43');

DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nis` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nisn` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_level` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `major` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('L','P') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `class_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`,`sort_order`),
  KEY `idx_class` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `teachers` (`id`,`name`,`nip`,`position`,`type`,`photo`,`education`,`subject`,`description`,`is_active`,`sort_order`,`created_at`,`updated_at`) VALUES ('4','Drs. H. Ahmad Suryana, M.Pd','19650101199001','Kepala Sekolah','guru','','S2 Manajemen Pendidikan','-','','1','1','2026-09-05 06:22:10','2026-09-08 18:54:28');
INSERT INTO `teachers` (`id`,`name`,`nip`,`position`,`type`,`photo`,`education`,`subject`,`description`,`is_active`,`sort_order`,`created_at`,`updated_at`) VALUES ('5','Siti Rahma, S.Pd','19820304200601','Waka Kurikulum','guru',NULL,'S1 Pendidikan Matematika','Matematika',NULL,'1','2','2026-09-05 06:22:10','2026-09-05 06:22:10');
INSERT INTO `teachers` (`id`,`name`,`nip`,`position`,`type`,`photo`,`education`,`subject`,`description`,`is_active`,`sort_order`,`created_at`,`updated_at`) VALUES ('6','Budi Santoso, S.Kom','19870510201001','Waka Kesiswaan','guru','','S1 Teknik Informatika','Informatika','','1','3','2026-09-05 06:22:10','2026-09-08 12:12:52');
INSERT INTO `teachers` (`id`,`name`,`nip`,`position`,`type`,`photo`,`education`,`subject`,`description`,`is_active`,`sort_order`,`created_at`,`updated_at`) VALUES ('7','Nur Huda, S.Pd.I','123456','Bendahara','guru','','S1 PGMI','Informatika','Profil singkat','1','5','2026-09-06 11:48:36','2026-09-08 15:51:16');

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
INSERT INTO `users` (`id`,`role_id`,`name`,`username`,`email`,`password`,`avatar`,`is_active`,`remember_token`,`last_login_at`,`created_at`,`updated_at`,`deleted_at`) VALUES ('4','1','Administrator','admin','admin@sekolah.sch.id','$2y$10$C1oEysEHyd3zwgeCe7AeleKht016Y2clXUDLkeyTD50JDR/4qL77y',NULL,'1',NULL,'2026-09-08 16:08:05','2026-09-05 06:22:21','2026-09-08 16:08:05',NULL);

DROP TABLE IF EXISTS `widgets`;
CREATE TABLE `widgets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `area` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `animation` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'zoom',
  PRIMARY KEY (`id`),
  KEY `idx_area` (`area`,`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `widgets` (`id`,`area`,`type`,`title`,`content`,`sort_order`,`is_active`,`animation`) VALUES ('2','sidebar','agenda','Agenda Bulan','','2','1','zoom');
INSERT INTO `widgets` (`id`,`area`,`type`,`title`,`content`,`sort_order`,`is_active`,`animation`) VALUES ('3','sidebar','announcements','Pengumuman Bulan','','3','1','zoom');
INSERT INTO `widgets` (`id`,`area`,`type`,`title`,`content`,`sort_order`,`is_active`,`animation`) VALUES ('4','sidebar','image','Infografis','20260909-102338-535c0f265914.jpg','1','1','zoom');
INSERT INTO `widgets` (`id`,`area`,`type`,`title`,`content`,`sort_order`,`is_active`,`animation`) VALUES ('6','footer','about','Tentang Sekolah','','1','1','zoom');
INSERT INTO `widgets` (`id`,`area`,`type`,`title`,`content`,`sort_order`,`is_active`,`animation`) VALUES ('7','footer','contact','Kontak','','2','1','zoom');
INSERT INTO `widgets` (`id`,`area`,`type`,`title`,`content`,`sort_order`,`is_active`,`animation`) VALUES ('8','footer','social','Ikuti Kami','','5','1','zoom');
INSERT INTO `widgets` (`id`,`area`,`type`,`title`,`content`,`sort_order`,`is_active`,`animation`) VALUES ('9','footer','menu','Menu Navigasi','','4','0','zoom');
INSERT INTO `widgets` (`id`,`area`,`type`,`title`,`content`,`sort_order`,`is_active`,`animation`) VALUES ('10','footer','links','Tautan Terkait','[{\"label\":\"PPDB Online\",\"url\":\"https://www.youtube.com/\",\"target\":\"_blank\"},{\"label\":\"Kemeang RI\",\"url\":\"#\",\"target\":\"_blank\"},{\"label\":\"Simpatika\",\"url\":\"#\",\"target\":\"_blank\"},{\"label\":\"Emis GTK\",\"url\":\"#\",\"target\":\"_blank\"}]','3','1','zoom');

SET FOREIGN_KEY_CHECKS=1;
