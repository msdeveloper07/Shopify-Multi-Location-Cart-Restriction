# Shopify Multi-Location Cart Restriction

A Laravel-based Shopify application designed to enforce location-based cart restrictions, preventing customers from combining products associated with multiple locations in a single cart.

## Overview

Shopify Multi-Location Cart Restriction helps Shopify stores enforce a single-location purchasing rule. It is intended for stores where products or inventory are associated with different locations and orders must comply with location-specific purchasing requirements.

## Features

* Restricts carts containing products from multiple locations.
* Supports location-based cart validation logic.
* Helps maintain consistent cart behavior for Shopify storefronts.
* Built with Laravel for backend application logic.
* Includes Docker configuration for containerized development or deployment, where configured.

## Tech Stack

* **Backend:** PHP, Laravel
* **Platform:** Shopify
* **Containerization:** Docker
* **Dependency Management:** Composer

## Requirements

Before setting up the application, ensure you have the following, as applicable to the project configuration:

* PHP version compatible with the Laravel application
* Composer
* A supported database, if required
* Shopify development store and appropriate app credentials
* Docker and Docker Compose, if using the containerized setup

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/msdeveloper07/Shopify-Multi-Location-Cart-Restriction.git
   cd Shopify-Multi-Location-Cart-Restriction
   ```

2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Create your environment configuration:

   ```bash
   cp .env.example .env
   ```

   On Windows, you can copy `.env.example` to `.env` manually.

4. Generate the Laravel application key if required:

   ```bash
   php artisan key:generate
   ```

5. Configure your database and Shopify app credentials in `.env`.

6. Run database migrations if the application uses them:

   ```bash
   php artisan migrate
   ```

7. Start the Laravel development server:

   ```bash
   php artisan serve
   ```

Follow the project's actual configuration for Shopify authentication, webhooks, frontend assets, queues, and other services.

## Docker Setup

The repository includes a `Dockerfile` and `docker-entrypoint.sh`.

Review the Docker configuration before building or starting the application. The required environment variables, exposed ports, services, and startup commands depend on the included Docker setup.

## Configuration and Security

* Never commit `.env` files, access tokens, API secrets, or customer data.
* Keep Shopify credentials in environment variables or an appropriate secrets manager.
* Use a separate Shopify development store for testing.
* Review webhook and application permission requirements before deployment.

## Usage

Once configured, install or connect the application to your Shopify development store and test cart behavior using products associated with different locations.

Expected behavior: the application should prevent a cart from containing products from multiple locations, according to the store's configured restriction rules.

## License

Specify the applicable license before distributing or reusing this project.

## Disclaimer

This project is intended for Shopify store integrations. Actual behavior depends on the application's implementation and the Shopify APIs and storefront mechanisms it uses.
