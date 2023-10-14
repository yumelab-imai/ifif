使用方法
```
$ docker compose build
$ docker compose up -d
$ docker compose exec app bash
# root@0e8d8ab6688f:/var/www/html# composer -V
# root@0e8d8ab6688f:/var/www/html# npm -v
<!-- コマンドの末尾の.はカレントディレクトリの意 -->
composer create-project --prefer-dist "laravel/laravel=10.*" .
```