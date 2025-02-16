<?php

namespace App\Components\Admin\Http\Controllers;

use App\Components\Admin\Http\Requests\NavigationItemRequest;
use App\Helpers\RouteHelper;
use App\Http\Controllers\Controller;
use App\Models\MenuType;
use App\Models\NavigationItem;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class NavigationItemsController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.navigation-items.php';

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
            abort(403, 'Unauthorized to view navigation items');
        }

        return DB::transaction(function () use ($request) {
            $navItems = NavigationItem::query()
                ->with(['menuType', 'parent'])
                ->search($request->input('search'))
                ->byMenuType($request->input('menu_type_id'))
                ->ordered()
                ->paginate(10)
                ->withQueryString();

            $menuTypes = MenuType::orderBy('name')->get();
            $activeNavItems = NavigationItem::active()->ordered()->get();

            return view('admin::navigation-items.index', [
                'navItems' => $navItems,
                'menuTypes' => $menuTypes,
                'activeNavItems' => $activeNavItems,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function create(): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create navigation items');
        }

        return DB::transaction(function () {
            return view('admin::navigation-items.create', [
                'menuTypes' => MenuType::orderBy('name')->get(),
                'parentItems' => NavigationItem::whereNull('parent_id')
                    ->orderBy('name')
                    ->get(),
                'activeNavItems' => NavigationItem::active()->ordered()->get(),
                'availableRoutes' => RouteHelper::getAdminNamedRoutes(),
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function store(NavigationItemRequest $request): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create navigation items');
        }

        return DB::transaction(function () use ($request) {
            $navigationItem = NavigationItem::create($request->validated());

            Log::info('Navigation item created successfully', [
                'id' => $navigationItem->id,
                'name' => $navigationItem->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.navigation-items.index')
                ->with('success', 'Navigation item created successfully');
        });
    }

    public function edit(NavigationItem $navigationItem): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit navigation items');
        }

        return DB::transaction(function () use ($navigationItem) {
            return view('admin::navigation-items.edit', [
                'navigationItem' => $navigationItem,
                'menuTypes' => MenuType::orderBy('name')->get(),
                'parentItems' => NavigationItem::where('id', '!=', $navigationItem->id)
                    ->whereNull('parent_id')
                    ->orderBy('name')
                    ->get(),
                'activeNavItems' => NavigationItem::active()->ordered()->get(),
                'availableRoutes' => RouteHelper::getAdminNamedRoutes(),
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function update(NavigationItemRequest $request, NavigationItem $navigationItem): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit navigation items');
        }

        return DB::transaction(function () use ($request, $navigationItem) {
            $navigationItem->update($request->validated());

            Log::info('Navigation item updated successfully', [
                'id' => $navigationItem->id,
                'name' => $navigationItem->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.navigation-items.index')
                ->with('success', 'Navigation item updated successfully');
        });
    }

    public function destroy(NavigationItem $navigationItem): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'delete')) {
            abort(403, 'Unauthorized to delete navigation items');
        }

        if ($navigationItem->children()->exists()) {
            return back()->withErrors([
                'error' => 'Cannot delete navigation item with child items'
            ]);
        }

        return DB::transaction(function () use ($navigationItem) {
            $navigationItem->delete();

            Log::info('Navigation item deleted successfully', [
                'id' => $navigationItem->id,
                'name' => $navigationItem->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.navigation-items.index')
                ->with('success', 'Navigation item deleted successfully');
        });
    }
}
