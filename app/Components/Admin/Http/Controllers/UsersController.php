<?php

namespace App\Components\Admin\Http\Controllers;

use App\Components\Admin\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UsersController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.users.php';

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
            abort(403, 'Unauthorized to view users');
        }

        return DB::transaction(function () use ($request) {
            $query = User::query()
                ->search($request->input('search'))
                ->filterByStatus($request->input('status'))
                ->orderBy($request->input('sort', 'name'));

            Log::info('User query:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $users = $query->paginate(10)->withQueryString();

            return view('admin::users.index', [
                'users' => $users,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function create(): View|RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create users');
        }

        return view('admin::users.create', [
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create users');
        }

        return DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            Log::info('User created successfully', [
                'id' => $user->id,
                'name' => $user->name
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User created successfully');
        });
    }

    public function edit(User $user): View|RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit users');
        }

        return view('admin::users.edit', [
            'user' => $user,
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit users');
        }

        return DB::transaction(function () use ($request, $user) {
            $validated = $request->validated();

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            Log::info('User updated successfully', [
                'id' => $user->id,
                'name' => $user->name
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User updated successfully');
        });
    }

    public function destroy(User $user): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'delete')) {
            abort(403, 'Unauthorized to delete users');
        }

        return DB::transaction(function () use ($user) {
            $user->delete();

            Log::info('User deleted successfully', [
                'id' => $user->id,
                'name' => $user->name
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User deleted successfully');
        });
    }
}
