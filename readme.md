# Geprekin - Manajemen Restoran (PHP Native)

Aplikasi manajemen restoran sederhana menggunakan PHP native (tanpa framework).
Fitur utama mencakup halaman admin & pelanggan, serta integrasi pembayaran (Midtrans).

---

## Fitur
- Auth: login / register / logout
- Admin panel (manajemen data)
- Pelanggan (fitur sisi user)
- Integrasi pembayaran (Midtrans)
- Assets (CSS/JS)

---

## Tech Stack
- PHP (native)
- MySQL/MariaDB
- Composer (dependency management)
- Midtrans (payment gateway)
- Ngrok (untuk callback/webhook Midtrans saat local)

---

## Requirement
- PHP >= 7.x (disarankan 8.x)
- MySQL/MariaDB
- Composer
- Web server: Apache (XAMPP/Laragon) atau Nginx
- (Opsional) Ngrok

---

## Cara Menjalankan (Local)

### 1) Clone repo
```bash
git clone <repo-url>
cd <folder-project>
```
### 2) Install dependency (Composer)
```bash
composer install
```
### 3) Setup Database
```bash
mysql -u root -p manajemen_restoran < "manajemen_restoran.sql"
```
### 4) Konfigurasi koneksi database
```bash
git clone <repo-url>
cd <folder-project>
```
### 5) Jalankan di localhost
```bash
git clone <repo-url>
cd <folder-project>
```
