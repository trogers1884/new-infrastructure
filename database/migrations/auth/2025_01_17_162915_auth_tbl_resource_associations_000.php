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
    protected $basename = 'resource_associations';

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
                id BIGSERIAL,
                user_id bigint NOT NULL,
                resource_type_id bigint NOT NULL,
                role_id bigint,
                description character varying COLLATE pg_catalog.\"default\",
                resource_id bigint,
                created_at timestamp(0) with time zone,
                updated_at timestamp(0) with time zone,
                deleted_at timestamp(0) with time zone,
                CONSTRAINT pk_resource_assoc_id PRIMARY KEY (id),
                CONSTRAINT fk_resource_assoc_resource_types_resource_type_id FOREIGN KEY (resource_type_id)
                    REFERENCES auth.tbl_resource_types (id) MATCH SIMPLE
                    ON UPDATE NO ACTION
                    ON DELETE NO ACTION
                    NOT VALID,
                CONSTRAINT fk_resource_assoc_users_user_id FOREIGN KEY (user_id)
                    REFERENCES public.users (id) MATCH SIMPLE
                    ON UPDATE NO ACTION
                    ON DELETE NO ACTION
                    NOT VALID
                        )";
        $idxs = ([
            "CREATE UNIQUE INDEX unq_resource_assoc_user_id_resource_type_id_role_id_resource_id ON {$this->schema}.tbl_{$this->basename} (user_id, resource_type_id, role_id, resource_id)"
            , "CREATE INDEX idx_resource_assoc_created_at ON {$this->schema}.tbl_{$this->basename} (created_at)"
            , "CREATE INDEX idx_resource_assoc_updated_at ON {$this->schema}.tbl_{$this->basename} (updated_at)"
        ]);

        DB::connection($this->connection)->statement($tblDef);

        foreach($idxs AS $idx){
            DB::connection($this->connection)->statement($idx);
        }

        $triggerDef = "CREATE TRIGGER check_valid_role_trigger
            BEFORE INSERT OR UPDATE
            ON auth.tbl_resource_associations
            FOR EACH ROW
            EXECUTE FUNCTION auth.check_valid_role();";

        DB::connection($this->connection)->statement($triggerDef);

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
