#! /bin/bash

echo '=== start install ========'

# 1. 源码安装node、npm、yarn
# 下的慢，直接放git
# wget https://cdn.npmmirror.com/binaries/node/latest-v16.x/node-v16.15.1-linux-x64.tar.xz
tar -xf node-v16.15.1-linux-x64.tar.xz
mv node-v16.15.1-linux-x64 /usr/local/node
ln -s /usr/local/node/bin/node /usr/bin/node
ln -s /usr/local/node/bin/npm /usr/bin/npm
npm install yarn -g
ln -s /usr/local/node/bin/yarn /usr/bin/yarn

# 2. 添加PHP PPA存储库
sudo add-apt-repository ppa:ondrej/php

# 3. 更新apt-get源
sudo apt-get update

# 4. 安装PHP扩展
sudo apt-get install -y dialog php7.4 php7.4-fpm php7.4-xml php7.4-mbstring php7.4-gd php7.4-mcrypt php7.4-curl php7.4-mysql php7.4-redis

# 5. 安装composer
# 下载比较慢，直接放git了
# curl -sS https://getcomposer.org/installer | php
chmod -R 777 composer.phar
sudo mv composer.phar /usr/local/bin/composer

# 6. 安装nginx
sudo apt-get install -y nginx

# 7. 安装redis
sudo apt-get install -y redis-server

echo '=== end install ========'

echo '=== start modify ========'

# 1. 修改redis配置文件
sed -i "s/$PIDFILE --chuid redis:redis --exec/$PIDFILE --exec/" /etc/init.d/redis-server

# 2. 修改php ini配置
sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/7.4/cli/php.ini

# 3. 修改php-fpm ini配置
sed -i "s/listen = \/run\/php\/php7.4-fpm.sock/listen = 127.0.0.1:9000/" /etc/php/7.4/fpm/pool.d/www.conf

# 4. 替换nginx目标
sudo tee /etc/nginx/sites-available/default <<-'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    root /workspace/clang-quickstart/dog/DogApi/public;
    index index.php index.html index.htm index.html;

    server_name _;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass 127.0.0.1:9000;
    }
}
EOF

echo '=== end modify ========'


echo '=== start load ========'

# 1. 创建php进程文件
mkdir /run/php
touch /run/php/php7.4-fpm.pid

# 8. 重启php-fpm
service php7.4-fpm restart

# 9. 启动nginx
nginx
service nginx reload

# 10. 重启redis
service redis-server start

echo '=== end load ========'

echo '=== start check version ========'
nginx -V
php -version
node -v
npm -v
redis-cli --version
echo '=== end check version ========'