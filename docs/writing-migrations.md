# Writing Migrations

```php
class CreateProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');

            // We don't know whether `user.id` is int or bigInt:
            $table->intOrBigInt('user_id')->detectFrom('user', 'id');

            //...

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }
//...
```
