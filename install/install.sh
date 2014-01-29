apt-get install git git-core  git-man
apt-get install nginx-common  nginx-full
apt-get install mysql-client mysql-common mysql-server mysql-server-core
apt-get install nodejs php5-common php5-fpm php5-cli php5-curl php5-json
mkdir -p /usr/share/nginx/www
cd /usr/share/nginx/www
git clone -b stable --depth 1 https://github.com/zoic21/jeedom.git
chmod 777 -R log
cp jeedom /etc/init.d/
update-rc.d jeedom defaults
service jeedom start