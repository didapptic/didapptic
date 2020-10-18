#!/usr/bin/env bash

# MIT License
#
# Copyright (c) 2020 didapptic, <info@didapptic.com>
#
# @author Dogan Ucar <dogan@dogan-ucar.de>
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.

# Variables
DBNAME=didapptic
DBUSER=didapptic
DBPASSWD=didapptic

apt-get -y install software-properties-common
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get update

debconf-set-selections <<< "mysql-server mysql-server/root_password password $DBPASSWD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $DBPASSWD"

apt-get -y install mysql-server
sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf

mysql -uroot -p$DBPASSWD -e "CREATE DATABASE $DBNAME"

mysql -uroot -p$DBPASSWD -e "CREATE DATABASE $DBNAME"
mysql -uroot -p$DBPASSWD -e "grant all privileges on $DBNAME.* to '$DBUSER'@'%' identified by '$DBPASSWD'"
mysql -uroot -p$DBPASSWD -e "flush privileges"

sudo apt-get -y install apache2 php7.4 php7.4-mysql php7.4-mbstring php7.4-dom php7.4-sqlite php7.4-zip php7.4-curl

sudo apt-get -y install curl composer zip unzip

sudo phpenmod pdo_mysql
sudo service apache2 restart
sudo service mysql restart
