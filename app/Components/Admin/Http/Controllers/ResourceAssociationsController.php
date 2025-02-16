<?php

namespace App\Components\Admin\Http\Controllers;

use App\Components\Admin\Http\Requests\ResourceAssociationRequest;
use App\Http\Controllers\Controller;
use App\Models\ResourceAssociation;
use App\Models\ResourceType;
use App\Models\Role;
use App\Models\User;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ResourceAssociationsController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.resource-associations.php';

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
            abort(403, 'Unauthorized to view resource associations');
        }

        return DB::transaction(function () use ($request) {
            $resourceAssociations = ResourceAssociation::with(['user', 'resourceType', 'role'])
                ->search($request->input('search'))
                ->sort(
                    $request->input('sort', 'created_at'),
                    $request->input('direction', 'desc')
                )
                ->paginate(10)
                ->withQueryString();

            $resourceMappings = DB::table('auth.tbl_resource_type_mappings')
                ->pluck('resource_value_column', 'resource_type_id');

            foreach ($resourceAssociations as $association) {
                $this->loadResourceValue($association, $resourceMappings);
            }

            return view('admin::resource-associations.index', [
                'resourceAssociations' => $resourceAssociations,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function create(): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create resource associations');
        }

        return DB::transaction(function () {
            return view('admin::resource-associations.create', [
                'users' => User::where('active', true)->orderBy('name')->get(),
                'resourceTypes' => ResourceType::orderBy('name')->get(),
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function store(ResourceAssociationRequest $request): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create resource associations');
        }

        return DB::transaction(function () use ($request) {
            $resourceAssociation = ResourceAssociation::create($request->validated());

            Log::info('Resource association created successfully', [
                'id' => $resourceAssociation->id,
                'user_id' => $resourceAssociation->user_id,
                'resource_type_id' => $resourceAssociation->resource_type_id,
                'role_id' => $resourceAssociation->role_id
            ]);

            return redirect()
                ->route('admin.resource-associations.index')
                ->with('success', 'Resource association created successfully');
        });
    }

    public function edit(ResourceAssociation $resourceAssociation): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit resource associations');
        }

        return DB::transaction(function () use ($resourceAssociation) {
            $resources = [];
            if ($resourceAssociation->resource_type_id) {
                $resources = DB::select(
                    "SELECT * FROM auth.get_resource_query(?)",
                    [$resourceAssociation->resource_type_id]
                );
            }

            return view('admin::resource-associations.edit', [
                'resourceAssociation' => $resourceAssociation,
                'users' => User::where('active', true)->orderBy('name')->get(),
                'resourceTypes' => ResourceType::orderBy('name')->get(),
                'roles' => Role::orderBy('name')->get(),
                'resources' => $resources,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function update(ResourceAssociationRequest $request, ResourceAssociation $resourceAssociation): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit resource associations');
        }

        return DB::transaction(function () use ($request, $resourceAssociation) {
            $resourceAssociation->update($request->validated());

            Log::info('Resource association updated successfully', [
                'id' => $resourceAssociation->id,
                'user_id' => $resourceAssociation->user_id,
                'resource_type_id' => $resourceAssociation->resource_type_id,
                'role_id' => $resourceAssociation->role_id
            ]);

            return redirect()
                ->route('admin.resource-associations.index')
                ->with('success', 'Resource association updated successfully');
        });
    }

    public function destroy(ResourceAssociation $resourceAssociation): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'delete')) {
            abort(403, 'Unauthorized to delete resource associations');
        }

        return DB::transaction(function () use ($resourceAssociation) {
            $resourceAssociation->delete();

            Log::info('Resource association deleted successfully', [
                'id' => $resourceAssociation->id,
                'user_id' => $resourceAssociation->user_id,
                'resource_type_id' => $resourceAssociation->resource_type_id,
                'role_id' => $resourceAssociation->role_id
            ]);

            return redirect()
                ->route('admin.resource-associations.index')
                ->with('success', 'Resource association deleted successfully');
        });
    }

    public function getRoles(Request $request): JsonResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userId = $request->get('user_id');
        if (!$userId) {
            return response()->json(['error' => 'User ID is required'], 400);
        }

        return DB::transaction(function () use ($userId) {
            $roles = DB::table('auth.tbl_user_roles as ur')
                ->join('auth.tbl_roles as r', 'ur.role_id', '=', 'r.id')
                ->where('ur.user_id', $userId)
                ->whereNull('ur.deleted_at')
                ->select('r.id', 'r.name', 'r.description')
                ->orderBy('r.name')
                ->get();

            return response()->json($roles);
        });
    }

    public function getResources(Request $request): JsonResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $resourceTypeId = $request->get('resource_type_id');
        if (!$resourceTypeId) {
            return response()->json(['error' => 'Resource type ID is required'], 400);
        }

        return DB::transaction(function () use ($resourceTypeId) {
            $mapping = DB::table('auth.tbl_resource_type_mappings')
                ->where('resource_type_id', $resourceTypeId)
                ->first();

            if (!$mapping) {
                return response()->json(['error' => 'No mapping found for this resource type'], 404);
            }

            $query = "SELECT id, {$mapping->resource_value_column} as value
                     FROM {$mapping->table_schema}.{$mapping->table_name}
                     WHERE deleted_at IS NULL
                     ORDER BY {$mapping->resource_value_column}";

            return response()->json(DB::select($query));
        });
    }

    private function loadResourceValue(ResourceAssociation $association, $resourceMappings): void
    {
        if ($association->resource_id && isset($resourceMappings[$association->resource_type_id])) {
            $mapping = DB::table('auth.tbl_resource_type_mappings')
                ->where('resource_type_id', $association->resource_type_id)
                ->first();

            if ($mapping) {
                $query = "SELECT {$mapping->resource_value_column} as value
                         FROM {$mapping->table_schema}.{$mapping->table_name}
                         WHERE id = ?";

                $result = DB::selectOne($query, [$association->resource_id]);
                $association->resource_value = $result
                    ? "{$mapping->resource_value_column}: {$result->value}"
                    : null;
            }
        }

        if (!isset($association->resource_value)) {
            $mapping = $resourceMappings[$association->resource_type_id] ?? null;
            $association->resource_value = $mapping ? "{$mapping}: all" : 'all';
        }
    }
}
