# Laravel Todo Reminder Project

This project is built with **Laravel 11** and provides a Todo list with full CRUD functionality. A reminder email is sent 10 minutes before the Todo’s due date. The email includes a CSV attachment containing 10 titles pulled from an external API. All email send attempts are logged in the database.

> **Note:** This project also uses Laravel Breeze for authentication.

## Features

- **Todo CRUD:** Create, read, update, and delete Todo items.
- **Due Date & Reminder Email:** Each Todo has a due date/time, and a reminder email is scheduled 10 minutes before the due date.
- **CSV Attachment:** The reminder email includes a CSV file containing 10 titles fetched from [jsonplaceholder](https://jsonplaceholder.typicode.com/posts).
- **Email Logging:** Every email send attempt (successful or failed) is logged in the `email_logs` table.
- **Email Notification Tag:** After a reminder email is successfully sent, the Todo item is updated with a flag (`reminder_email_sent`).
- **Route Model Binding:** Uses Laravel’s model binding for cleaner and more efficient controller code.
- **Scheduler & Queue:** Reminder emails are dispatched via Laravel’s scheduler and processed in the background using queues.

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/laravel-todo-reminder.git

cd laravel-todo-reminder
```

### 2. Install Dependencies

```bash
composer install
npm install
npm run dev
```

### 3. Environment Configuration

#### Copy the example environment file and update the settings

```bash
cp .env.example .env
```

#### Open the .env file and update the following

Database Settings: Set your DB connection

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE="${APP_NAME}"
DB_USERNAME=root
DB_PASSWORD=
```

Mail Settings:

```bash
MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"
```

Generate the application key:

```bash
php artisan key:generate
```

### 4. Run Migrations

```bash
php artisan migrate
php artisan migrate:fresh --seed
```

### 5. Start the Queue Worker

Reminder emails are processed via queues. Start a queue worker:

```bash
php artisan queue:work
```

### 6. Scheduler Setup

The project uses Laravel’s scheduler to dispatch reminder email jobs every minute. To start the scheduler, run:

```bash
php artisan schedule:work
```

### 7. Serve the Application

```bash
php artisan serve
```

Then open your browser at <http://localhost:8000>.
