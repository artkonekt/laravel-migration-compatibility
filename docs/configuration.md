# Configuration

If for any reason, this library fails to detect the actual type of the field type, application
developers can make a shortcut and tell the type from configuration.

To do this, the `migration.compatibility.map.<referenced_table>.<referenced_field>` configuration
value has to be set.

## Examples

To tell the migrations that the `users.id` field is unsigned bigint, you have to set the
`migration.compatibility.map.users.id` config key's value to `bigint unsigned`.

### From Within AppServiceProvider

```php
// app/Providers/AppServiceProvider.php
class AppServiceProvider extends ServiceProvider
{
    // ...
    
    public function boot()
    {
        $this->app['config']->set("migration.compatibility.map.users.id", 'bigint unsigned');
    }
    
    // ...
}
```

### From Within a Config File

Due to the way how Laravel is loading config files you can also create a file called `migration.php`
in the application's `config/` folder:

```php
// config/migration.php

return [
    'compatibility' => [
        'map' => [
            'users' => [
                'id' => 'int unsigned'
            ]
        ]        
    ]    
];
```

## Possible Values

This library only supports integer based types, the possible values in the configuration file are:

- `bigint unsigned`
- `int unsigned`
- `bigint` (will be signed)
- `int` (will be signed)

## Other Fields

The main reason for this library coming to birth was the ubiquity of Laravel's default `users` table.

This library can detect any other table+field combination:

```php
$table->intOrBigIntBasedOnRelated('comment_id', Schema::connection(null), 'comments.id');
```

To explicitly configure the type of the example above the `migration.compatibility.map.comments.id`
configuration value has to be set:

```php
config(['migration.compatibility.map.comments.id' => 'int unsigned']);
```
