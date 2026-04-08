# GAMEVO - Gaming Portal Platform

GAMEVO adalah platform gaming portal lengkap dengan sistem login/register user dan admin dashboard untuk mengelola penjualan. Website ini dibangun dengan PHP, MySQL, HTML, CSS, dan JavaScript.

## 🎮 Fitur Utama

### User Features
- ✅ **User Registration** - Registrasi akun dengan validasi email dan password
- ✅ **User Login** - Login dengan unified authentication system
- ✅ **User Profile** - Dashboard profil pengguna
- ✅ **Games/Products** - Tampilan produk game dalam grid layout
- ✅ **Responsive Design** - Mobile-friendly interface

### Admin Features
- ✅ **Admin Dashboard** - Statistik penjualan dan revenue real-time
- ✅ **Order Management** - Kelola status orders dengan update real-time
- ✅ **User Management** - Monitor semua registered users
- ✅ **Product Management** - Kelola produk/game yang tersedia
- ✅ **Login History** - Tracking admin login/logout activities

## 📁 Struktur File

```
gamevo/
├── index.php                 # Halaman utama / Homepage
├── login.php                 # Unified login page (user & admin)
├── register.php              # User registration page
├── logout.php                # Logout handler
├── profile.php               # User profile page (protected)
├── 
├── admin_dashboard.php       # Admin dashboard
├── admin_orders.php          # Order management
├── admin_users.php           # User management
├── admin_products.php        # Product management
├── admin_settings.php        # Admin settings
├── admin_logout.php          # Admin logout
├── 
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── auth.php              # User authentication functions
│   └── admin_auth.php        # Admin authentication & statistics
├── assets/
│   ├── css/
│   │   ├── style.css         # Main stylesheet
│   │   └── responsive.css    # Responsive design CSS
│   ├── js/
│   │   └── main.js           # JavaScript functionality
│   └── images/               # Image assets
├── database.sql              # Database schema
├── SETUP_GUIDE.md            # Setup & installation guide
├── ADMIN_GUIDE.md            # Admin panel documentation
└── README.md                 # This file
```

## 🚀 Cara Memulai

### 1. Prerequisites
- PHP 7.4 atau lebih tinggi
- MySQL/MariaDB
- XAMPP atau web server lainnya

### 2. Instalasi Database
```bash
# Buka terminal di folder project
cd c:\xampp\htdocs\gamevo

# Jalankan SQL script
Get-Content database.sql | mysql -u root
```

### 3. Konfigurasi Database
Edit file `config/database.php` jika diperlukan:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gamevo_db');
```

### 4. Akses Website
```
Homepage:  http://localhost/gamevo/index.php
```

## 🔐 Default Credentials

### User Example
Bisa buat account baru di halaman register

### Admin Account
- **Username**: `Admin`
- **Password**: `gamevoadmin`
- **Akses**: Double-click logo GAMEVO di homepage → login dengan credentials di atas

## 📊 Database Schema

### Main Tables
- **users** - User accounts & profile
- **admin** - Admin accounts
- **orders** - Penjualan/transactions
- **products** - Game products
- **login_history** - User login tracking
- **admin_login_history** - Admin login tracking

Lihat `database.sql` untuk detail schema lengkap.

## 🔒 Security Features

✅ Password hashing dengan BCRYPT
✅ SQL Injection protection dengan Prepared Statements
✅ Session-based authentication
✅ Admin access control
✅ Input validation & sanitization
✅ CSRF token ready (optional)

## 📝 File Dokumentasi

- **SETUP_GUIDE.md** - Panduan setup lengkap username/password, instalasi database
- **ADMIN_GUIDE.md** - Dokumentasi admin panel dan features

## 🔌 API Functions

### User Authentication (`includes/auth.php`)
- `registerUser()` - Register user baru
- `unifiedLogin()` - Login user/admin
- `isLoggedIn()` - Check user session
- `getCurrentUser()` - Get user info
- `logoutUser()` - Logout user

### Admin Functions (`includes/admin_auth.php`)
- `isAdminLoggedIn()` - Check admin session
- `getCurrentAdmin()` - Get admin info
- `getSalesStatistics()` - Get sales data
- `getRecentOrders()` - Get recent orders
- `updateOrderStatus()` - Update order status
- `getAllOrders()` - Get all orders with filters

## 🛠️ Development

### Running with PHP Built-in Server
```bash
cd c:\xampp\htdocs\gamevo
php -S localhost:8000
# Akses: http://localhost:8000
```

### Requirements untuk Development
- Text Editor / IDE (VS Code recommended)
- Git untuk version control
- Postman (untuk API testing - optional)

## 📱 Browser Compatibility

- ✅ Chrome/Edge
- ✅ Firefox
- ✅ Safari
- ✅ Mobile browsers

## 🔄 Update History

- **v1.0** - User login/register & Admin dashboard
  - Unified authentication system
  - Admin statistics & order management
  - User profile dan logout

## 📞 Support

Untuk issues atau pertanyaan, lihat dokumentasi:
- SETUP_GUIDE.md - Troubleshooting
- ADMIN_GUIDE.md - Admin features

---

**Last Updated**: April 8, 2026
**Version**: 1.0.0

### Warna Utama
Edit file `assets/css/style.css` bagian `:root`:
```css
--primary-color: #00d4ff;      /* Warna cyan/primary */
--secondary-color: #0a1428;    /* Warna secondary */
--accent-color: #ffa500;       /* Warna accent */
```

### Menambah Product
Edit `index.php` di section `<!-- Products Grid -->` dan tambah product card baru:
```html
<div class="product-card">
    <div class="product-image">
        <img src="assets/images/nama-game.jpg" alt="Nama Game">
    </div>
    <div class="product-info">
        <h4>Nama Game</h4>
    </div>
</div>
```

## 📝 File Structure

| File | Deskripsi |
|------|-----------|
| `index.php` | Halaman utama / landing page |
| `assets/css/style.css` | Styling utama |
| `assets/css/responsive.css` | Media queries & responsive design |
| `assets/js/main.js` | JavaScript functionality |

## 🔧 Fitur JavaScript

- **Smooth Scroll** - Navigasi yang smooth ke setiap section
- **Active Link Indicator** - Menunjukkan link aktif di navbar
- **Search Functionality** - Fitur pencarian (ready untuk implementasi)
- **Product Selection** - Handler untuk product card clicks

## 📱 Responsive Breakpoints

- **Desktop** - 1200px keatas
- **Tablet** - 768px hingga 1199px  
- **Mobile** - Dibawah 768px

## 🐛 Troubleshooting

### Gambar tidak tampil
- Pastikan gambar ada di folder `assets/images/`
- Check path gambar di HTML

### Style tidak loading
- Clear browser cache (Ctrl+F5)
- Check path CSS di HTML

### JavaScript error
- Open DevTools (F12) dan check Console tab
- Pastikan `assets/js/main.js` ter-load dengan benar

## 📄 License

Hak cipta © 2024 GAMEVO. Semua hak dilindungi.

## 👨‍💻 Pengembang

GAMEVO Development Team

---

**Status**: ✅ Production Ready

**Last Updated**: April 2024
