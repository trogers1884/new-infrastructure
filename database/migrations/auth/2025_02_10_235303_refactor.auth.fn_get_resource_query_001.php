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
    protected $basename = 'get_resource_query';

    protected ConsoleOutput $msg;
    protected int $msgKtr = 0;


    public function __construct()
    {
        $this->msg = new ConsoleOutput();
    }

    public function upInstructions(): void
    {
        $this->writeMsg("Replace function: {$this->schema}.{$this->basename}");
        $qryDef = "CREATE OR REPLACE FUNCTION {$this->schema}.{$this->basename}(
    	resource_type_id bigint)
            RETURNS TABLE(resource_id bigint, resource_value text)
            LANGUAGE 'plpgsql'
            COST 100
            VOLATILE PARALLEL UNSAFE
            ROWS 1000

        AS \$BODY\$
        DECLARE
            resource_query text;
        BEGIN
            SELECT format('SELECT id, %I::text AS resource_value FROM %I.%I WHERE deleted_at IS NULL', rtm.resource_value_column, rtm.table_schema, rtm.table_name)
            INTO resource_query
            FROM auth.resource_type_mappings rtm
            WHERE rtm.resource_type_id = $1;

            RETURN QUERY EXECUTE resource_query;
        END;
        \$BODY\$;
        ";

        DB::connection($this->connection)->statement($qryDef);
    }

    public function downInstructions(): void
    {
        $this->writeMsg("Replace function: {$this->schema}.{$this->basename}");
        $qryDef = "CREATE OR REPLACE FUNCTION {$this->schema}.{$this->basename}(
    	resource_type_id bigint)
            RETURNS TABLE(resource_id bigint, resource_value text)
            LANGUAGE 'plpgsql'
            COST 100
            VOLATILE PARALLEL UNSAFE
            ROWS 1000

        AS \$BODY\$
        DECLARE
            resource_query text;
        BEGIN
            SELECT format('SELECT id, %I::text AS resource_value FROM %I.%I', rtm.resource_value_column, rtm.table_schema, rtm.table_name)
            INTO resource_query
            FROM auth.resource_type_mappings rtm
            WHERE rtm.resource_type_id = $1;

            RETURN QUERY EXECUTE resource_query;
        END;
        \$BODY\$;
        ";

        DB::connection($this->connection)->statement($qryDef);
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
