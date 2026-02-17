# Deployment Guide (Nginx + Laravel + Queue)

## 1) Server Requirements

- Ubuntu 22.04+ (or equivalent Linux)
- PHP 8.3+
- Composer 2
- Node.js 20+
- MySQL 8+ or PostgreSQL 14+
- Redis
- Nginx
- Supervisor

## 2) Environment Variables

Set at least:

```env
APP_NAME="Majlis Tracker Pro+"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=majlis_tracker
DB_USERNAME=majlis_user
DB_PASSWORD=strong-password

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

```

## 3) Build + Migrate

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 4) Nginx Virtual Host

```nginx
server {
    listen 80;
    server_name your-domain.example;

    root /var/www/majlis-tracker/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    client_max_body_size 20M;
}
```

Enable TLS with Let's Encrypt:

```bash
sudo certbot --nginx -d your-domain.example
```

## 5) Queue Worker (Supervisor)

`/etc/supervisor/conf.d/majlis-tracker-worker.conf`

```ini
[program:majlis-tracker-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/majlis-tracker/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/majlis-tracker/storage/logs/worker.log
stopwaitsecs=3600
```

Apply:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start majlis-tracker-worker:*
```

## 6) File Permissions

```bash
sudo chown -R www-data:www-data /var/www/majlis-tracker
sudo chmod -R 775 /var/www/majlis-tracker/storage /var/www/majlis-tracker/bootstrap/cache
```

## 7) Post-Deploy Smoke Checks

- Login at `/admin`
- Create a meeting and task
- Open share link
- Download both PDFs
- Confirm queue worker is running
