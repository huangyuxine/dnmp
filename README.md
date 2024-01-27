- [镜像搜索](#镜像搜索)
- [拉取镜像](#拉取镜像)
- [查看镜像](#查看镜像)
- [删除镜像](#删除镜像)
- [查看容器](#查看容器)
- [停止容器](#停止容器)
- [启动容器](#启动容器)
- [进入容器](#进入容器)
- [删除容器](#删除容器)
- [实战](#实战)
  - [Nginx](#nginx)
    - [安装](#安装)
    - [运行](#运行)
  - [PHP](#php)
    - [安装](#安装-1)
    - [运行](#运行-1)
    - [进入容器](#进入容器-1)
    - [删除](#删除)
    - [重新运行](#重新运行)
    - [测试](#测试)
- [通信](#通信)
  - [使用IP](#使用ip)
  - [自定义网络](#自定义网络)
    - [创建](#创建)
    - [查看](#查看)
    - [删除网络](#删除网络)
    - [修改php和nginx的通信](#修改php和nginx的通信)
- [小插曲](#小插曲)


## 镜像搜索

```docker search php
docker search php
```

## 拉取镜像

```
docker pull php:8.3.2
```

## 查看镜像

```
docker images
```

## 删除镜像

```
docker image rm php:8.3.2
```

## 查看容器

停止、启动、删除可以使用 `CONTAINER ID｜NAMES`

查看所有

```
docker ps -a
```

正在运行的

```
docker ps
```

## 停止容器

```
docker stop f0af8d480cc3
```

## 启动容器

```
docker start f0af8d480cc3
```

## 进入容器

查看CONTAINER ID，进入容器内部

```
docker exec -it f0af8d480cc3 sh
```

退出容器

```
exit
```

## 删除容器

```
docker rm f0af8d480cc3
```

## Nginx+PHP

所在目录`～/php/test`

### Nginx

#### 安装

```
docker pull nginx:1.25.3-alpine
```

#### 运行

运行`IMAGE ID`

**--name：**给容器起名字

**-v：**目录挂载，宿主目录:容器内部目录:权限。ro表示容器内部文件指定只读，rw表示容器内部文件与宿主增删改查相互同步。

**-d：**后台运行

**-p：**指定端口映射，宿主端口:容器端口

```shell
docker run --name nginx \
-v ./www:/www:rw \
-v ./services/nginx/conf.d:/etc/nginx/conf.d:rw \
-v ./services/nginx/nginx.conf:/etc/nginx/nginx.conf:ro \
-v ./logs/nginx:/var/log/nginx:rw \
-p 8080:80 \
-d 74077e780ec7
```

确保容器映射的文件存在，不然会报下面错误

>Are you trying to mount a directory onto a file (or vice-versa)? Check if the specified host path exists and is the expected type.

先不挂载将配置文件复制过来，虽然没有完全运行起来，但是容器已经创建了，先把容器删掉

`docker ps -a` #查看所有容器，找到对应的CONTAINER ID,我的是ef0b2490ff34

`mkdir -p services/nginx`

```
docker cp f0af8d480cc3:/etc/nginx/nginx.conf ~/php/test/services/nginx
```

```
docker cp f0af8d480cc3:/etc/nginx/conf.d ~/php/test/services/nginx
```

然后删除容器

```
docker rm f0af8d480cc3
```

然后再运行上面的命令就成功了，我用的是相对路径，用绝对路径也行的

```shell
docker run --name nginx \
-v /Users/tomato/php/test/www:/www:rw \
-v /Users/tomato/php/test/services/nginx/conf.d:/etc/nginx/conf.d:rw \
-v /Users/tomato/php/test/services/nginx/nginx.conf:/etc/nginx/nginx.conf:ro \
-v /Users/tomato/php/test/logs/nginx:/var/log/nginx:rw \
-p 8080:80 \
-d 74077e780ec7
```

当前访问localhost:8080还是不行的，在www目录下创建index.html，修改配置文件conf.d/default.conf即可访问

```nginx
server {
    listen       80;
    listen  [::]:80;
    server_name  localhost;

    #access_log  /var/log/nginx/host.access.log  main;

    location / {
        root   /www;
        index  index.html index.htm;
    }

    #error_page  404              /404.html;

    # redirect server error pages to the static page /50x.html
    #
    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   /usr/share/nginx/html;
    }

    # proxy the PHP scripts to Apache listening on 127.0.0.1:80
    #
    #location ~ \.php$ {
    #    proxy_pass   http://127.0.0.1;
    #}

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    #
    #location ~ \.php$ {
    #    root           html;
    #    fastcgi_pass   127.0.0.1:9000;
    #    fastcgi_index  index.php;
    #    fastcgi_param  SCRIPT_FILENAME  /scripts$fastcgi_script_name;
    #    include        fastcgi_params;
    #}

    # deny access to .htaccess files, if Apache's document root
    # concurs with nginx's one
    #
    #location ~ /\.ht {
    #    deny  all;
    #}
}
```

### PHP

#### 安装

```
docker pull php:8.3.2-fpm
```

#### 运行

```
docker run --name php83 -v ./www:/www -p 9501:9000 -d aef26d7d2610
```

#### 进入容器

```
docker exec -it cc8fd0f5305e sh
```

复制一份配置

```
cd /usr/local/etc/php

cp php.ini-development php.ini

exit
```

复制到宿主

```
docker cp cc8fd0f5305e:/usr/local/etc/php/php.ini ./services/php/php.ini
```

#### 删除

```
docker rm -f  cc8fd0f5305e
```

#### 重新运行

```shell
docker run --name php83 -v ./www:/www:rw -p 9501:9000 \
-v  ./services/php83/php.ini:/usr/local/etc/php/php.ini:ro \
-d aef26d7d2610
```

#### 测试

因为nginx没有和php容器之间通信，所以删除容器重新运行

**--link：**

```shell
docker run --name nginx \
-v /Users/tomato/php/test/www:/www:rw \
-v /Users/tomato/php/test/services/nginx/conf.d:/etc/nginx/conf.d:rw \
-v /Users/tomato/php/test/services/nginx/nginx.conf:/etc/nginx/nginx.conf:ro \
-v /Users/tomato/php/test/logs/nginx:/var/log/nginx:rw \
-p 8080:80 \
--link php83:php83 \
-d 74077e780ec7
```

`www/localhost`下创建`index.php`

```php
<?php
  
	phpinfo();
```

配置`conf.d/default.conf`

```shell
server {
    listen       80;
    listen  [::]:80;
    server_name  localhost;
    root   /www/localhost;
    index  index.php index.html index.htm;

    #access_log  /var/log/nginx/host.access.log  main;

    access_log /dev/null;
    #access_log  /var/log/nginx/nginx.localhost.access.log  main;
    error_log  /var/log/nginx/localhost.error.log  warn;

    #error_page  404              /404.html;

    # redirect server error pages to the static page /50x.html
    #
    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   /usr/share/nginx/html;
    }

    # proxy the PHP scripts to Apache listening on 127.0.0.1:80
    #
    #location ~ \.php$ {
    #    proxy_pass   http://127.0.0.1;
    #}

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    # 
    location ~ \.php$ {
       fastcgi_pass   php83:9000;
    #    fastcgi_index  index.php;
    #    fastcgi_param  SCRIPT_FILENAME  /scripts$fastcgi_script_name;
       fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
       include        fastcgi_params;
    }

    # deny access to .htaccess files, if Apache's document root
    # concurs with nginx's one
    #
    #location ~ /\.ht {
    #    deny  all;
    #}
}
```

## 通信

为了实现不同容器通过容器名或别名的互连，docker提供了以下几种：

1. 在启动docker容器时加入`--link`参数，但是目前已经被废弃，废弃的主要原因是需要在连接的两个容器上都创建`--link`选项，当互连的容器数量较多时，操作的复杂度会显著增加；
2. 启动docker容器后进入容器并修改`/etc/host`配置文件，缺点是手动配置较为繁杂；
3. 用户自定义`bridge`网桥，这是目前解决此类问题的主要方法

### 使用IP

不使用`--link`去建立连接，把`php83`改成用ip表示

```nginx
fastcgi_pass   172.17.0.2:9000;
```

### 自定义网络

#### 创建

```
docker network create local
```

#### 查看

```
docker network list
```

#### 删除网络

```
docker network rm NAME
```

#### 修改php和nginx的通信

删除掉两个容器

```shell
docker run --name php83 \
-v ./www:/www -p 9501:9000 \
--net=local \
-d aef26d7d2610
```

```
docker run --name nginx \
-v /Users/tomato/php/test/www:/www:rw \
-v /Users/tomato/php/test/services/nginx/conf.d:/etc/nginx/conf.d:rw \
-v /Users/tomato/php/test/services/nginx/nginx.conf:/etc/nginx/nginx.conf:ro \
-v /Users/tomato/php/test/logs/nginx:/var/log/nginx:rw \
-p 8080:80 \
--net=local \
-d 74077e780ec
```

## 小插曲

[toc] 生成目录，github上不显示，vscode使用`Markdown All in One`插件。打开文档，`command+shift+p`输入找到`create table of contents`这个选项回车即可。

## MySQL+Redis

### MySQL

#### 拉取

```
docker pull mysql:8.3
```

#### 本地创建文件

`services/mysql83/mysql.cnf`

```
slow_query_log
long_query_time         = 3
slow-query-log-file     = /var/log/mysql/mysql.slow.log
log-error               = /var/log/mysql/mysql.error.log
```

#### 运行

```shell
docker run --name mysql83 \
-e MYSQL_ROOT_PASSWORD=123123 \
-v ./services/mysql83/mysql.cnf:/etc/mysql/conf.d/mysql.cnf:ro \
-v ./logs/mysql83:/var/log/mysql:rw \
-v ./data/mysql83:/var/lib/mysql/:rw \
--net=local \
-d mysql:8.3
```

#### 测试

`localhost/index.php`

```php
<?php

    $mysql = "mysql83";
    
    $pdo = new PDO("mysql:host={$mysql};dbname=mysql", 'root', '123123');

    var_dump($pdo);
```

报错如下

>**Fatal error**: Uncaught PDOException: could not find driver in /www/localhost/index.php:5 Stack trace: #0 /www/localhost/index.php(5): PDO->__construct('mysql:host=mysq...', 'root', Object(SensitiveParameterValue)) #1 {main} thrown in **/www/localhost/index.php** on line **5**

原因是没有`pdo_mysql`扩展

#### 安装扩展

进入php容器内部，执行

```
docker-php-ext-install pdo_mysql
```

然后刷新一下页面，至此mysql链接成功

### Redis

#### 文件创建

`services/redis`

切换到redis，并拉取配置文件

```
wget http://download.redis.io/redis-stable/redis.conf
```

修改配置

```
requirepass 123123
#bind 127.0.0.1 -::1
bind redis
```

#### 拉取

```
docker pull redis:7.2.4
```

#### 运行

```shell
docker run --name redis \
-v ./services/redis/redis.conf:/etc/redis/redis.conf \
-v ./data/redis:/data:rw \
--net=local \
-d redis:7.2.4 redis-server /etc/redis/redis.conf
```

#### 安装扩展

```shell
pecl install -o -f redis \
&& rm -rf /tmp/pear \ 
&& docker-php-ext-enable redis
```

#### 测试

```php
  $redis = new Redis();
  $redis->connect('redis', 6379);
  $auth = $redis->auth('123123'); 
  var_dump($auth);
```

