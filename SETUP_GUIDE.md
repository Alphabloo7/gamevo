# GAMEVO - Setup Guide untuk Login & Register

## 📋 File yang Telah Dibuat

1. **config/database.php** - Konfigurasi koneksi database
2. **database.sql** - Script untuk membuat database dan tabel
3. **includes/auth.php** - Fungsi-fungsi autentikasi
4. **login.php** - Halaman login
5. **register.php** - Halaman registrasi
6. **profile.php** - Halaman profil pengguna
7. **logout.php** - Script untuk logout
8. **index.php** - Updated with auth links in navbar

## 🗄️ Setup Database

### Langkah 1: Buat Database
1. Buka **phpMyAdmin** (http://localhost/phpmyadmin)
2. Klik tab **SQL** di menu atas
3. Copy seluruh kode dari file `database.sql`
4. Paste ke dalam textbox SQL Query
5. Klik **Go** untuk execute

Atau gunakan command line MySQL:
```bash
mysql -u root -p < c:\xampp\htdocs\gamevo\database.sql
```

### Langkah 2: Verifikasi Database

Buka phpMyAdmin dan pastikan:
- Database `gamevo_db` sudah terbuat
- Tabel `users` dan `login_history` sudah ada
- Structure tabel sudah sesuai

## 🔐 Konfigurasi Database Connection

File: `config/database.php`

**Default settings** (untuk XAMPP):
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gamevo_db');
```

Jika Anda menggunakan password untuk user root, ubah:
```php
define('DB_PASS', 'password_anda');
```

## 🚀 Cara Menggunakan

### 1. **Halaman Registrasi**
- URL: `http://localhost/gamevo/register.php`
- User bisa membuat akun baru
- Validation:
  - Username: min 3 karakter, harus unique
  - Email: harus valid dan unique
  - Password: min 6 karakter
  - Full Name: required

### 2. **Halaman Login**
- URL: `http://localhost/gamevo/login.php`
- User login dengan username dan password
- Session akan dibuat otomatis

### 3. **Halaman Profil** (Protected)
- URL: `http://localhost/gamevo/profile.php`
- Hanya bisa diakses jika sudah login
- Menampilkan informasi user

### 4. **Logout**
- URL: `http://localhost/gamevo/logout.php`
- Menghapus session dan redirect ke home

## 📊 Database Schema

### Tabel: users
| Column | Type | Constraints |
|--------|------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT |
| username | VARCHAR(50) | UNIQUE, NOT NULL |
| email | VARCHAR(100) | UNIQUE, NOT NULL |
| password | VARCHAR(255) | NOT NULL |
| full_name | VARCHAR(100) | - |
| avatar | VARCHAR(255) | - |
| bio | TEXT | - |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |
| updated_at | TIMESTAMP | ON UPDATE |
| is_active | BOOLEAN | DEFAULT TRUE |

### Tabel: login_history
| Column | Type | Constraints |
|--------|------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT |
| user_id | INT | FOREIGN KEY (users.id) |
| login_time | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |
| ip_address | VARCHAR(45) | - |
| user_agent | VARCHAR(255) | - |

## 🛡️ Security Features

✅ **Password Hashing** - Menggunakan PHP `password_hash()` dengan BCRYPT
✅ **Input Validation** - Semua input divalidasi
✅ **SQL Injection Protection** - Menggunakan Prepared Statements
✅ **Session Management** - Session-based authentication
✅ **Email & Username Uniqueness** - Validasi di database level

## 🔄 Authentication Functions

File: `includes/auth.php`

### Tersedia functions:

1. **registerUser($username, $email, $password, $full_name)**
   - Returns: `['success' => bool, 'message' => string]`

2. **loginUser($username, $password)**
   - Returns: `['success' => bool, 'message' => string]`

3. **isLoggedIn()**
   - Returns: `bool`

4. **getCurrentUser()**
   - Returns: `['id' => int, 'username' => string, 'full_name' => string]` atau `null`

5. **logoutUser()**
   - Returns: `['success' => bool, 'message' => string]`

6. **requireLogin()**
   - Redirect ke login jika belum login

7. **redirectIfLoggedIn()**
   - Redirect ke home jika sudah login

## 📱 Navigation Bar Updates

Di `index.php`, navbar akan menampilkan:

**Jika NOT logged in:**
- Tombol LOGIN (biru)
- Tombol DAFTAR (gradient purple)

**Jika logged in:**
- Avatar dengan inisial nama
- Username (link ke profile)
- Tombol LOGOUT (merah)

## 🧪 Testing

### Test Registrasi:
1. Buka http://localhost/gamevo/register.php
2. Isi form dengan data baru:
   - Full Name: John Doe
   - Username: johndoe
   - Email: john@example.com
   - Password: password123
3. Klik Daftar
4. Akan redirect ke login page

### Test Login:
1. Buka http://localhost/gamevo/login.php
2. Masukkan credentials yang baru dibuat
3. Klik Login
4. User akan masuk dan redirect ke home
5. Navbar akan menampilkan info user

### Test Profile:
1. Setelah login, klik username di navbar
2. Akan membuka halaman profil

### Test Logout:
1. Klik tombol LOGOUT
2. Session akan dihapus dan redirect ke home

## 🐛 Troubleshooting

### "Connection failed"
- Pastikan MySQL/XAMPP running
- Cek DB_HOST, DB_USER, DB_PASS di `config/database.php`

### "Username sudah digunakan"
- Username harus unique, gunakan username lain

### "Password tidak cocok (saat registrasi)"
- Pastikan password dan konfirmasi sama

### Session tidak bertahan
- Pastikan cookies enabled di browser
- Check file permissions untuk session folder

## 📝 Next Steps (Optional)

Untuk melengkapi sistem ini, Anda bisa menambahkan:

1. **Email Verification** - Verifikasi email saat registrasi
2. **Password Reset** - Fitur lupa password
3. **Edit Profile** - Update informasi user
4. **Profile Picture** - Upload avatar
5. **Two Factor Authentication** - Keamanan ekstra
6. **Remember Me** - Checkbox untuk tetap login

---

✅ **Sistem Login & Register GAMEVO sudah siap digunakan!**
