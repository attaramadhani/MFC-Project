# Geprekin - Manajemen Restoran (Laravel Version)

Aplikasi manajemen restoran berbasis **Laravel** yang
Menyediakan fitur admin & pelanggan serta integrasi pembayaran menggunakan **Midtrans**.

---

## 🚀 Fitur

* 🔐 Authentication (Login, Register, Logout)
* 👨‍💼 Admin Panel (manajemen menu & transaksi)
* 👥 Pelanggan (order & pembayaran)
* 💳 Integrasi Midtrans (payment gateway)
* 📦 Struktur MVC (Laravel)
* 🗄️ Database Migration & Seeder

---

## 🛠️ Tech Stack

* Laravel
* PHP >= 8.x
* MySQL / MariaDB
* Composer
* Node.js & NPM
* Midtrans
* Ngrok (opsional)

---

## 📦 Requirement

* PHP >= 8.x
* Composer
* MySQL / MariaDB
* Node.js & NPM

---

## ⚙️ Cara Menjalankan (Local)

### 1) Clone Repository

```bash
git clone https://github.com/andhikkadd/Simple-Resto-App.git
cd Simple-Resto-App
```

---

### 2) Install Dependency

```bash
composer install
npm install
```

---

### 3) Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Konfigurasi:

* Database
* Midtrans (SERVER_KEY & CLIENT_KEY)

---

### 4) Setup Database

```bash
php artisan migrate --seed
```

Perintah ini akan:

* Membuat struktur tabel (migration)
* Mengisi data awal (seeder)

---

### 5) Jalankan Server

```bash
php artisan serve
```

Akses:

```
http://127.0.0.1:8000
```

---

### 6) Midtrans Callback (Local)

```bash
ngrok http 8000
```

Gunakan URL dari ngrok untuk konfigurasi webhook Midtrans.

---

## 📁 Struktur Project

```
app/
routes/
resources/
database/
public/
```

---

## ⚠️ Catatan

* Project ini dibuat untuk pembelajaran dan pengembangan pribadi.
* Jalankan `php artisan migrate --seed` setelah clone project