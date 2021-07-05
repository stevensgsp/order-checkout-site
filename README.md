# ORDER CHECKOUT SITE

_Laravel 8.x project._

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Requirements

This is a Laravel 8.x project, so you must meet its requirements.

### Installing

Clone the project

```bash
git clone https://github.com/stevensgsp/order-checkout-site.git
cd order-checkout-site
composer install
cp .env.example .env
php artisan key:generate
```

Edit .env and put credentials, indicate environment, url and other settings.

```bash
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

PLACETOPAY_URL=
PLACETOPAY_LOGIN=
PLACETOPAY_TRANKEY=
```

Run migrations and seeders

```bash
php artisan migrate --seed
```
