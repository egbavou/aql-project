# aql project

## Prerequisites
- [Git](https://git-scm.com/downloads)
- [PHP (^8.2)](https://www.php.net/downloads.php) (ext : iconv)
- [Composer](https://getcomposer.org/download/)
- [Mysql / Mariadb](https://mariadb.org/download/)
- [Docker](https://docs.docker.com/get-started/get-docker/) (not required)
- [Node](https://nodejs.org/en/download)

## Full project setup, using docker
```bash
git clone https://github.com/egbavou/aql-project
cd aql-project
docker compose build
docker compose up -d
# Backend running on the port 8000
# phpmyadmin, running on the port 12234
# maildev running on the port 1080
# Other params can be changed in the .env
```

## API Setup (without docker)
```bash
git clone https://github.com/egbavou/aql-project
cd aql-project
cp .env.example .env

# Create the database and configure the .env file
composer install
php artisan key:generate

# To run the tests
php artisan test

# For the mails, maildev can be used to setup a local smtp server
docker run -p 1080:1080 -p 1025:1025 maildev/maildev
php artisan serve
```
## Frontend setup (without docker)

```bash
npm install
npm run dev
```
