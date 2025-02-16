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
    protected $basename = 'vw_user_authorizations';

    protected ConsoleOutput $msg;
    protected int $msgKtr = 0;


    public function __construct()
    {
        $this->msg = new ConsoleOutput();
    }

    public function upInstructions(): void
    {
        $this->writeMsg("Dropping view: {$this->schema}.{$this->basename}");
        $dropViewQry = "DROP VIEW IF EXISTS {$this->schema}.{$this->basename}";
        DB::connection($this->connection)->statement($dropViewQry);

        $this->writeMsg("Replacing view: {$this->schema}.{$this->basename}");
        $viewDef = "WITH user_data AS (
         SELECT pu.id AS user_id,
            pu.name AS user_name,
            ara.resource_type_id,
            art.name AS resource_type,
            ara.resource_id,
            ara.role_id,
            ar_1.name AS role
           FROM users pu
             LEFT JOIN auth.resource_associations ara ON ara.user_id = pu.id AND ara.deleted_at IS NULL
             LEFT JOIN auth.resource_types art ON art.id = ara.resource_type_id AND art.deleted_at IS NULL
             LEFT JOIN auth.roles ar_1 ON ar_1.id = ara.role_id AND ar_1.deleted_at IS NULL
          WHERE ara.resource_type_id IS NOT NULL
        ), resources AS (
         SELECT ud.user_id,
            ud.resource_type_id,
            ud.user_name,
            ud.resource_type,
            ud.role_id,
            ud.role,
            r_1.resource_id,
            r_1.resource_value
           FROM user_data ud
             LEFT JOIN LATERAL ( SELECT r_2.resource_id,
                    r_2.resource_value
                   FROM auth.get_resource_query(ud.resource_type_id) r_2(resource_id, resource_value)
                  WHERE ud.resource_id IS NULL OR r_2.resource_id = ud.resource_id) r_1 ON true
        )
, role_results AS ( SELECT DISTINCT r.user_id,
    r.resource_type_id,
    r.user_name,
    r.resource_type,
    r.resource_id,
    r.resource_value,
    COALESCE(r.role_id, ur.role_id) AS role_id,
    COALESCE(r.role, ar.name) AS role
   FROM resources r
     LEFT JOIN auth.user_roles ur ON ur.user_id = r.user_id AND ur.deleted_at IS NULL
     LEFT JOIN auth.roles ar ON ar.id = ur.role_id AND ar.deleted_at IS NULL
  WHERE r.resource_value IS NOT NULL
)
SELECT
	role_results.*
	, arp.permission_id
	, ap.name as permission_name
FROM role_results
LEFT JOIN auth.role_permissions arp ON arp.role_id = role_results.role_id AND arp.deleted_at IS NULL
LEFT JOIN auth.permissions ap ON ap.id = arp.permission_id AND ap.deleted_at IS NULL
ORDER BY user_id, resource_type_id, resource_id, role_id, permission_id
";
        $viewQry = "CREATE OR REPLACE VIEW {$this->schema}.{$this->basename} AS {$viewDef}";
        DB::connection($this->connection)->statement($viewQry);

    }

    public function downInstructions(): void
    {
        $this->writeMsg("Dropping view: {$this->schema}.{$this->basename}");
        $dropViewQry = "DROP VIEW IF EXISTS {$this->schema}.{$this->basename}";
        DB::connection($this->connection)->statement($dropViewQry);

        $this->writeMsg("Re-creating view: {$this->schema}.{$this->basename}");
        $viewDef = " WITH user_data AS (
         SELECT pu.id AS user_id,
            pu.name AS user_name,
            ara.resource_type_id,
            art.name AS resource_type,
            ara.resource_id,
            ara.role_id,
            ar_1.name AS role
           FROM users pu
             LEFT JOIN auth.resource_associations ara ON ara.user_id = pu.id
             LEFT JOIN auth.resource_types art ON art.id = ara.resource_type_id
             LEFT JOIN auth.roles ar_1 ON ar_1.id = ara.role_id
		 WHERE ara.resource_type_id IS NOT NULL

        ), resources AS (
         SELECT ud.user_id,
            ud.resource_type_id,
            ud.user_name,
            ud.resource_type,
            ud.role_id,
            ud.role,
            r_1.resource_id,
            r_1.resource_value
           FROM user_data ud
             LEFT JOIN LATERAL ( SELECT r_2.resource_id,
                    r_2.resource_value
                   FROM auth.get_resource_query(ud.resource_type_id) r_2(resource_id, resource_value)
                  WHERE ud.resource_id IS NULL OR r_2.resource_id = ud.resource_id) r_1 ON true
        )
 SELECT DISTINCT r.user_id,
    r.resource_type_id,
    r.user_name,
    r.resource_type,
    r.resource_id,
    r.resource_value,
    COALESCE(r.role_id, ur.role_id) AS role_id,
    COALESCE(r.role, ar.name) AS role
   FROM resources r
     LEFT JOIN auth.user_roles ur ON ur.user_id = r.user_id
     LEFT JOIN auth.roles ar ON ar.id = ur.role_id
  WHERE r.resource_value IS NOT NULL
  ORDER BY r.user_id;
";
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
