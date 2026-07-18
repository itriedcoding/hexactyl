<p align="center">
    <img src="docs/assets/hexactyl-logo.svg" alt="Hexactyl" width="120">
</p>

<h1 align="center">Hexactyl Panel</h1>

<p align="center">
    <strong>The next-generation, open-source game server management panel.</strong>
</p>

<p align="center">
    <a href="https://github.com/Hexactyl-Projects/hexactyl/blob/main/LICENSE.md">
        <img src="https://img.shields.io/badge/license-MIT-orange?style=for-the-badge" alt="License">
    </a>
    <a href="https://github.com/Hexactyl-Projects/hexactyl/stargazers">
        <img src="https://img.shields.io/github/stars/Hexactyl-Projects/hexactyl?style=for-the-badge&color=orange" alt="Stars">
    </a>
    <a href="https://github.com/Hexactyl-Projects/hexactyl/network/members">
        <img src="https://img.shields.io/github/forks/Hexactyl-Projects/hexactyl?style=for-the-badge&color=orange" alt="Forks">
    </a>
    <a href="https://github.com/Hexactyl-Projects/hexactyl/issues">
        <img src="https://img.shields.io/github/issues/Hexactyl-Projects/hexactyl?style=for-the-badge" alt="Issues">
    </a>
    <a href="https://hexactyl-docs.vercel.app">
        <img src="https://img.shields.io/badge/docs-hexactyl--docs.vercel.app-orange?style=for-the-badge" alt="Documentation">
    </a>
</p>

<p align="center">
    Built with PHP, React, and Go. Designed with security in mind, Hexactyl runs all game servers in isolated Docker containers while exposing a beautiful and intuitive UI to end users.
</p>

---

## Features

### Core

| Feature | Description |
|---------|-------------|
| **Docker Isolation** | Every game server runs in its own isolated Docker container |
| **Multi-Node** | Manage servers across multiple physical machines from one panel |
| **Real-Time Console** | Live server console with full terminal support via xterm.js |
| **File Manager** | Web-based file editor with syntax highlighting and directory browsing |
| **Database Management** | MySQL/MariaDB database creation, user management, and phpMyAdmin integration |
| **Backups** | Automated and manual backups with local and S3-compatible storage |
| **SFTP** | Secure file transfer with per-server credentials |
| **User Management** | Multi-user support with granular permissions and two-factor authentication |
| **REST API** | Full client and application API for external integrations |
| **Activity Logging** | Complete audit trail of all admin and user actions |

### Hexactyl Exclusive

| Feature | Description |
|---------|-------------|
| **5 Built-in Themes** | Hexactyl (Orange), Midnight Blue, Forest Green, Sunset Orange, Clean Light |
| **Theme Engine** | Dynamic CSS variable generation with admin and user theme switching |
| **Server Templates** | Pre-configured templates for one-click server deployment |
| **Server Cloning** | Duplicate servers with the same configuration |
| **Server Snapshots** | Save and restore server configurations |
| **Auto-Restart** | Cron-based scheduled server restarts |
| **Dashboard Analytics** | Server usage statistics and node health overview |
| **Enhanced UI** | Modernized interface with responsive design for all devices |

## Supported Games

Hexactyl supports a wide variety of games through Docker containers:

- **Minecraft** &mdash; Paper, Spigot, Bungeecord, Waterfall, Purpur, Velocity, Forge, Fabric
- **Rust**
- **Counter-Strike 2**
- **Valheim**
- **Terraria**
- **Garry's Mod**
- **ARK: Survival Evolved**
- **Factorio**
- **TeamSpeak 3**
- **Starbound**
- **Satisfactory**
- And [many more](https://pterodactyleggs.com) via community eggs

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.2 or higher |
| MySQL | 5.7+ |
| MariaDB | 10.2+ |
| Redis | 6.0+ |
| Docker | 20.10+ |
| Node.js | 18+ |
| Web Server | Nginx or Apache with mod_rewrite |

### PHP Extensions

`bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `mbstring`, `mysqlnd`, `pdo`, `pdo_mysql`, `tokenizer`, `zip`

## Installation

### Option 1: Manual Installation

```bash
# 1. Clone the repository
git clone https://github.com/Hexactyl-Projects/hexactyl.git
cd hexactyl

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Install Node.js dependencies
yarn install

# 4. Configure environment
cp .env.example .env
php artisan key:generate

# 5. Configure database in .env
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=hexactyl
# DB_USERNAME=hexactyl
# DB_PASSWORD=your_password

# 6. Run database migrations
php artisan migrate --force

# 7. Seed the database
php artisan db:seed --force

# 8. Create storage links
php artisan storage:link

# 9. Build frontend assets
yarn build:production

# 10. Create admin user
php artisan p:user:make
```

### Option 2: Docker Installation

```bash
# Clone and configure
git clone https://github.com/Hexactyl-Projects/hexactyl.git
cd hexactyl
cp .env.example .env
php artisan key:generate

# Start with Docker Compose
docker-compose up -d

# Run migrations inside the container
docker-compose exec panel php artisan migrate --force
docker-compose exec panel php artisan db:seed --force
docker-compose exec panel php artisan p:user:make
```

### Wings (Daemon) Installation

Wings is the daemon that runs on each node to manage game servers:

```bash
# Install Wings
curl -L https://github.com/Hexactyl-Projects/hexactyl-wings/releases/latest/download/wings_linux_amd64 -o /usr/local/bin/wings
chmod +x /usr/local/bin/wings

# Configure Wings
mkdir -p /etc/pterodactyl
curl -L https://your-panel.example.com/api/application/nodes/1/configuration -o /etc/pterodactyl/config.yml

# Run Wings
wings --config /etc/pterodactyl/config.yml
```

## Configuration

### Environment Variables

Key variables in `.env`:

```env
# Application
APP_NAME=Hexactyl
APP_ENV=production
APP_URL=https://panel.example.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hexactyl
DB_USERNAME=hexactyl
DB_PASSWORD=your_password

# Cache
REDIS_HOST=127.0.0.1
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_DRIVER=redis

# Theme (hexactyl, midnight, forest, sunset, light)
HEXACTYL_THEME=hexactyl
```

### Web Server

Nginx configuration example:

```nginx
server {
    listen 80;
    server_name panel.example.com;
    root /var/www/hexactyl/public;
    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## Themes

Hexactyl includes 5 built-in themes:

| Theme | Primary Color | Background | Description |
|-------|--------------|------------|-------------|
| **Hexactyl** | `#f97316` | `#0f172a` | Signature dark theme with orange accents |
| **Midnight Blue** | `#3b82f6` | `#020617` | Deep blue theme for night owls |
| **Forest Green** | `#16a34a` | `#052e16` | Nature-inspired green theme |
| **Sunset Orange** | `#ea580c` | `#1c0a00` | Warm orange and red tones |
| **Clean Light** | `#6d28d9` | `#f8fafc` | Bright light theme for daytime |

Themes can be switched from the admin panel at `/admin/settings/theme` or by users from their account settings.

## API

Hexactyl provides two APIs:

- **Client API** &mdash; For end users to manage their servers, files, databases, and backups
- **Application API** &mdash; For external tools and automation to manage the panel

### Authentication

Generate an API key from the Client area or Admin area. Use it in requests:

```bash
curl -H "Authorization: Bearer YOUR_API_KEY" \
     -H "Content-Type: application/json" \
     https://panel.example.com/api/client
```

### Example: List Servers

```bash
curl -H "Authorization: Bearer YOUR_API_KEY" \
     https://panel.example.com/api/client/servers
```

Full API documentation is available at [hexactyl-docs.vercel.app/api.html](https://hexactyl-docs.vercel.app/api.html).

## Documentation

Complete documentation is available at **[hexactyl-docs.vercel.app](https://hexactyl-docs.vercel.app)**

- [Introduction](https://hexactyl-docs.vercel.app)
- [Installation](https://hexactyl-docs.vercel.app/installation.html)
- [Getting Started](https://hexactyl-docs.vercel.app/getting-started.html)
- [Configuration](https://hexactyl-docs.vercel.app/configuration.html)
- [Themes](https://hexactyl-docs.vercel.app/themes.html)
- [Server Templates](https://hexactyl-docs.vercel.app/server-templates.html)
- [API Reference](https://hexactyl-docs.vercel.app/api.html)
- [FAQ](https://hexactyl-docs.vercel.app/faq.html)

## Contributing

Contributions are welcome. Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for detailed guidelines.

## Credits

- **Original Pterodactyl Panel** by [Dane Everitt](https://github.com/DaneEveritt) and contributors
- **Hexactyl** maintained by [itriedcoding](https://github.com/itriedcoding)

## License

Hexactyl is licensed under the MIT License. See [LICENSE.md](LICENSE.md) for details.

---

<p align="center">
    <a href="https://github.com/Hexactyl-Projects/hexactyl">GitHub</a> &bull;
    <a href="https://hexactyl-docs.vercel.app">Documentation</a> &bull;
    <a href="https://github.com/Hexactyl-Projects/hexactyl/issues">Report Issue</a>
</p>
