<!DOCTYPE html>
<html>
<head>
    <title>Admin - @yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 h-screen flex">
<!-- Sidebar -->
<div x-data="{ open: false }"
     class="fixed inset-y-0 left-0 z-30 w-96 bg-gray-800 text-white transform transition-transform duration-300 ease-in-out
                md:relative md:translate-x-0
                {{ $errors->any() ? '-translate-x-full' : '' }}"
     :class="{ '-translate-x-full': !open }">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-700">
        <div class="flex items-center">
            <a href="{{ route('admin.admin.dashboard') }}">
                <img src="{{ asset('images/pawpaw_logo.png') }}" alt="Admin Logo" class="w-full h-auto object-contain">
            </a>
        </div>
        <button @click="open = false" class="md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Admin Panel Title -->
    <div class="flex items-center justify-between p-4 border-b border-gray-700">
        <div class="flex items-center">
            <i class="fas fa-shield-alt mr-2"></i>
            <span class="text-xl font-bold">Admin Panel</span>
        </div>
        <button @click="open = false" class="md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="mt-5">
        @foreach($navigationItems as $item)
            <a href="{{ route($item->route) }}"
               class="flex items-center py-2 px-4 hover:bg-gray-700 {{ request()->routeIs($item->route . '*') ? 'bg-gray-700' : '' }}">
                @if($item->icon)
                    <i class="{{ \App\Helpers\IconHelper::getIconClasses($item->icon) }} w-6 mr-2"></i>
                @endif
                <span>{{ $item->name }}</span>
            </a>
        @endforeach
    </nav>

    <!-- Profile Section -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700">
        <div x-data="{ profileOpen: false }" class="relative">
            <button @click="profileOpen = !profileOpen"
                    class="w-full flex items-center justify-between py-2 px-4 hover:bg-gray-700 focus:outline-none">
                <div class="flex items-center">
                    <i class="fas fa-user-circle mr-2"></i>
                    <span>{{ Auth::user()->name }}</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200"
                     :class="{ 'transform rotate-180': profileOpen }"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="profileOpen"
                 @click.away="profileOpen = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute bottom-full left-0 right-0 bg-gray-700 rounded-t-lg shadow-lg">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center px-4 py-2 hover:bg-gray-600">
                    <i class="fas fa-user-edit mr-2"></i>
                    <span>My Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center px-4 py-2 hover:bg-gray-600 text-left">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Hamburger Button -->
<button @click="open = true"
        class="fixed top-4 left-4 z-40 md:hidden bg-white p-2 rounded-md shadow-md">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto p-8 ml-0 md:ml-16 mt-0">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
