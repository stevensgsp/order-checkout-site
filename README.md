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

## Running the tests

You may run them using ```phpunit```:

```bash
./vendor/bin/phpunit
```

In addition to the ```phpunit``` command, you may use the ```test``` Artisan command to run your tests. The Artisan test runner provides verbose test reports in order to ease development and debugging:

```bash
php artisan test
```

If you want to generate a code coverage report in HTML format, you may pass the ```--coverage-html``` phpunit command-line option.

```bash
php artisan test --coverage-html ..\coverage
```

### Current code coverage

<img src="https://i.imgur.com/sJCCwR6.png">

## Screenshots

<img src="https://i.imgur.com/SAS5tmr.png">
<img src="https://i.imgur.com/ipLLz1V.png">
<img src="https://i.imgur.com/EOIqb7H.png">
<img src="https://i.imgur.com/y9oLYrP.png">
