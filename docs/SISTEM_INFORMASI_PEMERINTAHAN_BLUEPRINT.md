# Blueprint Sistem Informasi Pemerintahan
## KBLI Reference System — Laravel + Flutter Architecture

> **Versi:** 1.0.0 | **Status:** Brainstorming & Design Phase | **Arsitektur:** Modular Monolith

---

## Daftar Isi

1. [System Architecture](#1-system-architecture)
2. [Database Design](#2-database-design)
3. [ERD — Entity Relationship Diagram](#3-erd)
4. [Struktur Migration Laravel](#4-migration-laravel)
5. [Laravel Folder Structure](#5-folder-structure)
6. [REST API Design](#6-rest-api-design)
7. [Data Flow](#7-data-flow)
8. [Security & Scalability](#8-security--scalability)
9. [Technology Stack](#9-technology-stack)
10. [Roadmap Pengembangan](#10-roadmap)

---

## 1. System Architecture

### 1.1 Gambaran Umum Arsitektur

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                             │
│                                                                 │
│   ┌──────────────────┐          ┌──────────────────────────┐   │
│   │   Flutter App    │          │   Web Admin (Laravel     │   │
│   │  (Mobile/Public) │          │    Blade + Livewire)     │   │
│   └────────┬─────────┘          └────────────┬─────────────┘   │
│            │ REST API                         │ Web Routes       │
└────────────┼─────────────────────────────────┼─────────────────┘
             │                                 │
             ▼                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│                    LARAVEL BACKEND (Modular Monolith)            │
│                                                                 │
│   ┌──────────────┐   ┌──────────────┐   ┌──────────────────┐  │
│   │  API Module  │   │ Admin Module │   │  Shared Module   │  │
│   │  (v1/public) │   │  (web auth)  │   │  (core services) │  │
│   └──────┬───────┘   └──────┬───────┘   └──────────────────┘  │
│          │                  │                                   │
│   ┌──────▼──────────────────▼──────────────────────────────┐   │
│   │              Service Layer & Repositories               │   │
│   └─────────────────────────┬──────────────────────────────┘   │
│                             │                                   │
│   ┌─────────┐  ┌────────┐  ┌┴──────┐  ┌────────┐             │
│   │  Queue  │  │ Cache  │  │  ORM  │  │ Logger │             │
│   │(Redis)  │  │(Redis) │  │(Eloq) │  │(Stack) │             │
│   └─────────┘  └────────┘  └───────┘  └────────┘             │
└─────────────────────────────┼───────────────────────────────────┘
                              │
              ┌───────────────▼───────────────┐
              │         DATABASE LAYER         │
              │                               │
              │  ┌──────────────────────────┐ │
              │  │    MySQL (Primary DB)     │ │
              │  └──────────────────────────┘ │
              │  ┌────────────┐ ┌───────────┐ │
              │  │   Redis    │ │ MinIO/S3  │ │
              │  │  (Cache)   │ │ (Storage) │ │
              │  └────────────┘ └───────────┘ │
              └───────────────────────────────┘
```

### 1.2 Modular Monolith Architecture

Sistem dirancang sebagai **Modular Monolith** — satu codebase Laravel, tetapi dibagi menjadi modul-modul independen yang dapat dipisahkan menjadi microservices di masa depan.

```
app/Modules/
├── Core/           → Shared utilities, base classes
├── Auth/           → Authentication & authorization
├── Kbli/           → Master data KBLI
├── Application/    → Pengajuan permohonan
├── News/           → Berita & pengumuman
├── Dashboard/      → Statistik & monitoring
└── Notification/   → Sistem notifikasi
```

**Keuntungan pendekatan ini:**
- Deployment tetap sederhana (1 server, 1 process)
- Setiap modul memiliki boundary yang jelas
- Antar modul berkomunikasi via Service/Interface, bukan langsung
- Migrasi ke microservices dilakukan per modul tanpa refactor besar
- Testing per modul lebih mudah

### 1.3 Alur Komunikasi

#### Flutter ↔ Laravel ↔ Database

```
Flutter App
    │
    │  HTTP Request (JSON)
    │  Headers: Accept: application/json
    │           X-API-Version: v1
    │           Authorization: Bearer {token} [future]
    ▼
Laravel API Routes (/api/v1/*)
    │
    │  Route::middleware(['throttle:api', 'api.version'])
    ▼
API Controller
    │
    │  Validate Request → Transform via FormRequest
    ▼
Service Layer
    │
    │  Business Logic, Rules, Events
    ▼
Repository Layer
    │
    │  Query via Eloquent ORM
    ▼
MySQL Database
    │
    │  Return Eloquent Collection/Model
    ▼
API Resource (Transformer)
    │
    │  JSON Response + Pagination
    ▼
Flutter App (Parse & Render)
```

#### Admin Web ↔ Laravel

```
Browser (Admin)
    │
    │  HTTP Request (Form/AJAX)
    │  Cookie: XSRF-TOKEN (Sanctum)
    ▼
Web Routes (/admin/*)
    │
    │  Route::middleware(['auth', 'role:admin'])
    ▼
Admin Controller
    │
    │  Validate → Service → Repository → DB
    ▼
Blade View / JSON Response
    │
    ▼
Browser Render
```

### 1.4 Public API vs Admin API

| Aspek | Public API (Flutter) | Admin API (Web) |
|---|---|---|
| Base URL | `/api/v1/` | `/admin/` |
| Auth | Tidak diperlukan (Phase 1) | Session + Sanctum |
| Rate Limit | 60 req/menit | 300 req/menit |
| Response | JSON (Resource) | JSON + HTML Blade |
| Caching | Redis (aggressive) | Minimal |
| Data | Read-heavy | CRUD penuh |

### 1.5 Struktur Deployment Awal

```
┌─────────────────── VPS / Cloud Server ───────────────────────┐
│                                                               │
│  ┌─────────────┐    ┌──────────────────────────────────┐    │
│  │   Nginx     │───▶│   Laravel App (PHP-FPM)          │    │
│  │ (Reverse    │    │   Port: 9000                     │    │
│  │  Proxy)     │    │   /var/www/html/sipemerintahan    │    │
│  │ Port: 80/443│    └──────────────────────────────────┘    │
│  └─────────────┘                                             │
│                    ┌──────────────────────────────────┐      │
│                    │   MySQL 8.0                      │      │
│                    │   Port: 3306                     │      │
│                    └──────────────────────────────────┘      │
│                    ┌──────────────────────────────────┐      │
│                    │   Redis                          │      │
│                    │   Port: 6379 (Cache + Queue)     │      │
│                    └──────────────────────────────────┘      │
│                    ┌──────────────────────────────────┐      │
│                    │   Supervisor (Queue Worker)      │      │
│                    │   php artisan queue:work         │      │
│                    └──────────────────────────────────┘      │
└───────────────────────────────────────────────────────────────┘
```

---

## 2. Database Design

### 2.1 Daftar Tabel Utama

| # | Nama Tabel | Fungsi |
|---|---|---|
| 1 | `users` | Pengguna sistem (admin, operator) |
| 2 | `roles` | Definisi peran pengguna |
| 3 | `role_user` | Pivot many-to-many user ↔ role |
| 4 | `permissions` | Definisi izin akses |
| 5 | `permission_role` | Pivot many-to-many permission ↔ role |
| 6 | `kbli_categories` | Kategori industri KBLI (level 1) |
| 7 | `kbli_sections` | Sub-seksi KBLI (level 2) |
| 8 | `kbli_items` | Item detail KBLI (level 3 - kode 5 digit) |
| 9 | `applications` | Pengajuan permohonan dari masyarakat |
| 10 | `application_documents` | Dokumen lampiran permohonan |
| 11 | `application_histories` | Log perubahan status permohonan |
| 12 | `news` | Berita pemerintahan |
| 13 | `news_categories` | Kategori berita |
| 14 | `announcements` | Pengumuman resmi |
| 15 | `services` | Layanan pemerintahan |
| 16 | `activity_logs` | Audit trail semua aksi admin |
| 17 | `media` | Manajemen file/dokumen terpusat |
| 18 | `settings` | Konfigurasi sistem (key-value) |

### 2.2 Penjelasan Fungsi Tabel

#### users
Menyimpan semua pengguna backend (admin, operator, superadmin). Masyarakat umum pada Phase 1 tidak memerlukan akun. Dirancang untuk menampung `applicant` accounts di Phase 2.

#### kbli_categories & kbli_items
Hierarki KBLI mengikuti Klasifikasi Baku Lapangan Usaha Indonesia. Struktur hierarki:
- `kbli_categories` → Kategori (A, B, C...) contoh: "C - Industri Pengolahan"
- `kbli_sections` → Sub-divisi (10, 11, 12...) contoh: "10 - Industri Makanan"
- `kbli_items` → Kode 5 digit (10110) contoh: "Industri pengolahan dan pengawetan daging"

#### applications
Inti sistem pengajuan. Setiap permohonan memiliki status lifecycle: `draft → submitted → under_review → approved/rejected`. Relasi ke `kbli_items` menunjukkan jenis usaha yang diajukan.

#### application_histories
Event sourcing sederhana untuk tracking status. Setiap perubahan status disimpan sebagai record baru — tidak ada data yang dihapus, hanya ditambahkan.

#### activity_logs
Audit trail komprehensif. Setiap aksi admin (create, update, delete, login, export) tercatat dengan user, IP address, user agent, dan payload perubahan (before/after).

### 2.3 Relasi Antar Tabel

```
users ──────────────────── role_user ─────────────── roles
  │                                                     │
  │                                              permission_role
  │                                                     │
  │                                               permissions
  │
  ├──[created_by]──── kbli_categories
  │                        │
  │                   kbli_sections
  │                        │
  │                   kbli_items ◄──────────────── applications
  │                                                     │
  ├──[reviewed_by]──────────────────────────────────────┤
  │                                                     │
  │                                         application_documents
  │                                                     │
  │                                         application_histories
  │
  ├──[created_by]──── news ─────── news_categories
  │
  ├──[created_by]──── announcements
  │
  └──[user_id]──────── activity_logs
```

### 2.4 Normalization

Semua tabel memenuhi **3NF (Third Normal Form)**:
- Setiap kolom bergantung pada primary key saja
- Tidak ada transitive dependency
- Lookup data (kategori, status) dipisahkan ke tabel referensi atau enum

Status fields menggunakan **PHP Enum** Laravel 11, disimpan sebagai string di database untuk readability.

### 2.5 Scalability Database

| Strategi | Detail |
|---|---|
| Indexing | Index pada `status`, `created_at`, `kbli_code`, dan semua FK |
| Soft Delete | `deleted_at` di tabel utama (data tidak dihapus permanen) |
| UUID vs BIGINT | BIGINT auto-increment untuk MVP; UUID untuk data publik |
| Partitioning | `activity_logs` dipartisi per bulan saat volume > 1M rows |
| Read Replica | Tambahkan read replica MySQL saat traffic meningkat |
| Full-text Search | MySQL `FULLTEXT INDEX` untuk pencarian KBLI (atau Meilisearch) |
| Caching | Redis cache untuk KBLI list (TTL 1 jam), jarang berubah |
| Engine | InnoDB untuk semua tabel (ACID, foreign key, row-level locking) |
| Charset | `utf8mb4` + collation `utf8mb4_unicode_ci` (support emoji & karakter khusus) |

---

## 3. ERD

### 3.1 ERD Lengkap (Text Format)

```
╔══════════════════╗       ╔═══════════════════╗
║      USERS       ║       ║      ROLES        ║
╠══════════════════╣       ╠═══════════════════╣
║ id (PK)          ║◄─────►║ id (PK)           ║
║ name             ║  M:M  ║ name              ║
║ email (UNIQUE)   ║       ║ slug              ║
║ password         ║       ║ description       ║
║ phone            ║       ║ created_at        ║
║ is_active        ║       ║ updated_at        ║
║ last_login_at    ║       ╚═══════════════════╝
║ created_at       ║               │
║ updated_at       ║               │ M:M
║ deleted_at       ║               ▼
╚══════════════════╝       ╔═══════════════════╗
         │                 ║   PERMISSIONS     ║
         │                 ╠═══════════════════╣
         │                 ║ id (PK)           ║
         │                 ║ name              ║
         │                 ║ slug              ║
         │                 ║ module            ║
         │                 ╚═══════════════════╝
         │
         │ 1:M (created_by)
         ▼
╔══════════════════════╗    ╔══════════════════════╗
║   KBLI_CATEGORIES   ║    ║    NEWS_CATEGORIES   ║
╠══════════════════════╣    ╠══════════════════════╣
║ id (PK)              ║    ║ id (PK)              ║
║ code (UNIQUE)        ║    ║ name                 ║
║ name                 ║    ║ slug                 ║
║ description          ║    ║ color                ║
║ is_active            ║    ╚══════════════════════╝
║ sort_order           ║              │
║ created_by (FK)      ║              │ 1:M
║ created_at           ║              ▼
║ updated_at           ║    ╔══════════════════════╗
╚══════════════════════╝    ║        NEWS          ║
         │                  ╠══════════════════════╣
         │ 1:M              ║ id (PK)              ║
         ▼                  ║ category_id (FK)     ║
╔══════════════════════╗    ║ title                ║
║   KBLI_SECTIONS     ║    ║ slug (UNIQUE)        ║
╠══════════════════════╣    ║ excerpt              ║
║ id (PK)              ║    ║ content (TEXT)       ║
║ category_id (FK)     ║    ║ thumbnail_id (FK)    ║
║ code (UNIQUE)        ║    ║ status (enum)        ║
║ name                 ║    ║ published_at         ║
║ description          ║    ║ view_count           ║
║ is_active            ║    ║ created_by (FK)      ║
╚══════════════════════╝    ║ created_at           ║
         │                  ║ updated_at           ║
         │ 1:M              ║ deleted_at           ║
         ▼                  ╚══════════════════════╝
╔══════════════════════╗
║    KBLI_ITEMS       ║    ╔══════════════════════╗
╠══════════════════════╣    ║   ANNOUNCEMENTS     ║
║ id (PK)              ║    ╠══════════════════════╣
║ section_id (FK)      ║    ║ id (PK)              ║
║ code (UNIQUE, 5-dig) ║    ║ title                ║
║ name                 ║    ║ content (TEXT)       ║
║ description (TEXT)   ║    ║ type (enum)          ║
║ riskLevel (enum)     ║    ║ priority (enum)      ║
║ capital_requirement  ║    ║ is_pinned            ║
║ employee_requirement ║    ║ start_date           ║
║ is_active            ║    ║ end_date             ║
║ meta_keywords        ║    ║ status (enum)        ║
║ created_by (FK)      ║    ║ created_by (FK)      ║
║ created_at           ║    ║ created_at           ║
║ updated_at           ║    ║ updated_at           ║
╚══════════════════════╝    ╚══════════════════════╝
         │
         │ 1:M
         ▼
╔══════════════════════╗
║    APPLICATIONS     ║
╠══════════════════════╣
║ id (PK)              ║
║ tracking_code (UNIQ) ║◄── unique public tracking code
║ kbli_item_id (FK)    ║
║ applicant_name       ║
║ applicant_nik        ║
║ applicant_phone      ║
║ applicant_email      ║
║ applicant_address    ║
║ business_name        ║
║ business_address     ║
║ status (enum)        ║
║ notes                ║
║ reviewed_by (FK)     ║
║ reviewed_at          ║
║ submitted_at         ║
║ created_at           ║
║ updated_at           ║
║ deleted_at           ║
╚══════════════════════╝
         │
         ├─────────────────────────────────────────┐
         │ 1:M                                     │ 1:M
         ▼                                         ▼
╔══════════════════════╗             ╔═════════════════════════╗
║ APPLICATION_DOCUMENTS║             ║  APPLICATION_HISTORIES  ║
╠══════════════════════╣             ╠═════════════════════════╣
║ id (PK)              ║             ║ id (PK)                 ║
║ application_id (FK)  ║             ║ application_id (FK)     ║
║ media_id (FK)        ║             ║ from_status             ║
║ document_type        ║             ║ to_status               ║
║ label                ║             ║ notes                   ║
║ is_required          ║             ║ changed_by (FK)         ║
║ is_verified          ║             ║ created_at              ║
║ created_at           ║             ╚═════════════════════════╝
╚══════════════════════╝

╔══════════════════════╗    ╔══════════════════════╗
║    ACTIVITY_LOGS    ║    ║       MEDIA          ║
╠══════════════════════╣    ╠══════════════════════╣
║ id (PK)              ║    ║ id (PK)              ║
║ user_id (FK)         ║    ║ disk                 ║
║ action               ║    ║ path                 ║
║ module               ║    ║ filename             ║
║ subject_type         ║    ║ original_name        ║
║ subject_id           ║    ║ mime_type            ║
║ description          ║    ║ size (bytes)         ║
║ old_values (JSON)    ║    ║ url                  ║
║ new_values (JSON)    ║    ║ uploaded_by (FK)     ║
║ ip_address           ║    ║ created_at           ║
║ user_agent           ║    ╚══════════════════════╝
║ created_at           ║
╚══════════════════════╝    ╔══════════════════════╗
                            ║      SETTINGS        ║
                            ╠══════════════════════╣
                            ║ id (PK)              ║
                            ║ key (UNIQUE)         ║
                            ║ value (TEXT)         ║
                            ║ type (enum)          ║
                            ║ group                ║
                            ║ label                ║
                            ║ updated_by (FK)      ║
                            ║ updated_at           ║
                            ╚══════════════════════╝
```

---

## 4. Migration Laravel

### 4.1 `2024_01_01_000001_create_roles_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // ⚙️ MySQL: Pastikan config/database.php menggunakan:
    // 'charset'   => 'utf8mb4',
    // 'collation' => 'utf8mb4_unicode_ci',
    // 'engine'    => 'InnoDB',

    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('module', 50)->index(); // 'kbli', 'news', 'application'
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
```

### 4.2 `2024_01_01_000002_create_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 191)->unique(); // 191 = batas aman index utf8mb4
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 191)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
```

### 4.3 `2024_01_01_000003_create_kbli_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Level 1: Kategori (A, B, C...)
        Schema::create('kbli_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique(); // 'A', 'B', 'C'
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // Level 2: Seksi (10, 11, 12...)
        Schema::create('kbli_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('kbli_categories')->cascadeOnDelete();
            $table->string('code', 4)->unique(); // '1011', '1012'
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['category_id', 'is_active']);
        });

        // Level 3: Item KBLI (5 digit)
        Schema::create('kbli_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('kbli_sections')->cascadeOnDelete();
            $table->string('code', 6)->unique(); // '10110'
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium')->index();
            $table->bigInteger('capital_requirement')->unsigned()->nullable()
                  ->comment('Minimum modal dalam rupiah');
            $table->unsignedInteger('employee_requirement')->nullable()
                  ->comment('Minimum tenaga kerja');
            $table->text('requirements')->nullable()
                  ->comment('Persyaratan umum dalam format teks/JSON');
            $table->boolean('is_active')->default(true)->index();
            $table->json('meta_keywords')->nullable()->comment('Kata kunci pencarian');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Composite index untuk filtering umum
            $table->index(['code', 'is_active']);
            $table->index(['section_id', 'is_active', 'risk_level']);

            // MySQL FULLTEXT index untuk pencarian nama & deskripsi KBLI
            // Requires InnoDB engine (default di MySQL 5.6+)
            $table->fullText(['name', 'description'], 'kbli_items_fulltext');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kbli_items');
        Schema::dropIfExists('kbli_sections');
        Schema::dropIfExists('kbli_categories');
    }
};
```

### 4.4 `2024_01_01_000004_create_media_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('disk')->default('public'); // 'public', 's3', 'local'
            $table->string('path');
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size')->comment('Size in bytes');
            $table->string('url');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['disk', 'path']);
            $table->index('mime_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
```

### 4.5 `2024_01_01_000005_create_applications_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // Tracking public (no login needed)
            $table->string('tracking_code', 20)->unique()
                  ->comment('Format: APP-YYYYMMDD-XXXXX');

            // KBLI yang diajukan
            $table->foreignId('kbli_item_id')->constrained('kbli_items')->restrictOnDelete();

            // Data pemohon
            $table->string('applicant_name', 150);
            $table->string('applicant_nik', 16);
            $table->string('applicant_phone', 20);
            $table->string('applicant_email', 191)->nullable();
            $table->text('applicant_address');

            // Data usaha
            $table->string('business_name', 200);
            $table->text('business_address');
            $table->string('business_npwp', 20)->nullable();

            // Status lifecycle — MySQL ENUM tersimpan efisien (1-2 byte)
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'need_revision',
                'approved',
                'rejected',
            ])->default('draft')->index();

            // Admin notes
            $table->text('admin_notes')->nullable();

            // Review tracking
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Composite indexes untuk filtering umum
            $table->index(['status', 'submitted_at']);
            $table->index(['kbli_item_id', 'status']);
            $table->index('applicant_nik');
        });

        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->restrictOnDelete();
            $table->string('document_type', 50); // 'ktp', 'npwp', 'surat_permohonan'
            $table->string('label', 150);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['application_id', 'document_type']);
        });

        Schema::create('application_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['application_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_histories');
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('applications');
    }
};
```

### 4.6 `2024_01_01_000006_create_news_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('color', 7)->default('#3B82F6'); // Hex color
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('news_categories')->nullOnDelete();
            $table->string('title', 255);
            $table->string('slug', 191)->unique(); // 191 = batas aman unique index utf8mb4
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->foreignId('thumbnail_id')->nullable()->constrained('media')->nullOnDelete();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->boolean('is_featured')->default(false)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['category_id', 'status']);

            // MySQL FULLTEXT index untuk pencarian berita
            $table->fullText(['title', 'excerpt'], 'news_fulltext');
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->longText('content');
            $table->enum('type', ['general', 'urgent', 'maintenance', 'regulation'])
                  ->default('general')->index();
            $table->enum('priority', ['low', 'normal', 'high', 'critical'])
                  ->default('normal')->index();
            $table->boolean('is_pinned')->default(false)->index();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'active', 'expired'])->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_categories');
    }
};
```

### 4.7 `2024_01_01_000007_create_activity_logs_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50)->index();    // 'created', 'updated', 'deleted', 'login'
            $table->string('module', 50)->index();    // 'kbli', 'news', 'application'
            $table->string('subject_type', 100)->nullable(); // Model class
            $table->unsignedBigInteger('subject_id')->nullable(); // Model ID
            $table->string('description', 500);
            // MySQL JSON type (native sejak 5.7.8) — validasi otomatis oleh engine
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable(); // 45 char = cukup untuk IPv6
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Composite indexes untuk audit queries
            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'integer', 'boolean', 'json', 'text'])
                  ->default('string');
            $table->string('group', 50)->default('general')->index();
            $table->string('label', 150);
            $table->text('description')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
    }
};
```

### 4.8 Konfigurasi MySQL — `config/database.php`

```php
// config/database.php
'mysql' => [
    'driver'         => 'mysql',
    'host'           => env('DB_HOST', '127.0.0.1'),
    'port'           => env('DB_PORT', '3306'),
    'database'       => env('DB_DATABASE', 'sipemerintahan'),
    'username'       => env('DB_USERNAME', 'root'),
    'password'       => env('DB_PASSWORD', ''),
    'unix_socket'    => env('DB_SOCKET', ''),

    // ⚠️ WAJIB: utf8mb4 untuk support emoji, karakter Arab, Cina, dll
    'charset'        => 'utf8mb4',
    'collation'      => 'utf8mb4_unicode_ci',

    // InnoDB: ACID transactions, foreign keys, row-level locking
    'engine'         => 'InnoDB',

    'prefix'         => '',
    'prefix_indexes' => true,
    'strict'         => true,        // SQL strict mode — tolak data invalid
    'modes'          => [
        'ONLY_FULL_GROUP_BY',
        'STRICT_TRANS_TABLES',
        'NO_ZERO_IN_DATE',
        'NO_ZERO_DATE',
        'ERROR_FOR_DIVISION_BY_ZERO',
        'NO_ENGINE_SUBSTITUTION',
    ],
],
```

### 4.9 MySQL FULLTEXT Search — Implementasi di Repository

```php
// app/Modules/Kbli/Repositories/KbliRepository.php

public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
{
    // Opsi 1: MySQL FULLTEXT MATCH...AGAINST (relevance-based)
    return KbliItem::query()
        ->whereRaw(
            "MATCH(name, description) AGAINST(? IN BOOLEAN MODE)",
            ['+' . $keyword . '*']
        )
        ->where('is_active', true)
        ->with('section.category')
        ->paginate($perPage);
}

// Opsi 2: LIKE biasa (fallback jika keyword terlalu pendek < 3 char)
public function searchFallback(string $keyword, int $perPage = 15): LengthAwarePaginator
{
    return KbliItem::query()
        ->where('is_active', true)
        ->where(function ($q) use ($keyword) {
            $q->where('name', 'LIKE', "%{$keyword}%")
              ->orWhere('code', 'LIKE', "%{$keyword}%")
              ->orWhere('description', 'LIKE', "%{$keyword}%");
        })
        ->with('section.category')
        ->paginate($perPage);
}

// Opsi 3 (Rekomendasi Production): Meilisearch
// Install: composer require meilisearch/meilisearch-php
// Tambahkan Laravel\Scout\Searchable ke model KbliItem
// php artisan scout:import "App\Modules\Kbli\Models\KbliItem"
```

---



### 5.1 Struktur Lengkap

```
app/
├── Console/
│   └── Commands/
│       ├── GenerateTrackingCode.php
│       └── CleanupExpiredApplications.php
│
├── Exceptions/
│   └── Handler.php
│
├── Http/
│   └── Middleware/
│       ├── ApiVersionMiddleware.php
│       ├── RoleMiddleware.php
│       └── LogActivityMiddleware.php
│
├── Models/                          ← Eloquent models (shared)
│   ├── User.php
│   ├── Role.php
│   ├── Permission.php
│   └── Media.php
│
├── Modules/                         ← CORE MODULAR STRUCTURE
│   │
│   ├── Core/                        ← Shared utilities
│   │   ├── Contracts/
│   │   │   ├── Repositories/
│   │   │   │   └── BaseRepositoryInterface.php
│   │   │   └── Services/
│   │   │       └── BaseServiceInterface.php
│   │   ├── Repositories/
│   │   │   └── BaseRepository.php
│   │   ├── Services/
│   │   │   ├── MediaService.php
│   │   │   ├── SettingService.php
│   │   │   └── ActivityLogService.php
│   │   ├── Traits/
│   │   │   ├── HasActivityLog.php
│   │   │   └── ApiResponse.php
│   │   └── Helpers/
│   │       └── TrackingCodeGenerator.php
│   │
│   ├── Auth/                        ← Authentication module
│   │   ├── Controllers/
│   │   │   └── Web/
│   │   │       └── AuthController.php
│   │   ├── Services/
│   │   │   └── AuthService.php
│   │   └── Requests/
│   │       └── LoginRequest.php
│   │
│   ├── Kbli/                        ← KBLI module
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── KbliCategoryApiController.php
│   │   │   │   └── KbliItemApiController.php
│   │   │   └── Web/
│   │   │       ├── KbliCategoryController.php
│   │   │       └── KbliItemController.php
│   │   ├── Models/
│   │   │   ├── KbliCategory.php
│   │   │   ├── KbliSection.php
│   │   │   └── KbliItem.php
│   │   ├── Repositories/
│   │   │   ├── Contracts/
│   │   │   │   └── KbliRepositoryInterface.php
│   │   │   └── KbliRepository.php
│   │   ├── Services/
│   │   │   └── KbliService.php
│   │   ├── Resources/               ← API Transformers
│   │   │   ├── KbliCategoryResource.php
│   │   │   ├── KbliItemResource.php
│   │   │   └── KbliItemCollection.php
│   │   ├── Requests/
│   │   │   ├── StoreKbliCategoryRequest.php
│   │   │   └── StoreKbliItemRequest.php
│   │   └── Policies/
│   │       └── KbliPolicy.php
│   │
│   ├── Application/                 ← Pengajuan permohonan
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── ApplicationApiController.php
│   │   │   └── Web/
│   │   │       └── ApplicationController.php
│   │   ├── Models/
│   │   │   ├── Application.php
│   │   │   ├── ApplicationDocument.php
│   │   │   └── ApplicationHistory.php
│   │   ├── Repositories/
│   │   │   └── ApplicationRepository.php
│   │   ├── Services/
│   │   │   └── ApplicationService.php
│   │   ├── Resources/
│   │   │   ├── ApplicationResource.php
│   │   │   └── ApplicationTrackingResource.php
│   │   ├── Requests/
│   │   │   ├── StoreApplicationRequest.php
│   │   │   └── ReviewApplicationRequest.php
│   │   ├── Events/
│   │   │   ├── ApplicationSubmitted.php
│   │   │   └── ApplicationStatusChanged.php
│   │   ├── Listeners/
│   │   │   ├── SendApplicationConfirmation.php
│   │   │   └── LogStatusChange.php
│   │   ├── Enums/
│   │   │   └── ApplicationStatus.php
│   │   └── Jobs/
│   │       └── ProcessApplicationNotification.php
│   │
│   ├── News/                        ← Berita & pengumuman
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── NewsApiController.php
│   │   │   │   └── AnnouncementApiController.php
│   │   │   └── Web/
│   │   │       ├── NewsController.php
│   │   │       └── AnnouncementController.php
│   │   ├── Models/
│   │   │   ├── News.php
│   │   │   ├── NewsCategory.php
│   │   │   └── Announcement.php
│   │   ├── Repositories/
│   │   │   └── NewsRepository.php
│   │   ├── Services/
│   │   │   └── NewsService.php
│   │   └── Resources/
│   │       ├── NewsResource.php
│   │       └── AnnouncementResource.php
│   │
│   └── Dashboard/                   ← Statistik & monitoring
│       ├── Controllers/
│       │   └── Web/
│       │       └── DashboardController.php
│       └── Services/
│           └── StatisticsService.php
│
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── auth.blade.php
│   ├── auth/
│   │   └── login.blade.php
│   ├── admin/
│   │   ├── dashboard/
│   │   ├── kbli/
│   │   ├── applications/
│   │   ├── news/
│   │   └── announcements/
│   └── components/
│
routes/
├── web.php                          ← Admin web routes
├── api.php                          ← API v1 routes
└── console.php
│
config/
├── modules.php                      ← Module registry
└── api.php                          ← API configuration
```

### 5.2 Contoh Model dengan Best Practice

```php
// app/Modules/Application/Models/Application.php

<?php

namespace App\Modules\Application\Models;

use App\Modules\Application\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tracking_code', 'kbli_item_id', 'applicant_name',
        'applicant_nik', 'applicant_phone', 'applicant_email',
        'applicant_address', 'business_name', 'business_address',
        'business_npwp', 'status', 'admin_notes',
        'reviewed_by', 'reviewed_at', 'submitted_at',
    ];

    protected $casts = [
        'status'      => ApplicationStatus::class,
        'reviewed_at' => 'datetime',
        'submitted_at'=> 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Application $model) {
            $model->tracking_code ??= TrackingCodeGenerator::generate();
        });
    }

    public function kbliItem(): BelongsTo
    {
        return $this->belongsTo(KbliItem::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ApplicationHistory::class)->orderBy('created_at');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeByStatus($query, ApplicationStatus $status)
    {
        return $query->where('status', $status->value);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [
            ApplicationStatus::Draft,
            ApplicationStatus::NeedRevision,
        ]);
    }
}
```

### 5.3 Enum Status Pengajuan

```php
// app/Modules/Application/Enums/ApplicationStatus.php

<?php

namespace App\Modules\Application\Enums;

enum ApplicationStatus: string
{
    case Draft       = 'draft';
    case Submitted   = 'submitted';
    case UnderReview = 'under_review';
    case NeedRevision= 'need_revision';
    case Approved    = 'approved';
    case Rejected    = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Draft        => 'Draf',
            self::Submitted    => 'Diajukan',
            self::UnderReview  => 'Sedang Ditinjau',
            self::NeedRevision => 'Perlu Revisi',
            self::Approved     => 'Disetujui',
            self::Rejected     => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft        => 'gray',
            self::Submitted    => 'blue',
            self::UnderReview  => 'yellow',
            self::NeedRevision => 'orange',
            self::Approved     => 'green',
            self::Rejected     => 'red',
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::Draft        => in_array($newStatus, [self::Submitted]),
            self::Submitted    => in_array($newStatus, [self::UnderReview, self::Rejected]),
            self::UnderReview  => in_array($newStatus, [self::Approved, self::Rejected, self::NeedRevision]),
            self::NeedRevision => in_array($newStatus, [self::Submitted]),
            default            => false,
        };
    }
}
```

---

## 6. REST API Design

### 6.1 API Versioning & Base URL

```
Production:  https://api.sipemerintahan.go.id/api/v1/
Staging:     https://staging-api.sipemerintahan.go.id/api/v1/
Development: http://localhost:8000/api/v1/
```

### 6.2 Standard Response Format

```json
// ✅ Success — Single Resource
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": {
    "id": 1,
    "code": "10110",
    "name": "Industri pengolahan dan pengawetan daging"
  },
  "meta": {
    "api_version": "v1",
    "timestamp": "2024-01-15T08:30:00Z"
  }
}

// ✅ Success — Collection with Pagination
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [...],
  "meta": {
    "api_version": "v1",
    "timestamp": "2024-01-15T08:30:00Z",
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 250,
      "last_page": 17,
      "from": 1,
      "to": 15,
      "links": {
        "first": "https://api.../v1/kbli?page=1",
        "prev": null,
        "next": "https://api.../v1/kbli?page=2",
        "last": "https://api.../v1/kbli?page=17"
      }
    }
  }
}

// ❌ Error Response
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "applicant_nik": ["NIK harus 16 digit"],
    "applicant_phone": ["Format nomor telepon tidak valid"]
  },
  "meta": {
    "api_version": "v1",
    "timestamp": "2024-01-15T08:30:00Z"
  }
}
```

### 6.3 Public API Endpoints (Flutter)

#### KBLI Endpoints

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/v1/kbli/categories` | Daftar semua kategori KBLI |
| GET | `/api/v1/kbli/categories/{code}` | Detail kategori + list sections |
| GET | `/api/v1/kbli/items` | Daftar item KBLI (paginated) |
| GET | `/api/v1/kbli/items/{code}` | Detail item KBLI |
| GET | `/api/v1/kbli/search?q={keyword}` | Pencarian KBLI |

#### News & Announcement Endpoints

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/v1/news` | Daftar berita (paginated) |
| GET | `/api/v1/news/{slug}` | Detail berita |
| GET | `/api/v1/announcements` | Daftar pengumuman aktif |
| GET | `/api/v1/announcements/{id}` | Detail pengumuman |

#### Application Endpoints

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/api/v1/applications` | Submit pengajuan baru |
| GET | `/api/v1/applications/track/{code}` | Tracking status by code |
| POST | `/api/v1/applications/{id}/documents` | Upload dokumen |

#### General Endpoints

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/v1/services` | Daftar layanan |
| GET | `/api/v1/ping` | Health check |
| GET | `/api/v1/settings/public` | Konfigurasi publik (nama instansi, dll) |

### 6.4 Admin API Endpoints

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/api/v1/admin/auth/login` | Login admin |
| POST | `/api/v1/admin/auth/logout` | Logout |
| GET | `/api/v1/admin/applications` | Daftar semua permohonan |
| PUT | `/api/v1/admin/applications/{id}/status` | Update status permohonan |
| GET | `/api/v1/admin/dashboard/stats` | Data statistik dashboard |
| GET | `/api/v1/admin/activity-logs` | Audit log |

### 6.5 HTTP Status Codes

| Code | Kondisi |
|---|---|
| 200 | OK — Request berhasil |
| 201 | Created — Resource baru berhasil dibuat |
| 204 | No Content — Delete berhasil |
| 400 | Bad Request — Request tidak valid |
| 401 | Unauthorized — Token tidak ada/invalid |
| 403 | Forbidden — Tidak punya akses |
| 404 | Not Found — Resource tidak ditemukan |
| 422 | Unprocessable Entity — Validasi gagal |
| 429 | Too Many Requests — Rate limit |
| 500 | Internal Server Error |

### 6.6 Contoh Route Definition

```php
// routes/api.php

<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Kbli\Controllers\Api\KbliCategoryApiController;
use App\Modules\Kbli\Controllers\Api\KbliItemApiController;
use App\Modules\Application\Controllers\Api\ApplicationApiController;
use App\Modules\News\Controllers\Api\NewsApiController;
use App\Modules\News\Controllers\Api\AnnouncementApiController;

Route::prefix('v1')->middleware(['throttle:api', 'api.version'])->group(function () {

    // Public endpoints (no auth required)
    Route::prefix('kbli')->name('api.kbli.')->group(function () {
        Route::get('/categories', [KbliCategoryApiController::class, 'index'])->name('categories.index');
        Route::get('/categories/{code}', [KbliCategoryApiController::class, 'show'])->name('categories.show');
        Route::get('/items', [KbliItemApiController::class, 'index'])->name('items.index');
        Route::get('/items/{code}', [KbliItemApiController::class, 'show'])->name('items.show');
        Route::get('/search', [KbliItemApiController::class, 'search'])->name('items.search');
    });

    Route::prefix('news')->name('api.news.')->group(function () {
        Route::get('/', [NewsApiController::class, 'index'])->name('index');
        Route::get('/{slug}', [NewsApiController::class, 'show'])->name('show');
    });

    Route::prefix('announcements')->name('api.announcements.')->group(function () {
        Route::get('/', [AnnouncementApiController::class, 'index'])->name('index');
        Route::get('/{id}', [AnnouncementApiController::class, 'show'])->name('show');
    });

    Route::prefix('applications')->name('api.applications.')->group(function () {
        Route::post('/', [ApplicationApiController::class, 'store'])->name('store');
        Route::get('/track/{code}', [ApplicationApiController::class, 'track'])->name('track');
        Route::post('/{id}/documents', [ApplicationApiController::class, 'uploadDocument'])->name('document.upload');
    });

    // Admin endpoints (auth required - Phase 2)
    Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin,operator'])->group(function () {
        // ... admin routes
    });
});
```

---

## 7. Data Flow

### 7.1 Flow: Admin Membuat Data KBLI

```
Admin (Web Browser)
    │
    │  1. POST /admin/kbli/items
    │     Body: {section_id, code, name, description, ...}
    │
    ▼
KbliItemController::store(StoreKbliItemRequest $request)
    │
    │  2. Validasi otomatis via FormRequest:
    │     - code: unique, 5 digit
    │     - name: required, max:255
    │     - section_id: exists in kbli_sections
    │
    ▼
KbliService::createItem(array $data)
    │
    │  3. Business logic:
    │     - Normalize code (uppercase)
    │     - Set default values
    │     - Fire event: KbliItemCreated
    │
    ▼
KbliRepository::create(array $data)
    │
    │  4. Eloquent: KbliItem::create($data)
    │
    ▼
MySQL (INSERT INTO kbli_items)
    │
    │  5. Model saved, event fires
    │
    ▼
ActivityLogService::log('created', 'kbli', $kbliItem)
    │
    │  6. INSERT INTO activity_logs (audit trail)
    │
    ▼
Redis Cache::forget('kbli_categories_all')
    │
    │  7. Invalidate related cache keys
    │
    ▼
Response::redirect('/admin/kbli')->with('success', 'KBLI berhasil ditambahkan')
```

### 7.2 Flow: Flutter Mengambil Data KBLI

```
Flutter App
    │
    │  1. GET /api/v1/kbli/items?page=1&category=C
    │     Headers: Accept: application/json
    │
    ▼
Nginx (Check SSL, Rate Limit)
    │
    ▼
Laravel Route → KbliItemApiController::index()
    │
    │  2. Check Redis cache:
    │     Cache::remember('kbli_items_page_1_cat_C', 3600, ...)
    │
    ├──[Cache HIT]──► Return cached JSON ──────────────────────┐
    │                                                          │
    │  [Cache MISS]                                            │
    ▼                                                          │
KbliService::getPaginatedItems(filters)                        │
    │                                                          │
    ▼                                                          │
KbliRepository::query()                                        │
    │                                                          │
    │  3. SELECT dengan eager loading:                         │
    │     KbliItem::with(['section.category'])                 │
    │              ->active()->filter($request)                │
    │              ->paginate(15)                              │
    │                                                          │
    ▼                                                          │
MySQL (SELECT + JOIN)                                          │
    │                                                          │
    ▼                                                          │
KbliItemCollection::make($items)                               │
    │                                                          │
    │  4. Transform via API Resource:                          │
    │     Hanya field yang diperlukan Flutter                  │
    │     Include pagination meta                              │
    │                                                          │
    ▼                                                          │
Redis Cache::put(key, response, 3600)                          │
    │                                                          │
    ▼◄─────────────────────────────────────────────────────────┘
JSON Response → Flutter
    │
    │  5. Flutter parse JSON:
    │     KbliModel.fromJson(json)
    │
    ▼
ListView.builder() → Render UI
```

### 7.3 Flow: Masyarakat Submit Pengajuan

```
Flutter App (Form Input)
    │
    │  1. User isi form:
    │     NIK, nama, alamat, nama usaha,
    │     pilih KBLI, upload dokumen
    │
    ▼
    │  2. POST /api/v1/applications
    │     Content-Type: multipart/form-data
    │     Body: {applicant_*, business_*, kbli_item_id}
    │
    ▼
ApplicationApiController::store()
    │
    │  3. Validate StoreApplicationRequest:
    │     - NIK: 16 digit, numeric
    │     - kbli_item_id: exists & active
    │     - applicant_phone: valid phone
    │
    ▼
ApplicationService::submit(array $data)
    │
    │  4. DB::transaction():
    │     a. Generate tracking_code (APP-20240115-00001)
    │     b. Application::create($data) → status: 'submitted'
    │     c. Store documents via MediaService
    │     d. ApplicationHistory::create(to: 'submitted')
    │
    ▼
Event: ApplicationSubmitted($application)
    │
    ├──► Queue Job: SendApplicationConfirmation
    │         Email/WA ke pemohon dengan tracking code
    │
    └──► Queue Job: NotifyAdminNewApplication
              Notifikasi ke admin
    │
    ▼
Response 201: {
  "success": true,
  "data": {
    "tracking_code": "APP-20240115-00001",
    "status": "submitted",
    "message": "Simpan kode tracking Anda"
  }
}
    │
    ▼
Flutter → Tampilkan tracking code ke user
```

### 7.4 Flow: Admin Memvalidasi Pengajuan

```
Admin (Web Dashboard)
    │
    │  1. GET /admin/applications → List permohonan baru
    │
    ▼
    │  2. Klik detail → GET /admin/applications/{id}
    │     Tampil: data pemohon, dokumen, history
    │
    ▼
Admin Review Dokumen
    │
    │  3. Admin klik "Setujui" atau "Tolak"
    │     POST /admin/applications/{id}/review
    │     Body: {action: 'approved', notes: '...'}
    │
    ▼
ApplicationController::review(ReviewApplicationRequest $request)
    │
    ▼
ApplicationService::changeStatus($application, $newStatus, $notes)
    │
    │  4. Validate transition:
    │     ApplicationStatus::canTransitionTo() → check valid
    │
    │  5. DB::transaction():
    │     a. $application->update(['status' => $newStatus])
    │     b. ApplicationHistory::create({
    │           from: 'under_review',
    │           to: 'approved',
    │           notes: $notes,
    │           changed_by: auth()->id()
    │        })
    │     c. ActivityLog::record(...)
    │
    ▼
Event: ApplicationStatusChanged($application, $oldStatus, $newStatus)
    │
    └──► Queue Job: SendStatusNotification
              → Email/SMS ke pemohon
    │
    ▼
Response: redirect ke dashboard dengan success message
```

### 7.5 Flow: User Tracking Status

```
Flutter App
    │
    │  User input tracking code: APP-20240115-00001
    │
    ▼
    │  GET /api/v1/applications/track/APP-20240115-00001
    │
    ▼
ApplicationApiController::track($trackingCode)
    │
    ▼
ApplicationRepository::findByTrackingCode($code)
    │  → Application::with(['histories', 'kbliItem'])
    │              ->where('tracking_code', $code)->firstOrFail()
    │
    ▼
ApplicationTrackingResource::make($application)
    │  → Transform: hanya data tracking yang relevan
    │    TIDAK expose: admin_notes internal, NIK penuh, dll
    │
    ▼
JSON Response:
{
  "tracking_code": "APP-20240115-00001",
  "status": "approved",
  "status_label": "Disetujui",
  "kbli": { "code": "10110", "name": "..." },
  "submitted_at": "2024-01-15T08:00:00Z",
  "updated_at": "2024-01-20T14:30:00Z",
  "timeline": [
    { "status": "submitted", "date": "2024-01-15", "note": "Pengajuan diterima" },
    { "status": "under_review", "date": "2024-01-17", "note": "Sedang ditinjau" },
    { "status": "approved", "date": "2024-01-20", "note": "Pengajuan disetujui" }
  ]
}
    │
    ▼
Flutter → Render timeline status visual
```

---

## 8. Security & Scalability

### 8.1 Authentication Strategy

```php
// Phase 1: Admin menggunakan Laravel Sanctum (Session)
// config/sanctum.php — sudah terintegrasi Laravel 11

// Admin login route
Route::post('/admin/auth/login', [AuthController::class, 'login'])
     ->middleware('throttle:5,1'); // Max 5 attempt per menit

// Sanctum SPA authentication
Route::middleware(['auth:sanctum'])->group(function () {
    // Admin routes
});

// Phase 2: API Token untuk mobile user (pemohon login)
// Generate token saat register/login
$token = $user->createToken('flutter-app', ['read', 'application:write'])->plainTextToken;
```

### 8.2 Role & Permission System

```php
// Roles yang didefinisikan:
// - superadmin: akses penuh
// - admin: CRUD semua + validasi pengajuan
// - operator: view + validasi pengajuan saja
// - viewer: read-only

// Middleware check
Route::middleware(['role:admin,superadmin'])->group(function () {
    // Hanya admin & superadmin
});

// Policy-based authorization
class ApplicationPolicy
{
    public function review(User $user, Application $application): bool
    {
        return $user->hasRole(['admin', 'superadmin', 'operator'])
            && $application->status === ApplicationStatus::Submitted;
    }
}
```

### 8.3 Rate Limiting

```php
// config/api.php & AppServiceProvider

RateLimiter::for('api', function (Request $request) {
    return [
        // Global: 60 request per menit per IP
        Limit::perMinute(60)->by($request->ip()),
        // Per endpoint sensitif
        Limit::perMinute(10)->by('submit:'.$request->ip()),
    ];
});

RateLimiter::for('admin-api', function (Request $request) {
    return Limit::perMinute(300)->by($request->user()?->id ?: $request->ip());
});
```

### 8.4 File Upload Security

```php
// MediaService.php
class MediaService
{
    private const ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/webp',
        'application/pdf',
    ];

    private const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5MB

    public function validateAndStore(UploadedFile $file, string $collection): Media
    {
        // 1. Validate MIME type (bukan hanya extension)
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new InvalidFileTypeException();
        }

        // 2. Validate size
        if ($file->getSize() > self::MAX_SIZE_BYTES) {
            throw new FileTooLargeException();
        }

        // 3. Scan virus (opsional, integrate ClamAV)
        // $this->virusScanner->scan($file->path());

        // 4. Generate safe filename (JANGAN gunakan original filename)
        $filename = Str::uuid() . '.' . $file->extension();

        // 5. Store di private disk (tidak public URL langsung)
        $path = $file->storeAs(
            'documents/' . date('Y/m'),
            $filename,
            'private'
        );

        // 6. Generate signed URL untuk akses (sementara)
        return Media::create([...]);
    }
}
```

### 8.5 Queue System

```php
// Jobs yang berjalan di background (tidak block response)

// 1. Notifikasi email/WA setelah submit
class SendApplicationConfirmation implements ShouldQueue
{
    public int $tries = 3;
    public int $backoff = 60; // Retry setelah 60 detik

    public function handle(): void
    {
        // Send email/WhatsApp notification
    }
}

// 2. Generate laporan statistik
class GenerateDashboardStats implements ShouldQueue
{
    // Jalankan setiap jam via schedule
}

// config/queue.php — Gunakan Redis
'default' => env('QUEUE_CONNECTION', 'redis'),

// .env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 8.6 Caching Strategy

```php
// Three-tier caching approach

// TIER 1: Route-level cache (nginx/CDN) → Static assets, public pages

// TIER 2: Application cache (Redis)
class KbliService
{
    public function getCategories(): Collection
    {
        return Cache::remember('kbli:categories:all', now()->addHour(), function () {
            return KbliCategory::with('sections')->active()->get();
        });
    }

    public function invalidateKbliCache(): void
    {
        Cache::forget('kbli:categories:all');
        Cache::tags(['kbli'])->flush(); // Jika pakai cache tags
    }
}

// TIER 3: Query cache (Eloquent level)
// Untuk query sangat berat, gunakan remember() di repository

// TTL Strategy:
// KBLI data:           1 jam  (jarang berubah)
// Berita list:         15 menit
// Dashboard stats:     5 menit
// Pengumuman aktif:    10 menit
// Settings:            30 menit
```

### 8.7 Logging & Audit Trail

```php
// ActivityLogService.php
class ActivityLogService
{
    public function log(
        string $action,
        string $module,
        ?Model $subject = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $description = ''
    ): void {
        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'module'       => $module,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'description'  => $description,
            'old_values'   => $oldValues,
            'new_values'   => $newValues,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    }
}

// Gunakan via Trait di controller
trait HasActivityLog
{
    protected function logActivity(string $action, Model $subject, ...$args): void
    {
        app(ActivityLogService::class)->log($action, $this->module, $subject, ...$args);
    }
}
```

### 8.8 Future Microservices Migration Path

```
Current: Modular Monolith
    │
    │  Saat traffic meningkat atau tim berkembang:
    │
Phase 1 → Pisahkan Notification module
    │       (paling tidak bergantung ke modul lain)
    │       Deploy sebagai service terpisah
    │       Komunikasi via REST/Queue
    │
Phase 2 → Pisahkan KBLI module (data statis, cache-heavy)
    │       Deploy dengan CDN
    │
Phase 3 → Pisahkan Application module
    │       Database sendiri (application_*)
    │       Event-driven communication
    │
Result: Microservices Architecture
        (setiap modul sudah siap sejak awal)
```

---

## 9. Technology Stack

### 9.1 Stack Lengkap

| Layer | Teknologi | Versi | Alasan |
|---|---|---|---|
| **Backend Framework** | Laravel | 11.x | Mature, ekosistem kaya, cocok government |
| **PHP** | PHP | 8.3+ | Performance, fitur modern (Fibers, Enums) |
| **Mobile** | Flutter | 3.x | Cross-platform, performance native |
| **Database Utama** | MySQL | 8.0 | Luas digunakan, ekosistem besar, dukungan hosting lokal sangat baik |
| **Cache & Queue** | Redis | 7.x | Fast, reliable, pub/sub support |
| **Web Server** | Nginx | 1.25 | Reverse proxy, SSL, load balancing |
| **File Storage** | MinIO (dev) / AWS S3 (prod) | — | S3-compatible, scalable |
| **Process Manager** | Supervisor | — | Queue worker management |
| **Search** | Meilisearch | — | Fast full-text search untuk KBLI |
| **Admin UI** | Blade + Livewire + Alpine.js | — | Server-side, cepat, tidak butuh SPA |
| **CSS Framework** | Tailwind CSS | 3.x | Utility-first, mudah custom |
| **Deployment** | Docker + Docker Compose | — | Consistency dev/prod |
| **CI/CD** | GitHub Actions | — | Auto deploy, testing |
| **Monitoring** | Laravel Telescope (dev) | — | Debug & profiling |
| **Error Tracking** | Sentry | — | Production error monitoring |
| **SSL** | Let's Encrypt + Certbot | — | Free, auto-renew |

### 9.2 Flutter Dependencies

```yaml
# pubspec.yaml (Flutter)
dependencies:
  flutter:
    sdk: flutter
  dio: ^5.4.0              # HTTP client (lebih powerful dari http)
  get_it: ^7.6.0           # Dependency injection
  go_router: ^13.0.0       # Declarative routing
  riverpod: ^2.4.0         # State management
  freezed: ^2.4.0          # Immutable models
  json_annotation: ^4.8.0  # JSON serialization
  cached_network_image: ^3.3.0  # Image caching
  flutter_secure_storage: ^9.0.0  # Secure token storage (Phase 2)
  connectivity_plus: ^5.0.0  # Network status
  intl: ^0.18.0            # i18n, date formatting
```

### 9.3 Laravel Packages

```json
// composer.json (PHP)
{
  "require": {
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.0",
    "spatie/laravel-permission": "^6.0",
    "spatie/laravel-query-builder": "^5.0",
    "spatie/laravel-medialibrary": "^11.0",
    "spatie/laravel-activitylog": "^4.0",
    "laravel/telescope": "^5.0",
    "meilisearch/meilisearch-php": "^1.6"
  }
}
```

---

## 10. Roadmap Pengembangan

### Phase 1 — Foundation (Bulan 1-2)

**Goal:** Backend berjalan, data KBLI tersedia, admin bisa login.

```
✅ Setup Laravel 11 project
✅ Setup MySQL 8.0 + Redis
✅ Migration semua tabel utama
✅ Seeder: Roles, Permissions, Admin user
✅ Seeder: Data KBLI (import dari BPS)
✅ Auth admin (login/logout/session)
✅ CRUD KBLI Categories & Items
✅ CRUD News & Announcements
✅ Activity Log middleware
✅ REST API v1 — endpoint KBLI (GET only)
✅ REST API v1 — endpoint News & Announcements
✅ API Response format standardization
✅ Rate limiting
✅ Basic admin dashboard (tampilan data)
✅ File upload (dokumen & gambar)
✅ Unit tests: Models, Services
✅ Dokumentasi API (Swagger/Postman)
```

### Phase 2 — Public API (Bulan 3-4)

**Goal:** Flutter app terhubung penuh, pengajuan bisa dilakukan.

```
✅ Flutter app scaffold (routing, state management)
✅ Halaman daftar & detail KBLI
✅ Halaman berita & pengumuman
✅ Form pengajuan permohonan (Flutter)
✅ Upload dokumen dari Flutter
✅ Tracking status permohonan
✅ Admin: validasi & review pengajuan
✅ Status history timeline
✅ Notifikasi email (queue + mail driver)
✅ Redis caching untuk endpoint publik
✅ Search KBLI (Meilisearch integration)
✅ Pagination & filtering API
✅ Integration tests: API endpoints
✅ Flutter: offline mode dasar
```

### Phase 3 — Dashboard & Monitoring (Bulan 5-6)

**Goal:** Admin punya insight data, sistem lebih observable.

```
✅ Dashboard statistik real-time
   - Total pengajuan per hari/bulan
   - Distribusi status pengajuan
   - KBLI terpopuler
   - Grafik tren
✅ Export laporan (Excel/PDF)
✅ Admin: manajemen user & role
✅ Audit log UI (filter, search, export)
✅ Notifikasi in-app (Flutter)
✅ Push notification (FCM)
✅ Full-text search di Flutter
✅ Laravel Telescope (profiling)
✅ Sentry integration
✅ Performance tuning (N+1 queries, indexes)
✅ API versioning v2 strategy
✅ Load testing (k6/JMeter)
```

### Phase 4 — Advanced Government Features (Bulan 7+)

**Goal:** Fitur lengkap sistem informasi pemerintahan.

```
🔜 Login masyarakat/pemohon (Flutter auth)
🔜 Profil pemohon & history pengajuan
🔜 Digital signature dokumen
🔜 Integrasi Dukcapil API (validasi NIK)
🔜 Integrasi OSS (Online Single Submission)
🔜 Multi-tenancy (support multi dinas/daerah)
🔜 Approval workflow multi-level (kabid → kadis)
🔜 SMS/WhatsApp notification (Twilio/Fonnte)
🔜 Laporan otomatis ke pimpinan
🔜 Public API untuk integrasi pihak ketiga
🔜 Mobile app versi web (PWA)
🔜 Arsip digital dokumen (long-term storage)
🔜 Enkripsi data sensitif (NIK, NPWP) at-rest
🔜 Disaster recovery & backup otomatis
🔜 Read replica MySQL (master-slave replication)
🔜 CDN untuk static assets
🔜 Kubernetes deployment (skala besar)
```

---

## Appendix: Environment Configuration

```bash
# .env — Development

APP_NAME="Sistem Informasi Pemerintahan"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipemerintahan_dev
DB_USERNAME=root
DB_PASSWORD=secret
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

FILESYSTEM_DISK=local
AWS_BUCKET=sipemerintahan-dev
# (MinIO untuk local dev)
AWS_ENDPOINT=http://localhost:9000

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io

# API Config
API_RATE_LIMIT_PUBLIC=60    # per minute
API_RATE_LIMIT_ADMIN=300    # per minute
API_CACHE_TTL_KBLI=3600     # 1 jam
API_CACHE_TTL_NEWS=900      # 15 menit
```

---

*Blueprint ini dirancang untuk dikembangkan secara bertahap. Fokus pada Phase 1 dan Phase 2 untuk mendapatkan MVP yang berfungsi, kemudian iterasi sesuai kebutuhan nyata di lapangan.*

*Dibuat menggunakan pendekatan Software Architecture profesional — scalable, maintainable, dan siap dikembangkan jangka panjang.*
