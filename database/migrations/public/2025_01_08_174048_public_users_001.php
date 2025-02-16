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
    protected $schema = 'public';
    protected $basename = 'users';

    protected ConsoleOutput $msg;
    protected int $msgKtr = 0;


    public function __construct()
    {
        $this->msg = new ConsoleOutput();
    }

    public function upInstructions(): void
    {
        $this->writeMsg("Add column to: {$this->schema}.{$this->basename}");
        $tblDef = "ALTER TABLE {$this->schema}.{$this->basename} ADD COLUMN active bool DEFAULT true";
        DB::connection($this->connection)->statement($tblDef);
    }

    public function downInstructions(): void
    {

        $this->writeMsg("Dropping column from: {$this->schema}.{$this->basename}");
        $dropTblQry = "ALTER TABLE {$this->schema}.{$this->basename} DROP COLUMN active";
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
