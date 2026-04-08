# GAMEVO - Admin Dashboard Guide

## 🎯 Akses Admin Panel

**URL**: `http://localhost/gamevo/admin_login.php`

### Credentials Default:
- **Username**: `Admin`
- **Password**: `gamevoadmin`

---

## 📊 Dashboard Admin

### Menu Utama

1. **Dashboard** 
   - Tampilan overview statistik penjualan
   - Total revenue (semua transaksi)
   - Revenue hari ini
   - Total orders dengan breakdown status
   - Total users terdaftar
   - 10 recent orders

2. **Daftar Order** (`admin_orders.php`)
   - Lihat semua orders
   - Filter berdasarkan status (Pending, Processing, Completed, Cancelled)
   - Update status order secara real-time
   - Informasi detail order

3. **Kelola Users** (`admin_users.php`)
   - Lihat data semua users terdaftar
   - Informasi username, email, full name
   - Status aktif/nonaktif
   - Tanggal registrasi

4. **Kelola Produk** (`admin_products.php`)
   - Lihat semua produk/game
   - Informasi harga, kategori
   - Status aktif/nonaktif
   - Tanggal produk dibuat

5. **Pengaturan** (`admin_settings.php`)
   - Informasi profil admin
   - Sistem information (PHP version, Database, OS, dll)
   - Catatan dan guidance

---

## 📈 Statistik & Fitur Utama

### Statistics Card di Dashboard

| Card | Keterangan |
|------|-----------|
| **Total Revenue** | Total pendapatan dari semua transaksi yang completed |
| **Revenue Hari Ini** | Pendapatan dari transaksi yang completed hari ini |
| **Total Orders** | Breakdown: total, completed, pending orders |
| **Total Users** | Jumlah users yang terdaftar di sistem |

### Order Management

**Status Orders:**
- 🟡 **Pending** - Order baru yang belum diproses
- 🔵 **Processing** - Order sedang diproses
- ✅ **Completed** - Order selesai
- ❌ **Cancelled** - Order dibatalkan

**Cara Update Status:**
1. Buka halaman "Daftar Order"
2. Gunakan dropdown di kolom "Aksi"
3. Pilih status baru
4. Klik "Update"
5. Selesai! Status akan berubah dan revenue akan terupdate jika completed

---

## 🗄️ Database Tables

### Tabel: admin
| Column | Tipe | Deskripsi |
|--------|------|-----------|
| id | INT | Primary Key |
| username | VARCHAR(50) | Username unik admin |
| password | VARCHAR(255) | Password (hashed dengan bcrypt) |
| full_name | VARCHAR(100) | Nama lengkap |
| email | VARCHAR(100) | Email unik |
| role | VARCHAR(50) | Role (super_admin, admin) |
| created_at | TIMESTAMP | Waktu pembuatan |
| updated_at | TIMESTAMP | Waktu update terakhir |
| is_active | BOOLEAN | Status aktif/nonaktif |

### Tabel: admin_login_history
| Column | Tipe | Deskripsi |
|--------|------|-----------|
| id | INT | Primary Key |
| admin_id | INT | Foreign Key ke admin |
| login_time | TIMESTAMP | Waktu login |
| logout_time | TIMESTAMP | Waktu logout |
| ip_address | VARCHAR(45) | IP address |
| user_agent | VARCHAR(255) | Browser/device info |

### Tabel: orders
| Column | Tipe | Deskripsi |
|--------|------|-----------|
| id | INT | Primary Key |
| user_id | INT | Foreign Key ke users |
| product_id | INT | Foreign Key ke products |
| quantity | INT | Jumlah pembelian |
| total_price | DECIMAL(10,2) | Total harga |
| status | VARCHAR(50) | Status order |
| payment_method | VARCHAR(50) | Metode pembayaran |
| order_date | TIMESTAMP | Tanggal order |
| completed_date | TIMESTAMP | Tanggal completion |
| notes | TEXT | Catatan tambahan |

### Tabel: products
| Column | Tipe | Deskripsi |
|--------|------|-----------|
| id | INT | Primary Key |
| name | VARCHAR(100) | Nama produk/game |
| category | VARCHAR(50) | Kategori game |
| price | DECIMAL(10,2) | Harga |
| description | TEXT | Deskripsi produk |
| image_url | VARCHAR(255) | URL gambar |
| created_at | TIMESTAMP | Tanggal dibuat |
| is_active | BOOLEAN | Status aktif/nonaktif |

---

## 🔐 Security Features

✅ **Password Hashing** - Menggunakan BCRYPT algorithm
✅ **Session Management** - Secure session handling
✅ **Login History Tracking** - Semua admin login tercatat
✅ **Logout Tracking** - Automatic logout time recording
✅ **SQL Injection Protection** - Prepared statements
✅ **Access Control** - Hanya admin yang bisa akses admin panel

---

## 🔄 Admin Authentication Functions

File: `includes/admin_auth.php`

### Available Functions:

```php
// Login admin
loginAdmin($username, $password)
// Returns: ['success' => bool, 'message' => string]

// Check if logged in
isAdminLoggedIn()
// Returns: bool

// Get current admin
getCurrentAdmin()
// Returns: ['id', 'username', 'full_name', 'role']

// Logout admin
logoutAdmin()

// Require login (redirect if not)
requireAdminLogin()

// Get sales statistics
getSalesStatistics()
// Returns: array with stats

// Get recent orders
getRecentOrders($limit)

// Update order status
updateOrderStatus($order_id, $status)

// Get all orders with filter
getAllOrders($status, $limit, $offset)
```

---

## 🧪 Testing Admin Panel

### Step 1: Akses Login
1. Buka browser: `http://localhost/gamevo/admin_login.php`
2. Masukkan:
   - Username: `Admin`
   - Password: `gamevoadmin`
3. Klik "Login Admin"

### Step 2: Explore Dashboard
- Lihat statistik di dashboard
- Perhatikan revenue dan order counts

### Step 3: Test Order Management
1. Buka "Daftar Order"
2. Ubah status order
3. Lihat perubahan di dashboard

### Step 4: Check Users & Products
1. Buka "Kelola Users" - lihat users terdaftar
2. Buka "Kelola Produk" - lihat products

### Step 5: Logout
1. Klik "Logout" di top right corner
2. Akan redirect ke home page

---

## 💡 Tips & Tricks

### Revenue Calculation
- Hanya orders dengan status **"completed"** yang dihitung
- Revenue today = completed orders pada hari ini
- Revenue total = semua completed orders

### Order Status Flow
```
Pending → Processing → Completed
   ↓
Cancelled (bisa dari state manapun)
```

### Monitoring Users
- Track semua registered users
- Lihat tanggal registrasi
- Monitor user activity

### Managing Products
- Update produk dan harganya
- Set status active/inactive
- Kategori untuk filtering

---

## 🚀 Next Steps (Optional Features)

Untuk meningkatkan admin panel:

1. **Advanced Analytics**
   - Grafik sales trend
   - Chart by product/category
   - Revenue comparison (daily/monthly/yearly)

2. **Bulk Operations**
   - Bulk update order status
   - Bulk activate/deactivate products
   - Export data to CSV/PDF

3. **Email Notifications**
   - Email notification untuk baru orders
   - Email ke customers untuk update order

4. **Add/Edit Features**
   - Tambah admin user baru
   - Edit product details
   - Add product kategori

5. **Audit Log**
   - Track semua actions admin
   - History perubahan data
   - Admin activity report

---

## ❓ Troubleshooting

### "Database Error: Unknown database 'gamevo_db'"
- Jalankan `database.sql` lagi
- Verifikasi MySQL running

### "Username atau password salah"
- Pastikan username: `Admin` (capital A)
- Password: `gamevoadmin`
- Cek caps lock

### Session timeout
- Login lagi
- Clear browser cache/cookies

### Logout tidak berfungsi
- Cek koneksi database
- Verify file `admin_logout.php` exists

---

✅ **Admin Dashboard GAMEVO Siap Digunakan!**

Admin dapat mengelola seluruh sistem dari sini. 🎮
