# Vice City Services - Car Rental System

A modern, feature-rich car rental management system built with Core PHP, Bootstrap 5, and glassmorphism UI design.


## 📁 Project Structure

```
Vice City Services/
├── config/
│   └── db.php                  # Database connection & BASE_URL config
├── includes/
│   └── common.php              # Session management & helper functions
├── auth/                       # Authentication module
│   ├── login.php               # Modern login (supports both roles)
│   ├── register.php            # Customer registration
│   ├── register_agency.php     # Agency registration
│   └── logout.php              # Logout handler
├── agency/                     # Agency management module
│   ├── dashboard.php           # Agency dashboard with stats
│   ├── add_car.php             # Add new vehicle
│   ├── edit_car.php            # Edit vehicle details
│   ├── delete_car.php          # Delete vehicle
│   ├── list_cars.php           # View all agency cars
│   └── view_bookings.php       # View all bookings & revenue
├── customer/                   # Customer module
│   ├── dashboard.php           # Customer dashboard with booking history
│   ├── book_car.php            # Car booking form
│   └── rent_car.php            # Booking processor
├── public/                     # Public assets & landing pages
│   ├── available_cars.php      # Browse & book cars
│   ├── style.css               # Legacy stylesheet
│   ├── modern-style.css        # Modern UI styles (545 lines)
│   └── animations.js           # Interactive animations (286 lines)
├── schema.sql                  # Complete database schema
├── .htaccess                   # Apache configuration & security
├── README.md                   # Project documentation
├── AUTHENTICATION_GUIDE.md     # Testing guide
├── TEST_CREDENTIALS.md         # Sample login credentials
└── index.php                   # Homepage with hero section
```

## 🚀 Setup Instructions

### 1. Database Setup

1. Import the database schema:
   ```bash
   mysql -u root -p < schema.sql
   ```

2. Or use phpMyAdmin to import `schema.sql`

### 2. Configuration

Edit `config/db.php` to match your MySQL settings:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vice_city_services');
```

### 3. Run the Application

1. Start your local server (XAMPP, WAMP, or PHP built-in server):
   ```bash
   php -S localhost:8000
   ```

2. Access the application:
   ```
   http://localhost:8000
   ```

### 4. Test Login Credentials

**Pre-configured test accounts (if sample data loaded):**
- **Agency**: agency@example.com / agency123
- **Customer**: customer@example.com / customer123

**Or register new accounts:**
- **Customer**: http://localhost:8000/auth/register.php
- **Agency**: http://localhost:8000/auth/register_agency.php

## 🎯 Features & Capabilities

### 🔐 Authentication System
- ✅ Separate registration pages for Customer & Agency with modern UI
- ✅ Single unified login form for both roles
- ✅ Role-based authentication and automatic redirects
- ✅ Session management (user_id, role, name)
- ✅ Password hashing with PHP `password_hash()`
- ✅ Password verification with `password_verify()`
- ✅ Input validation and sanitization
- ✅ SQL injection prevention with prepared statements
- ✅ Secure logout with session destruction

### 👤 Customer Features
- ✅ **Browse Cars** - View all available vehicles with modern card layout
- ✅ **Book Cars** - Select dates and rental duration
- ✅ **Real-time Cost Calculator** - Automatic price calculation based on days
- ✅ **Date Validation** - Prevents booking past dates
- ✅ **Booking Dashboard** - View complete booking history with stats
- ✅ **Stats Overview** - Total bookings count and total amount spent
- ✅ **Booking Details** - Car model, duration, dates, and total cost
- ✅ **Success/Error Messages** - Clear feedback for all actions

### 🏢 Agency Features
- ✅ **Agency Dashboard** - Overview with total cars, bookings, and revenue
- ✅ **Add Cars** - Add new vehicles with validation
- ✅ **Edit Cars** - Update vehicle details (model, number, capacity, rent)
- ✅ **Delete Cars** - Remove vehicles with confirmation dialog
- ✅ **List Cars** - View complete inventory with details
- ✅ **View Bookings** - See all bookings for agency vehicles
- ✅ **Revenue Tracking** - Total earnings calculation from all bookings
- ✅ **Booking Statistics** - Customer details, dates, and amounts
- ✅ **Unique Vehicle Numbers** - Automatic validation to prevent duplicates
- ✅ **Form Validation** - Client-side and server-side validation

### 💎 Modern UI/UX Features
- ✅ **Glassmorphism Cards** - Frosted glass effect with backdrop-filter
- ✅ **Gradient Backgrounds** - Animated shifting gradients
- ✅ **Scroll Animations** - Elements reveal on scroll
- ✅ **Floating Shapes** - Animated background decorations
- ✅ **Responsive Design** - Mobile, tablet, and desktop optimized
- ✅ **Bootstrap Navbar** - Modern navigation with dropdowns
- ✅ **Hero Sections** - Eye-catching landing sections
- ✅ **Stats Cards** - Animated counter displays
- ✅ **Form Styling** - Modern inputs with icons and validation
- ✅ **Button Animations** - Ripple effects and hover transitions
- ✅ **Alert System** - Auto-dismissing success/error messages
- ✅ **Loading States** - Visual feedback for user actions

## 👥 User Roles

### Customer
- Browse available cars with search/filter capability
- Book cars with date range selection
- View personal booking history
- Track total spending
- Real-time cost calculations
- Dashboard with statistics

### Agency
- Complete CRUD operations for vehicle inventory
- Add, edit, delete, and list all cars
- View all bookings for their vehicles
- Track total revenue and booking counts
- Manage vehicle details (model, number, capacity, rent)
- Dashboard with comprehensive statistics

## 📊 Database Schema

### Tables

#### 1. users
**User authentication and profiles**
- `id` - Primary key (AUTO_INCREMENT)
- `name` - User/Agency name (VARCHAR 100)
- `email` - Unique email (VARCHAR 150, UNIQUE INDEX)
- `password` - Hashed password using `password_hash()`
- `role` - ENUM('customer', 'agency')
- `created_at` - Registration timestamp
- **Indexes**: email, role, created_at

#### 2. cars
**Vehicle inventory managed by agencies**
- `id` - Primary key (AUTO_INCREMENT)
- `agency_id` - Foreign key to users table
- `vehicle_model` - Car model name (VARCHAR 100)
- `vehicle_number` - Unique vehicle registration (VARCHAR 50, UNIQUE)
- `seating_capacity` - Number of seats (TINYINT, 1-50)
- `rent_per_day` - Daily rental price (DECIMAL 10,2)
- `created_at` - Date added to inventory
- **Foreign Key**: CASCADE delete/update with users
- **Indexes**: agency_id, vehicle_number, rent_per_day, created_at
- **Constraint**: seating_capacity between 1-50

#### 3. bookings
**Rental bookings linking customers to cars**
- `id` - Booking ID (AUTO_INCREMENT)
- `customer_id` - Foreign key to users table
- `car_id` - Foreign key to cars table
- `number_of_days` - Rental duration (INT, 1-365 days)
- `start_date` - Rental start date (DATE, cannot be past)
- `created_at` - Booking timestamp
- **Foreign Keys**: CASCADE delete/update with users and cars
- **Indexes**: customer_id, car_id, start_date, created_at
- **Constraint**: number_of_days between 1-365

### Relationships
- One Agency (user) → Many Cars (1:N)
- One Customer (user) → Many Bookings (1:N)
- One Car → Many Bookings (1:N)
- CASCADE deletion: Deleting agency removes all their cars and related bookings
- CASCADE deletion: Deleting car removes all related bookings

## 🛠️ Technology Stack

### Backend
- **PHP 8.x** - Core programming language
- **MySQL** - Database with InnoDB engine
- **MySQLi** - Database driver with prepared statements
- **Session Management** - Secure user authentication
- **Password Hashing** - PHP `password_hash()` and `password_verify()`

### Frontend
- **HTML5** - Semantic markup
- **Bootstrap 5.3.0** - Responsive CSS framework (CDN)
- **Bootstrap Icons 1.11.0** - Icon library (CDN)
- **CSS3** - Custom modern styles with animations
- **Google Fonts (Poppins)** - Typography (CDN)
- **JavaScript (Vanilla)** - Interactive features and animations

### Design
- **Glassmorphism** - Modern UI trend with frosted glass effects
- **CSS Animations** - Keyframe animations for gradients and shapes
- **Scroll Reveal** - Intersection Observer API for animations
- **Responsive Design** - Mobile-first approach
- **CSS Variables** - Theming and consistency

### Security
- **Prepared Statements** - SQL injection prevention
- **Password Hashing** - bcrypt algorithm
- **Input Sanitization** - XSS prevention
- **CSRF Protection** - Form validation
- **Role-based Access** - Authorization checks
- **.htaccess** - Directory protection and config security

## 📝 Development Notes

### Code Organization
- **MVC-like Architecture** - Separation of concerns with modules
- **Prepared Statements** - All database queries use parameterized queries
- **Helper Functions** - Centralized in `includes/common.php`
- **Session Management** - Secure session handling throughout
- **UTF-8 Support** - Full international character support
- **Error Handling** - Comprehensive validation and user feedback

### File Descriptions

#### Configuration Files
- `config/db.php` - Database credentials and BASE_URL constant
- `.htaccess` - Apache security rules and directory protection

#### Core Files
- `includes/common.php` - 10+ helper functions for auth, sanitization, redirects
- `index.php` - Modern landing page with hero section

#### Authentication (`auth/`)
- `login.php` - Unified login with role-based redirection
- `register.php` - Customer registration with validation
- `register_agency.php` - Agency registration with validation
- `logout.php` - Session destruction and redirect

#### Customer Module (`customer/`)
- `dashboard.php` - Booking history with stats cards
- `book_car.php` - Booking form with date validation
- `rent_car.php` - Backend processor for bookings

#### Agency Module (`agency/`)
- `dashboard.php` - Stats overview (cars, bookings, revenue)
- `add_car.php` - Add vehicle form with validation
- `edit_car.php` - Update vehicle details
- `delete_car.php` - Confirmation dialog and deletion
- `list_cars.php` - Complete inventory view
- `view_bookings.php` - All bookings with revenue calculation

#### Public Assets (`public/`)
- `available_cars.php` - Main car browsing page
- `modern-style.css` - 545 lines of modern UI styles
- `animations.js` - 286 lines of interactive JavaScript
- `style.css` - Legacy stylesheet

### Key Features Implementation

**Real-time Cost Calculator:**
```javascript
// In book_car.php and available_cars.php
function calculateTotal() {
    const days = document.getElementById('number_of_days').value;
    const rentPerDay = parseFloat(car_rent);
    const total = days * rentPerDay;
    document.getElementById('total_cost').textContent = '₹' + total.toFixed(2);
}
```

**Role-based Access Control:**
```php
// Helper functions in common.php
requireRole('customer');  // Restricts to customer only
requireRole('agency');    // Restricts to agency only
requireAuth();            // Requires any logged-in user
```

**Scroll Reveal Animations:**
```javascript
// Intersection Observer in animations.js
const revealElements = document.querySelectorAll('.reveal');
// Automatically triggers on scroll
```

## 🚀 Future Enhancements

### Planned Features
- [ ] **Car Availability System** - Prevent double-bookings with date ranges
- [ ] **Booking Conflict Validation** - Check overlapping rental periods
- [ ] **Payment Gateway Integration** - Razorpay/PayPal/Stripe
- [ ] **Admin Panel** - Super admin for managing agencies and customers
- [ ] **Advanced Search** - Filter by price, seats, model, location
- [ ] **Car Images** - Upload and display vehicle photos
- [ ] **Rating System** - Customer reviews for cars and agencies
- [ ] **Booking Modification** - Edit/cancel existing bookings
- [ ] **Email Notifications** - Booking confirmations and reminders
- [ ] **PDF Invoices** - Generate rental receipts
- [ ] **Location-based Search** - Find cars near you
- [ ] **Multi-language Support** - Internationalization
- [ ] **Dark Mode** - Theme toggle
- [ ] **Analytics Dashboard** - Charts and reports for agencies
- [ ] **Export Data** - CSV/Excel export for bookings

### Potential Improvements
- API development for mobile apps
- Real-time notifications with WebSocket
- Advanced calendar view for bookings
- Integration with mapping services
- SMS notifications
- Two-factor authentication
- Social media login
- Progressive Web App (PWA)
- Performance optimization with caching

## 📄 License & Credits

**Framework:** Core PHP (No framework dependencies)  
**Database:** MySQL 5.7+ / MariaDB 10.3+  
**Architecture:** Modular structure with role-based access  
**UI Framework:** Bootstrap 5.3.0  
**Icons:** Bootstrap Icons 1.11.0  
**Fonts:** Google Fonts (Poppins)

---

**Version:** 2.0 (Modern UI Update)  
**Last Updated:** February 2026  
**Author:** Vice City Services Team
