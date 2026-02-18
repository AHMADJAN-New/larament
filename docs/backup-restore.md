# Backup and Restore Guide

## Automated Backup

The project includes a command:

```bash
php artisan app:backup
```

It performs:

1. Database dump (MySQL, PostgreSQL, or SQLite copy)
2. Storage archive (`storage/app/public` and `storage/app/private`)
3. Retention cleanup (default 14 days)

Optional retention:

```bash
php artisan app:backup --retention=30
```

Scheduled daily at **01:00** from `routes/console.php`.

## Backup Files

Generated under:

```text
storage/app/backups/
```

Examples:

- `database_20260217_010000.sql`
- `storage_20260217_010000.tar.gz`

## Restore Database

### MySQL

```bash
mysql -u majlis_user -p majlis_tracker < storage/app/backups/database_YYYYMMDD_HHMMSS.sql
```

### PostgreSQL

```bash
psql -U majlis_user -d majlis_tracker -f storage/app/backups/database_YYYYMMDD_HHMMSS.sql
```

### SQLite

Copy backup file to your configured sqlite database path.

## Restore Storage Files

```bash
tar -xzf storage/app/backups/storage_YYYYMMDD_HHMMSS.tar.gz -C storage
```

## Recommended Operations Flow

1. Put app in maintenance mode:
   ```bash
   php artisan down
   ```
2. Restore database
3. Restore storage archive
4. Clear caches:
   ```bash
   php artisan optimize:clear
   ```
5. Bring app up:
   ```bash
   php artisan up
   ```
