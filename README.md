# DineSpot

**Tuong Nguyen Pham** — Student ID: 110192780  
**COMP 3340** — World Wide Web Info System Dev (Course Project)

DineSpot is a restaurant discovery website where users can browse Canadian restaurants, read reviews, save favourites, and request table reservations. There is also an admin area for managing the site.

**Repository:** https://github.com/npham-uwindsor/dinespot

**Live URL:** https://pham39.myweb.cs.uwindsor.ca/dinespot

---

## What you need installed

- PHP 8.0 or newer (with PDO MySQL extension)
- MySQL or MariaDB
- A web server — I used XAMPP locally, but anything that can run PHP works

---

## Installation

These steps assume you are setting it up on your own machine (like XAMPP/WAMP).

### 1. Get the code

Clone the repo or download it and put the `dinespot` folder somewhere your web server can read it.

```bash
git clone https://github.com/npham-uwindsor/dinespot.git
```

### 2. Create the database

1. Open phpMyAdmin (or MySQL command line).
2. Create a database called `dinespot` (utf8mb4).
3. Uncomment lines in the `schema/schema_mysql.sql` file for the database creation if you have not created one.
4. Import the file into the web server's MySQL database.

That SQL file creates all the tables and loads sample data. You can modify the data as you wish.

### 3. Configure database connection

Edit `includes/config.php` and update the DB settings if yours are different:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dinespot');
define('DB_USER', 'root');
define('DB_PASS', '');
```

There is also a production block in that file if you deploy to a hosting server — just switch `$env` from `'development'` to whatever you need.

### 4. Run the site

Point your browser at the project folder. With XAMPP, it is usually:

```
http://localhost/dinespot/
```

If something breaks, check that MySQL is running and that the database has been imported without errors.

### Default login accounts

Sample login accounts:
| Role   | Email                    | Password  |
|--------|--------------------------|-----------|
| Admin  | admin@dinespot.ca        | admin123  |
| Client | sarah.chen@example.com   | client123 |

All clients password is `client123`.

---

## Front-end documentation

### Tech stack

- **HTML5** — semantic markup (`header`, `nav`, `main`, `section`, `footer`)
- **CSS** — custom styles in `assets/css/style.css`, plus 3 theme files
- **JavaScript** — vanilla JS in `assets/js/main.js` (no framework)
- **PHP + MySQL** — server-side pages and database queries

### Themes

The site supports 3 colour themes that admins can switch:

- **Classic** — burgundy/gold
- **Refresh** — teal/coral
- **Forest** — green/amber

Themes are stored in `site_settings` and loaded in `includes/header.php`. Users can also preview a theme before saving it.

### Responsive design

The layout uses a mobile-first approach with breakpoints around **768px** (mobile nav hamburger menu) and **900px** (grids stack to single column). Tested on desktop browser and phone-sized viewport.

### Multimedia and interactive features

| Feature | Where |
|---------|-------|
| Restaurant photos | DB `image_path` on listing/detail pages |
| YouTube video | Home page hero (`index.php`) |
| Audio help | Help pages (`help/browsing.php`, `help/account.php`, `help/reservations.php`, `help/updating-content.php`) |
| Interactive map | Restaurant detail page — Leaflet + OpenStreetMap |
| Tabbed menus | Restaurant detail — categories switch with JS |
| Charts | `charts/index.php` — Chart.js bar + pie charts |

### SEO

Each page sets its own `$pageTitle` and `$pageDescription`. The header also includes meta keywords, author, and robots tags. There is a human-readable sitemap at `sitemap.php` and a `robots.txt` that blocks admin/client folders from crawlers.

### JavaScript (`assets/js/main.js`)

Handles:

- Mobile navigation toggle
- FAQ accordion on `faq.php`
- Restaurant menu category tabs
- Leaflet map init on restaurant pages
- Interactive step wizard on `guide.php`
- Meal cost estimator on restaurant detail page
- Reservation estimate on reservation page
- Confirm dialogs before cancel/delete actions
- Chart.js rendering on the insights page

---

## End-user guide (on the website)

Regular users should use the built-in guide instead of reading this README:

- **Quick Guide** — `guide.php` (step-by-step wizard with Previous/Next buttons)
- **Help** — `help/index.php` (list of help topics)
- **FAQ** — `faq.php` (frequently asked questions)
- **Site Map** — `sitemap.php` (list of all pages)

All are linked from the site footer.

---

## Admin guide

Sign in as admin at `client/login.php` — you will get redirected to the admin dashboard.

### Dashboard (`admin/dashboard.php`)

Shows quick stats (users, restaurants, pending reservations) and links to everything else.

### Manage Users (`admin/users/list.php`)

View all registered accounts. You can **suspend** or **restore** client accounts. Suspended users cannot log in. Admin accounts cannot be suspended from the UI.

### Manage Restaurants (`admin/restaurants/`)

- **List** — see all restaurants
- **Add** — create a new listing
- **Edit** — update an existing restaurant

### Manage Reservations (`admin/reservations/list.php`)

Reservation requests start as `pending`. Admin can **approve**, **reject**, or **cancel** them.

### Manage Reviews (`admin/reviews/list.php`)

View all reviews. Delete ones that break the guidelines (spam, abusive content, etc.).

### Theme Settings (`admin/theme/settings.php`)

Pick the default site theme (Classic, Refresh, or Forest). You can preview a theme before saving. The choice applies site-wide for visitors who have not picked their own cookie override.

### System Monitoring (`monitor.php`)

Admin-only page that checks if core features are working — database connection, restaurant data, auth, maps, CDN assets, etc. Each service shows **online** or **offline** with a short message.

### Admin account pages

Under **My Account** in the sidebar you can view and edit your admin profile the same way clients edit theirs.

---

## Database overview

The full schema and seed data live in `schema/schema_mysql.sql`. Import that file into a MySQL database named `dinespot` (utf8mb4) before running the site.

### Tables

| Table | Purpose |
|-------|---------|
| `users` | Client and admin accounts (e.g., email login, hashed password, role, status) |
| `restaurants` | Catalogue listings — name, cuisine, location, image, price range, map coordinates |
| `menu_items` | Menu options per restaurant (category, name, description, price) |
| `reviews` | One review per user per restaurant (1–5 star rating + comment) |
| `reservations` | Table booking requests with date, time, party size, and approval status |
| `favourites` | Many-to-many link between users and saved restaurants |
| `site_settings` | Key/value store for site-wide options (e.g. active theme) |

### Relationships

- `restaurants` → `menu_items` (one restaurant, many menu items)
- `users` + `restaurants` → `reviews` (each user may review a restaurant once)
- `users` + `restaurants` → `reservations` (booking requests tied to both)
- `users` + `restaurants` → `favourites` (composite primary key on both IDs)

### Sample data (after import)

| Table | Records |
|-------|---------|
| `users` | 5 (1 admin, 4 clients) |
| `restaurants` | 20 |
| `menu_items` | 55 |
| `reviews` | 11 |
| `reservations` | 6 |
| `favourites` | 10 |
| `site_settings` | 2 |

Default passwords are documented in the SQL file: `admin123` (admin), `client123` (all clients).

---

## Public vs private areas

**Public** — home, about, FAQ, contact, restaurant browse/search/view, charts, guide, terms, privacy

**Private (client)** — requires login: profile, edit profile, change password, my reservations, favourites, my reviews, add/edit review, reserve table

**Private (admin)** — requires admin role: everything under `admin/` and `monitor.php`.

Auth is session-based in `includes/auth.php` with `password_hash` / `password_verify`.

---

## License

GNU GPL v3 — see `LICENSE` file.
