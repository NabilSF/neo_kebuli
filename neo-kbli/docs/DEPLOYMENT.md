# Neo KBLI - Deployment & Operations Guide

Panduan ini berisi langkah-langkah untuk menyiapkan, merilis, dan memelihara aplikasi Neo KBLI di lingkungan *Staging* atau *Production*.

## 1. Server Requirements

- **OS:** Linux (Ubuntu 20.04/22.04 LTS direkomendasikan)
- **Web Server:** Nginx atau Apache
- **PHP:** 8.2 atau 8.3
- **Database:** MySQL 8.0+ atau MariaDB 10.6+
- **Cache/Queue:** Redis 6.0+ (Sangat direkomendasikan untuk stabilitas Production)
- **PHP Extensions:** BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, cURL, GD/Imagick, Zip.
- **Node.js & NPM:** (Hanya jika perlu re-compile assets/Vite di server)
- **Composer:** Versi 2.x

## 2. Environment Variables (.env)

Pastikan konfigurasi `.env` production Anda menggunakan nilai yang aman. Jangan FIX — Login & Registration UX

Konteks:
Halaman login Neo Kebuli saat ini masih terlihat seperti default Laravel (lihat screenshot terlampir). Pengguna belum memiliki tombol/link yang jelas untuk membuat akun baru.

Tujuan:
Perbaiki UX autentikasi agar pengguna dapat login ATAU menuju halaman registrasi dengan jelas, tanpa mengubah business logic/authentication flow yang sudah berjalan.

WAJIB AUDIT DULU:
1. Cek route auth yang tersedia:
   - register
   - login
   - password.forgot / password.request
   - password.reset
2. Cek controller/action auth yang sudah tersedia.
3. Cek resources/views auth yang digunakan.
4. Jangan membuat route/controller baru jika fitur register sebenarnya sudah tersedia.
5. Jika registration backend belum tersedia, laporkan terlebih dahulu. Jangan membuat migration/model baru tanpa kebutuhan.

PERBAIKAN LOGIN:
1. Ganti branding "Laravel" menjadi branding resmi aplikasi "Neo Kebuli".
2. Pertahankan bahasa Indonesia.
3. Tambahkan CTA registrasi yang jelas, misalnya:
   "Belum punya akun? Daftar sekarang"
   dengan "Daftar sekarang" sebagai link ke route register.
4. Letakkan CTA registrasi secara natural di bawah tombol "Masuk ke Sistem".
5. Pertahankan:
   - Email
   - Password
   - Lupa Kata Sandi?
   - Ingat saya
   - Kembali ke Beranda
6. Jangan menghilangkan fitur existing.
7. Jangan mengubah desain menjadi template SaaS/AI generik.
8. Pertahankan visual design system Neo Kebuli yang sudah ada.
9. Pastikan responsive/mobile-first.
10. Jangan gunakan hardcoded URL. Gunakan named route Laravel.

REGISTRATION PAGE:
Jika route/register memang sudah tersedia:
1. Audit halaman register.
2. Pastikan branding juga "Neo Kebuli", bukan "Laravel".
3. Pastikan tersedia link:
   "Sudah punya akun? Masuk"
   yang menuju route login.
4. Gunakan visual/layout yang konsisten dengan login.
5. Jangan mengubah proses registrasi/backend jika sudah benar.

SECURITY:
- Jangan bypass middleware auth/guest.
- Jangan mengubah password hashing.
- Jangan mengubah validation existing kecuali ditemukan bug.
- Jangan menambahkan role Admin/Super Admin pada proses registrasi publik.
- Akun hasil registrasi publik harus tetap menjadi user biasa sesuai sistem existing.
- Jangan mengubah database schema.

ARCHITECTURE:
- Jangan query database dari Blade.
- Jangan menaruh business logic di Blade.
- Gunakan named routes.
- Jangan membuat duplicate auth implementation.
- Pertahankan existing Laravel authentication architecture.

VERIFIKASI:
Jalankan:
php artisan optimize:clear
php artisan event:cache
php artisan route:list
php artisan test

Kemudian verifikasi:
- GET /login → 200
- GET /register → 200 jika route tersedia
- login tetap berfungsi
- link "Daftar sekarang" menuju route register
- link "Sudah punya akun? Masuk" menuju login
- guest tidak diarahkan secara salah
- tidak ada branding "Laravel" yang tersisa pada halaman auth yang relevan

Jangan melakukan perubahan di luar scope Login & Registration UX.

OUTPUT:
- Root cause
- Route auth yang ditemukan
- File yang diubah
- Before → After
- Test result
- Konfirmasi registration tidak memberikan role admin/super admin
- Remaining issue jika adagunakan setting local.

```env
APP_NAME="Neo KBLI"
APP_ENV=production
APP_KEY= # Generate dengan php artisan key:generate
APP_DEBUG=false
APP_URL=https://domain-anda.com

# Database (Gunakan kredensial aman)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=neo_kbli_prod
DB_USERNAME=
DB_PASSWORD=

# Cache, Session, dan Queue
# DIREKOMENDASIKAN menggunakan redis untuk Production
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true

# Redis Config
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=
REDIS_PORT=6379

# Mail Config (Wajib diisi agar notifikasi berjalan)
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@domain-anda.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 3. Installation & Deployment Steps

Jalankan perintah berikut di direktori aplikasi saat pertama kali rilis atau update:

### A. Persiapan Kode & Dependencies
```bash
git pull origin main
composer install --optimize-autoloader --no-dev
```

### B. Database Migration
```bash
php artisan migrate --force
```

### C. Optimization (Caching)
Perintah ini **WAJIB** dijalankan di production untuk meminimalkan I/O disk dan mempercepat respons:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### D. File Storage Setup
Pastikan folder storage memiliki izin tulis *(writable)*:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```
Catatan: Walaupun aplikasi menggunakan disk `private` secara internal untuk attachment workflow, menjalankan storage link tetap disarankan untuk public assets (jika ada):
```bash
php artisan storage:link
```

### E. Queue Worker (Supervisor)
Aplikasi sangat bergantung pada *Queue* untuk mengirim notifikasi Approval/Rejection/Penugasan KBLI. Anda **WAJIB** menjalankan Queue Worker menggunakan Supervisor di Linux.

Buat file konfigurasi `/etc/supervisor/conf.d/neo-kbli-worker.conf`:
```ini
[program:neo-kbli-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/neo-kbli/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/neo-kbli/storage/logs/worker.log
stopwaitsecs=3600
```
Update supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start neo-kbli-worker:*
```

## 4. Rollback Procedure

Jika terjadi masalah kritis setelah deployment:
1. Aktifkan Maintenance Mode: `php artisan down --secret="secret-token-anda"`
2. Revert commit Git: `git checkout <previous-commit-hash>`
3. Rollback Migration (hanya jika skema berubah dan aman): `php artisan migrate:rollback --step=1`
4. Bersihkan Cache: `php artisan optimize:clear`
5. Matikan Maintenance Mode: `php artisan up`

## 5. Troubleshooting Umum

- **Error 500 (Terjadi Kesalahan Internal):** 
  Jangan nyalakan `APP_DEBUG=true`! Baca pesan aslinya di `storage/logs/laravel.log`.
- **Notifikasi Email Tidak Masuk / Proses Workflow Terasa Lama (Hang):** 
  Periksa status Queue Worker. Jika worker mati, restart via `sudo supervisorctl restart neo-kbli-worker:*`. Anda juga bisa memeriksa job gagal via `php artisan queue:failed`.
- **File Upload Gagal atau 403 / 404 pada Gambar/PDF:** 
  Periksa permission direktori `storage/app/private`. Pastikan user web server (misal `www-data`) memiliki akses baca dan tulis penuh ke direktori tersebut.
- **Konfigurasi Tidak Berubah Setelah Edit .env:** 
  Ingat selalu menjalankan `php artisan config:cache` setiap kali Anda mengubah `.env` di production.

## 6. Health Check
Sistem dilengkapi dengan observabilitas minimal. Anda dapat memverifikasi bahwa HTTP server merespons dengan mengakses endpoint:
`GET /health` atau `GET /up`
