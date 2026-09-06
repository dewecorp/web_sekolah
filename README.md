# SchoolCMS - Website Sekolah Native PHP

## Kebutuhan
- Apache 2.4, PHP 8.x (ext: pdo_mysql, mbstring, fileinfo, gd), MySQL 8 / MariaDB 10.6+
- mod_rewrite aktif

## Instalasi
1. Copy folder ke `htdocs` / `www`, misal `web_sekolah`.
2. Buat DB `school_cms` utf8mb4.
3. Import: `database/schema.sql` lalu `database/seed.sql`.
4. Copy `.env.example` -> `.env`, sesuaikan DB_HOST, DB_NAME, DB_USER, DB_PASS, APP_URL.
5. Buka `http://localhost/web_sekolah/install` untuk installer 6 langkah, atau langsung pakai.
6. Login `http://localhost/web_sekolah/admin/login` user `admin` pass `admin123`. Ganti segera.
7. Hapus/kunci folder `install/` setelah selesai (`install/lock.php` dibuat otomatis).

## Konfigurasi
- DB: `config/database.php` baca `.env` (`DB_HOST/NAME/USER/PASS`).
- App: `config/app.php` (upload max 5MB, MIME jpg/png/webp/gif, block php/phtml/phar/exe).
- URL bersih: `.htaccess` rewrite ke `index.php`, `core/Router.php` petakan `/`, `/berita/:slug`, `/profil`, dll. Tanpa `index.php/.php`.
- Upload: `assets/uploads/`, validasi finfo+MIME+ext+size+getimagesize.

## Login & Role
- `admin/login`: CSRF, rate-limit 5/10mnt/IP (`login_attempts`), `password_hash/verify`, `session_regenerate_id`, remember-me hash sha256.
- Role: `administrator` penuh, `editor` konten+tampilan, `author` hanya posts/pages/media/gallery milik sendiri. Filter di `admin/index.php` + sidebar.
- Logout POST+CSRF, SweetAlert2 konfirmasi.

## Struktur
- `config/`, `core/` (Database, Auth, Security, Session, Validator, Helper, Router), `admin/` (CRUD per modul), `pages/` (frontend), `templates/frontend|admin|error`, `assets/`, `database/`, `install/`, `api/`.

## Menu & Mega Menu
- Admin > Menu: buat menu, tambah item, parent untuk submenu, toggle aktif. Render `templates/frontend/navbar.php`, dropdown desktop hover, mobile `<details>` accordion.
- Admin > Mega Menu: judul, kait menu_item, 3 kolom (judul + links `Label|/url` per baris, JSON `columns_json`). Hover grid desktop, accordion mobile.

## Media & Galeri
- Media: upload image only, cari, hapus fisik+DB.
- Galeri: album + foto multi, frontend grid + lightbox SweetAlert2 (`data-lightbox`).

## Section Homepage
- Admin > Section Homepage: on/off `hero,sambutan,statistik,berita,agenda,galeri,cta`. `pages/home.php` cek `$on($k)`.

## Backup
- Admin > Log & Backup (admin only): tombol unduh `.sql` full dump, `activity_logs` catat user/aksi/modul/IP.

## Deployment
- Set `APP_ENV=production`, `APP_URL` real, hapus `install/`, `chmod 755 assets/uploads`, backup rutin via menu Backup, enforce HTTPS di vhost.

## Demo
- `database/seed.sql` isi admin, settings, profil, kategori, sections, menu, 3 berita, 3 guru, agenda, pengumuman, slider, ekskul, prestasi. Kosongkan tabel terkait untuk produksi.
