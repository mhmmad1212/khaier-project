<?php

namespace App\Filament\Admin\Pages;

use App\Models\Menu;
use App\Models\MenuItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MenuBuilderPro extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static string $view = 'filament.admin.pages.menu-builder-pro';
    protected static ?string $navigationLabel = 'بناء القوائم PRO';
    protected static ?string $title = 'بناء القوائم PRO';

    public $menuId;

    public function mount(): void
    {
        $this->menuId = Menu::query()->first()?->id;
    }

    public function getTree(): array
    {
        if (! $this->menuId) {
            return [];
        }

        $items = MenuItem::query()
            ->where('menu_id', $this->menuId)
            ->orderBy('sort_order')
            ->get();

        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->parent_id ?? 0][] = $item;
        }

        $build = function ($parentId = 0) use (&$build, $grouped) {
            $nodes = [];

            foreach ($grouped[$parentId] ?? [] as $item) {
                $nodes[] = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'children' => $build($item->id),
                ];
            }

            return $nodes;
        };

        return $build(0);
    }

    public function saveTree($tree): void
    {
        if (! $this->menuId || ! is_array($tree)) {
            return;
        }

        $this->updateItems($tree, null);

        Notification::make()
            ->title('تم حفظ ترتيب القوائم')
            ->success()
            ->send();
    }

    private function updateItems(array $items, ?int $parentId): void
    {
        foreach ($items as $index => $item) {
            $menuItem = MenuItem::query()
                ->where('menu_id', $this->menuId)
                ->whereKey($item['id'])
                ->first();

            if (! $menuItem) {
                continue;
            }

            $menuItem->parent_id = $parentId;
            $menuItem->sort_order = $index + 1;
            $menuItem->save();

            if (! empty($item['children']) && is_array($item['children'])) {
                $this->updateItems($item['children'], $menuItem->id);
            }
        }
    }
}
