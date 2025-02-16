<?php

namespace App\Components\Admin\Http\Controllers;

use App\Components\Admin\Helpers\AuthorizationHelper;
use App\Components\Admin\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RolesController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.roles.php';

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
            abort(403, 'Unauthorized to view roles');
        }

        return DB::transaction(function () use ($request) {
            $roles = Role::query()
                ->search($request->input('search'))
                ->orderBy($request->input('sort', 'name'))
                ->paginate(10)
                ->withQueryString();

            return view('admin::roles.index', [
                'roles' => $roles,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function create(): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create roles');
        }

        return view('admin::roles.create', [
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create roles');
        }

        return DB::transaction(function () use ($request) {
            $role = Role::create($request->validated());

            Log::info('Role created successfully', [
                'id' => $role->id,
                'name' => $role->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role created successfully');
        });
    }

    public function edit(Role $role): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit roles');
        }

        return view('admin::roles.edit', [
            'role' => $role,
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit roles');
        }

        return DB::transaction(function () use ($request, $role) {
            $role->update($request->validated());

            Log::info('Role updated successfully', [
                'id' => $role->id,
                'name' => $role->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role updated successfully');
        });
    }

    public function managePermissions(Role $role): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to manage role permissions');
        }

        return DB::transaction(function () use ($role) {
            $permissions = Permission::orderBy('name')->get();
            $rolePermissionIds = $role->permissions->pluck('id')->toArray();

            $criticalPermissions = $permissions->mapWithKeys(function ($permission) {
                return [$permission->id => AuthorizationHelper::isSystemCriticalPermission($permission->name)];
            })->toArray();

            $isSuperAdmin = auth()->user()->hasRole('super_admin');
            $isSystemRole = AuthorizationHelper::isSystemRole($role->name);

            if ($isSystemRole) {
                Log::info('System role permissions being accessed', [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'user_id' => auth()->id(),
                    'user_email' => auth()->user()->email
                ]);
            }

            return view('admin::roles.manage-permissions', [
                'role' => $role,
                'permissions' => $permissions,
                'rolePermissionIds' => $rolePermissionIds,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue,
                'criticalPermissions' => $criticalPermissions,
                'isSuperAdmin' => $isSuperAdmin,
                'isSystemRole' => $isSystemRole
            ]);
        });
    }

    public function updatePermissions(Request $request, Role $role): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to update role permissions');
        }

        $validated = $request->validate([
            'permissions' => 'array|nullable',
            'permissions.*' => [Rule::exists('pgsql.auth.permissions', 'id')]
        ]);

        return DB::transaction(function () use ($validated, $role) {
            $permissions = $validated['permissions'] ?? [];
            $role->permissions()->sync($permissions);

            Log::info('Role permissions updated successfully', [
                'role_id' => $role->id,
                'permissions' => $permissions,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Permissions updated successfully');
        });
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'delete')) {
            abort(403, 'Unauthorized to delete roles');
        }

        if ($role->users()->exists()) {
            return back()->withErrors([
                'error' => 'Cannot delete role that is assigned to users'
            ]);
        }

        return DB::transaction(function () use ($role) {
            $role->permissions()->detach();
            $role->delete();

            Log::info('Role deleted successfully', [
                'id' => $role->id,
                'name' => $role->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Role deleted successfully');
        });
    }
}
