<?php

namespace App\Components\Admin\Http\Controllers;

use App\Components\Admin\Http\Requests\UserRoleRequest;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UserRolesController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.user-roles.php';

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
            abort(403, 'Unauthorized to view user roles');
        }

        return DB::transaction(function () use ($request) {
            $userRoles = UserRole::with(['user', 'role'])
                ->search($request->input('search'))
                ->sort(
                    $request->input('sort', 'created_at'),
                    $request->input('direction', 'desc')
                )
                ->paginate(10)
                ->withQueryString();

            return view('admin::user-roles.index', [
                'userRoles' => $userRoles,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function create(): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create user roles');
        }

        return DB::transaction(function () {
            return view('admin::user-roles.create', [
                'users' => User::orderBy('name')->get(['id', 'name']),
                'roles' => Role::orderBy('name')->get(['id', 'name']),
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function store(UserRoleRequest $request): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create user roles');
        }

        return DB::transaction(function () use ($request) {
            $userRole = UserRole::create($request->validated());

            Log::info('User role created successfully', [
                'id' => $userRole->id,
                'user_id' => $userRole->user_id,
                'role_id' => $userRole->role_id,
                'created_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.user-roles.index')
                ->with('success', 'User role assigned successfully');
        });
    }

    public function edit(UserRole $userRole): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit user roles');
        }

        return DB::transaction(function () use ($userRole) {
            return view('admin::user-roles.edit', [
                'userRole' => $userRole,
                'users' => User::orderBy('name')->get(['id', 'name']),
                'roles' => Role::orderBy('name')->get(['id', 'name']),
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function update(UserRoleRequest $request, UserRole $userRole): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit user roles');
        }

        return DB::transaction(function () use ($request, $userRole) {
            $userRole->update($request->validated());

            Log::info('User role updated successfully', [
                'id' => $userRole->id,
                'user_id' => $userRole->user_id,
                'role_id' => $userRole->role_id,
                'updated_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.user-roles.index')
                ->with('success', 'User role updated successfully');
        });
    }

    public function destroy(UserRole $userRole): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'delete')) {
            abort(403, 'Unauthorized to delete user roles');
        }

        return DB::transaction(function () use ($userRole) {
            $userRole->delete();

            Log::info('User role deleted successfully', [
                'id' => $userRole->id,
                'user_id' => $userRole->user_id,
                'role_id' => $userRole->role_id,
                'deleted_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.user-roles.index')
                ->with('success', 'User role removed successfully');
        });
    }
}
