# Case Study - News Aggregator

## Table of Contents

- [Introduction](#introduction)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Running the Project with Docker](#running-the-project-with-docker)
- [Tests](#Running-Tests)
- [Seeding the Database](#seeding-the-database)
- [Postman Collection](#postman-collection)
- [Documentation](#documentation)
- [Contact](#contact)

---

## Introduction

This project is to build a RESTful API for a news aggregator service that pulls articles from various
sources and provides endpoints for a frontend application to consume.

## Prerequisites

Ensure you have the following installed on your system:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)

## Installation

To set up the project, follow these steps:

1. Clone the repository:
    ```bash
    git clone https://github.com/AmirTahmasbii/news-aggregator.git
    cd news-aggregator
    ```

2. Copy the example environment file and modify it:
    ```bash
    cp .env.example .env
    ```

3. Update the `.env` file with your environment variables (e.g., database credentials, API keys).

## Running the Project with Docker

You can run the project using Docker with the following steps:

1. Build and start the containers:
    ```bash
    docker-compose up -d
    ```

2. Install the PHP dependencies:
    ```bash
    docker-compose exec app composer install
    ```

3. Generate the Laravel application key:
    ```bash
    docker-compose exec app php artisan key:generate
    ```

4. Run the database migrations and seeders:
    ```bash
    docker-compose exec app php artisan migrate --seed
    ```

5. Access the application by visiting `http://localhost:8000` in your browser.


## Running Tests

To run the tests for this project, follow the steps below:

1. Install PHPUnit:

    If PHPUnit is not already installed, you can add it to your     project:

    ```bash
    composer require --dev phpunit/phpunit ^9
    ```
2. Running Tests:

    Use the following command to run all tests:

    ```bash
    ./vendor/bin/phpunit
    ```
3. Testing Features:

    The test suite covers all core features.

## Postman Collection

In the root of this project, you'll find a Postman collection export file: `news-aggregator.postman_collection.json`. This file contains pre-configured API requests that can be imported directly into Postman for testing the API.

### How to Use

1. Download and install [Postman](https://www.postman.com/downloads/).
2. Open Postman and click on **Import**.
3. Select the `news-aggregator.postman_collection.json` file from the root of this project.
4. Once imported, you can see and test all the API endpoints (such as creating a group, joining a group, sending messages, etc.) pre-configured within Postman.

You can now easily test the API by running the requests included in the collection.

## Documentation
For detailed API documentation, visit the following link:
    - [API Documentation](http://localhost:8000/docs)
