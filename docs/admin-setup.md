# Admin Setup Instructions

## Default Admin (Seeder)

The seeder creates one default admin user:

- Name: from `DEFAULT_USER_NAME`
- Email: from `DEFAULT_USER_EMAIL`
- Password: from `DEFAULT_USER_PASSWORD`
- Role: `admin`

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
