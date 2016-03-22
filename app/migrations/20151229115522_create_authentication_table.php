<?php use Phpmig\Migration\Migration;
class CreateAuthenticationTable extends Migration
{
    /* @var Table Name */
    protected $table_name;
    /* @var \Illuminate\Database\Schema\Builder $schema */
    protected $schema;
    /* Initialize variables */
    public function init() {
        $this->table_name = 'authentications';
        $this->schema = $this->get('schema');
    }
    /**
     * Do the migration
     */
    public function up () {
      $this->schema->create($this->table_name, function ($table) {
          $table->engine = 'InnoDB';
          $table->increments('id')->unsigned();
          $table->string("api_key")->unique();
          $table->integer('user')->unsigned();
          $table->boolean('expired')->default(false);
          
          $table->foreign('user')->references('id')->on('users')->onDelete('CASCADE');

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
