## Instructions for running the application and setting up tenants:

-   How to create new tenant:

    `php artisan tenant:create "TenantName" "tenant_db_name"`

-   How to create table for tenants:

    `php artisan tenant:migration create_products_table`

-   How to run master database migration:

    `php artisan migrate`

-   How to run tenants migration:

    `php artisan tenant:migrate`

-   How to run test:

    `php artisan test --filter ProductTest`

---

## Changed Files:

1. Multi-Tenancy Database Setup

    - .env
    - database/migrations/2024_10_03_121344_create_tenants_table.php

2. Dynamic Database Switching

    - config/database.php
    - app/Http/Middleware/TenantDatabaseSwitcher.php
    - app/Http/Kernel.php

3. Tenant Database Schema

    - app/Console/Commands/MakeTenantMigration.php
    - database/migrations/tenant/2024_10_03_121749_create_products_table.php

4. Models and Relationships

    - app/Models/Tenant.php
    - app/Models/Product.php

5. Laravel Command for Tenant Database Setup

    - app/Console/Commands/CreateTenantDatabase.php

6. API Endpoint for Adding a Product

    - app/Http/Controllers/ProductController.php
    - routes/api.php

7. Testing

    - tests/Feature/ProductTest.php

8. Run migrations for all tenants
    - app/Console/Commands/RunTenantMigrations.php

---

## Used Commands:

1. Multi-Tenancy Database Setup

    - `php artisan make:migration create_tenants_table`
    - `php artisan migrate`

2. Dynamic Database Switching

    - `php artisan make:middleware TenantDatabaseSwitcher`

3. Tenant Database Schema

    - `php artisan make:command MakeTenantMigration`
    - `php artisan tenant:migration create_products_table`

4. Models and Relationships

    - `php artisan make:model Tenant`
    - `php artisan make:model Product`

5. Laravel Command for Tenant Database Setup

    - `php artisan make:command CreateTenantDatabase`
    - `php artisan tenant:create "TenantName" "tenant_db_name"`

6. API Endpoint for Adding a Product

    - `php artisan make:controller ProductController`

7. Testing

    - `php artisan make:test ProductTest`
    - `php artisan test --filter ProductTest`

8. Run migrations for all tenants
    - `php artisan make:command RunTenantMigrations`
    - `php artisan tenant:migrate`
