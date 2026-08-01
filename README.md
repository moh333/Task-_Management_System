# Task Management System API

A robust RESTful API built with **Laravel 11**, **PHP 8.3**, **Sanctum Authentication**, **MySQL**, and **Swagger / OpenAPI Documentation**.

---

## Table of Contents

- [Overview & Features](#overview--features)
- [Environment Setup](#environment-setup)
- [Installation Steps](#installation-steps)
- [API Documentation](#api-documentation)
  - [Accessing Swagger UI](#accessing-swagger-ui)
  - [Regenerating API Docs](#regenerating-api-docs)
  - [API Endpoints Overview](#api-endpoints-overview)
  - [Authentication Flow](#authentication-flow)
- [Queue Workers & Overdue Tasks](#queue-workers--overdue-tasks)
  - [Running the Overdue Tasks Check](#running-the-overdue-tasks-check)
  - [Running the Queue Worker](#running-the-queue-worker)
  - [Running the Scheduler](#running-the-scheduler)
- [Useful Commands](#useful-commands)

---

## Overview & Features

- **Authentication**: Secure User Registration, Login, and Token-based Auth using Laravel Sanctum.
- **Projects Module**: Full CRUD operations for project management.
- **Tasks Module**: Task creation, updates, detailed views, and deletion linked to projects.
- **Notifications**: User notification management (list, mark single/all as read).
- **Dashboard**: Aggregated stats & insights for current user tasks/projects.
- **Swagger Documentation**: Interactive OpenAPI / Swagger UI provided by `l5-swagger`.
- **Background Jobs & Scheduling**: Automated overdue tasks check and notifications dispatch.

---

## Environment Setup

1. Copy the example environment file `.env.example` to `.env`:

   ```bash
   cp .env.example .env
   ```

2. Configure environment variables inside `.env`:

   ```ini
   APP_NAME="Task Management System"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8080

   L5_SWAGGER_CONST_HOST=http://127.0.0.1:8080

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_managment_system
   DB_USERNAME=root
   DB_PASSWORD=your_mysql_password

   QUEUE_CONNECTION=database
   ```

---

## Installation Steps

Prerequisites: **PHP >= 8.2**, **Composer**, **MySQL**.

1. **Install PHP Dependencies**
   ```bash
   composer install
   ```

2. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

3. **Run Database Migrations**
   ```bash
   php artisan migrate
   ```

4. **Generate API Documentation**
   ```bash
   php artisan l5-swagger:generate
   ```

5. **Start Local Development Server**
   ```bash
   php artisan serve --port=8080
   ```

   The app will be available at: **http://127.0.0.1:8080/**

---

## API Documentation

### Accessing Swagger UI

Interactive Swagger / OpenAPI UI is accessible directly in your web browser:
👉 **[http://127.0.0.1:8080/api/documentation](http://127.0.0.1:8080/api/documentation)**

### Regenerating API Docs

If you add or update Swagger annotations in controllers/requests, regenerate the Swagger JSON file by running:

```bash
php artisan l5-swagger:generate
```

---

### API Endpoints Overview

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| **POST** | `/api/register` | Register a new user | ❌ Public |
| **POST** | `/api/login` | Log in and receive Bearer Token | ❌ Public |
| **POST/GET** | `/api/logout` | Revoke current user token |  Sanctum |
| **GET** | `/api/user` | Get authenticated user info |  Sanctum |
| **GET** | `/api/dashboard` | Get dashboard statistics |  Sanctum |
| **GET** | `/api/notifications` | List user notifications |  Sanctum |
| **GET** | `/api/notification/{id}/read` | Mark single notification as read |  Sanctum |
| **GET** | `/api/notifications/read-all` | Mark all notifications as read |  Sanctum |
| **GET** | `/api/projects` | List all projects |  Sanctum |
| **POST** | `/api/project/create` | Create a new project |  Sanctum |
| **GET** | `/api/project/show/{id}` | Get project details |  Sanctum |
| **PUT/PATCH** | `/api/project/update/{id}` | Update a project |  Sanctum |
| **DELETE** | `/api/project/delete/{id}` | Delete a project |  Sanctum |
| **GET** | `/api/tasks` | List all tasks |  Sanctum |
| **POST** | `/api/task/create` | Create a new task |  Sanctum |
| **GET** | `/api/task/show/{id}` | Get task details |  Sanctum |
| **PUT/PATCH** | `/api/task/update/{id}` | Update a task |  Sanctum |
| **DELETE** | `/api/task/delete/{id}` | Delete a task |  Sanctum |

---

### Authentication Flow

1. Call `POST /api/login` or `POST /api/register` with user credentials.
2. Receive a response containing the Sanctum access token:
   ```json
   {
     "status": true,
     "message": "User logged in successfully",
     "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
   }
   ```
3. Include the token in the `Authorization` header for protected endpoints:
   ```http
   Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   ```
4. In **Swagger UI** (`http://127.0.0.1:8080/api/documentation`), click the **Authorize** button and enter:
   `Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`

---

## Queue Workers & Overdue Tasks

### Running the Overdue Tasks Check

To check for overdue tasks manually and dispatch background notification jobs (`CheckOverdueTasksJob`), run:

```bash
php artisan tasks:check-overdue
```

This command inspects all tasks where `status != Done` and `due_date < Today`, sending a `TaskOverdueNotification` to the task's project owner.

### Running the Queue Worker

To process queued jobs (such as sending overdue task notifications in the background):

```bash
php artisan queue:work
```

> **Note**: If `QUEUE_CONNECTION=sync` in `.env`, jobs are processed synchronously immediately. If set to `database`, run `php artisan queue:work` to consume queued jobs.

### Running the Scheduler

The overdue tasks check command is scheduled to run daily in `routes/console.php`.

To run the scheduler locally for testing:

```bash
php artisan schedule:work
```

---

## Useful Commands

- **Check Overdue Tasks**:
  ```bash
  php artisan tasks:check-overdue
  ```

- **Start Queue Worker**:
  ```bash
  php artisan queue:work
  ```

- **Start Scheduler Worker**:
  ```bash
  php artisan schedule:work
  ```

- **Run Database Migrations**:
  ```bash
  php artisan migrate
  ```

- **Reset Database & Seed**:
  ```bash
  php artisan migrate:fresh --seed
  ```

- **Clear Application Caches**:
  ```bash
  php artisan config:clear
  php artisan route:clear
  php artisan cache:clear
  ```

- **Run Automated Tests**:
  ```bash
  php artisan test
  ```
