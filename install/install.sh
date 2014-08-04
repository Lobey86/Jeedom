webserver=${1-nginx}

if [ "${webserver}" = "nginx" ] ; then
    echo "Etes-vous sur de vouloir installer Jeedom ? Attention : ceci ecrasera la configuration par défaut de NGINX s'il elle existe !"
    while true
    do
            echo -n "oui/non: "
            read ANSWER < /dev/tty
            case $ANSWER in
                    oui)
                            break
                            ;;
                    non)
                             echo "Annulation de l'installation"
                            exit 1
                            ;;
            esac
            echo "Répondez oui ou non"
    done
fi

if [ "${webserver}" = "apache" ] ; then
    echo "Etes-vous sur de vouloir installer Jeedom ? Attention : ceci ecrasera la configuration par défaut de APACHE s'il elle existe !"
    while true
    do
            echo -n "oui/non: "
            read ANSWER < /dev/tty
            case $ANSWER in
                    oui)
                            break
                            ;;
                    non)
                             echo "Annulation de l'installation"
                            exit 1
                            ;;
            esac
            echo "Répondez oui ou non"
    done
fi

echo "********************************************************"
echo "*             Installation des dépendances             *"
echo "********************************************************"
sudo apt-get update

if [ "${webserver}" = "nginx" ] ; then 
    sudo apt-get install -y nginx-common nginx-full
fi

if [ "${webserver}" = "apache" ] ; then 
    sudo apt-get install -y apache2 libapache2-mod-php5
    sudo apt-get install -y autoconf make subversion
    sudo svn checkout http://svn.apache.org/repos/asf/httpd/httpd/tags/2.2.22/ httpd-2.2.22
    sudo wget http://cafarelli.fr/gentoo/apache-2.2.24-wstunnel.patch
    sudo cd httpd-2.2.22
    sudo patch -p1 < ../apache-2.2.24-wstunnel.patch
    sudo svn co http://svn.apache.org/repos/asf/apr/apr/branches/1.4.x srclib/apr
    sudo svn co http://svn.apache.org/repos/asf/apr/apr-util/branches/1.3.x srclib/apr-util
    sudo ./buildconf
    sudo ./configure --enable-proxy=shared --enable-proxy_wstunnel=shared
    sudo make
    sudo cp modules/proxy/.libs/mod_proxy{_wstunnel,}.so /usr/lib/apache2/modules/
    sudo chmod 644 /usr/lib/apache2/modules/mod_proxy{_wstunnel,}.so
    echo "# Depends: proxy\nLoadModule proxy_wstunnel_module /usr/lib/apache2/modules/mod_proxy_wstunnel.so" | sudo tee -a /etc/apache2/mods-available/proxy_wstunnel.load
    sudo a2enmod proxy_wstunnel
    sudo a2enmod proxy_http
    sudo a2enmod proxy
    sudo service apache2 restart
fi

sudo apt-get install -y ffmpeg
sudo apt-get install -y libssh2-php
sudo apt-get install -y ntp
sudo apt-get install -y unzip
sudo apt-get install -y mysql-client mysql-common mysql-server mysql-server-core-5.5
echo "Quel mot de passe venez vous de taper (mot de passe root de la MySql) ?"
while true
do
        read MySQL_root < /dev/tty
        echo "Confirmez vous que le mot de passe est : "${MySQL_root}
        while true
        do
            echo -n "oui/non: "
            read ANSWER < /dev/tty
            case $ANSWER in
			oui)
				break
				;;
			non)
				break
				;;
            esac
            echo "Répondez oui ou non"
        done    
        if [ "${ANSWER}" = "oui" ]; then
            break
        fi
done

sudo apt-get install -y nodejs
nodeJS=$?
sudo apt-get install -y php5-common php5-fpm php5-cli php5-curl php5-json php5-mysql
sudo apt-get install -y usb-modeswitch python-serial


echo "********************************************************"
echo "* Création des répertoire et mise en place des droits  *"
echo "********************************************************"

if [ "${webserver}" = "nginx" ] ; then 
    sudo mkdir -p /usr/share/nginx/www
    cd /usr/share/nginx/www
    chown www-data:www-data -R /usr/share/nginx/www
fi
if [ "${webserver}" = "apache" ] ; then 
    sudo mkdir -p /var/www
    cd /var/www
    chown www-data:www-data -R /var/www
fi

echo "********************************************************"
echo "*             Copie des fichiers de Jeedom             *"
echo "********************************************************"
if [ -d "jeedom" ] ; then
    rm -rf jeedom
fi
wget -O jeedom.zip https://market.jeedom.fr/jeedom/stable/jeedom.zip
if [  $? -ne 0 ] ; then
    wget -O jeedom.zip https://market.jeedom.fr/jeedom/stable/jeedom.zip
    if [  $? -ne 0 ] ; then
        echo "Impossible de télécharger le fichier";
        exit 0
    fi
fi
unzip jeedom.zip -d jeedom
if [ "${webserver}" = "nginx" ] ; then 
    sudo mkdir /usr/share/nginx/www/jeedom/tmp
    sudo chmod 775 -R /usr/share/nginx/www
    sudo chown -R www-data:www-data /usr/share/nginx/www
fi
if [ "${webserver}" = "apache" ] ; then 
    sudo mkdir /var/www/jeedom/tmp
    sudo chmod 775 -R /var/www
    sudo chown -R www-data:www-data /var/www
fi
rm -rf jeedom.zip
cd jeedom

if [ ${nodeJS} -ne 0 ] ; then
    x86=$(uname -a | grep x86_64 | wc -l)
    if [ ${x86} -ne 0 ] ; then
        echo "********************************************************"
        echo "*          Installation de nodeJS manuellement x86     *"
        echo "********************************************************"
        sudo deb http://http.debian.net/debian wheezy-backports main
        sudo apt-get install -y nodejs
    else
        echo "********************************************************"
        echo "*          Installation de nodeJS manuellement ARM     *"
        echo "********************************************************"
        wget https://jeedom.fr/ressources/nodejs/node-v0.10.21-cubie.tar.xz
        sudo tar xJvf node-v0.10.21-cubie.tar.xz -C /usr/local --strip-components 1
        if [ ! -f '/usr/bin/nodejs' ] && [ -f '/usr/local/bin/node' ]; then
            sudo ln -s /usr/local/bin/node /usr/bin/nodejs
        fi
        sudo rm -rf node-v0.10.21-cubie.tar.xz
    fi
fi
if [ $( cat /etc/os-release | grep raspbian | wc -l) -gt 0 ] ; then
    echo "********************************************************"
    echo "*  Installation de nodeJS manuellement pour Raspberry  *"
    echo "********************************************************"
    wget https://jeedom.fr/ressources/nodejs/node-raspberry.bin
    sudo rm -rf /usr/local/bin/node
    sudo rm -rf /usr/bin/nodejs
    sudo mv node-raspberry.bin /usr/local/bin/node
    sudo ln -s /usr/local/bin/node /usr/bin/nodejs
    sudo chmod +x /usr/local/bin/node
fi

echo "********************************************************"
echo "*          Configuration de la base de données         *"
echo "********************************************************"
bdd_password=$(cat /dev/urandom | tr -cd 'a-f0-9' | head -c 15)
echo "DROP USER 'jeedom'@'localhost'" | mysql -uroot -p${MySQL_root}
echo "CREATE USER 'jeedom'@'localhost' IDENTIFIED BY '${bdd_password}';" | mysql -uroot -p${MySQL_root}
echo "DROP DATABASE IF EXISTS jeedom;" | mysql -uroot -p${MySQL_root}
echo "CREATE DATABASE jeedom;" | mysql -uroot -p${MySQL_root}
echo "GRANT ALL PRIVILEGES ON jeedom.* TO 'jeedom'@'localhost';" | mysql -uroot -p${MySQL_root}


echo "********************************************************"
echo "*                Installation de Jeedom                *"
echo "********************************************************"
sudo cp core/config/common.config.sample.php core/config/common.config.php
sudo sed -i -e "s/#PASSWORD#/${bdd_password}/g" core/config/common.config.php 
sudo chown www-data:www-data core/config/common.config.php
sudo php install/install.php mode=force


echo "********************************************************"
echo "*                Mise en place du cron                 *"
echo "********************************************************"
if [ "${webserver}" = "nginx" ] ; then 
    croncmd="su --shell=/bin/bash - www-data -c '/usr/bin/php /usr/share/nginx/www/jeedom/core/php/jeeCron.php' >> /dev/null"
fi
if [ "${webserver}" = "apache" ] ; then
    croncmd="su --shell=/bin/bash - www-data -c '/usr/bin/php /var/www/jeedom/core/php/jeeCron.php' >> /dev/null"
fi
cronjob="* * * * * $croncmd"
( crontab -l | grep -v "$croncmd" ; echo "$cronjob" ) | crontab -


if [ "${webserver}" = "nginx" ] ; then 
    echo "********************************************************"
    echo "*                Configuration de NGINX                *"
    echo "********************************************************"
    if [ -f '/etc/init.d/apache2' ]; then
        sudo service apache2 stop
        sudo update-rc.d apache2 remove
    fi
    if [ -f '/etc/init.d/apache' ]; then
        sudo service apache stop
        sudo update-rc.d apache remove
    fi

    sudo service nginx stop
    if [ -f '/etc/nginx/sites-available/defaults' ]; then
        sudo rm /etc/nginx/sites-available/default
    fi
    sudo cp install/nginx_default /etc/nginx/sites-available/default
    if [ ! -f '/etc/nginx/sites-enabled/default' ]; then
        sudo ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
    fi
    sudo service nginx restart
    sudo adduser www-data dialout
    sudo adduser www-data gpio
    sudo sed -i 's/max_execution_time = 30/max_execution_time = 300/g' /etc/php5/fpm/php.ini
    sudo sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 1G/g' /etc/php5/fpm/php.ini
    sudo sed -i 's/post_max_size = 8M/post_max_size = 1G/g' /etc/php5/fpm/php.ini
    sudo service php5-fpm restart
    sudo /etc/init.d/php5-fpm restart
fi

if [ "${webserver}" = "apache" ] ; then 
    echo "********************************************************"
    echo "*                Configuration de APACHE                *"
    echo "********************************************************"
    sudo cp install/apache_default /etc/apache2/sites-available/000-default.conf
    if [ ! -f '/etc/apache2/sites-enabled/000-default.conf' ]; then
        sudo a2ensite 000-default
    fi
    sudo service apache2 restart
    sudo adduser www-data dialout
    sudo adduser www-data gpio
    sudo sed -i 's/max_execution_time = 30/max_execution_time = 300/g' /etc/php5/fpm/php.ini
    sudo sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 1G/g' /etc/php5/fpm/php.ini
    sudo sed -i 's/post_max_size = 8M/post_max_size = 1G/g' /etc/php5/fpm/php.ini
    sudo service php5-fpm restart
    sudo /etc/init.d/php5-fpm restart
fi

echo "********************************************************"
echo "*             Mise en place service nodeJS             *"
echo "********************************************************"
sudo cp jeedom /etc/init.d/
sudo chmod +x /etc/init.d/jeedom
sudo update-rc.d jeedom defaults
if [ "${webserver}" = "apache" ] ; then 
    sudo sed -i 's%PATH_TO_JEEDOM="/usr/share/nginx/www/jeedom"%PATH_TO_JEEDOM="/var/www/jeedom"%g' /etc/init.d/jeedom
fi


echo "********************************************************"
echo "*             Démarrage du service nodeJS              *"
echo "********************************************************"
sudo service jeedom start

echo "********************************************************"
echo "*             Action post installation                 *"
echo "********************************************************"
sudo cp install/motd /etc
sudo chown root:root /etc/motd
sudo chmod 644 /etc/motd

echo "********************************************************"
echo "*                 Installation finie                   *"
echo "********************************************************"
IP=$(ifconfig eth0 | grep 'inet adr:' | cut -d: -f2 | awk '{print $1}')
echo "Vous pouvez vous connecter sur jeedom en allant sur $IP/jeedom et en utilisant les identifiants admin/admin"