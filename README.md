## Environment Setup Process

This guide walks you through setting up the repository.

- Clone the Repository
```bash
   git clone https://github.com/MuhammadMahediHasan/small-e-commerce-startup.git
   cd small-e-commerce-startup 
```
- Configure Environment Variables

```bash
    cp .env.example .env
```

- Install Dependencies
```bash
    composer install
```

Update the database credentials in .env:
```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
```

- Run Migrations
```bash
  php artisan migrate
```

- Run Test Cases
```bash
   ./vendor/bin/pest
```

### For Docker

- Install `docker & docker-compose` in your machine.
- Copy .env `cp .env .env-example`.
- Set `NGINX_PORT`, `PMA_PORT` and database variables.
```
DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=small_ecommerce
DB_USERNAME=@@root
DB_PASSWORD=@@password
```
- Build docker container `sudo docker-compose up -d --build`
- Go to application container, run `composer install && php artisan migrate`
- Finally, run your application on given NGINX port.
