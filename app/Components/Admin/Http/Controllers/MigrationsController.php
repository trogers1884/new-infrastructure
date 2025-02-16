<?php

namespace App\Components\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Migration;
use App\Traits\WebPageAuthorization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MigrationsController extends Controller
{
    use WebPageAuthorization;

    private const RESOURCE_TYPE = 'web_pages';
    private const RESOURCE_VALUE = 'admin.migrations.php';

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
            abort(403, 'Unauthorized to view migrations');
        }

        return DB::transaction(function () use ($request) {
            $migrations = Migration::query()
                ->when($request->input('search'), function ($query, $search) {
                    return $query->search($search);
                })
                ->orderBy($request->input('sort', 'id'), $request->input('direction', 'desc'))
                ->paginate(10)
                ->withQueryString();

            return view('admin::migrations.index', [
                'migrations' => $migrations,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }

    public function show(int $id): View|RedirectResponse
    {
        if (!$this->checkResourcePermission($this->resourceType, $this->resourceValue, 'view')) {
            abort(403, 'Unauthorized to view migration details');
        }

        return DB::transaction(function () use ($id) {
            $migration = Migration::findOrFail($id);

            Log::info('Migration details accessed', [
                'migration_id' => $id,
                'user_id' => auth()->id()
            ]);

            return view('admin::migrations.show', [
                'migration' => $migration,
                'thisResourceType' => $this->resourceType,
                'thisResourceValue' => $this->resourceValue
            ]);
        });
    }
}
