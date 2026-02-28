# Admin Setup Instructions

## Default Admin (Seeder)

The seeder creates one admin user for initial access:

- Username: `amin`
- Password: `amin123`
- Role: `admin`

Log in at `/admin` with **username** and password (not email).

Run:

```bash
php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder
```

## Create an Additional Admin

Use Filament Users screen:

1. Login to `/admin`
2. Open `کاروونکي`
3. Create a new user
4. Select role `مدیر`

## Roles

- `مدیر` (admin): full access
- `منشي` (secretary): meeting/task/template management
- `غړی` (member): read + own task status update capability
- `کتونکی` (viewer): read-only visibility
