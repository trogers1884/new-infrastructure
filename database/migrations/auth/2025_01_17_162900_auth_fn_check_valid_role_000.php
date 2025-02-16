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
    protected $basename = 'check_valid_role';

    protected ConsoleOutput $msg;
    protected int $msgKtr = 0;


    public function __construct()
    {
        $this->msg = new ConsoleOutput();
    }

    public function upInstructions(): void
    {
        $this->writeMsg("Add function: {$this->schema}.{$this->basename}");
        $qryDef = "CREATE OR REPLACE FUNCTION {$this->schema}.{$this->basename}()
            RETURNS trigger
            LANGUAGE 'plpgsql'
            COST 100
            VOLATILE NOT LEAKPROOF
        AS \$BODY\$
        BEGIN
            IF NEW.role_id IS NOT NULL THEN
                IF NOT EXISTS (
                    SELECT 1
                    FROM auth.tbl_user_roles
                    WHERE user_id = NEW.user_id
                    AND role_id = NEW.role_id
                ) THEN
                    RAISE EXCEPTION 'Invalid role_id for user. The role must be assigned to the user in tbl_user_roles.';
                END IF;
            END IF;
            RETURN NEW;
        END;
        \$BODY\$;
        ";

        DB::connection($this->connection)->statement($qryDef);
    }

    public function downInstructions(): void
    {
        $this->writeMsg("Drop function: {$this->schema}.{$this->basename}");
        $dropQry = "DROP FUNCTION IF EXISTS {$this->schema}.{$this->basename}";
        DB::connection($this->connection)->statement($dropQry);
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
