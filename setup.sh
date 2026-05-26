#!/bin/bash
# setup.sh — Run this on your EC2 instance after SSH login
# Works on Ubuntu/Debian. For Amazon Linux 2023, replace apt with dnf.

set -e

# ── 1. Install Apache + PHP ───────────────────
sudo apt update -y
sudo apt install -y apache2 php libapache2-mod-php php-common

# ── 2. Enable required Apache modules ─────────
sudo a2enmod rewrite headers php8.2   # adjust php version if needed
sudo a2enmod rewrite headers expires deflate php8.2
sudo apache2ctl configtest
sudo a2enmod ssl
sudo a2ensite default-ssl
sudo systemctl restart apache2

# ── 3. Deploy site files ──────────────────────
# Copy your /var/www/html files here (scp or git clone)
# Example:
   git clone https://github.com/rajasekharsura/VajraDefend.git /var/www/html

# Set correct permissions
sudo chown -R www-data:www-data /var/www/html
sudo find /var/www/html -type d -exec chmod 755 {} \;
sudo find /var/www/html -type f -exec chmod 644 {} \;

# Create contact log file with write permission
sudo touch /var/log/contact_submissions.log
sudo chown www-data:www-data /var/log/contact_submissions.log
sudo chmod 640 /var/log/contact_submissions.log

# ── 4. Enable your site config ────────────────
sudo cp mysite.conf /etc/apache2/sites-available/mysite.conf
sudo a2ensite mysite.conf
sudo a2dissite 000-default.conf   # disable default site
sudo systemctl reload apache2

# ── 5. Tor Hidden Service setup ───────────────
sudo apt install -y tor
cat <<EOF | sudo tee -a /etc/tor/torrc

HiddenServiceDir  /var/lib/tor/hidden_service/
HiddenServicePort 80 127.0.0.1:80
EOF

sudo systemctl restart tor
sleep 5

# Print .onion address
echo ""
echo "=== Your .onion address ==="
sudo cat /var/lib/tor/hidden_service/hostname

# ── 6. Open firewall (ufw) ────────────────────
# Note: Also open port 80/443 in AWS Security Group (EC2 console)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp
sudo ufw enable

echo ""
echo "=== Setup complete! ==="
echo "Visit: http://$(curl -s ifconfig.me)"
echo "Tor:   check .onion address above"
