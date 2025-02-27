# aql project

## API Setup

### Prerequisites
- [Git](https://git-scm.com/downloads)
- [PHP (^8.2)](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/download/)
- [Mysql / Mariadb](https://mariadb.org/download/)
- [Docker](https://docs.docker.com/get-started/get-docker/) (not required)
- [Node](https://nodejs.org/en/download)

### Steps
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
## Frontend setup

```bash
npm install
npm run dev
```
