# 🚀 DICATAT.IN - Backend API

Repositori ini berisi *source code* Backend REST API untuk aplikasi **DICATAT.IN** (Ekosistem PROTEK). Dibangun menggunakan Laravel dan diorkestrasi secara penuh dengan Docker untuk memastikan konsistensi *environment* pengembangan lintas tim (Frontend, Backend, dan Machine Learning).

API ini menangani otentikasi pengguna, manajemen *workspaces*, serta bertugas sebagai jembatan komunikasi *asynchronous* (melalui sistem Queue) dengan *service* AI/Machine Learning untuk memproses gambar catatan.

---

## 🛠️ Tech Stack Utama

| Komponen | Teknologi |
|---|---|
| **Framework** | Laravel (PHP) |
| **Database** | PostgreSQL |
| **Authentication** | Laravel Sanctum (Token-based) |
| **Storage** | Cloudinary (Manajemen Aset Gambar) |
| **API Documentation** | Swagger / OpenAPI 3.0 |
| **Infrastructure** | Docker & Docker Compose |

---

## ⚙️ Prasyarat (Prerequisites)

Sebelum memulai, pastikan perangkatmu sudah terinstal:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) *(Wajib menyala saat pengembangan)*
- [Git](https://git-scm.com/)

---

## 🚀 Instalasi & Setup Lokal

Ikuti langkah-langkah berikut untuk menjalankan *server* secara lokal. Semua *service* (Backend, Database, Queue, dan ML) akan berjalan otomatis di dalam *container*.

### 1. Clone Repositori

```bash
git clone <URL_REPO_BE_DICATATIN>
cd be-dicatatin
```

### 2. Konfigurasi Environment (`.env`)

Salin file konfigurasi bawaan dan sesuaikan nilainya:

```bash
cp .env.example .env
```

> ⚠️ **PENTING:** Buka file `.env` dan pastikan konfigurasi database mengarah ke nama *container* Docker, bukan `localhost`:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=dicatatin_db
DB_USERNAME=postgres
DB_PASSWORD=password_rahasiamu

# Konfigurasi Cloudinary
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
```

### 3. Eksekusi Docker Compose

Jalankan perintah ini di dalam folder workspace utama (yang berisi `docker-compose.yml`):

```bash
docker compose up -d
```

> **Catatan:** Proses pertama kali akan memakan waktu sedikit lebih lama untuk membangun *image* dan mengunduh dependensi.

### 4. Setup Database & Key Laravel

Masuk ke dalam *container* backend untuk menjalankan migrasi dan *generate key*:

```bash
docker exec dicatatin-backend php artisan key:generate
docker exec dicatatin-backend php artisan migrate
```

---

## 📚 Dokumentasi API (Swagger)

Aplikasi ini menggunakan antarmuka interaktif **Swagger UI** untuk memudahkan *testing* dan eksplorasi rute API. Setelah *container* berhasil menyala, buka browser dan akses:

👉 **http://localhost:8000/docs**

Di dalam Swagger, kamu bisa langsung mencoba endpoint otentikasi (`/auth/register`, `/auth/login`) dan menyalin **Bearer Token** yang didapat ke tombol **"Authorize"** 🔒 untuk mengakses endpoint terproteksi (seperti `/workspaces`).

---

## 🧑‍💻 Cheatsheet Perintah Terminal

Berikut adalah beberapa perintah yang sering digunakan saat *debugging* atau mengembangkan fitur baru di dalam ekosistem Docker.

**Melihat log error backend secara real-time:**

```bash
docker compose logs -f backend
```

**Restart hanya service backend** *(berguna jika ada proses yang "nyangkut")*:

```bash
docker compose restart backend
```

**Membersihkan cache total Laravel** *(wajib dilakukan jika mengubah file `.env`)*:

```bash
docker exec dicatatin-backend php artisan optimize:clear
```

**Masuk ke Database Tinker** *(mengecek data via CLI)*:

```bash
docker exec -it dicatatin-backend php artisan tinker
```
