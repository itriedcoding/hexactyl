#!/bin/bash
# ============================================================
# Hexactyl Panel - Quick Install Script
# https://github.com/Hexactyl-Projects/hexactyl
# ============================================================
# Usage: curl -sSL https://raw.githubusercontent.com/Hexactyl-Projects/hexactyl/main/install.sh | bash
# ============================================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
ORANGE='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m'

PANEL_DIR="/var/www/hexactyl"
DB_NAME="hexactyl"
DB_USER="hexactyl"
DB_PASS=$(openssl rand -base64 16)
APP_KEY=$(openssl rand -base64 32)

print_banner() {
    echo -e "${ORANGE}"
    echo "  _   _  ____  ____  ____  _  __"
    echo " | | | |/ ___||  _ \\|  _ \\| |/ /"
    echo " | |_| | |    | |_) | | | | ' / "
    echo " |  _  | |___ |  __/| |_| | . \\ "
    echo " |_| |_|\\____||_|   |____/|_|\\_\\"
    echo -e "${NC}"
    echo -e "${BLUE}  Game Server Management Panel${NC}"
    echo -e "${BLUE}  https://hexactyl-docs.vercel.app${NC}"
    echo ""
}

check_root() {
    if [[ $EUID -ne 0 ]]; then
        echo -e "${RED}[ERROR] This script must be run as root.${NC}"
        echo "Usage: sudo bash install.sh"
        exit 1
    fi
}

detect_os() {
    if [[ -f /etc/debian_version ]]; then
        OS="debian"
        PKG="apt"
    elif [[ -f /etc/redhat-release ]]; then
        OS="redhat"
        PKG="yum"
    else
        echo -e "${RED}[ERROR] Unsupported OS. Use Debian/Ubuntu or CentOS/RHEL.${NC}"
        exit 1
    fi
    echo -e "${GREEN}[✓] Detected OS: ${OS}${NC}"
}

install_dependencies() {
    echo -e "${ORANGE}[...] Installing dependencies...${NC}"
    
    if [[ "$OS" == "debian" ]]; then
        export DEBIAN_FRONTEND=noninteractive
        apt-get update -y
        apt-get install -y software-properties-common curl wget git unzip nginx mariadb-server redis-server \
            php8.2 php8.2-{fpm,mysql,bcmath,ctype,dom,fileinfo,gd,mbstring,pdo,tokenizer,zip,curl,xml} \
            composer nodejs npm
    else
        yum install -y epel-release
        yum install -y nginx mariadb-server redis php php-{mysql,bcmath,ctype,dom,fileinfo,gd,mbstring,pdo,tokenizer,zip,curl,xml} \
            composer nodejs npm
    fi
    
    echo -e "${GREEN}[✓] Dependencies installed${NC}"
}

setup_database() {
    echo -e "${ORANGE}[...] Configuring database...${NC}"
    
    systemctl start mariadb
    systemctl enable mariadb
    
    mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"
    mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
    mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"
    
    echo -e "${GREEN}[✓] Database configured${NC}"
}

setup_panel() {
    echo -e "${ORANGE}[...] Downloading Hexactyl...${NC}"
    
    git clone https://github.com/Hexactyl-Projects/hexactyl.git ${PANEL_DIR}
    cd ${PANEL_DIR}
    
    echo -e "${ORANGE}[...] Installing PHP dependencies...${NC}"
    composer install --no-dev --optimize-autoloader
    
    echo -e "${ORANGE}[...] Installing Node.js dependencies...${NC}"
    npm install
    
    echo -e "${ORANGE}[...] Configuring environment...${NC}"
    cp .env.example .env
    php artisan key:generate --force
    
    sed -i "s/DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/" .env
    sed -i "s/DB_PORT=3306/DB_PORT=3306/" .env
    sed -i "s/DB_DATABASE=panel/DB_DATABASE=${DB_NAME}/" .env
    sed -i "s/DB_USERNAME=hexactyl/DB_USERNAME=${DB_USER}/" .env
    sed -i "s/DB_PASSWORD=/DB_PASSWORD=${DB_PASS}/" .env
    sed -i "s/APP_URL=http:\/\/panel.example.com/APP_URL=http:\/\/$(hostname -f)/" .env
    
    echo -e "${ORANGE}[...] Running migrations...${NC}"
    php artisan migrate --force
    php artisan db:seed --force
    
    echo -e "${ORANGE}[...] Building assets...${NC}"
    npm run build:production
    
    echo -e "${ORANGE}[...] Setting permissions...${NC}"
    chown -R www-data:www-data ${PANEL_DIR}
    chmod -R 755 ${PANEL_DIR}
    chmod -R 775 ${PANEL_DIR}/storage
    chmod -R 775 ${PANEL_DIR}/bootstrap/cache
    
    php artisan storage:link
    
    echo -e "${GREEN}[✓] Panel installed${NC}"
}

setup_nginx() {
    echo -e "${ORANGE}[...] Configuring Nginx...${NC}"
    
    cat > /etc/nginx/sites-available/hexactyl << 'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/hexactyl/public;
    index index.html index.php;

    charset utf-8;
    client_max_body_size 100M;

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
EOF
    
    ln -sf /etc/nginx/sites-available/hexactyl /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    
    systemctl restart nginx
    systemctl enable nginx
    
    echo -e "${GREEN}[✓] Nginx configured${NC}"
}

setup_queue() {
    echo -e "${ORANGE}[...] Setting up queue worker...${NC}"
    
    cat > /etc/systemd/system/hexactyl-worker.service << EOF
[Unit]
Description=Hexactyl Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=$(which php) ${PANEL_DIR}/artisan queue:work --queue=high,standard,low --sleep=3 --tries=3
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF
    
    systemctl daemon-reload
    systemctl enable hexactyl-worker
    systemctl start hexactyl-worker
    
    echo -e "${GREEN}[✓] Queue worker configured${NC}"
}

setup_cron() {
    echo -e "${ORANGE}[...] Setting up cron...${NC}"
    
    (crontab -l 2>/dev/null; echo "* * * * * php ${PANEL_DIR}/artisan schedule:run >> /dev/null 2>&1") | crontab -
    
    echo -e "${GREEN}[✓] Cron configured${NC}"
}

print_summary() {
    SERVER_IP=$(hostname -I | awk '{print $1}')
    
    echo ""
    echo -e "${GREEN}============================================================${NC}"
    echo -e "${GREEN}  Hexactyl Panel Installed Successfully!${NC}"
    echo -e "${GREEN}============================================================${NC}"
    echo ""
    echo -e "${ORANGE}  Panel URL:${NC}      http://${SERVER_IP}"
    echo -e "${ORANGE}  Admin Email:${NC}    (create with: php artisan p:user:make)"
    echo -e "${ORANGE}  Database:${NC}       ${DB_NAME}"
    echo -e "${ORANGE}  DB User:${NC}        ${DB_USER}"
    echo -e "${ORANGE}  DB Password:${NC}    ${DB_PASS}"
    echo -e "${ORANGE}  Panel Path:${NC}     ${PANEL_DIR}"
    echo ""
    echo -e "${BLUE}  Create admin user:${NC}"
    echo -e "    cd ${PANEL_DIR}"
    echo -e "    php artisan p:user:make"
    echo ""
    echo -e "${BLUE}  Documentation:${NC}"
    echo -e "    https://hexactyl-docs.vercel.app"
    echo ""
    echo -e "${GREEN}============================================================${NC}"
    
    echo -e "\n${ORANGE}Save these credentials!${NC}" > /root/hexactyl-credentials.txt
    echo "Database: ${DB_NAME}" >> /root/hexactyl-credentials.txt
    echo "DB User: ${DB_USER}" >> /root/hexactyl-credentials.txt
    echo "DB Pass: ${DB_PASS}" >> /root/hexactyl-credentials.txt
    chmod 600 /root/hexactyl-credentials.txt
}

# Run installation
print_banner
check_root
detect_os
install_dependencies
setup_database
setup_panel
setup_nginx
setup_queue
setup_cron
print_summary
