<?php use Phpmig\Migration\Migration;
class CreateDownloadsTable extends Migration
{
    /* @var Table Name */
    protected $table_name;
    /* @var \Illuminate\Database\Schema\Builder $schema */
    protected $schema;
    /* Initialize variables */
    public function init()
    {
        $this->table_name = 'downloads';
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
            $table-> integer('book')->unsigned()->nullable();
            $table->string ("download_key",2000);
            $table->string ("request_url",2000);
            $table->string ("ip",15);

            $table->foreign('book')->references('id')->on('books')->onDelete('CASCADE');

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
