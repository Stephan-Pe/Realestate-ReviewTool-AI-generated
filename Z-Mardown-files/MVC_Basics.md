# PHP MVC projectpage

## Project Description

A PHP MVC (Model-View-Controller) project that demonstrates the basic structure and functionality of a typical MVC application. The project includes a simple user registration form, which is handled by the controller, validated by the model, and displayed in the view.

## Features

1. **User Registration Form**: A simple HTML form for users to register.
2. **Controller**: Handles user input and interacts with the model.
3. **Model**: Validates user data and stores it in the database.
4. **View**: Displays the registration result or error messages.

## Technical Requirements

1. Gulp for building assets **important** `Type Module`
2. PHP 8.x for server-side scripting
3. MySQL for database management
4. Twig for templating engine
5. Composer for dependency management
6. npm for package management
7. SCSS for styling

---

# Developer Guide — Building New Features

This guide explains how to add new **Controllers**, **Models**, and **Views** to the Onepager application.

## Table of Contents

1. [Project Structure](#1-project-structure)
2. [Routing](#2-routing)
3. [Creating a New Controller](#3-creating-a-new-controller)
4. [Creating a New Model](#4-creating-a-new-model)
5. [Creating a New View (Twig Template)](#5-creating-a-new-view-twig-template)
6. [Working with Flash Messages](#6-working-with-flash-messages)
7. [Authentication & Authorization](#7-authentication--authorization)
8. [File Uploads](#8-file-uploads)
9. [CSRF Protection](#9-csrf-protection)
10. [Captcha](#10-captcha)
11. [Database Queries](#11-database-queries)
12. [Twig View Helpers & Globals](#12-twig-view-helpers--globals)
13. [Example: Building a Complete Feature (e.g., "Blog")](#13-example-building-a-complete-feature-e-g-blog)

---

## 1. Project Structure

```
Onepager/
├── App/
│   ├── Auth.php              # Authentication logic (login, logout, session)
│   ├── Config.php            # App configuration (DB, SMTP, Captcha keys)
│   ├── Flash.php             # Flash message helper (session-based notifications)
│   ├── Mail.php              # Mail sending helper (SMTP)
│   ├── Token.php             # Token generation for passwords, 2FA, etc.
│   ├── Controllers/
│   │   ├── Homes.php         # Example public controller
│   │   ├── About.php         # Example simple controller
│   │   ├── Profile.php       # Example authenticated controller
│   │   ├── Authenticated.php # Base class for auth-required controllers
│   │   └── Admin/
│   │       └── Users.php     # Admin namespace controller
│   ├── Middleware/
│   │   ├── AccessControl.php # Role-based access control
│   │   ├── CaptchaMiddleware.php
│   │   └── CsrfMiddleware.php
│   ├── Models/
│   │   ├── Home.php          # Example model
│   │   ├── User.php          # User model with UserData DTO
│   │   └── RememberedLogin.php
│   ├── Security/
│   ├── Services/
│   └── Views/                # Twig templates + .phtml partials
│       ├── Home/
│       ├── About/
│       ├── Admin/
│       ├── Login/
│       ├── Profile/
│       ├── Signup/
│       ├── Password/
│       └── snippets/         # Reusable partials
├── Core/
│   ├── Controller.php        # Abstract base controller
│   ├── Model.php             # Abstract base model (PDO helper)
│   ├── View.php              # Twig + .phtml rendering
│   ├── Router.php            # Custom router
│   ├── Validate.php          # Validation helpers
│   ├── Helper.php            # Utility functions
│   ├── Error.php             # Error handling
│   ├── AssetExtension.php    # Twig extension for asset hashes
│   └── ContactMailService.php
├── public/
│   ├── index.php             # Front controller (entry point)
│   ├── .htaccess             # URL rewriting (all → index.php)
│   ├── css/
│   ├── img/
│   ├── js/
│   └── fonts/
├── vendor/                   # Composer dependencies
├── node_modules/             # npm dependencies
├── scss/                     # SCSS source files
├── gulp_modules/             # Gulp plugins
├── gulpfile.mjs              # Gulp build config
├── package.json              # npm packages
├── composer.json             # Composer packages
├── .env                      # Environment variables (APP_ENV)
└── onepager.sql              # Database schema
```

---

## 2. Routing

All routes are defined in `public/index.php`. The router uses a custom `Core\Router` class.

### Route Syntax

| Pattern | Description |
|---------|-------------|
| `home` | Static route → `Homes::indexAction()` |
| `{controller}/{action}` | Dynamic route — matches any controller/action pair |
| `{controller}/{id:\d+}/{action}` | Dynamic route with numeric ID parameter |
| `password/reset/{token:[\da-f]+}` | Dynamic route with regex-constrained token |
| `admin/{controller}/{action}` | Admin namespace → `App\Controllers\Admin\{Controller}` |

### Route Registration

Routes are added in `public/index.php`:

```php
$router = new Core\Router();

// Static routes
$router->add('', ['controller' => 'Homes', 'action' => 'index']);
$router->add('about', ['controller' => 'About', 'action' => 'index']);

// Dynamic routes
$router->add('homes/show/{id:\d+}', ['controller' => 'Homes', 'action' => 'show']);

// Catch-all for new controllers
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');

// Admin namespace
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);
```

### URL to Controller Mapping

The router converts URL segments automatically:

| URL | Controller Class | Action Method |
|-----|-----------------|---------------|
| `/blog/show/42` | `App\Controllers\Blog` | `showAction()` |
| `/admin/posts/index` | `App\Controllers\Admin\Posts` | `indexAction()` |
| `/blog/new` | `App\Controllers\Blog` | `newAction()` |

**Naming convention**: `blog` → `Blog`, `contact-us` → `ContactUs`

---

## 3. Creating a New Controller

### Step 1: Create the Controller File

Create `App/Controllers/Blog.php`:

```php
<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Blog;
use \Core\Validate;

/**
 * Blog Controller
 * Handles CRUD operations for blog posts.
 */
class Blog extends \Core\Controller
{
    /**
     * Before filter — runs before every action method.
     * Use to set up shared data or enforce access control.
     */
    protected function before()
    {
        // Example: require login for all blog actions
        // $this->requireLogin();

        // Example: load a shared variable
        // $this->categories = Blog::getAllCategories();
    }

    /**
     * After filter — runs after every action method.
     * Use for cleanup or logging.
     */
    protected function after()
    {
        // No-op by default; override if needed
    }

    /**
     * Show all blog posts (index page)
     *
     * URL: /blog
     */
    public function indexAction()
    {
        $posts = Blog::getAll();

        View::renderTemplate('Blog/index.html', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show a single blog post
     *
     * URL: /blog/show/42
     */
    public function showAction()
    {
        $id = $this->route_params['id'];
        $post = Blog::findByID($id);

        if (!$post) {
            Flash::addMessage('Post not found.', Flash::WARNING);
            $this->redirect('/blog');
        }

        View::renderTemplate('Blog/show.html', [
            'post' => $post,
        ]);
    }

    /**
     * Show the "create new post" form (requires login)
     *
     * URL: /blog/new
     */
    public function newAction()
    {
        $this->requireLogin();

        View::renderTemplate('Blog/new.html');
    }

    /**
     * Create a new blog post from form submission
     *
     * URL: POST /blog/create
     */
    public function createAction()
    {
        $this->requireLogin();

        // Validate input
        $blog = new Blog($_POST);
        $blog->validate();

        if (!empty($blog->errors)) {
            Flash::addMessage('Please fix the errors below.', Flash::WARNING);
            View::renderTemplate('Blog/new.html', [
                'post' => $blog,
                'errors' => $blog->errors,
            ]);
            return;
        }

        // Save the new post
        $blog->save();

        Flash::addMessage('Post created successfully!', Flash::SUCCESS);
        $this->redirect('/blog/show/' . $blog->id);
    }

    /**
     * Show the "edit post" form (requires login)
     *
     * URL: /blog/edit/42
     */
    public function editAction()
    {
        $this->requireLogin();

        $id = $this->route_params['id'];
        $post = Blog::findByID($id);

        if (!$post) {
            Flash::addMessage('Post not found.', Flash::WARNING);
            $this->redirect('/blog');
        }

        View::renderTemplate('Blog/edit.html', [
            'post' => $post,
        ]);
    }

    /**
     * Update an existing blog post
     *
     * URL: POST /blog/update
     */
    public function updateAction()
    {
        $this->requireLogin();

        $blog = new Blog($_POST);
        $blog->validate();

        if (!empty($blog->errors)) {
            Flash::addMessage('Please fix the errors below.', Flash::WARNING);
            View::renderTemplate('Blog/edit.html', [
                'post' => $blog,
                'errors' => $blog->errors,
            ]);
            return;
        }

        $blog->update();

        Flash::addMessage('Post updated successfully!', Flash::SUCCESS);
        $this->redirect('/blog/show/' . $blog->id);
    }

    /**
     * Delete a blog post (requires login + admin role)
     *
     * URL: POST /blog/delete
     */
    public function deleteAction()
    {
        $this->requireLogin();
        AccessControl::requireRole('admin');

        Blog::deleteByID($_POST['id']);

        Flash::addMessage('Post deleted.', Flash::SUCCESS);
        $this->redirect('/blog');
    }
}
```

### Controller Patterns

| Pattern | Use Case |
|---------|----------|
| `extends \Core\Controller` | Public page (no auth required) |
| `extends Authenticated` | Authenticated-only page |
| `extends \Core\Controller` + `$this->requireLogin()` | Mixed: some public, some private |
| `AccessControl::requireRole('admin')` | Admin-only access |

### Action Method Naming

- Controller methods are called via `__call()` magic method.
- The URL `blog/show/42` maps to `showAction()`.
- **Important**: Remove the `Action` suffix when calling — the router strips it automatically.
- If you try to call `showAction()` directly, the router throws an exception.

### Available Controller Helpers

| Method | Description |
|--------|-------------|
| `$this->redirect($url)` | Redirect to URL (303) |
| `$this->requireLogin()` | Require authenticated user, redirect to `/login` |
| `$this->route_params['id']` | Access route parameters (e.g., `{id}`) |
| `$this->before()` | Override for pre-action logic |
| `$this->after()` | Override for post-action logic |

---

## 4. Creating a New Model

### Step 1: Create the Model File

Create `App/Models/Blog.php`:

```php
<?php

namespace App\Models;

use PDO;
use Core\View;

/**
 * Blog Post Model
 * Handles database operations for blog posts.
 */
class Blog extends \Core\Model
{
    /**
     * Model properties (mapped to database columns)
     */
    public ?int $id = null;
    public ?string $title = null;
    public ?string $slug = null;
    public ?string $body = null;
    public ?string $category = null;
    public ?string $author_id = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    /**
     * Validation error messages
     * @var array
     */
    public $errors = [];

    /**
     * Base path for file operations
     */
    protected const BASE_PATH = __DIR__ . '/../..';

    /**
     * Constructor — accepts an array of initial values (e.g., from POST or DB row)
     *
     * @param array $data Initial property values
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get all blog posts
     *
     * @return array
     */
    public static function getAll()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM blog_posts ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a blog post by ID
     *
     * @param string|int $id The post ID
     * @return array|false The post data as associative array, or false
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM blog_posts WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: false;
    }

    /**
     * Find a blog post by slug
     *
     * @param string $slug
     * @return array|false
     */
    public static function findBySlug(string $slug)
    {
        $sql = 'SELECT * FROM blog_posts WHERE slug = :slug';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: false;
    }

    /**
     * Save (insert) a new blog post
     *
     * @return bool|int The last insert ID, or false on failure
     */
    public function save()
    {
        // Validate before saving
        $this->validate();
        if (!empty($this->errors)) {
            return false;
        }

        $sql = 'INSERT INTO blog_posts (title, slug, body, category, author_id, created_at, updated_at)
                VALUES (:title, :slug, :body, :category, :author_id, NOW(), NOW())';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
        $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
        $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
        $stmt->bindValue(':author_id', $this->author_id, PDO::PARAM_INT);

        $stmt->execute();
        return $db->lastInsertId();
    }

    /**
     * Update an existing blog post
     *
     * @return bool
     */
    public function update()
    {
        $this->validate();
        if (!empty($this->errors)) {
            return false;
        }

        $sql = 'UPDATE blog_posts
                SET title = :title,
                    slug = :slug,
                    body = :body,
                    category = :category,
                    updated_at = NOW()
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
        $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
        $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Delete a blog post by ID (static convenience method)
     *
     * @param int $id
     * @return bool
     */
    public static function deleteByID(int $id): bool
    {
        $sql = 'DELETE FROM blog_posts WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Validate the model data
     * Adds error messages to $this->errors array
     */
    public function validate()
    {
        if (empty($this->title)) {
            $this->errors[] = 'Title is required.';
        }

        if (empty($this->body)) {
            $this->errors[] = 'Body content is required.';
        }

        if (empty($this->category)) {
            $this->errors[] = 'Category is required.';
        }

        // Auto-generate slug if not set
        if (empty($this->slug)) {
            $this->slug = strtolower(
                preg_replace('/[^a-zA-Z0-9äöüÄÖÜß\s-]/', '', $this->title)
            );
            $this->slug = preg_replace('/\s+/', '-', $this->slug);
        }
    }

    /**
     * Get posts by category
     *
     * @param string $category
     * @return array
     */
    public static function getByCategory(string $category)
    {
        $sql = 'SELECT * FROM blog_posts WHERE category = :category ORDER BY created_at DESC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

### Model Patterns

| Pattern | Description |
|---------|-------------|
| `extends \Core\Model` | Base model gives you `getDB()` and `randString()` |
| `static::getDB()` | Get the singleton PDO connection |
| `PDO::prepare()` + `bindValue()` | Always use prepared statements |
| `PDO::FETCH_ASSOC` | Fetch as associative array |
| Model properties match DB columns | Use nullable types `?string`, `?int` |
| `$this->errors` array | Collect validation errors |
| `validate()` method | Core validation logic |
| `BASE_PATH` const | For file operations relative to project root |

---

## 5. Creating a New View (Twig Template)

### Step 1: Create the View Directory & Template

Create `App/Views/Blog/index.html` (note: `.html` extension for Twig):

```twig
{% extends "base.html" %}

{% block title %}Blog{% endblock %}

{% block body %}
<main class="main">
    <div class="main__wrapper">
        <h1 class="sub__lead bold">Blog</h1>

        {% if posts is defined and posts|length > 0 %}
            {% for post in posts %}
                <article class="blog-post">
                    <h2><a href="/blog/show/{{ post.id }}">{{ post.title }}</a></h2>
                    <time>{{ post.created_at|date('d.m.Y') }}</time>
                    <p>{{ post.body|striptags|slice(0, 200) }}...</p>
                    <a href="/blog/show/{{ post.id }}" class="btn">Read More</a>
                </article>
            {% endfor %}
        {% else %}
            <p>No posts available yet.</p>
        {% endif %}
    </div>
</main>
{% endblock %}
```

### Twig Template Conventions

| Convention | Detail |
|------------|--------|
| File extension | `.html` (not `.twig`) |
| Directory | `App/Views/{ControllerName}/` |
| Template name | Matches the action, e.g. `index.html`, `show.html`, `new.html` |
| Extends base | `{% extends "base.html" %}` — extends the master layout |
| Blocks | `{% block title %}...{% endblock %}` and `{% block body %}...{% endblock %}` |
| Passing data | `View::renderTemplate('Blog/show.html', ['post' => $post])` |
| Access data | `{{ post.title }}` in Twig |
| Conditionals | `{% if post is defined and post %}` |
| Loops | `{% for item in items %}...{% endfor %}` |
| Filters | `{{ value|date('Y-m-d') }}`, `{{ value|striptags }}` |

### Available Twig Globals (auto-available in all templates)

| Variable | Description |
|----------|-------------|
| `current_user` | The logged-in `User` model object, or `null` |
| `flash_messages` | Array of flash notification messages |
| `hasRole('admin')` | Twig function — checks user role |
| `csrf_field()` | Twig function — outputs CSRF token input |

### Twig Functions

```twig
{# Check user role #}
{% if hasRole('admin') %}
    <a href="/admin/posts">Admin Panel</a>
{% endif %}

{# Output CSRF token in forms #}
<form method="POST">
    {{ csrf_field() }}
    ...
</form>

{# Access flash messages #}
{% for message in flash_messages %}
    <div class="alert alert-{{ message.type }}">
        {{ message.body }}
    </div>
{% endfor %}
```

---

## 6. Working with Flash Messages

Flash messages are one-time session notifications displayed after a redirect.

### In the Controller

```php
use \App\Flash;

// Add a success message
Flash::addMessage('Post created successfully!', Flash::SUCCESS);

// Add an info message
Flash::addMessage('Please check your email.', Flash::INFO);

// Add a warning message
Flash::addMessage('Something went wrong.', Flash::WARNING);

// Redirect (flash messages are available on the next page)
$this->redirect('/blog');
```

### In the View (Twig)

```twig
{% for message in flash_messages %}
    <div class="alert alert-{{ message.type }}">
        {{ message.body }}
    </div>
{% endfor %}
```

---

## 7. Authentication & Authorization

### Authentication (Login/Logout)

```php
use \App\Auth;
use \App\Models\User;
use \App\Flash;

// Login a user
$user = User::findByEmail($email);
if (User::authenticate($email, $password)) {
    Auth::login($user, $remember_me);
    Flash::addMessage('Welcome back!');
    $this->redirect(Auth::getReturnToPage());
}

// Check if user is logged in
if (Auth::getUser()) {
    $user = Auth::getUser();  // returns User model or null
}

// Logout
Auth::logout();
$this->redirect('/');

// Require login before a page
$this->requireLogin();  // in controller beforeAction or at start of action
```

### Authorization (Role-based Access)

```php
use \App\Middleware\AccessControl;

// Require minimum role level
AccessControl::requireRole('admin');    // admin or super only
AccessControl::requireRole('manager');  // manager, admin, or super
AccessControl::requireRole('user');     // logged-in user

// Role hierarchy:
// guest (0) → user (1) → manager (2) → admin (3) → super (4)
```

### Extending `Authenticated`

For controllers that require login for **all** actions, extend `Authenticated` instead of `\Core\Controller`:

```php
class Profile extends Authenticated
{
    protected function before()
    {
        parent::before();  // calls Auth::getUser() check
        $this->user = Auth::getUser();
    }
}
```

---

## 8. File Uploads

### Handling Image Uploads in Controller

```php
use \Core\Validate;
use \App\Flash;

public function createAction()
{
    $this->requireLogin();

    $data = $_FILES['image'];
    $uploadDir = 'img/uploads/';

    // Validate file
    Validate::validateImages($data);

    $file_name = $_FILES['image']['name'];
    $file_tmp = $_FILES['image']['tmp_name'];

    // Generate unique filename
    $newFileName = \Core\Helper::newFilename($file_name);
    $targetDir = $uploadDir . $newFileName;

    if (move_uploaded_file($file_tmp, $targetDir)) {
        // Save path to database
        $post = new Blog($_POST);
        $post->image = '/' . $uploadDir . $newFileName;
        $post->save();

        Flash::addMessage('Image uploaded!', Flash::SUCCESS);
    } else {
        Flash::addMessage('Upload failed.', Flash::WARNING);
    }

    $this->redirect('/blog');
}
```

### Allowed File Formats

| Type | Formats | Max Size |
|------|---------|----------|
| Images | `.jpg`, `.jpeg`, `.png`, `.webp` | 500 KB |
| PDF | `.pdf` | 8 MB |
| Video | `.avi`, `.mov`, `.mp4` | 300 MB |

---

## 9. CSRF Protection

CSRF protection is automatic for **all POST requests** via `CsrfMiddleware` in `public/index.php`.

### In Your Form (Twig)

```twig
<form method="POST" action="/blog/create" enctype="multipart/form-data">
    {{ csrf_field() }}

    <label>Title</label>
    <input type="text" name="title" value="{{ post.title ?? '' }}">

    <label>Body</label>
    <textarea name="body">{{ post.body ?? '' }}</textarea>

    <button type="submit">Create</button>
</form>
```

> **Note**: `csrf_field()` is available as a global Twig function. It outputs a hidden `<input>` with the CSRF token.

---

## 10. Captcha

### In the Controller

```php
use \App\Middleware\CaptchaMiddleware;

public function sendAction()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!CaptchaMiddleware::checkCaptcha()) {
            Flash::addMessage('Captcha error, please try again.', Flash::WARNING);
            $this->redirect('/contact');
        }
        // Process form...
    }
}

public function captchaAction()
{
    \Core\Helper::outputCaptchaImage();
    exit;
}
```

### In the View (Twig)

```twig
<img src="/homes/captcha" alt="Captcha" onclick="this.src='/homes/captcha?' + Math.random()">
<input type="text" name="captcha_value" placeholder="Enter captcha">
```

---

## 11. Database Queries

### Base Model PDO Access

```php
// Get the PDO connection (singleton)
$db = static::getDB();

// Prepared statement — INSERT
$sql = 'INSERT INTO blog_posts (title, body) VALUES (:title, :body)';
$stmt = $db->prepare($sql);
$stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
$stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
$stmt->execute();

// Prepared statement — SELECT
$sql = 'SELECT * FROM blog_posts WHERE id = :id';
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Prepared statement — UPDATE
$sql = 'UPDATE blog_posts SET title = :title WHERE id = :id';
$stmt = $db->prepare($sql);
$stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
$stmt->execute();

// Prepared statement — DELETE
$sql = 'DELETE FROM blog_posts WHERE id = :id';
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// Fetch all results
$stmt = $db->query('SELECT * FROM blog_posts ORDER BY created_at DESC');
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get last insert ID
$lastId = $db->lastInsertId();

// Transaction
$db->beginTransaction();
try {
    $stmt1->execute();
    $stmt2->execute();
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    throw $e;
}
```

### Available PDO Parameter Types

| Constant | Use For |
|----------|---------|
| `PDO::PARAM_STR` | Strings |
| `PDO::PARAM_INT` | Integers |
| `PDO::PARAM_BOOL` | Booleans |
| `PDO::PARAM_NULL` | NULL values |

---

## 12. Twig View Helpers & Globals

### Available in All Templates

```twig
{# Current logged-in user #}
{% if current_user %}
    Welcome, {{ current_user.user_name }}!
{% else %}
    <a href="/login">Login</a>
{% endif %}

{# Flash messages #}
{% for msg in flash_messages %}
    <div class="alert alert-{{ msg.type }}">{{ msg.body }}</div>
{% endfor %}

{# Role check #}
{% if hasRole('admin') %}
    <a href="/admin/dashboard">Admin</a>
{% endif %}

{# CSRF token in forms #}
{{ csrf_field() }}

{# Date formatting #}
{{ post.created_at|date('d.m.Y H:i') }}

{# String filters #}
{{ post.title|upper }}
{{ post.body|striptags|slice(0, 100) }}

{# Conditional rendering #}
{% if posts is defined and posts|length > 0 %}
    ...
{% else %}
    <p>No items.</p>
{% endif %}
```

---

## 13. Example: Building a Complete Feature (e.g., "Blog")

Here is the complete checklist for adding a new **"Blog"** feature:

### 1. Create the Database Table

Add to `onepager.sql` or run directly:

```sql
CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. Create the Model

**File**: `App/Models/Blog.php`

- Extend `\Core\Model`
- Define properties matching DB columns (use nullable types `?string`, `?int`)
- Implement `getAll()`, `findByID()`, `save()`, `update()`, `validate()`
- Use `static::getDB()` for all DB access
- Always use prepared statements with `bindValue()`

### 3. Create the Controller

**File**: `App/Controllers/Blog.php`

- Extend `\Core\Controller` (or `Authenticated` if all actions require login)
- Define action methods with `Action` suffix:
  - `indexAction()` — list all items
  - `showAction()` — show single item (uses `$this->route_params['id']`)
  - `newAction()` — show create form
  - `createAction()` — handle form POST
  - `editAction()` — show edit form
  - `updateAction()` — handle form POST
  - `deleteAction()` — handle delete POST
- Use `$this->requireLogin()` where needed
- Use `$this->redirect()` after POST operations
- Use `Flash::addMessage()` for notifications
- Render views with `View::renderTemplate('Blog/index.html', [...])`

### 4. Add Routes

In `public/index.php`, add:

```php
$router->add('blog', ['controller' => 'Blog', 'action' => 'index']);
$router->add('blog/show/{id:\d+}', ['controller' => 'Blog', 'action' => 'show']);
$router->add('blog/new', ['controller' => 'Blog', 'action' => 'new']);
$router->add('blog/edit/{id:\d+}', ['controller' => 'Blog', 'action' => 'edit']);
```

### 5. Create the Views

| View File | Purpose |
|-----------|---------|
| `App/Views/Blog/index.html` | List all blog posts |
| `App/Views/Blog/show.html` | Display a single post |
| `App/Views/Blog/new.html` | Form to create a new post |
| `App/Views/Blog/edit.html` | Form to edit an existing post |

Each template extends `base.html` and defines `{% block title %}` and `{% block body %}`.

### 6. Test the Feature

| URL | Expected Result |
|-----|-----------------|
| `/blog` | Shows list of all blog posts |
| `/blog/show/1` | Shows single post with ID=1 |
| `/blog/new` | Shows create form (requires login) |
| `/blog/edit/1` | Shows edit form (requires login) |

### 7. Admin Version (Optional)

For admin-only blog management:

- Create `App/Controllers/Admin/BlogPosts.php`
- Extend `\Core\Controller`
- Add `before()` filter with `AccessControl::requireRole('admin')`
- Register route: `$router->add('admin/blog-posts/{action}', ['namespace' => 'Admin'])`
- Views go in `App/Views/Admin/BlogPosts/`

---

## Quick Reference: Common Patterns

### CRUD Controller Skeleton

```php
<?php
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\YourModel;

class YourController extends \Core\Controller
{
    protected function before()
    {
        // $this->requireLogin();  // Uncomment if needed
    }

    public function indexAction()
    {
        $items = YourModel::getAll();
        View::renderTemplate('Your/index.html', ['items' => $items]);
    }

    public function showAction()
    {
        $item = YourModel::findByID($this->route_params['id']);
        View::renderTemplate('Your/show.html', ['item' => $item]);
    }

    public function newAction()
    {
        $this->requireLogin();
        View::renderTemplate('Your/new.html');
    }

    public function createAction()
    {
        $this->requireLogin();
        $model = new YourModel($_POST);
        $model->validate();

        if (!empty($model->errors)) {
            Flash::addMessage('Please fix errors.', Flash::WARNING);
            View::renderTemplate('Your/new.html',
                ['item' => $model, 'errors' => $model->errors]);
            return;
        }

        $model->save();
        Flash::addMessage('Created successfully!', Flash::SUCCESS);
        $this->redirect('/your/show/' . $model->id);
    }

    public function editAction()
    {
        $this->requireLogin();
        $item = YourModel::findByID($this->route_params['id']);
        View::renderTemplate('Your/edit.html', ['item' => $item]);
    }

    public function updateAction()
    {
        $this->requireLogin();
        $model = new YourModel($_POST);
        $model->validate();

        if (!empty($model->errors)) {
            Flash::addMessage('Please fix errors.', Flash::WARNING);
            View::renderTemplate('Your/edit.html',
                ['item' => $model, 'errors' => $model->errors]);
            return;
        }

        $model->update();
        Flash::addMessage('Updated successfully!', Flash::SUCCESS);
        $this->redirect('/your/show/' . $model->id);
    }

    public function deleteAction()
    {
        $this->requireLogin();
        YourModel::deleteByID($_POST['id']);
        Flash::addMessage('Deleted.', Flash::SUCCESS);
        $this->redirect('/your');
    }
}
```

### Model Skeleton

```php
<?php
namespace App\Models;

use PDO;

class YourModel extends \Core\Model
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $description = null;

    public $errors = [];
    protected const BASE_PATH = __DIR__ . '/../..';

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function getAll()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM your_table ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByID($id)
    {
        $sql = 'SELECT * FROM your_table WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: false;
    }

    public function save()
    {
        $this->validate();
        if (!empty($this->errors)) return false;

        $sql = 'INSERT INTO your_table (name, description)
                VALUES (:name, :description)';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public function update()
    {
        $this->validate();
        if (!empty($this->errors)) return false;

        $sql = 'UPDATE your_table SET name = :name,
                description = :description WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function deleteByID(int $id): bool
    {
        $sql = 'DELETE FROM your_table WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function validate()
    {
        if (empty($this->name)) {
            $this->errors[] = 'Name is required.';
        }
    }
}
```

### View Skeleton (Twig)

```twig
{% extends "base.html" %}

{% block title %}Your Page Title{% endblock %}

{% block body %}
<main class="main">
    <div class="main__wrapper">
        <h1 class="sub__lead bold">Your Page Title</h1>

        {% for message in flash_messages %}
            <div class="alert alert-{{ message.type }}">
                {{ message.body }}
            </div>
        {% endfor %}

        {% block content %}
        <p>Default content goes here.</p>
        {% endblock %}
    </div>
</main>
{% endblock %}
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| **404 "Controller not found"** | Check route registration in `public/index.php` and controller namespace |
| **500 "Method not found"** | Ensure action method has `Action` suffix (e.g., `indexAction`) |
| **Blank page** | Check `error_reporting(E_ALL)` and `Core\Error::errorHandler` |
| **CSRF error** | Ensure `{{ csrf_field() }}` is in your form |
| **Flash message not showing** | Flash messages are consumed after first read; ensure you're on the redirected page |
| **Database connection error** | Verify `App/Config.php` credentials match your MySQL setup |
| **Twig template not found** | Check the path: `App/Views/{ControllerName}/{template}.html` |

---

## Environment Configuration

Set the environment in `.env`:

```
APP_ENV=dev    # or 'prod'
```

- **dev**: Twig debug mode enabled, detailed errors
- **prod**: Twig debug disabled, optimized rendering

## Database Configuration

Edit `App/Config.php` to set your database credentials:

```php
class Config
{
    const DB_HOST = 'localhost';
    const DB_NAME = 'review_tool';
    const DB_USER = 'root';
    const DB_PASSWORD = '';
}
```

For production (Hostpoint), use the configs in `Z-Hostpoint/Config.php` and `Z-Prod/Config.php`.
