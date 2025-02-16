<?php

namespace App\Components\Admin\Http\Controllers;

use App\Components\Admin\Http\Requests\WebPageRequest;
use App\Http\Controllers\Controller;
use App\Models\WebPage;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WebPagesController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.web-pages.php';

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
            abort(403, 'Unauthorized to view web pages');
        }

        return DB::transaction(function () use ($request) {
            $webPages = WebPage::query()
                ->search($request->input('search'))
                ->orderBy($request->input('sort', 'url'))
                ->paginate(10)
                ->withQueryString();

            return view('admin::web-pages.index', [
                'webPages' => $webPages,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function create(): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create web pages');
        }

        return view('admin::web-pages.create', [
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function store(WebPageRequest $request): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'create')) {
            abort(403, 'Unauthorized to create web pages');
        }

        return DB::transaction(function () use ($request) {
            $webPage = WebPage::create($request->validated());

            Log::info('Web page created successfully', [
                'id' => $webPage->id,
                'url' => $webPage->url,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.web-pages.index')
                ->with('success', 'Web page created successfully');
        });
    }

    public function edit(WebPage $webPage): View
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit this web page');
        }

        return view('admin::web-pages.edit', [
            'webPage' => $webPage,
            'thisResourceType' => $this->resourceType,
            'thisResourceValue' => $this->resourceValue
        ]);
    }

    public function update(WebPageRequest $request, WebPage $webPage): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'edit')) {
            abort(403, 'Unauthorized to edit this web page');
        }

        return DB::transaction(function () use ($request, $webPage) {
            $webPage->update($request->validated());

            Log::info('Web page updated successfully', [
                'id' => $webPage->id,
                'url' => $webPage->url,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.web-pages.index')
                ->with('success', 'Web page updated successfully');
        });
    }

    public function destroy(WebPage $webPage): RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'delete')) {
            abort(403, 'Unauthorized to delete this web page');
        }

        return DB::transaction(function () use ($webPage) {
            $pageInfo = [
                'id' => $webPage->id,
                'url' => $webPage->url,
                'user_id' => auth()->id()
            ];

            $webPage->delete();

            Log::info('Web page deleted successfully', $pageInfo);

            return redirect()
                ->route('admin.web-pages.index')
                ->with('success', 'Web page deleted successfully');
        });
    }
}
