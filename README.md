# Hexactyl Panel

**The Next-Generation Game Server Management Panel**

Hexactyl is a free, open-source game server management panel built with PHP, React, and Go. Designed with security, performance, and flexibility in mind, Hexactyl runs all game servers in isolated Docker containers while exposing a beautiful and intuitive UI to end users.

> Built on the foundation of Hexactyl, enhanced with modern features and a fresh identity.

## Features

### Core Features
- **Docker Isolation** - Run game servers in completely isolated containers
- **Multi-Node Support** - Manage servers across multiple physical machines
- **Real-Time Console** - Live server console with xterm.js integration
- **File Manager** - Full-featured web-based file editor and manager
- **Database Management** - MySQL/MariaDB database creation and management
- **Backup System** - Automated and manual backup support with S3 compatibility
- **SFTP Access** - Secure file transfer with per-server credentials
- **User Management** - Multi-user support with granular permissions
- **API Access** - Full REST API for external integrations
- **Activity Logging** - Complete audit trail of all admin and user actions

### Enhanced Features (Hexactyl Exclusive)
- **Modern UI/UX** - Refreshed interface with improved navigation and responsiveness
- **Server Templates** - Pre-configured templates for quick server deployment
- **Advanced Monitoring** - Enhanced resource usage tracking and alerts
- **Theme Support** - Customizable panel appearance with dark/light modes
- **Quick Actions** - Streamlined server management workflows
- **Improved Search** - Global search across servers, users, and settings
- **Dashboard Analytics** - Server usage statistics and insights
- **Mobile Optimized** - Fully responsive design for mobile management
- **Performance Improvements** - Optimized codebase for faster load times
- **Security Enhancements** - Additional security features and hardening

### Supported Games
Hexactyl supports a wide variety of games including:

- Minecraft (Paper, Spigot, Bungeecord, Waterfall, Purpur, Velocity)
- Rust
- Terraria
- TeamSpeak
- Counter-Strike 2
- Garry's Mod
- ARK: Survival Evolved
- Valheim
- Factorio
- And many more...

## Requirements

- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.2+
- Redis
- Docker
- Node.js 18+
- Web Server (Nginx/Apache)

## Installation

### Quick Start

```bash
# Clone the repository
git clone https://github.com/itriedcoding/Hexactyl.git

# Install PHP dependencies
composer install

# Install Node.js dependencies
yarn install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Build frontend assets
yarn build:production

# Create admin user
php artisan p:user:make
```

### Docker Installation

```bash
# Using Docker Compose
docker-compose up -d
```

## Documentation

- [Panel Documentation](https://hexactyl.io/panel/getting_started.html)
- [Wings Documentation](https://hexactyl.io/wings/installing.html)
- [API Documentation](https://hexactyl.io/api/)
- [Community Guides](https://hexactyl.io/community/)

## Contributing

Contributions are welcome! Please read our contributing guidelines before submitting PRs.

## License

Hexactyl is licensed under the MIT License. See [LICENSE.md](LICENSE.md) for details.

Based on [Hexactyl Panel](https://github.com/Hexactyl/panel) by Dane Everitt and contributors.

## Credits

- Original Hexactyl Panel by Dane Everitt and contributors
- Hexactyl maintained by [itriedcoding](https://github.com/itriedcoding)

## Support

- [GitHub Issues](https://github.com/itriedcoding/Hexactyl/issues)
- [Discord Community](https://discord.gg/hexactyl)
