# RelataGPT

**RelataGPT** is a Laravel-based application that allows users to ask ChatGPT about the relationship between specific email participants, Abu Nayem and Aftab Girach. The system uses background jobs to process requests asynchronously and supports high volumes of simultaneous requests.

## Table of Contents

- [Requirements](#requirements)
- [Setup](#setup)
- [Running the Application](#running-the-application)
- [Queue Management](#queue-management)
- [Running Tests](#running-tests)
- [Troubleshooting](#troubleshooting)

## Requirements

Make sure you have the following installed on your machine:

- Docker & Docker Compose (for Laravel Sail)
- Composer
- Node.js & npm (for frontend assets, if necessary)

## Setup

Follow these steps to set up and run the application:

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/your-username/RelataGPT.git
   cd RelataGPT
   ```

2. **Install Dependencies:**

   Install PHP dependencies using Composer:

   ```bash
   composer install
   ```

3. **Create Environment Configuration:**

   Copy the `.env` example file and adjust it to your environment:

   ```bash
   cp .env.example .env
   ```

4. **Set Up Your Environment Variables:**

   Update the `.env` file with your database and OpenAI API credentials:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=relatagpt
   DB_USERNAME=your-credentials
   DB_PASSWORD=your-credentials

   OPENAI_API_KEY=your-openai-api-key
   ```

5. **Generate Application Key:**

   ```bash
   sail artisan key:generate
   ```

6. **Start Docker Containers (via Sail):**

   Start the Docker containers:

   ```bash
   ./vendor/bin/sail up -d
   ```

7. **Run Database Migrations:**

   Run the migrations to set up the database schema:

   ```bash
   ./vendor/bin/sail artisan migrate
   ```

    Import emails JSON file from public folder (public/order_id_50782.json)
    ```bash
    ./vendor/bin/sail artisan import:emails public/order_id_50782.json
    ```

8. **Seed the Database (Optional):**

   If you need to insert test data (like emails), you can run the seeder:

   ```bash
   ./vendor/bin/sail artisan db:seed --class=EmailSeeder
   ```

## Running the Application

Once the environment is set up, you can access the application in your browser:

1. Open a browser and go to `http://localhost`.

2. Use the provided form to ask a question (e.g., "What is the relationship between Abu Nayem and Aftab Girach?").

## Queue Management

This application processes ChatGPT requests asynchronously using Laravel queues.

1. **Start the Queue Worker:**

   You need to run a queue worker to process jobs:

   ```bash
   ./vendor/bin/sail artisan queue:work
   ```

   This will start a worker that listens for new jobs (requests to ChatGPT) and processes them in the background.

2. **Monitor the Queue:**

   You can monitor the status of jobs and workers using the following command:

   ```bash
   ./vendor/bin/sail artisan queue:failed
   ```

## Running Tests

You can run both unit and feature tests using PHPUnit. Follow these steps to run the test suite:

1. **Run Tests:**

   ```bash
   ./vendor/bin/sail test
   ```

   This will execute all the test cases, including the validation and ChatGPT API tests.

2. **Optional - Testing with MySQL:**

   If you're using MySQL for testing, ensure that your `.env.testing` file is correctly set up with your test database configuration.

## Troubleshooting

Here are some common issues and their solutions:

- **Jobs not processing:** Ensure that the queue worker is running (`queue:work`) and check for any failed jobs using `queue:failed`.
- **Database connection issues:** Ensure that your `.env` file contains the correct database credentials and that the MySQL container is running (`sail up -d`).
- **ChatGPT API errors:** Make sure you have a valid `OPENAI_API_KEY` in your `.env` file and that you are not exceeding the rate limits for the OpenAI API.
- **Timeout or Performance Issues:** Increase the polling interval for checking job status on the frontend, or consider scaling the queue worker instances.

---

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
```

