# Gemini LMS Project Context

This project is a **Learning Management System (LMS)** built with **Laravel 11**. It supports multiple user roles, including Students, Teachers, Parents, Admins, Operators, and Vice Principals.

## Project Overview

-   **Primary Framework:** Laravel 11.x (PHP 8.2+)
-   **Frontend Stack:** Vite, Tailwind CSS 4.0, Bootstrap 5.2, Sass.
-   **Database:** Likely MySQL/PostgreSQL (standard Laravel setup).
-   **Key Libraries:**
    -   `spatie/laravel-permission`: Role and permission management.
    -   `spatie/laravel-activitylog`: Tracks user actions.
    -   `yajra/laravel-datatables`: Server-side table handling.
    -   `barryvdh/laravel-dompdf`: PDF report generation.
    -   `rap2hpoutre/fast-excel`: Excel import/export.
    -   `mews/purifier`: XSS protection for user-generated content.

## Architecture & Structure

### Core Components
-   **Models:** Located in `app/Models/`. Key models include `User`, `Student`, `Teacher`, `Subject`, `Period`, `Curriculum`, `Schedule`, `Attendance`, `Exam`, and `Task`.
-   **Observers:** Models like `Period`, `Subject`, `Student`, and `Curriculum` have observers in `app/Observers/` to handle lifecycle events (e.g., auto-generating slugs or related data).
-   **Policies:** Authorization logic is located in `app/Policies/`.
-   **Services:** Complex logic, like scoring, is encapsulated in services (e.g., `app/Services/ExamScoringService.php`).

### Routing
Routes are modularized for better maintainability:
-   `routes/web.php`: Main entry point, including authentication and account routes.
-   `routes/web/`: Sub-directories containing role-specific routes (admin, teacher, student, parent, operator, vice-principal).

### Middleware
Role-based access is enforced via Spatie's middleware aliases:
-   `role`: Restricts access to specific roles.
-   `permission`: Restricts access to specific permissions.
-   `role_or_permission`: Combined check.

## Building and Running

### Prerequisites
-   PHP 8.2+
-   Composer
-   Node.js & NPM

### Setup Commands
1.  **Install PHP dependencies:**
    ```bash
    composer install
    ```
2.  **Install JS dependencies:**
    ```bash
    npm install
    ```
3.  **Environment Setup:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4.  **Database Migration & Seeding:**
    ```bash
    php artisan migrate --seed
    ```
5.  **Compile Assets:**
    ```bash
    npm run dev  # for development
    npm run build # for production
    ```
6.  **Run the application:**
    ```bash
    php artisan serve
    ```

## Development Conventions

-   **Code Style:** Follows standard Laravel/PSR-12 conventions. Laravel Pint is used for linting (`composer run lint`).
-   **Database:** Always use migrations for schema changes. Seeders are available in `database/seeders/` for initial data.
-   **Frontend:** Prefer Tailwind CSS 4 utility classes. Components may use Bootstrap for layout where specified.
-   **Security:**
    -   Use `mews/purifier` for any HTML input.
    -   Role-based access control (RBAC) must be applied to all administrative or personal routes.
-   **Testing:** Pest/PHPUnit tests are located in `tests/`. Run tests using `php artisan test`.

## Key Features & Logic
-   **Auto-Username Generation:** Users have usernames auto-generated from their names (e.g., `ryan_syukur_b3s4`).
-   **DataTable Integration:** Extensive use of Yajra DataTables with custom "Fit Content" toggle logic (see `README.md` for implementation details).
-   **Activity Logging:** Critical actions are logged using Spatie Activity Log.
