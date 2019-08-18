# Laravel Migration Compatibility

## The Problem: Laravel 5.8 and BigIncrements

As of [Laravel 5.8](https://github.com/laravel/framework/pull/26472), migration stubs use the
`bigIncrements` method on ID columns by default. Previously, ID columns were created using the
increments method.

Foreign key columns must be of the same type. Therefore, a column created using the increments
method can not reference a column created using the bigIncrements method.

This small change is a big [source of problems](https://laraveldaily.com/be-careful-laravel-5-8-added-bigincrements-as-defaults/)
for packages that define references to the default Laravel user table.

### Detection Based on Laravel Version

Unfortunately detecting if Laravel version is 5.8+ is not enough to find out whether `user.id` is
bigInt or int, since the host application could use bigInt even before Laravel 5.8 and can still use
plain int even after 5.8.

```php
if (version_compare(App::version(), '5.8.0', '>=')) {
    $table->bigInteger('user_id')->unsigned()->nullable();
} else {
    $table->integer('user_id')->unsigned()->nullable();
}
```

**Failure Examples:**

**Project has been started with Laravel 5.7** (or earlier) and later has been upgraded to Laravel 5.8.

The `user.id` field is still integer.
A package containing a migration with FK to `users` table was being added to the project when it was
already on Laravel 5.8.

Relying on the Laravel version (code snippet above) would mislead the migration, thinking user.id is
`bigInt`, but it's actually `int`.

**Project has been started on Laravel 5.8**, but as a very first step, the default Laravel migration
has been modified back to using `int` for `user.id`.

Again, the Laravel version is not sufficient to tell whether the user table's id field is `int` or
`bigInt`.

## The Solution

The best solution is to detect the actual type from the database on which the migration is being
ran. This is however not super simple since some circumstances can interfere with it.

This package provides an additional, flexible pseudo field type for migration called
`intOrBigIntBasedOnRelated()` that can be either `INT` or `BIGINT` based on the environment it is
running in.

It attempts to obtain the actual type in the following order:

1. Read it from the actual database; if it can not, then
2. Read the type from the application's configuration; if that fails as well, then
3. Makes a guess based on the Laravel version (as a fallback).

**Next**: [Installation &raquo;](installation.md)


