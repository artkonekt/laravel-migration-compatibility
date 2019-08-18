# Writing Migrations

In order to make your package's migrations seamlessly work with the actual type, use the
`intOrBigIntBasedOnRelated()` method:

```php
class CreateProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');

            // Make `user_id` field the same type as the `id` field of the `user` table:
            $table->intOrBigIntBasedOnRelated('user_id', Schema::connection(null), 'users.id');

            //...

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }
//...
}
```

This will detect the type of `users.id`, ie. the `id` field in the `users` table and make the
`user_id` field in the `profiles` table to be the same type of field.

Internally it calls the `integer()` or the `bigInteger()` methods of the `Blueprint` class, so it's
compatible with other parts of Laravel migrations. The library also takes care of detecting whether
the field is signed or unsigned.

## Compatibility

The library was tested with:

- PHP 7.1 - 7.3
- Laravel 5.4 - 5.8
- Sqlite, MySQL and Postgres.

## Possible Problems

Unfortunately Doctrine DBAL cannot be properly used for detecting the existing schema with Laravel,
therefore this library is using PDO and raw engine specific queries to find out the real schema.

As a consequence, it only works with the supported DB engines (see above).

Newer (or older) versions of the supported DB engines might return answers about their schema in a
different format than this library was prepared to. This can also lead to the impossibility of
detecting the actual field type.

To solve this problem, this package provides a configuration option where the application owner can
tell your library what the actual type of the field is.

**Next**: [Configuration &raquo;](configuration.md)
