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
    protected $schema = 'auth';
    protected $basename = 'resource_types';

    protected ConsoleOutput $msg;
    protected int $msgKtr = 0;


    public function __construct()
    {
        $this->msg = new ConsoleOutput();
    }

    public function upInstructions(): void
    {
        $this->writeMsg("Replacing view: {$this->schema}.{$this->basename}");
        $viewDef = "SELECT * FROM {$this->schema}.tbl_{$this->basename} WHERE deleted_at IS NULL";
        $viewQry = "CREATE OR REPLACE VIEW {$this->schema}.{$this->basename} AS {$viewDef}";
        DB::connection($this->connection)->statement($viewQry);

        $this->writeMsg("Creating view: {$this->schema}.vdel_{$this->basename}");
        $viewDef = "SELECT * FROM {$this->schema}.tbl_{$this->basename} WHERE deleted_at IS NOT NULL";
        $viewQry = "CREATE OR REPLACE VIEW {$this->schema}.vdel_{$this->basename} AS {$viewDef}";
        DB::connection($this->connection)->statement($viewQry);

    }

    public function downInstructions(): void
    {
        $this->writeMsg("Dropping view: {$this->schema}.vdel_{$this->basename}");
        $dropViewQry = "DROP VIEW IF EXISTS {$this->schema}.vdel_{$this->basename}";
        DB::connection($this->connection)->statement($dropViewQry);

        $this->writeMsg("Replacing view: {$this->schema}.{$this->basename}");
        $viewDef = "SELECT * FROM {$this->schema}.tbl_{$this->basename}";
        $viewQry = "CREATE OR REPLACE VIEW {$this->schema}.{$this->basename} AS {$viewDef}";
        DB::connection($this->connection)->statement($viewQry);
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
