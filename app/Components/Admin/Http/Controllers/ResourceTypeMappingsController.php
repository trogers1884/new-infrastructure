<?php

namespace App\Components\Admin\Http\Controllers;

use App\Components\Admin\Http\Requests\ResourceTypeMappingRequest;
use App\Http\Controllers\Controller;
use App\Models\ResourceType;
use App\Models\ResourceTypeMapping;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ResourceTypeMappingsController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.resource-type-mappings.php';

    protected string $resourceType;
    protected string $resourceValue;

    public function __construct()
    {
        $this->resourceType = self::RESOURCE_TYPE;
        $this->resourceValue = self::RESOURCE_VALUE;
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'view')) {
            abort(403, 'Unauthorized to view resource type mappings');
        }

        return DB::transaction(function () use ($request) {
            $resourceTypeMappings = ResourceTypeMapping::query()
                ->with('resourceType')
                ->search($request->input('search'))
                ->bySchema($request->input('schema'))
                ->ordered($request->input('sort'), $request->input('direction'))
                ->paginate(10)
                ->withQueryString();

            return view('admin::resource-type-mappings.index', [
                'resource_type_mappings' => $resourceTypeMappings,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function create(): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create resource type mappings');
        }

        return DB::transaction(function () {
            $resourceTypes = ResourceType::whereNotIn('id', function($query) {
                $query->select('resource_type_id')
                    ->from('auth.tbl_resource_type_mappings')
                    ->whereNull('deleted_at');
            })->orderBy('name')->get();

            return view('admin::resource-type-mappings.create', [
                'resourceTypes' => $resourceTypes,
                'schemas' => $this->getAvailableSchemas(),
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function store(ResourceTypeMappingRequest $request): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create resource type mappings');
        }

        return DB::transaction(function () use ($request) {
            $resourceTypeMapping = ResourceTypeMapping::create($request->validated());

            Log::info('Resource type mapping created successfully', [
                'resource_type_id' => $resourceTypeMapping->resource_type_id,
                'table' => $resourceTypeMapping->getFullTableName(),
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.resource-type-mappings.index')
                ->with('success', 'Resource type mapping created successfully');
        });
    }

    public function edit(ResourceTypeMapping $resourceTypeMapping): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit resource type mappings');
        }

        return DB::transaction(function () use ($resourceTypeMapping) {
            $resourceTypes = ResourceType::whereNotIn('id', function($query) use ($resourceTypeMapping) {
                $query->select('resource_type_id')
                    ->from('auth.tbl_resource_type_mappings')
                    ->where('resource_type_id', '!=', $resourceTypeMapping->resource_type_id)
                    ->whereNull('deleted_at');
            })->orderBy('name')->get();

            return view('admin::resource-type-mappings.edit', [
                'resource_type_mapping' => $resourceTypeMapping,
                'resourceTypes' => $resourceTypes,
                'schemas' => $this->getAvailableSchemas(),
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function update(ResourceTypeMappingRequest $request, ResourceTypeMapping $resourceTypeMapping): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit resource type mappings');
        }

        return DB::transaction(function () use ($request, $resourceTypeMapping) {
            $resourceTypeMapping->update($request->validated());

            Log::info('Resource type mapping updated successfully', [
                'resource_type_id' => $resourceTypeMapping->resource_type_id,
                'table' => $resourceTypeMapping->getFullTableName(),
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.resource-type-mappings.index')
                ->with('success', 'Resource type mapping updated successfully');
        });
    }

    public function destroy(ResourceTypeMapping $resourceTypeMapping): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'delete')) {
            abort(403, 'Unauthorized to delete resource type mappings');
        }

        return DB::transaction(function () use ($resourceTypeMapping) {
            $resourceTypeMapping->delete();

            Log::info('Resource type mapping deleted successfully', [
                'resource_type_id' => $resourceTypeMapping->resource_type_id,
                'table' => $resourceTypeMapping->getFullTableName(),
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.resource-type-mappings.index')
                ->with('success', 'Resource type mapping deleted successfully');
        });
    }

    public function getTables(Request $request): JsonResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'view')) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $schema = $request->get('schema');
        if (!$schema) {
            return response()->json(['error' => 'Schema is required'], 400);
        }

        return DB::transaction(function () use ($schema) {
            $tables = $this->getTablesForSchema($schema);

            return response()->json(array_map(function($table) {
                return ['table_name' => $table->table_name];
            }, $tables));
        });
    }

    public function getColumns(Request $request): JsonResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'view')) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $schema = $request->get('schema');
        $table = $request->get('table');

        if (!$schema || !$table) {
            return response()->json(['error' => 'Schema and table are required'], 400);
        }

        return DB::transaction(function () use ($schema, $table) {
            return response()->json($this->getColumnsForTable($schema, $table));
        });
    }

    private function getAvailableSchemas(): array
    {
        return DB::select("
            SELECT schema_name
            FROM information_schema.schemata
            WHERE schema_name NOT IN ('information_schema', 'pg_catalog')
            ORDER BY schema_name
        ");
    }

    private function getTablesForSchema(string $schema): array
    {
        return DB::select("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = ?
            AND table_type = 'BASE TABLE'
            ORDER BY table_name
        ", [$schema]);
    }

    private function getColumnsForTable(string $schema, string $table): array
    {
        return DB::select("
            SELECT column_name
            FROM information_schema.columns
            WHERE table_schema = ?
            AND table_name = ?
            ORDER BY ordinal_position
        ", [$schema, $table]);
    }
}
