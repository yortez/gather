# Gather

Gather is a lightweight form builder for creating shareable forms and collecting responses in one place.

## Features

- Create, edit, publish, and manage forms.
- Add short-answer, paragraph, email, date, address, multiple-choice, and checkbox questions.
- Share a public link so people can submit responses without an account.
- Review responses and export them as CSV.

## Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm
- SQLite (the default database)

## Setup

From the project directory, install the dependencies:

```sh
composer install
npm install
```

Create the environment file (use `Copy-Item .env.example .env` in PowerShell):

```sh
cp .env.example .env
```

Prepare the application key and SQLite database, then run migrations:

```sh
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate
```

Build the frontend assets and start the Laravel development server:

```sh
npm run build
php artisan serve
```

Open the URL printed by `php artisan serve`. During frontend development, run `npm run dev` in a second terminal instead of building assets each time.

## Development

To contribute to the project:

1. Install the dependencies and prepare the application by following the [setup instructions](#setup).
2. Start the Laravel server and frontend asset watcher together:

	```sh
	composer run dev
	```

3. Make focused changes and add or update tests for new behavior.
4. Run the test suite before submitting a contribution:

	```sh
	php artisan test
	```

5. Format modified PHP files with Laravel Pint:

	```sh
	vendor/bin/pint --dirty --format agent
	```

Keep pull requests focused and include a clear description of the changes.

## Tests

Run the test suite with:

```sh
php artisan test
```
