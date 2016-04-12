<?php use Phpmig\Migration\Migration;
class CreateUserAuthorizationTable extends Migration
{
    /* @var Table Name */
    protected $table_name;
    /* @var \Illuminate\Database\Schema\Builder $schema */
    protected $schema;
    /* Initialize variables */
    public function init() {
        $this->table_name = 'user_authorization';
        $this->schema = $this->get('schema');
    }
    /**
     * Do the migration
     */
    public function up() {
      $this->schema->create($this->table_name, function ($table) {
          $table->engine = 'InnoDB';
          $table->increments('id')->unsigned();

          $table->integer('user')->unsigned();
          $table->integer('authorization')->unsigned();

          $table->foreign('user')->references('id')->on('users')->onDelete('CASCADE');
          $table->foreign('authorization')->references('id')->on('authorizations')->onDelete('CASCADE');

          $table->timestamps();
          $table->softDeletes();
      });
    }
    /**
     * Undo the migration
     */
    public function down() {
        $this->schema->drop($this->table_name);
    }
}
