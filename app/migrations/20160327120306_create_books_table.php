<?php use Phpmig\Migration\Migration;
class CreateBooksTable extends Migration
{
    /* @var Table Name */
    protected $table_name;
    /* @var \Illuminate\Database\Schema\Builder $schema */
    protected $schema;
    /* Initialize variables */
    public function init()
    {
        $this->table_name = 'books';
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
            $table->integer('book_id')->unique()->unsigned();
            $table->string('title');
            $table->text('description');
            $table->string('volumeInfo');
            $table->string('series');
            $table->string('periodical');
            $table->string('author');
            $table->integer('year');
            $table->string('edition');
            $table->string('publisher');
            $table->string('city');
            $table->integer('pages');
            $table->integer('pagesInFile');
            $table->string('language');
            $table->string('topic');
            $table->string('library');
            $table->string('issue');
            $table->string('identifier');
            $table->string('ISSN');
            $table->string('ASIN');
            $table->string('UDC');
            $table->string('LBC');
            $table->string('DDC');
            $table->string('LCC');
            $table->string('doi');
            $table->string('googleBookId');
            $table->string('openLibraryID');
            $table->string('commentary');
            $table->string('DPI');
            $table->string('color');
            $table->string('cleaned');
            $table->string('orientation');
            $table->string('paginated');
            $table->string('scanned');
            $table->string('bookmarked');
            $table->string('searchable');
            $table->integer('filesize');
            $table->string('extension');
            $table->string('md5');
            $table->string('generic');
            $table->string('filename');
            $table->string('visible');
            $table->string('locator');
            $table->string('local');
            $table->datetime('timeAdded');
            $table->datetime('timeLastModified');
            $table->string('coverURL');
            $table->string('tags');
            $table->string('identifierWODash');
            $table->boolean('is_blocked')->default(false);
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
