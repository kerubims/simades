# Panduan Deploy SIMADES (Docker & Cloudflare Tunnel)

Dokumen ini berisi panduan langkah-demi-langkah untuk melakukan deployment aplikasi SIMADES menggunakan **Docker Compose** dengan port **9004** dan integrasi **Cloudflare Tunnel**.

## 1. Persiapan File Konfigurasi

Pastikan Anda memiliki file berikut di root direktori server Anda:

### A. File `Dockerfile` (Root Project)
```dockerfile
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 80 (Internal)
EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
```

### B. File `docker-compose.yml`
```yaml
services:
  app:
    build: .
    container_name: simades-app
    restart: always
    ports:
      - "9004:80"
    volumes:
      - .:/var/www/html
      - ./storage:/var/www/html/storage
    env_file: .env
    networks:
      - simades-net

  worker:
    build: .
    container_name: simades-worker
    restart: always
    command: php artisan queue:work --tries=3
    volumes:
      - .:/var/www/html
    env_file: .env
    depends_on:
      - app
    networks:
      - simades-net

  whatsapp:
    build: ./whatsapp-gateway
    container_name: simades-whatsapp
    restart: always
    ports:
      - "9005:3000"
    volumes:
      - ./whatsapp-gateway:/app
      - ./whatsapp-gateway/sessions:/app/sessions
    env_file: .env
    networks:
      - simades-net

networks:
  simades-net:
    driver: bridge
```

## 2. Pengaturan `.env` (PENTING)

Sesuaikan file `.env` di server Anda:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://simades.desa.id  # URL Cloudflare Anda

# Koneksi ke WA Gateway di Docker
WA_GATEWAY_URL=http://whatsapp:3000

# Jika DB MySQL ada di Host (Luar Docker)
DB_HOST=172.17.0.1  # IP Host Docker
```

## 3. Langkah Instalasi

1.  **Build Image & Jalankan Container:**
    ```bash
    docker compose up -d --build
    ```

2.  **Generate Frontend Assets (Vite):**
    ```bash
    docker compose exec app npm install
    docker compose exec app npm run build
    ```

3.  **Setup Awal Aplikasi:**
    ```bash
    docker compose exec app php artisan key:generate
    docker compose exec app php artisan storage:link
    docker compose exec app php artisan config:cache
    docker compose exec app php artisan route:cache
    ```

## 4. Konfigurasi Cloudflare Tunnel

Jika menggunakan `cloudflared` CLI, jalankan perintah:
```bash
cloudflared tunnel route dns <TUNNEL_NAME> simades.desa.id
```
Lalu konfigurasi ingress rules di `config.yml` cloudflared:
```yaml
ingress:
  - hostname: simades.desa.id
    service: http://localhost:9004
  - service: http_status:404
```

## 5. Troubleshooting & Maintenance

*   **Cek Log Aplikasi:**
    ```bash
    docker compose logs -f app
    ```
*   **Restart Worker (Jika antrian macet):**
    ```bash
    docker compose restart worker
    ```
*   **Update Aplikasi:**
    ```bash
    git pull
    docker compose up -d --build
    ```
