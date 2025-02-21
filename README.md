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
use Riidme\Exception\RiidmeException;

// Initialize using factory method
$client = Client::create([
    'base_url' => 'https://riid.me',  // Optional
    'timeout'  => 5,                  // Optional
    'retries'  => 3                   // Optional
]);

try {
    $result = $client->shorten('https://example.com/very/long/url');
    echo $result->getShortUrl(); // https://riid.me/abc123
    // or simply
    echo $result; // https://riid.me/abc123
} catch (RiidmeException $e) {
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
composer test
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 📄 License

The Apache2 License. Please see [License File](LICENSE.md) for more information.