# DBHC - Dashboard Kepegawaian Regional 1 PT Angkasa Pura Indonesia

## Architecture Overview

This is a Laravel 12 employee management system with dual-model architecture:
- **Legacy**: `Employee` model (employees table) - original structure
- **Active**: `DataKaryawan` model (data_karyawan table) - current primary data store
- **Bridge**: `Formasi` model links positions via `kode_jabatan` field

Key architectural patterns:
- Automatic change tracking via `EmployeeObserver` → `EmployeeHistory` 
- Version control system through `Snapshot` and `Version` models
- Role-based access using Spatie Laravel Permission
- Excel import/export via Maatwebsite Excel with batch processing

## Development Environment

**Build & Serve:**
```bash
# Backend
php artisan serve
php artisan migrate --seed

# Frontend (Vite + React)
npm run dev          # Development
npm run build        # Production
```

**Key Commands:**
- `php artisan app:migrate-employees` - Sync Employee → DataKaryawan
- `php artisan make:export ExportName` - Create Excel exports
- `php artisan make:import ImportName` - Create Excel imports

## Database Conventions

**Primary Models:**
- `DataKaryawan` - Main employee data (Indonesian field names, date format: dd/mm/yyyy)
- `Employee` - Legacy structure (English field names, computed age/masa_kerja)  
- `Formasi` - Job position templates with quotas

**Key Relationships:**
```php
DataKaryawan::with(['formasi'])->get(); // Position details via kode_jabatan
```

**Date Handling:**
- Store as dd/mm/yyyy strings (Indonesian format)
- Use helper methods: `calculateAge()`, `calculateWorkPeriod()` in controllers
- Excel imports auto-convert serial dates via PhpSpreadsheet

## Code Patterns

**Controller Structure:**
- Dashboard analytics in `DashboardController` with extensive KPI calculations
- CRUD + Import/Export pattern: `DataKaryawanController`, `FormasiController`
- Separate analytics controller: `AnalitikController` for chart data

**Import/Export Pattern:**
```php
// Import with batch processing + chunk reading
class DataKaryawanImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}
```

**Frontend Integration:**
- Blade templates with Alpine.js for interactivity
- Chart.js + React components for analytics
- Bootstrap + Tailwind hybrid styling
- Vite handles React component compilation

## Authentication & Authorization

**User Roles:**
- `admin` - Full CRUD access, imports, user management
- `user` - View-only access to data and analytics

**Route Protection:**
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin-only routes
});
```

## File Organization

**Key Directories:**
- `app/Imports/` - Excel import classes with validation
- `app/Exports/` - Excel export classes with templates  
- `app/Observers/` - Model event handlers for history tracking
- `resources/views/layouts/` - Shared Blade components
- `database/migrations/` - Schema with Indonesian table/field names

**Configuration:**
- SQLite for development (`database/database.sqlite`)
- Vite config includes React plugin for chart components
- Permission seeding in `DatabaseSeeder`

## Testing & Quality

Run tests with: `php artisan test`
Code style: `./vendor/bin/pint` (Laravel Pint)

## Common Tasks

**Adding New Import/Export:**
1. Create import class extending required interfaces
2. Add batch/chunk processing for large datasets  
3. Handle dd/mm/yyyy date conversion from Excel
4. Create corresponding export with proper headers

**Extending Analytics:**
- Add methods to `DashboardController` or `AnalitikController`
- Use Chart.js components in `resources/views/`
- Follow existing KPI calculation patterns

**Database Changes:**
- Always use Indonesian field names to match existing pattern
- Add foreign keys via `kode_jabatan` for position relationships
- Include history tracking for audit trail