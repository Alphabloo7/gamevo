# GAMEVO - Gaming Portal Website

GAMEVO adalah portal gaming yang menyediakan akses ke berbagai game dan layanan gaming dalam satu platform. Website ini dibangun dengan PHP, HTML, CSS, dan JavaScript.

## 🎮 Fitur Utama

- **Landing Page Modern** - Desain responsive dengan tema dark modern
- **Navigasi Intuitif** - Menu navigasi dan search functionality
- **Product Grid** - Tampilan produk game dalam grid layout
- **Responsive Design** - Kompatibel dengan semua ukuran screen
- **Dynamic Interactions** - JavaScript untuk interaksi user yang smooth

## 📁 Struktur Project

```
gamevo/
├── index.php                 # Halaman utama
├── assets/
│   ├── css/
│   │   ├── style.css        # Stylesheet utama
│   │   └── responsive.css   # Media queries untuk responsive
│   ├── js/
│   │   └── main.js          # JavaScript utama
│   └── images/              # Folder untuk gambar/aset
├── includes/                # PHP includes dan utilities
└── README.md               # Dokumentasi
```

## 🚀 Cara Menjalankan

### Requirement
- PHP 7.4 atau lebih tinggi
- Web Server (Apache, Nginx, atau PHP Built-in Server)

### Setup Local Development

#### Menggunakan PHP Built-in Server
```bash
cd c:\Users\acer\Downloads\gamevo
php -S localhost:8000
```

Kemudian buka browser dan akses: **http://localhost:8000**

#### Menggunakan Apache/Xampp
1. Copy folder `gamevo` ke direktori htdocs (atau www)
2. Buka http://localhost/gamevo di browser

## 🎨 Customization

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
