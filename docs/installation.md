# Installation

Use composer to install the package:

```bash
composer require konekt/laravel-migration-compatibility
```

> Side note: make sure you're not accidentally installing Laravel 10 and Doctrine DBAL 4 together!
> They're incompatible, but between Laravel 10.0.0 - Laravel 10.38.1 it is technically possible.

The Service provider is automatically registered by Laravel.

**Next**: [Writing Migrations &raquo;](writing-migrations.md)
