# Riid.me PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/riidme/php-sdk.svg)](https://packagist.org/packages/riidme/php-sdk)
[![Tests](https://github.com/prykris/riid.me-php/actions/workflows/tests.yml/badge.svg)](https://github.com/prykris/riid.me-php/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/riidme/php-sdk.svg)](https://packagist.org/packages/riidme/php-sdk)

Official PHP SDK for the riid.me URL shortener service. This package provides a clean and simple way to interact with the riid.me API.

## 📦 Related Packages

- [riidme/laravel](https://github.com/prykris/riid.me-laravel) - Laravel integration for riid.me

## 🔧 Requirements

- PHP 8.3 or higher
- PSR-17 HTTP Factory implementation
- PSR-18 HTTP Client implementation

## 📥 Installation

You can install the package via composer:

```bash
composer require riidme/php-sdk
```

## 🚀 Usage

```php
use Riidme\Client;
// Initialize the client
$client = new Client([
'base_url' => 'https://riid.me', // Optional, defaults to https://riid.me
'timeout' => 5, // Optional, defaults to 5 seconds
'retries' => 3 // Optional, defaults to 3 retries
]);
try {
// Shorten a URL
$result = $client->shorten('https://example.com/very/long/url');
// Get the shortened URL
echo $result->getShortUrl(); // https://riid.me/abc123
} catch (RiidmeException $e) {
// Handle errors
echo "Error: " . $e->getMessage();
}
```

## 🛠️ Laravel Integration

If you're using Laravel, check out our dedicated Laravel package [riidme/laravel](https://github.com/prykris/riid.me-laravel) which provides:

- Laravel service provider
- Facade for convenient access
- Laravel-specific configuration
- Integration with Laravel's HTTP client
- Automatic retry handling
- Queue support

## ✅ Testing

```bash
composer install
composer run test
```