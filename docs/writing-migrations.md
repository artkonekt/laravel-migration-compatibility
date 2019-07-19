# Writing Migrations

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
```
