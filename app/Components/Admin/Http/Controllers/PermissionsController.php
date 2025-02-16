<?php

namespace App\Components\Admin\Http\Controllers;

use App\Components\Admin\Http\Requests\PermissionRequest;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PermissionsController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.permissions.php';

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
            abort(403, 'Unauthorized to view permissions');
        }

        return DB::transaction(function () use ($request) {
            $permissions = Permission::query()
                ->search($request->input('search'))
                ->orderBy($request->input('sort', 'name'))
                ->paginate(10)
                ->withQueryString();

            return view('admin::permissions.index', [
                'permissions' => $permissions,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function create(): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create permissions');
        }

        return view('admin::permissions.create', [
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function store(PermissionRequest $request): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create permissions');
        }

        return DB::transaction(function () use ($request) {
            $permission = Permission::create($request->validated());

            Log::info('Permission created successfully', [
                'id' => $permission->id,
                'name' => $permission->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.permissions.index')
                ->with('success', 'Permission created successfully');
        });
    }

    public function edit(Permission $permission): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit this permission');
        }

        return view('admin::permissions.edit', [
            'permission' => $permission,
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function update(PermissionRequest $request, Permission $permission): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit this permission');
        }

        return DB::transaction(function () use ($request, $permission) {
            $permission->update($request->validated());

            Log::info('Permission updated successfully', [
                'id' => $permission->id,
                'name' => $permission->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.permissions.index')
                ->with('success', 'Permission updated successfully');
        });
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'delete')) {
            abort(403, 'Unauthorized to delete permissions');
        }

        if ($permission->roles()->exists()) {
            return back()->withErrors([
                'error' => 'Cannot delete permission that is assigned to roles'
            ]);
        }

        return DB::transaction(function () use ($permission) {
            $pageInfo = [
                'id' => $permission->id,
                'name' => $permission->name,
                'user_id' => auth()->id()
            ];

            $permission->delete();

            Log::info('Permission deleted successfully', $pageInfo);

            return redirect()
                ->route('admin.permissions.index')
                ->with('success', 'Permission deleted successfully');
        });
    }
}
