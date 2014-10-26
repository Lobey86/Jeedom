#!/bin/sh
########################################################################
#
# Jeedom installer: shell script to deploy Jeedom and its dependencies
#
########################################################################

############################ Translations ##############################
# MUST be embedded, otherwise the network installation of Jeedom
# won't work!

install_msg_en()
{
	msg_installer_welcome="*           Welcome to the Jeedom installer            *"
	msg_usage1="Usage: $0 [<webserver_name>]"
	msg_usage2="            webserver_name can be 'apache' or 'nginx' (default)"
	msg_manual_install_nodejs_x86="*          Manual installation of nodeJS for x86       *"
	msg_manual_install_nodejs_ARM="*          Manual installation of nodeJS for ARM       *"
	msg_manual_install_nodejs_RPI="*          Manual installation of nodeJS for Raspberry *"
	msg_nginx_config="*                  NGINX configuration                 *"
	msg_apache_config="*                  APACHE configuration                *"
	msg_question_install_jeedom="Are you sure you want to install Jeedom?"
	msg_warning_install_jeedom="Warning: this will overwrite the default ${ws_upname} configuration if it exists!"
	msg_warning_overwrite_jeedom="Warning: your existing Jeedom installation will be overwritten!"
	msg_yes="yes"
	msg_no="no"
	msg_yesno="yes/no: "
	msg_cancel_install="Canceling the installation"
	msg_answer_yesno="Answer yes or no"
	msg_install_deps="*               Dependencies installation              *"
	msg_passwd_mysql="What password do you have just typed (MySQL root password)?"
	msg_confirm_passwd_mysql="Do you confirm that the password is:"
	msg_bad_passwd_mysql="The MySQL password provided is invalid!"
	msg_setup_dirs_and_privs="*      Creating directories and setting up rights      *"
	msg_copy_jeedom_files="*                Copying files of Jeedom               *"
	msg_unable_to_download_file="Unable to download the file"
	msg_config_db="*               Configuring the database               *"
	msg_install_jeedom="*                 Installing de Jeedom                 *"
	msg_setup_cron="*                   Setting up cron                    *"
	msg_setup_nodejs_service="*               Setting up nodeJS service              *"
	msg_startup_nodejs_service="*             Starting the nodeJS service              *"
	msg_post_install_actions="*             Post-installation actions                *"
	msg_install_complete="*                Installation complete                 *"
	msg_or="or"
	msg_login_info1="You can log in to Jeedom by going on:"
	msg_login_info2="Your credentials are:"
	msg_optimize_webserver_cache="* Checking for webserver cache optimization            *"
	msg_php_version="PHP version ${PHP_VERSION} found"
	msg_php_already_optimized="PHP is already optimized (using ${PHP_OPTIMIZATION})"
	msg_optimize_webserver_cache_apc="Installing APC cache optimization"
	msg_optimize_webserver_cache_opcache="Installing Zend OpCache cache optimization"
}

install_msg_fr()
{
	msg_installer_welcome="*         Bienvenue dans l'installateur Jeedom         *"
	msg_usage1="Utilisation: $0 [<nom_du_webserver>]"
	msg_usage2="             nom_du_webserver peut être 'apache' ou 'nginx' (par défaut)"
	msg_manual_install_nodejs_x86="*        Installation manuelle de nodeJS pour x86       *"
	msg_manual_install_nodejs_ARM="*        Installation manuelle de nodeJS pour ARM       *"
	msg_manual_install_nodejs_RPI="*     Installation manuelle de nodeJS pour Raspberry    *"
	msg_nginx_config="*                Configuration de NGINX                *"
	msg_apache_config="*                Configuration de APACHE               *"
	msg_question_install_jeedom="Etes-vous sûr de vouloir installer Jeedom ?"
	msg_warning_install_jeedom="Attention : ceci écrasera la configuration par défaut de ${ws_upname} si elle existe !"
	msg_warning_overwrite_jeedom="Attention : votre installation existante de Jeedom va être écrasée !"
	msg_yes="oui"
	msg_no="non"
	msg_yesno="oui / non : "
	msg_cancel_install="Annulation de l'installation"
	msg_answer_yesno="Répondez oui ou non"
	msg_install_deps="*             Installation des dépendances             *"
	msg_passwd_mysql="Quel mot de passe venez vous de taper (mot de passe root de la MySql) ?"
	msg_confirm_passwd_mysql="Confirmez vous que le mot de passe est :"
	msg_bad_passwd_mysql="Le mot de passe MySQL fourni est invalide !"
	msg_setup_dirs_and_privs="* Création des répertoires et mise en place des droits *"
	msg_copy_jeedom_files="*             Copie des fichiers de Jeedom             *"
	msg_unable_to_download_file="Impossible de télécharger le fichier"
	msg_config_db="*          Configuration de la base de données         *"
	msg_install_jeedom="*                Installation de Jeedom                *"
	msg_setup_cron="*                Mise en place du cron                 *"
	msg_setup_nodejs_service="*            Mise en place du service nodeJS           *"
	msg_startup_nodejs_service="*             Démarrage du service nodeJS              *"
	msg_post_install_actions="*             Action post installation                 *"
	msg_install_complete="*                Installation terminée                 *"
	msg_or="ou"
	msg_login_info1="Vous pouvez vous connecter sur Jeedom en allant sur :"
	msg_login_info2="Vos identifiants sont :"
	msg_optimize_webserver_cache="* Vérification de l'optimisation de cache              *"
	msg_php_version="PHP version ${PHP_VERSION} trouvé"
	msg_php_already_optimized="PHP est déjà optimisé (utilisation d'${PHP_OPTIMIZATION})"
	msg_optimize_webserver_cache_apc"Installation de l'optimisation de cache APC"
	msg_optimize_webserver_cache_opcache="Installation de l'optimisation de cache Zend OpCache"
}

########################## Helper functions ############################

setup_i18n()
{
	lang=${LANG:=en_US}
	case ${lang} in
		[Ff][Rr]*)
			install_msg_fr
			;;
		[Ee][Nn]*|*)
			install_msg_en
			;;
	esac
}

usage_help()
{
	echo "${msg_usage1}"
	echo "${msg_usage2}"
	exit 1
}
configure_php()
{
    sudo adduser www-data dialout
    sudo adduser www-data gpio
    sudo sed -i 's/max_execution_time = 30/max_execution_time = 300/g' /etc/php5/fpm/php.ini
    sudo sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 1G/g' /etc/php5/fpm/php.ini
    sudo sed -i 's/post_max_size = 8M/post_max_size = 1G/g' /etc/php5/fpm/php.ini
    sudo service php5-fpm restart
    sudo /etc/init.d/php5-fpm restart
}

# Install nodeJS from alternative sources, in case it was not in the
# official repository
nodejs_manual_install()
{
	if [ ${1} -ne 0 ] ; then
		x86=$(uname -a | grep x86_64 | wc -l)
		if [ ${x86} -ne 0 ] ; then
			echo "********************************************************"
			echo "${msg_manual_install_nodejs_x86}"
			echo "********************************************************"
			sudo deb http://http.debian.net/debian wheezy-backports main
			sudo apt-get install -y nodejs
		else
			echo "********************************************************"
			echo "${msg_manual_install_nodejs_ARM}"
			echo "********************************************************"
			wget --no-check-certificate https://jeedom.fr/ressources/nodejs/node-v0.10.21-cubie.tar.xz
			sudo tar xJvf node-v0.10.21-cubie.tar.xz -C /usr/local --strip-components 1
			if [ ! -f '/usr/bin/nodejs' ] && [ -f '/usr/local/bin/node' ]; then
				sudo ln -s /usr/local/bin/node /usr/bin/nodejs
			fi
			sudo rm -rf node-v0.10.21-cubie.tar.xz
		fi
	fi
	if [ $( cat /etc/os-release | grep raspbian | wc -l) -gt 0 ] ; then
		echo "********************************************************"
		echo "${msg_manual_install_nodejs_RPI}"
		echo "********************************************************"
		wget --no-check-certificate https://jeedom.fr/ressources/nodejs/node-raspberry.bin
		sudo rm -rf /usr/local/bin/node
		sudo rm -rf /usr/bin/nodejs
		sudo mv node-raspberry.bin /usr/local/bin/node
		sudo ln -s /usr/local/bin/node /usr/bin/nodejs
		sudo chmod +x /usr/local/bin/node
	fi
}

configure_nginx()
{
    echo "********************************************************"
	echo "${msg_nginx_config}"
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
    configure_php
    sudo update-rc.d nginx defaults
}

configure_apache()
{
    echo "********************************************************"
	echo "${msg_apache_config}"
    echo "********************************************************"
    sudo cp install/apache_default /etc/apache2/sites-available/000-default.conf
    if [ ! -f '/etc/apache2/sites-enabled/000-default.conf' ]; then
        sudo a2ensite 000-default.conf
    fi
    sudo service apache2 restart
    configure_php
}

# Compare two "X.Y.Z" formated versions
# Return 0 if $1 is lesser than $2
# Return 1 if $1 is greater than or equal to $2
is_version_greater_or_equal()
{
	# only compare the 2 first digits
	for i in 1 2 3
	do
		REF="`echo $2 | cut -d. -f$i`"
		CMP="`echo $1 | cut -d. -f$i`"
		if [ $CMP -lt $REF ]; then
			# not greater or equal
			return 0
			break
		fi
	done
	# greater or equal
	return 1
}

optimize_webserver_cache_apc()
{
	# php < 5.5 => APC
	echo "${msg_optimize_webserver_cache_apc}"
	sudo apt-get install -y php-apc php-pear php5-dev build-essential libpcre3-dev
	sudo pear config-set php_ini /etc/php5/fpm/php_ini
	sudo pear config-set php_ini /etc/php5/cli/php_ini
	sudo pecl config-set php_ini /etc/php5/fpm/php_ini
	sudo pecl config-set php_ini /etc/php5/cli/php_ini
	# Force pecl unattended mode
	yes '' | sudo pecl install -fs apc
	echo 'apc.enable_cli = 1' >> /etc/php5/cli/conf.d/20-apc.ini
}

optimize_webserver_cache_opcache()
{
	# php >= 5.5 => OPcache
	echo "${msg_optimize_webserver_cache_opcache}"
	sudo apt-get install -y php-pear php5-dev build-essential
	# Force pecl unattended mode
	yes '' | sudo pecl install -fs zendopcache-7.0.3

	# Enable cache for FPM and CLI
	for i in fpm cli
	do
		echo "zend_extension=opcache.so" >> /etc/php5/${i}/php.ini
		echo "opcache.memory_consumption=256"  >> /etc/php5/${i}/php.ini
		echo "opcache.interned_strings_buffer=8"  >> /etc/php5/${i}/php.ini
		echo "opcache.max_accelerated_files=4000"  >> /etc/php5/${i}/php.ini
		echo "opcache.revalidate_freq=1"  >> /etc/php5/${i}/php.ini
		echo "opcache.fast_shutdown=1"  >> /etc/php5/${i}/php.ini
		echo "opcache.enable_cli=1"  >> /etc/php5/${i}/php.ini
		echo "opcache.enable=1"  >> /etc/php5/${i}/php.ini
	done
}

# Check the version of PHP, and if already optimized
# Otherwise, install cache optimization according to PHP version
optimize_webserver_cache()
{
	echo "${msg_php_version}"

	# Check if PHP is already optimized or not (empty string)
	if [ -n "${PHP_OPTIMIZATION}" ]; then
		echo "${msg_php_already_optimized}"
		return
	fi

	is_version_greater_or_equal "${PHP_VERSION}" "5.5.0"
	case $? in
		0)
			optimize_webserver_cache_apc
			;;
		1)
			optimize_webserver_cache_opcache
			;;
	esac
	# FIXME: may be done in common with configure_php()
	service php5-fpm restart
}

##################### Main (script entry point) ########################

webserver=${1-nginx}
ws_upname="$(echo ${webserver} | tr 'a-z' 'A-Z')"

# Get the currently installed php version
PHP_VERSION="`php -v | awk '/PHP [0-9].[0-9].[0-9].*/{ print $2 }' | cut -d'-' -f1`"
PHP_OPTIMIZATION="`php -v | grep -e 'OPcache' -o -e 'APC'`"

# Select the right language, among available ones
setup_i18n

echo "********************************************************"
echo "${msg_installer_welcome}"
echo "********************************************************"

# Check that the provided ${webserver} is supported [nginx,apache]
case ${webserver} in
	nginx)
		# Configuration
		webserver_home="/usr/share/nginx/www"
		croncmd="su --shell=/bin/bash - www-data -c 'nice -n 19 /usr/bin/php /usr/share/nginx/www/jeedom/core/php/jeeCron.php' >> /dev/null"
		;;
	apache)
		# Configuration
		webserver_home="/var/www"
		croncmd="su --shell=/bin/bash - www-data -c 'nice -n 19 /usr/bin/php /var/www/jeedom/core/php/jeeCron.php' >> /dev/null"
		;;
	*)
		usage_help
		exit 1
		;;
esac

echo "${msg_question_install_jeedom}"
echo "${msg_warning_install_jeedom}"
[ -d "${webserver_home}/jeedom/" ] && echo "${msg_warning_overwrite_jeedom}"
while true
do
		echo -n "${msg_yesno}"
		read ANSWER < /dev/tty
		case $ANSWER in
				${msg_yes})
						break
						;;
				${msg_no})
						echo "${msg_cancel_install}"
						exit 1
						;;
		esac
		echo "${msg_answer_yesno}"
done

echo "********************************************************"
echo "${msg_install_deps}"
echo "********************************************************"
sudo apt-get update

if [ "${webserver}" = "nginx" ] ; then 
    # Packages dependencies
    sudo apt-get install -y nginx-common nginx-full
fi

if [ "${webserver}" = "apache" ] ; then 
    # Packages dependencies
    sudo apt-get install -y apache2 libapache2-mod-php5
    sudo apt-get install -y autoconf make subversion
    sudo svn checkout http://svn.apache.org/repos/asf/httpd/httpd/tags/2.2.22/ httpd-2.2.22
    sudo wget --no-check-certificate http://cafarelli.fr/gentoo/apache-2.2.24-wstunnel.patch
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
sudo apt-get install -y miniupnpc
sudo apt-get install -y mysql-client mysql-common mysql-server mysql-server-core-5.5
echo "${msg_passwd_mysql}"
while true
do
        read MySQL_root < /dev/tty
        echo "${msg_confirm_passwd_mysql} ${MySQL_root}"
        while true
        do
            echo -n "${msg_yesno}"
            read ANSWER < /dev/tty
            case $ANSWER in
			${msg_yes})
				break
				;;
			${msg_no})
				break
				;;
            esac
            echo "${msg_answer_yesno}"
        done    
        if [ "${ANSWER}" = "${msg_yes}" ]; then
			# Test access immediately
			# to ensure that the provided password is valid
			CMD="`echo "show databases;" | mysql -uroot -p${MySQL_root}`"
			if [ $? -eq 0 ]; then
				# good password
				break
			else
				echo "${msg_bad_passwd_mysql}"
				echo "${msg_passwd_mysql}"
				continue
			fi
        fi
done

sudo apt-get install -y nodejs
nodeJS=$?
sudo apt-get install -y php5-common php5-fpm php5-cli php5-curl php5-json php5-mysql
sudo apt-get install -y usb-modeswitch python-serial


echo "********************************************************"
echo "${msg_setup_dirs_and_privs}"
echo "********************************************************"

sudo mkdir -p "${webserver_home}"
cd "${webserver_home}"
chown www-data:www-data -R "${webserver_home}"

echo "********************************************************"
echo "${msg_copy_jeedom_files}"
echo "********************************************************"
if [ -d "jeedom" ] ; then
    rm -rf jeedom
fi
wget --no-check-certificate -O jeedom.zip https://market.jeedom.fr/jeedom/stable/jeedom.zip
if [  $? -ne 0 ] ; then
    wget --no-check-certificate -O jeedom.zip https://market.jeedom.fr/jeedom/stable/jeedom.zip
    if [  $? -ne 0 ] ; then
        echo "${msg_unable_to_download_file}"
        exit 0
    fi
fi
unzip jeedom.zip -d jeedom
sudo mkdir "${webserver_home}"/jeedom/tmp
sudo chmod 775 -R "${webserver_home}"
sudo chown -R www-data:www-data "${webserver_home}"
rm -rf jeedom.zip
cd jeedom

# Check if nodeJS was actually, otherwise do a manual install
nodejs_manual_install ${nodeJS}

echo "********************************************************"
echo "${msg_config_db}"
echo "********************************************************"
bdd_password=$(cat /dev/urandom | tr -cd 'a-f0-9' | head -c 15)
echo "DROP USER 'jeedom'@'localhost'" | mysql -uroot -p${MySQL_root}
echo "CREATE USER 'jeedom'@'localhost' IDENTIFIED BY '${bdd_password}';" | mysql -uroot -p${MySQL_root}
echo "DROP DATABASE IF EXISTS jeedom;" | mysql -uroot -p${MySQL_root}
echo "CREATE DATABASE jeedom;" | mysql -uroot -p${MySQL_root}
echo "GRANT ALL PRIVILEGES ON jeedom.* TO 'jeedom'@'localhost';" | mysql -uroot -p${MySQL_root}


echo "********************************************************"
echo "${msg_install_jeedom}"
echo "********************************************************"
sudo cp core/config/common.config.sample.php core/config/common.config.php
sudo sed -i -e "s/#PASSWORD#/${bdd_password}/g" core/config/common.config.php 
sudo chown www-data:www-data core/config/common.config.php
sudo php install/install.php mode=force

echo "********************************************************"
echo "${msg_setup_cron}"
echo "********************************************************"

croncmd="su --shell=/bin/bash - www-data -c 'nice -n 19 /usr/bin/php /usr/share/nginx/www/jeedom/core/php/jeeCron.php' >> /dev/null"
cronjob="* * * * * $croncmd"
( crontab -l | grep -v "$croncmd" ; echo "$cronjob" ) | crontab -

case ${webserver} in
	nginx)
		configure_nginx
		;;
	apache)
		configure_apache
		;;
esac

echo "********************************************************"
echo "${msg_optimize_webserver_cache}"
echo "********************************************************"
optimize_webserver_cache

echo "********************************************************"
echo "${msg_setup_nodejs_service}"
echo "********************************************************"
sudo cp jeedom /etc/init.d/
sudo chmod +x /etc/init.d/jeedom
sudo update-rc.d jeedom defaults
if [ "${webserver}" = "apache" ] ; then 
    sudo sed -i 's%PATH_TO_JEEDOM="/usr/share/nginx/www/jeedom"%PATH_TO_JEEDOM="/var/www/jeedom"%g' /etc/init.d/jeedom
fi

echo "********************************************************"
echo "${msg_startup_nodejs_service}"
echo "********************************************************"
sudo service jeedom start

echo "********************************************************"
echo "${msg_post_install_actions}"
echo "********************************************************"
sudo cp install/motd /etc
sudo chown root:root /etc/motd
sudo chmod 644 /etc/motd

echo "********************************************************"
echo "${msg_install_complete}"
echo "********************************************************"
IP=$(ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{print $1}')
HOST=$(hostname -f)
echo "${msg_login_info1}"
echo "\n\t\thttp://$IP/jeedom ${msg_or} http://$HOST/jeedom\n"
echo "${msg_login_info2} admin/admin"
