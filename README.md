# apigeneradordata

## Project Overview

This project is a RESTful API built with Laravel 10, designed to execute queries via API endpoints. It utilizes Docker for local development, ensuring a consistent environment across different setups.

## Project Structure

The project is organized as follows:

- **src/app**: Contains the core application logic, including models, controllers, and services.
- **src/bootstrap**: Contains files that bootstrap the application, including the application instance and service providers.
- **src/config**: Contains configuration files for various services and settings used in the application.
- **src/database**: Contains database migrations, seeders, and factories.
- **src/public**: Contains the front-facing files, including the index.php file that serves as the entry point for the application.
- **src/resources**: Contains views, language files, and other resources.
- **src/routes**: Contains the route definitions for the application.
- **src/storage**: Contains compiled views, file caches, and logs.
- **src/tests**: Contains the test cases for the application.
- **src/artisan**: The command-line interface for the Laravel application.
- **src/composer.json**: Configuration file for Composer, listing dependencies and autoloading information.
- **src/composer.lock**: Locks the dependencies to specific versions.
- **src/package.json**: Configuration file for npm, listing JavaScript dependencies and scripts.
- **src/phpunit.xml**: Configuration file for PHPUnit, specifying test settings.
- **src/server.php**: An alternative entry point for the application.
- **src/webpack.mix.js**: Used for defining Webpack build steps for assets.
- **src/Dockerfile**: Contains instructions to build the Docker image for the Laravel application, including PHP 8 and Xdebug configuration.

## Docker Configuration

The project uses **Docker Compose v2** (native plugin) with PHP 8.2, MySQL 8.0, and Xdebug.

**Prerequisites:**
- Docker Engine with Compose v2 (integrated plugin)
- Git
- Ports 8001 (API) and 3307 (MySQL) available

## Setup Instructions

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd apigeneradordata
   ```

2. Run the setup script (generates encryption keys and JWT secret):
   ```bash
   bash setup.sh
   ```
   
   **Manual alternative** (if you prefer not to use the script):
   - Copy `src/.env.example` to `src/.env`
   - Replace `GENERATE_ME_WITH_SETUP_SCRIPT` values with generated keys:
     ```bash
     # In src/.env:
     APP_KEY=base64:$(openssl rand -base64 32)
     JWT_SECRET=$(openssl rand -base64 32)
     ```

3. Build and start services:
   ```bash
   docker compose up -d --build
   ```

4. Run database migrations:
   ```bash
   docker compose exec app php artisan migrate
   ```

5. Verify services:
   ```bash
   docker compose ps
   ```

6. Access the application:
   - **API**: http://localhost:8001
   - **MySQL Client**: `mysql -h 127.0.0.1 -P 3307 -u laravel -psecret`

7. View logs:
   ```bash
   docker compose logs -f app
   ```

8. Stop services:
   ```bash
   docker compose stop
   ```

9. Remove containers and volumes:
   ```bash
   docker compose down -v
   ```

## Usage

You can interact with the API using tools like Postman or cURL. The API endpoints are defined in the `src/routes` directory.

## Contributing

Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License

This project is licensed under the MIT License. See the LICENSE file for details.