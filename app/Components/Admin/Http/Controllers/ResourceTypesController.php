<?php

namespace App\Components\Admin\Http\Controllers;

use App\Components\Admin\Http\Requests\ResourceTypeRequest;
use App\Http\Controllers\Controller;
use App\Models\ResourceType;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ResourceTypesController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.resource-types.php';

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
            abort(403, 'Unauthorized to view resource types');
        }

        return DB::transaction(function () use ($request) {
            $resourceTypes = ResourceType::query()
                ->search($request->input('search'))
                ->orderBy($request->input('sort', 'name'))
                ->paginate(10)
                ->withQueryString();

            return view('admin::resource-types.index', [
                'resourceTypes' => $resourceTypes,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function create(): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create resource types');
        }

        return view('admin::resource-types.create', [
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function store(ResourceTypeRequest $request): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create resource types');
        }

        return DB::transaction(function () use ($request) {
            $resourceType = ResourceType::create($request->validated());

            Log::info('Resource type created successfully', [
                'id' => $resourceType->id,
                'name' => $resourceType->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.resource-types.index')
                ->with('success', 'Resource type created successfully');
        });
    }

    public function edit(ResourceType $resourceType): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit resource types');
        }

        return view('admin::resource-types.edit', [
            'resourceType' => $resourceType,
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function update(ResourceTypeRequest $request, ResourceType $resourceType): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit resource types');
        }

        return DB::transaction(function () use ($request, $resourceType) {
            $resourceType->update($request->validated());

            Log::info('Resource type updated successfully', [
                'id' => $resourceType->id,
                'name' => $resourceType->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.resource-types.index')
                ->with('success', 'Resource type updated successfully');
        });
    }

    public function destroy(ResourceType $resourceType): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'delete')) {
            abort(403, 'Unauthorized to delete resource types');
        }

        return DB::transaction(function () use ($resourceType) {
            $pageInfo = [
                'id' => $resourceType->id,
                'name' => $resourceType->name,
                'user_id' => auth()->id()
            ];

            $resourceType->delete();

            Log::info('Resource type deleted successfully', $pageInfo);

            return redirect()
                ->route('admin.resource-types.index')
                ->with('success', 'Resource type deleted successfully');
        });
    }
}
