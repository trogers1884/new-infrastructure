<?php

namespace App\Components\Admin\View\Composers;

use App\Models\NavigationItem;
use Illuminate\View\View;

class NavigationComposer
{
    public function compose(View $view)
    {
        $navigationItems = NavigationItem::with(['children'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order_index')
            ->get();

        $view->with('navigationItems', $navigationItems);
    }
}
