# Laravel Quick Login

A simple Laravel package that allows users to log in using a **one-time token** — no database table needed. Tokens are stored in cache and expire after a short time.

## 📦 Installation

```bash
composer require abdulaziz96/laravel-quick-login
```

## ⚙️ Setup

Add this route to your `routes/web.php`:

```php
use Illuminate\Http\Request;
use LaravelQuickLogin\OneTimeLoginService;

Route::get('/login/token/{token}', function (Request $request, string $token, OneTimeLoginService $service) {
    $user = $service->loginWithToken($token);

    return $user
        ? redirect('/')->with('status', 'Logged in successfully.')
        : redirect('/login')->withErrors(['token' => 'Invalid or expired token.']);
})->name('login.via.token');
```

## 🧪 Usage Artisan

You can generate a one-time login link via Artisan:

```bash
php artisan uli 123 --minutes=5
```

## 🚀 Usage Tinker

You can generate login URLs like this:

```php
use LaravelQuickLogin\OneTimeLoginService;

$service = app(OneTimeLoginService::class);
$url = $service->generateLoginUrl($userId); // valid for 2 minutes by default
```

Or customize expiration time:

```php
$url = $service->generateLoginUrl($userId, 10); // 10 minutes
```

## 🔐 Security

- Token is **deleted after first use**
- Token expires quickly (default: 2 minutes)
- Use **HTTPS** in production

---

Made with ❤️ by [Abdulaziz zaid]
