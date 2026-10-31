# ✈️ Flying411: Global Aviation Marketplace

**Flying411** is a full-stack web platform where people can **buy and sell aircraft, aircraft engines, and aviation parts**, and **find aviation services** such as maintenance shops, avionics shops, flight schools, and flight instructors, all in one place.

It works like an online marketplace (similar in spirit to eBay or OLX), built for the aviation industry. Sellers list their products, buyers search and compare them, and an admin team reviews every listing before it goes live.

> Project presented at the **International University Expo**.

---

## 📌 Table of Contents

1. [The Problem](#-the-problem)
2. [Our Solution](#-our-solution)
3. [Key Features](#-key-features)
4. [User Roles](#-user-roles)
5. [How It Works](#-how-it-works)
6. [Technology Stack](#-technology-stack)
7. [System Architecture](#-system-architecture)
8. [Project Structure](#-project-structure)
9. [Database Overview](#-database-overview)
10. [Installation & Setup](#-installation--setup)
11. [Security Measures](#-security-measures)
12. [Future Improvements](#-future-improvements)

---

## ❗ The Problem

The aviation market is scattered:

- Aircraft, engines, and parts are sold through many separate websites, brokers, and phone calls.
- Buyers struggle to compare listings, check part numbers, or find trusted local service providers.
- Small sellers, mechanics, and flight schools have no affordable way to reach a global audience.

## 💡 Our Solution

Flying411 brings the whole aviation trade into **one searchable, verified marketplace**:

| For Buyers | For Sellers / Service Providers | For Administrators |
|---|---|---|
| Search and filter aircraft, engines, parts, and services | List products with photos, documents, and specs | Approve or reject every listing |
| View details, images, and documents | Manage inventory from a personal dashboard | Manage users, orders, payments, and shipping |
| Request a quote (RFQ) or add items to the cart | Track views, sales, and KPIs | View analytics and reports |
| Find nearby services on a map | Upload many parts at once using CSV | Publish blog articles and manage SEO |

---

## ⭐ Key Features

### 🛒 Public Marketplace
- **Aircraft for Sale**: browse approved aircraft with image galleries, specifications, and documents.
- **Engines**: search engines by manufacturer, model, and condition.
- **Parts**: search a large parts inventory by part number, category, aircraft model, and region.
- **Services**: find maintenance shops, avionics shops, local mechanics, flight schools, and flight instructors, with **Google Maps** location search.
- **Smart search**: live search suggestions and multi-filter results (AJAX).
- **Request for Quote (RFQ)**: buyers send quote requests directly by email.
- **Shopping cart and checkout** for parts and products.
- **Blog / Articles** with aviation news and guides.
- **FAQ, Contact, and Privacy Policy** pages.

### 👤 User Accounts
- Registration with **Google reCAPTCHA** spam protection.
- Secure login with **hashed passwords**.
- Forgot-password / reset flow.
- During signup the user picks the services they offer. The system then gives them a role automatically (**Vendor** or **Client**).

### 🧑‍💼 Vendor & Client Dashboards
- Add, edit, duplicate, and delete aircraft, engines, parts, and services.
- Drag-and-drop **image ordering** for listing galleries.
- **Bulk CSV upload** for parts and engines.
- Mark items as sold.
- Order management, payments, and sales reports (downloadable).
- **KPI dashboard** showing how many people viewed each listing.
- Business profile, notifications, and support messages.

### 🛡️ Admin Panel
- **Approval workflow**: every listing is *Pending → Approved / Rejected*.
- Manage vendors, clients, roles, and admins.
- Manage orders, payments, withdrawals, shipping, and discounts.
- **Analytics**: page views, time spent on page, and product/aircraft/engine/service analytics, plus a Google Analytics report.
- **Content and SEO**: write blog articles, manage authors, and edit meta titles and descriptions for products and blogs.
- Bulk upload (for example, a flight school directory from CSV) and export of the full parts list.

### ⚙️ Automation
- **Listing expiry reminders**: a scheduled (cron) script emails sellers 7 days before their aircraft listing expires.
- **Search-friendly URLs** (e.g. `/parts/part-name/123`) using Apache `.htaccess` rewrite rules.
- **Dynamic XML sitemap** for search engines.
- **Built-in page tracking**: each visit and time-on-page is stored for analytics.

---

## 👥 User Roles

| Role | Who they are | What they can do |
|---|---|---|
| **Visitor** | Anyone on the website | Browse, search, read blogs, send quote requests |
| **Client** | A user who only sells aircraft | List aircraft and manage them from the client dashboard |
| **Vendor** | Parts houses, engine sellers, shops, schools, instructors | List engines, parts, and services; manage orders and payments |
| **Admin** | Platform staff | Approve content, manage users, finances, analytics, and SEO |

---

## 🔄 How It Works

```
 Seller registers ──► Chooses services ──► Gets Vendor/Client dashboard
        │
        ▼
 Adds a listing (aircraft / engine / part / service) + photos + documents
        │
        ▼
 Listing status = "Pending" ──► Admin reviews ──► Approved ✅ / Rejected ❌
        │
        ▼
 Approved listing appears on the public marketplace
        │
        ▼
 Buyer searches ──► Views details ──► Requests quote / Adds to cart ──► Checkout
        │
        ▼
 Views & clicks are tracked ──► Seller and Admin see KPIs and reports
```

---

## 🧰 Technology Stack

| Layer | Technology |
|---|---|
| **Front end** | HTML5, CSS3, Bootstrap, JavaScript, jQuery, AJAX |
| **Back end** | PHP 8 (PDO with prepared statements) |
| **Database** | MySQL / MariaDB |
| **Web server** | Apache (XAMPP for local development), `.htaccess` URL rewriting |
| **Dashboards** | Skydash Bootstrap admin template, Chart.js, DataTables |
| **Package manager** | Composer |
| **Email** | PHPMailer (SMTP), SendGrid |
| **Payments** | Stripe SDK, PayPal SDK (integrated libraries) |
| **SMS** | Twilio SDK |
| **Maps** | Google Maps JavaScript API |
| **Security** | Google reCAPTCHA, `password_hash` / `password_verify` |
| **Config** | `vlucas/phpdotenv` for environment variables |
| **Hosting** | Hostinger (production) |

---

## 🏗️ System Architecture

```
┌──────────────────────────────────────────────────────────────┐
│                         USERS (Browser)                      │
│     Visitors  •  Clients  •  Vendors  •  Administrators      │
└───────────────┬───────────────────────┬──────────────────────┘
                │ HTTP / AJAX           │
┌───────────────▼───────────┐  ┌────────▼─────────────────────┐
│   Public Website (PHP)    │  │ Dashboards (PHP)             │
│ index, aircraft, engine,  │  │ /vendors  /clients  /admin   │
│ parts, services, blogs,   │  │ listings, orders, payments,  │
│ cart, checkout, login     │  │ KPIs, analytics, SEO         │
└───────────────┬───────────┘  └────────┬─────────────────────┘
                │      PDO (prepared statements)
        ┌───────▼────────────────────────▼───────┐
        │          MySQL Database                 │
        │  flying411_db                           │
        └───────┬────────────────────────────────┘
                │
   ┌────────────▼──────────────────────────────────────────┐
   │ External Services: Google Maps • reCAPTCHA • SendGrid │
   │ PHPMailer SMTP • Stripe • PayPal • Twilio             │
   └───────────────────────────────────────────────────────┘
```

---

## 📁 Project Structure

```
flying411/
├── index.php                 # Home page (latest aircraft, engines, parts, blogs)
├── aircraft.php              # Aircraft listings + filters
├── aircraft_details.php      # Single aircraft page
├── engine.php / engine_details.php
├── parts.php / parts_details.php / part-info.php
├── services.php / services_details.php
├── blogs.php / blog-details.php
├── cart.php / checkout.php
├── login.php / register.php / forgot_password.php
├── login_action.php / register_action.php   # Authentication logic
├── search_*.php / filter_*.php / fetch_*.php / get_*.php  # AJAX endpoints
├── send_quote.php            # Request-for-Quote emails
├── track_*.php               # Page and product view tracking
├── check_expired_aircrafts.php  # Cron job: expiry reminder emails
├── sitemap.php               # Dynamic XML sitemap
├── config.php                # Database connection (PDO)
├── .htaccess                 # Clean URLs and redirects
│
├── admin/                    # Admin panel (approvals, users, finance, analytics, SEO)
├── vendors/                  # Vendor dashboard (parts, engines, services, CSV upload)
├── clients/                  # Client dashboard (aircraft listings)
├── assets/                   # CSS, JS, images, plugins
└── vendor/                   # Composer dependencies (not committed)
```

---

## 🗄️ Database Overview

Main tables used by the application:

| Table | Purpose |
|---|---|
| `vendors` | All registered users (vendors & clients), profiles, and roles |
| `admins` | Administrator accounts |
| `aircrafts`, `aircraft_models` | Aircraft listings and model data |
| `engines`, `engines_details` | Engine listings |
| `parts`, `parts_master` | Parts listings and master part catalogue |
| `services` | Aviation service providers |
| `product_images` | Images for all listing types, with sort order |
| `product_documents`, `product_parts_documents`, `product_aircraft_documents` | Uploaded documents (logbooks, certificates, etc.) |
| `categories` | Product categories |
| `orders`, `order_items`, `payments`, `shipping` | E-commerce data |
| `articles`, `content_people` | Blog posts and authors |
| `seo_meta`, `product_seo` | SEO titles and descriptions |
| `page_analytics`, `aircraft_analytics`, `engine_analytics`, `service_analytics` | Visitor tracking and KPIs |
| `notifications`, `support_messages` | Alerts and user support |

---

## 🚀 Installation & Setup

### Requirements
- PHP 8.0 or newer
- MySQL 5.7+ / MariaDB
- Apache with `mod_rewrite` enabled (XAMPP is the easiest option)
- Composer

### Steps

1. **Clone the repository** into your web server folder:
   ```bash
   cd C:/xampp/htdocs
   git clone https://github.com/<your-username>/flying411.git
   ```

2. **Install PHP dependencies:**
   ```bash
   cd flying411
   composer install
   ```

3. **Create the database:**
   - Open phpMyAdmin at `http://localhost/phpmyadmin`.
   - Create a database named `flying411_db`.
   - Import the SQL dump (e.g. `database/flying411_db.sql`, if provided).

4. **Configure the database connection** in `config.php` (and `admin/config.php`, `vendors/config.php`, `clients/config.php`):
   ```php
   $host     = 'localhost';
   $db_name  = 'flying411_db';
   $username = 'root';
   $password = '';
   ```

5. **Add your API keys** (Google Maps, reCAPTCHA, SendGrid / SMTP, Stripe, PayPal) in a `.env` file. Never commit real keys to GitHub.

6. **Start Apache and MySQL** in XAMPP and open:
   ```
   http://localhost/flying411
   ```

7. *(Optional)* Schedule the expiry reminder as a daily cron job:
   ```bash
   0 9 * * * php /path/to/flying411/check_expired_aircrafts.php
   ```

---

## 🔐 Security Measures

- **PDO prepared statements** to prevent SQL injection.
- **Password hashing** with PHP's `password_hash()` / `password_verify()`.
- **Google reCAPTCHA** on registration to block bots.
- **Session-based authentication** with separate areas for admins, vendors, and clients.
- **Admin approval** before any listing becomes public.
- **Duplicate-visit protection** in analytics (ignores page refreshes within 5 seconds).
- Sensitive files (`.env`, `config.php`, `uploads/`, `vendor/`) are listed in `.gitignore`.

---

## 🔮 Future Improvements

- Mobile app (Android / iOS) for buyers and sellers.
- Live chat between buyers and sellers.
- AI-based price suggestions and part-compatibility matching.
- Multi-language and multi-currency support.
- Escrow payments for high-value aircraft deals.
- Unit and integration tests and a CI/CD pipeline.

---

## 👨‍💻 Author

**Shahbaz Haider**
📧 shahbaz.haider@rapextech.com

---

## 📄 License

This project was built for academic presentation at the International University Expo. All rights reserved by the author unless stated otherwise.
