<?php

namespace App\Components\Admin\Http\Controllers;

use App\Components\Admin\Http\Requests\MenuTypeRequest;
use App\Http\Controllers\Controller;
use App\Models\MenuType;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MenuTypesController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.menu-types.php';

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
            abort(403, 'Unauthorized to view menu types');
        }

        return DB::transaction(function () use ($request) {
            $menuTypes = MenuType::query()
                ->search($request->input('search'))
                ->sort(
                    $request->input('sort', 'name'),
                    $request->input('direction', 'asc')
                )
                ->withCount('navigationItems')
                ->paginate(10)
                ->withQueryString();

            return view('admin::menu-types.index', [
                'menuTypes' => $menuTypes,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function create(): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create menu types');
        }

        return view('admin::menu-types.create', [
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function store(MenuTypeRequest $request): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create menu types');
        }

        return DB::transaction(function () use ($request) {
            $menuType = MenuType::create($request->validated());

            Log::info('Menu type created successfully', [
                'id' => $menuType->id,
                'name' => $menuType->name,
                'created_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.menu-types.index')
                ->with('success', 'Menu type created successfully');
        });
    }

    public function edit(MenuType $menuType): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit menu types');
        }

        return view('admin::menu-types.edit', [
            'menuType' => $menuType,
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function update(MenuTypeRequest $request, MenuType $menuType): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit menu types');
        }

        return DB::transaction(function () use ($request, $menuType) {
            $menuType->update($request->validated());

            Log::info('Menu type updated successfully', [
                'id' => $menuType->id,
                'name' => $menuType->name,
                'updated_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.menu-types.index')
                ->with('success', 'Menu type updated successfully');
        });
    }

    public function destroy(MenuType $menuType): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'delete')) {
            abort(403, 'Unauthorized to delete menu types');
        }

        if ($menuType->navigationItems()->exists()) {
            return back()->withErrors([
                'error' => 'Cannot delete menu type: It has associated navigation items'
            ]);
        }

        return DB::transaction(function () use ($menuType) {
            $menuTypeDetails = [
                'id' => $menuType->id,
                'name' => $menuType->name
            ];

            $menuType->delete();

            Log::info('Menu type deleted successfully', [
                ...$menuTypeDetails,
                'deleted_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.menu-types.index')
                ->with('success', 'Menu type deleted successfully');
        });
    }
}
