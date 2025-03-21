Moneysky is a powerful financial management application that helps you automate your finance tracking and stay informed with real-time updates. Users have two options to access the application:

1. Subscribe to the hosted version at https://moneysky.app/
2. Self-host the application

For those who choose to self-host Moneysky, the following third-party services are likely required for the application to function properly:

- [GoCardless account](https://gocardless.com/bank-account-data/)
- [Alphavantage API KEY](https://www.alphavantage.co/)
- [Moralis API KEY](https://developers.moralis.com/)
- [Fixer API KEY](https://fixer.io/)
- [OpenAI API KEY](https://platform.openai.com/docs/overview)

### Optional services are:

- [Stripe](https://stripe.com/)
- [Sentry](https://sentry.io/)

By using Moneysky, whether through subscription or self-hosting, you can benefit from features such as expense tracking, budget management, and financial insights to help you make informed decisions about your personal finances.

## Features

- Expense tracking
- Budget management
- Financial insights and reports
- User authentication and account management

## Tech Stack

- Backend: PHP (Laravel framework)
- Frontend: TypeScript
- Database: PostgreSQL
- Docker support for easy deployment and development

## Getting Started

### Prerequisites

- PHP ^8.4
- Composer
- Node.js and npm
- Docker (optional)

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/antonL95/moneysky.git
   cd moneysky
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Copy the example environment file and modify it according to your setup:
   ```bash
   cp .env.example .env
   ```

5. Generate an application key:
   ```bash
   php artisan key:generate
   ```

6. Run database migrations:
   ```bash
   php artisan migrate
   ```

7. Compile assets:
   ```bash
   npm run dev
   ```

### Running the Application

#### Using PHP's built-in server:
```bash
composer dev
```

#### Using Docker:
```bash
docker-compose up -d
```

## Development

- This project uses Laravel as the PHP framework.
- Frontend is built with TypeScript.
- ESLint is used for JavaScript/TypeScript linting.
- Prettier is used for code formatting.
- PHPStan (max level) is used for static analysis of PHP code.
- Pint is used for PHP code style fixing.
- Rector is used for automated refactoring and upgrading of PHP code.

## Testing

Run PHP tests using PestPHP:
```bash
composer test
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

