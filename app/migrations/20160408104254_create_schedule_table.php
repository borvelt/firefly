<?php use Phpmig\Migration\Migration;
class CreateScheduleTable extends Migration
{
    /* @var Table Name */
    protected $table_name;
    /* @var \Illuminate\Database\Schema\Builder $schema */
    protected $schema;
    /* Initialize variables */
    public function init()
    {
        $this->table_name = 'schedules';
        $this->schema = $this->get('schema');
    }
    /**
     * Do the migration
     */
    public function up()
    {
        $this->schema->create($this->table_name, function ($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('bulk',11);
            $table->string('limitation',11);
            $table->string('request_url');
            $table->text('response');
            $table->boolean ('is_done')->default(false) ;
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
