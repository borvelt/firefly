<?php

use Phpmig\Migration\Migration;

class CreateUsersTable extends Migration
{
    /* @var Table Name */
    protected $table_name;
    /* @var \Illuminate\Database\Schema\Builder $schema */
    protected $schema;
    /* Initialize variables */
    public function init()
    {
        $this->table_name = 'users';
        $this->schema = $this->get('schema');
    }
    /**
     * Do the migration
     */
    public function up()
    {
        $this->schema->create($this->table_name, function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name_family', 100);
            $table->string('nikname', 50);
            $table->string('password');
            $table->string('age', 3);
            $table->string('email')->unique()->index();
            $table->text('confirmation_key');
            $table->boolean('is_confirmed')->default(false);
            // Avatar
            $table->string('avatar_attachment_file_name');
            $table->string('avatar_attachment_file_size');
            $table->string('avatar_attachment_content_type');
            $table->timestamp('avatar_attachment_updated_at');
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Undo the migration
     */
    public function down()
    {
        $this->schema->drop($this->table_name);
    }
}
