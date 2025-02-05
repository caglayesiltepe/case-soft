<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Laravel Sanctum RESTful API

Bu proje, Laravel Sanctum kullanarak geliştirilmiş bir RESTful API'dir. API, kullanıcıların kimlik doğrulaması yapmasını ve çeşitli kaynaklarla etkileşimde bulunmasını sağlar. API, siparişler, ürünler, müşteriler ve indirimler gibi temel işlevsellikleri içerir.

## Başlangıç

Projenizi başlatmak için aşağıdaki adımları izleyin.

# Dış Kaynaklar (External Libraries)
- **Laravel**: PHP framework (https://laravel.com)
- **Sanctum**: Laravel için basit API token tabanlı kimlik doğrulama (https://laravel.com/docs/9.x/sanctum)
- **Swagger/OpenAPI**: API dokümantasyonu ve test aracı (https://swagger.io/)
- **Docker**: Uygulamalarınızı konteynerlerde çalıştıran platform (https://www.docker.com/)

## Gereksinimler

Projeyi çalıştırmak için yerel bilgisayarınızda aşağıdaki gereksinimlerin yüklü olması gerekmektedir:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

## Kurulum


1. Docker container'larını başlatmak için aşağıdaki komutu çalıştırın:

    ```bash
    docker compose up -d --build
    ```

   
2. İlgili containera giriş yapın
    ```bash
    docker exec -ti laraval_app bash
    ```
   
3. Composer bağımlılıklarını yüklemek için şu komutu çalıştırın:
    ```bash
    composer install
    ```
   
4. Migration dosylarını çalıştırın:
    ```bash
    php artisan migrate
    ```

5. Tablolara ilgili verileri eklemek için seeder çalıştırın.
    ```bash
    php artisan db:seed
    ```

6. Swager sayfasına giriş yapın
    ```bash
    http://localhost:9002/api/documentation
    ```
