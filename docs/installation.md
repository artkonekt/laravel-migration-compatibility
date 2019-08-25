# Installation

Use composer to install the package:

```bash
composer require konekt/laravel-migration-compatibility
```

The Service provided is automatically registered by Laravel (v5.5+).

**For Laravel 5.4** you need to register the package manually:

```php
// config/app.php
'providers' => [
    // ...
    Konekt\LaravelMigrationCompatibility\LaravelMigrationCompatibilityProvider::class,
],
```

**Next**: [Writing Migrations &raquo;](writing-migrations.md)
