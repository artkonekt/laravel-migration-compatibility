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

**Next**: [Installation &raquo;](installation.md)


