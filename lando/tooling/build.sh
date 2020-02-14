#!/bin/bash
apt update
apt install sudo -y
adduser www-data sudo
echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers
sudo chmod 755 web/sites/default
