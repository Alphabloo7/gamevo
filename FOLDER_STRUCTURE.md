# GAMEVO Project Structure

## 📋 Overview

GAMEVO is organized by **role-based folders** for better code organization and maintainability. All pages are grouped by user role (User vs Admin), making it clear which parts of the application belong to which role.

## 📁 Complete Directory Structure

```
gamevo/
├── pages/                           # ROLE-BASED PAGES
│   ├── user/                        # USER PAGES
│   │   ├── login.php                # Unified login (user + admin detection)
│   │   ├── register.php             # User registration
│   │   ├── profile.php              # User profile (protected)
│   │   ├── logout.php               # User logout
│   │   └── top-up.php               # Game top-up / product selection
│   │
│   └── admin/                       # ADMIN PAGES
│       ├── dashboard.php            # Admin dashboard with sales stats
│       ├── orders.php               # Order management UI
│       ├── users.php                # User management UI
│       ├── products.php             # Product/game management UI
│       ├── settings.php             # Admin settings page
│       └── logout.php               # Admin logout
│
├── includes/                        # SHARED PHP FUNCTIONS
│   ├── auth.php                     # User authentication & registerUser(), unifiedLogin()
│   └── admin_auth.php               # Admin auth & getSalesStatistics(), updateOrderStatus()
│
├── config/                          # CONFIGURATION FILES
│   └── database.php                 # MySQL/MariaDB connection config
│
├── assets/                          # STATIC FILES
│   ├── css/
│   │   ├── style.css                # Main stylesheet
│   │   ├── responsive.css           # Mobile responsive styles
│   │   └── topup.css                # Top-up page styles
│   ├── js/
│   │   └── main.js                  # Client-side JavaScript
│   └── images/                      # Game icons and assets
│
├── index.php                        # PUBLIC HOMEPAGE
├── login.php                        # Login redirect (compatibility)
├── register.php                     # Register redirect (compatibility)
├── profile.php                      # Profile redirect (compatibility)
├── logout.php                       # Logout redirect (compatibility)
├── top-up.php                       # Top-up redirect (compatibility)
├── admin_dashboard.php              # Admin redirect (compatibility)
├── admin_orders.php                 # Admin orders redirect (compatibility)
├── admin_users.php                  # Admin users redirect (compatibility)
├── admin_products.php               # Admin products redirect (compatibility)
├── admin_settings.php               # Admin settings redirect (compatibility)
├── admin_logout.php                 # Admin logout redirect (compatibility)
│
├── database.sql                     # Database schema & initial data
├── .git/                            # Git version control
│
├── README.md                        # Project overview & features
├── SETUP_GUIDE.md                   # Installation & setup instructions
├── ADMIN_GUIDE.md                   # Admin panel user guide
└── FOLDER_STRUCTURE.md              # This file
```

## 🔐 Access Control

### Public Pages (No Login Required)
- `index.php` - Homepage with product grid
- `pages/user/login.php` - Login page (unified for user & admin)
- `pages/user/register.php` - User registration
- `pages/user/top-up.php` - Product selection & top-up form

### User Pages (Login Required - User Role)
- `pages/user/profile.php` - View & edit user profile
- `pages/user/logout.php` - User logout handler

### Admin Pages (Login Required - Admin Role)
- `pages/admin/dashboard.php` - Sales statistics & analytics
- `pages/admin/orders.php` - Order management & status updates
- `pages/admin/users.php` - User listing & management
- `pages/admin/products.php` - Product/game management
- `pages/admin/settings.php` - Admin settings & configuration
- `pages/admin/logout.php` - Admin logout handler

## 🔄 Path Structure

### Accessing Resources from Role Pages
All pages in `pages/user/` and `pages/admin/` use relative paths to access root resources:

```php
// From pages/user/login.php or pages/admin/dashboard.php:
require_once '../../includes/auth.php';              // Access includes/
require_once '../../includes/admin_auth.php';        // Access includes/
<link rel="stylesheet" href="../../assets/css/style.css">  // Access assets/
header("Location: ../../index.php");                 // Redirect to homepage
header("Location: ../admin/dashboard.php");          // Admin redirect
```

### Accessing Pages from index.php
The homepage uses relative paths to link to user pages:

```php
<a href="login.php">LOGIN</a>                         // Redirects to login.php → pages/user/login.php
<a href="register.php">DAFTAR</a>                     // Redirects to register.php → pages/user/register.php
<a href="profile.php">PROFIL</a>                      // Redirects to profile.php → pages/user/profile.php
<a href="logout.php">LOGOUT</a>                       // Redirects to logout.php → pages/user/logout.php
<a href="top-up.php?game=roblox">ROBLOX</a>          // Redirects to top-up.php → pages/user/top-up.php
```

## 🔀 Backward Compatibility

Root-level **redirect files** maintain backward compatibility with old URLs:

| Old URL | Redirects To | Purpose |
|---------|--------------|---------|
| login.php | pages/user/login.php | User login |
| register.php | pages/user/register.php | User registration |
| profile.php | pages/user/profile.php | User profile |
| logout.php | pages/user/logout.php | User logout |
| top-up.php | pages/user/top-up.php | Product top-up |
| admin_dashboard.php | pages/admin/dashboard.php | Admin dashboard |
| admin_orders.php | pages/admin/orders.php | Order management |
| admin_users.php | pages/admin/users.php | User management |
| admin_products.php | pages/admin/products.php | Product management |
| admin_settings.php | pages/admin/settings.php | Admin settings |
| admin_logout.php | pages/admin/logout.php | Admin logout |

This ensures that any external links or bookmarks to old URLs continue to work seamlessly.

## 💾 Database Connection

### File: `config/database.php`
Defines MySQL/MariaDB connection settings:
- **Database**: gamevo_db
- **Tables**: users, admin, orders, products, login_history, admin_login_history

### File: `database.sql`
Contains complete database schema and initial data:
- User and admin tables with all fields
- Orders and products tables
- Login history tables for audit trails
- Admin user: Username "Admin", Password "gamevoadmin"

## 🔑 Key Controllers

### File: `includes/auth.php`
User authentication system:
- `registerUser($username, $email, $password, $full_name)` - User registration
- `unifiedLogin($username, $password)` - Universal login (detects user vs admin automatically)
- `isLoggedIn()` - Check if user is logged in
- `getCurrentUser()` - Get current user data
- `logoutUser()` - User logout

### File: `includes/admin_auth.php`
Admin authentication & statistics:
- `requireAdminLogin()` - Protect admin pages
- `getCurrentAdmin()` - Get current admin data
- `getSalesStatistics()` - Get revenue, orders, users data
- `getRecentOrders($limit)` - Get recent orders list
- `updateOrderStatus($order_id, $status)` - Update order status
- `logoutAdmin()` - Admin logout

## 🎯 User Flow

```
Public User
    ↓
[index.php] (homepage with product grid)
    ↓
[pages/user/login.php] (unified login)
    ↓
{Login Check in includes/auth.php}
    ├─→ Admin detected → Redirect to pages/admin/dashboard.php
    └─→ User detected → Redirect to index.php
    ↓
[pages/user/profile.php] (view profile)
[pages/user/top-up.php] (select & purchase)
[pages/user/logout.php] (exit)
```

```
Admin User
    ↓
[index.php] (homepage)
    ↓
[pages/user/login.php] (unified login - enter admin credentials)
    ↓
{Login Check in includes/auth.php - admin detected}
    ↓
[pages/admin/dashboard.php] (admin dashboard)
    ├─→ [pages/admin/orders.php] (manage orders)
    ├─→ [pages/admin/users.php] (manage users)
    ├─→ [pages/admin/products.php] (manage products)
    ├─→ [pages/admin/settings.php] (admin settings)
    └─→ [pages/admin/logout.php] (exit admin)
```

## ✅ Migration Status

✅ **COMPLETE** - All pages successfully migrated to role-based folder structure:
- ✅ All user pages in `pages/user/`
- ✅ All admin pages in `pages/admin/`
- ✅ All relative paths updated (../../ prefix for accessing root resources)
- ✅ Backward compatibility redirects in place
- ✅ Git repository updated with organized structure
