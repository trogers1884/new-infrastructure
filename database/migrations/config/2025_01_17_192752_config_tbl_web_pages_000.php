<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Helpers\Timer;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration
{
    protected $connection = 'pgsql';
    protected $schema = 'config';
    protected $basename = 'web_pages';

    protected ConsoleOutput $msg;
    protected int $msgKtr = 0;


    public function __construct()
    {
        $this->msg = new ConsoleOutput();
    }

    public function upInstructions(): void
    {
        $this->writeMsg("Creating table: {$this->schema}.tbl_{$this->basename}");
        $tblDef = "CREATE TABLE IF NOT EXISTS {$this->schema}.tbl_{$this->basename}
            (
                id BIGSERIAL
                , url character varying(255)
                , description text
                , created_at timestamp(0) with time zone
                , updated_at timestamp(0) with time zone
                , deleted_at timestamp(0) with time zone
                , CONSTRAINT pk_{$this->basename}_id PRIMARY KEY (id)
            )";
        $idxs = ([
            "CREATE UNIQUE INDEX unq_{$this->basename}_url ON {$this->schema}.tbl_{$this->basename} (url)"
            , "CREATE INDEX idx_{$this->basename}_created_at ON {$this->schema}.tbl_{$this->basename} (created_at)"
            , "CREATE INDEX idx_{$this->basename}_updated_at ON {$this->schema}.tbl_{$this->basename} (updated_at)"
        ]);

        DB::connection($this->connection)->statement($tblDef);

        foreach($idxs AS $idx){
            DB::connection($this->connection)->statement($idx);
        }

        $this->writeMsg("Creating view: {$this->schema}.{$this->basename}");
        $viewDef = "SELECT * FROM {$this->schema}.tbl_{$this->basename}";
        $viewQry = "CREATE OR REPLACE VIEW {$this->schema}.{$this->basename} AS {$viewDef}";
        DB::connection($this->connection)->statement($viewQry);
    }

    public function downInstructions(): void
    {
        $this->writeMsg("Dropping view: {$this->schema}.{$this->basename}");
        $dropViewQry = "DROP VIEW IF EXISTS {$this->schema}.{$this->basename}";
        DB::connection($this->connection)->statement($dropViewQry);

        $this->writeMsg("Dropping table: {$this->schema}.tbl_{$this->basename}");
        $dropTblQry = "DROP TABLE IF EXISTS {$this->schema}.tbl_{$this->basename}";
        DB::connection($this->connection)->statement($dropTblQry);
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $upTimer = new Timer();
        $this->upInstructions();
        $this->writeMsg("This task took {$upTimer->getElapsedTime()} minutes");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $downTimer = new Timer();
        $this->downInstructions();
        $this->writeMsg("This task took {$downTimer->getElapsedTime()} minutes");
    }

    // Output messages to the console when the
    public function writeMsg($msg): void
    {
        if(!$this->msgKtr){
            $this->msg->writeln('');
        }
        $this->msgKtr++;
        $this->msg->writeln("$this->msgKtr) $msg");
    }


};
