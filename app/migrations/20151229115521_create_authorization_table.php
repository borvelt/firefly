<?php use Phpmig\Migration\Migration;
class CreateAuthorizationTable extends Migration
{
    /* @var Table Name */
    protected $table_name;
    /* @var \Illuminate\Database\Schema\Builder $schema */
    protected $schema;
    /* Initialize variables */
    public function init() {
        $this->table_name = 'authorizations';
        $this->schema = $this->get('schema');
    }
    /**
     * Do the migration
     */
    public function up() {
      $this->schema->create($this->table_name, function ($table) {
          $table->engine = 'InnoDB';
          $table->increments('id')->unsigned();
          $table->string('group',25);
          $table->string('uri_pattern');
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
