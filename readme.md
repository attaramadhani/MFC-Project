### 1) Install Dependency

```bash
composer install
npm install
```

---

### 2) Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Konfigurasi:

* Database
* Midtrans (SERVER_KEY & CLIENT_KEY)

---

### 3) Setup Database

```bash
php artisan migrate --seed
```

---

### 4) Jalankan Server

```bash
php artisan serve
```

Akses:

```
http://127.0.0.1:8000
```

---

### 5) Midtrans Callback (Local)

```bash
ngrok http 8000
```

---
