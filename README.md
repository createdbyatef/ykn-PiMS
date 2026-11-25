# e-Pilot YKN — Vehicle & Pilot Booking Management System

Internal web application for **Yayasan Kebajikan Negara (YKN)** to manage office vehicle bookings, the **4 HQ Pilots**, approval workflows, odometer tracking, and official Bahasa Melayu PDF forms.

Built with **Laravel 12**, **PHP 8.2+**, **MySQL**, **Bootstrap 5**, **FullCalendar.js**, and **barryvdh/laravel-dompdf**.

---

## Key Features

- **Role-Based Portals (`staff`, `admin`, `pilot`)**:
  - **Staff (`staff`)**: Live availability calendar, booking request form (`Kenderaan + Pilot`, `Kenderaan Sahaja / Self-Drive`, `Pilot Sahaja`), personal booking history, and official PDF downloads.
  - **Unit Pentadbiran (`admin`)**: Command dashboard with 4 live KPI cards (Pending Requests, Active Outstation Trips, Available Pilots, Vehicles in Maintenance), conflict-aware approval/rejection workflow, Vehicles CRUD, Users & 4 HQ Pilots management, and PDF printing.
  - **Pilot (`pilot`)**: Mobile-friendly trip dashboard (Today, Upcoming, Completed) with live distance calculation and odometer completion sync.
- **Core Business Logic**:
  - **Double-Booking & Maintenance Prevention**: Strict overlapping datetime check (`start_datetime < existing.end_datetime AND end_datetime > existing.start_datetime`) inside database transactions with row locking (`lockForUpdate()`). Blocks assigning vehicles in `maintenance` or `inactive` status.
  - **YKN 3-Day Notice Rule**: Displays a live Bootstrap warning alert on the booking form and automatically flags `is_urgent = true` whenever `start_datetime` is less than 3 days from submission.
  - **Odometer Sync**: Validates `odometer_end > odometer_start` and automatically updates `vehicles.current_odometer` upon trip completion.
- **Official YKN Print-Ready PDFs (`barryvdh/laravel-dompdf`)**:
  1. `BORANG KEBENARAN MENGGUNAKAN KENDERAAN PEJABAT` (`pdf.borang_kenderaan`) — faithful two-column YKN layout with `[X]` approval/rejection checkboxes and odometer log.
  2. `MEMO - KEBENARAN MEMANDU KENDERAAN PEJABAT` (`pdf.memo_memandu`) — enabled exclusively when `request_type === 'vehicle_only'` and `status === 'approved'`.

---

## Setup & Installation

### 1. Database Configuration (MySQL)

Update `.env` with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=epilot_ykn
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

*(By default, `.env` is pre-configured with local SQLite so you can also preview the seeded app immediately before connecting MySQL.)*

### 2. Run Migrations & Seed Demo Data

```bash
php artisan migrate:fresh --seed
```

### 3. Start the Application

```bash
php artisan serve
```

Visit `http://127.0.0.1:8000`.

---

## Seeded Demo Accounts (Password: `password`)

| Role | Name | Email |
| :--- | :--- | :--- |
| **Admin (Unit Pentadbiran)** | Encik Azman bin Razak | `admin@ykn.gov.my` |
| **Staff** | Puan Nurul Huda binti Ismail | `huda@ykn.gov.my` |
| **Pilot HQ #1** | Encik Roslan bin Ahmad | `roslan.pilot@ykn.gov.my` |
| **Pilot HQ #2** | Encik Zulkifli bin Hassan | `zulkifli.pilot@ykn.gov.my` |

---

## Running Automated Tests

```bash
php artisan test
```
