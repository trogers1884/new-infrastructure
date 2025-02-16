@extends('admin::layouts.admin')
@section('title', 'Web Pages')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Web Pages</h1>
        @if(auth()->user()->checkResourcePermission($thisResourceType, 'admin.web-pages.php','create'))
            <a href="{{ route('admin.web-pages.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Page
            </a>
        @endif
    </div>

    <x-alert type="success" :message="session('success')" />
    <x-alert type="error" :message="session('error')" />

    <!-- Search Section -->
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <x-form :action="route('admin.web-pages.index')" method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="search">
                    Search
                </label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="Search web pages...">
            </div>
            <div class="w-48">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="sort">
                    Sort By
                </label>
                <select id="sort"
                        name="sort"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="url" {{ request('sort', 'url') === 'url' ? 'selected' : '' }}>URL</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                    <option value="updated_at" {{ request('sort') === 'updated_at' ? 'selected' : '' }}>Last Updated</option>
                </select>
            </div>
            <div>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'sort']))
                    <a href="{{ route('admin.web-pages.index') }}"
                       class="ml-2 text-gray-600 hover:text-gray-900">
                        Clear
                    </a>
                @endif
            </div>
        </x-form>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @if($webPages->isEmpty())
            <div class="text-center py-4">
                <p class="text-gray-500">No web pages found.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        URL
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Created At
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($webPages as $page)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $page->url }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $page->description ?: 'No description' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $page->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if(auth()->user()->checkResourcePermission('web_pages', 'admin.web-pages.php','edit'))
                                <a href="{{ route('admin.web-pages.edit', $page) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            @endif

                            @if(auth()->user()->checkResourcePermission('web_pages', 'admin.web-pages.php','delete'))
                                <x-form
                                    :action="route('admin.web-pages.destroy', $page)"
                                    method="DELETE"
                                    class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this web page? This action cannot be undone.');">
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </x-form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $webPages->links() }}
            </div>
        @endif
    </div>
@endsection
