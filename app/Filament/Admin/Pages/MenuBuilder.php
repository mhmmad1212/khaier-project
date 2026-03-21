<?php

namespace App\Filament\Admin\Pages;

use App\Forms\Components\IconPicker;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page as SitePage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;

class MenuBuilder extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationLabel = 'بناء القوائم';
    protected static ?string $title = 'بناء القوائم';
    protected static ?string $navigationGroup = 'الموقع';
    protected static string $view = 'filament.admin.pages.menu-builder';

    public ?int $selectedMenuId = null;
    public array $data = [];
    public string $menuTreeJson = '[]';

    public function mount(): void
    {
        $firstMenu = Menu::query()->orderBy('id')->first();
        $this->selectedMenuId = $firstMenu?->id;

        $this->form->fill([
            'menu_id' => $this->selectedMenuId,
            'type' => 'link',
            'target' => '_self',
        ]);

        $this->refreshTree();
    }

    public function form(Form $form): Form
    {
        $schema = [
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Select::make('menu_id')
                        ->label('القائمة')
                        ->options(Menu::query()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->default($this->selectedMenuId)
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->selectedMenuId = $state ? (int) $state : null;
                            $this->refreshTree();
                        })
                        ->required(),

                    Forms\Components\Placeholder::make('menu_hint')
                        ->label('معلومة')
                        ->content('يمكنك إضافة العناصر هنا ثم ترتيبها بالسحب والإفلات مثل ووردبريس.'),
                ]),

            Forms\Components\Section::make('إضافة عنصر جديد')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema(array_filter([
                            Forms\Components\TextInput::make('title')
                                ->label('العنوان')
                                ->required(),

                            Forms\Components\Select::make('type')
                                ->label('النوع')
                                ->options([
                                    'link' => 'رابط داخلي',
                                    'page' => 'صفحة داخلية',
                                    'news' => 'أخبار',
                                    'external' => 'رابط خارجي',
                                ])
                                ->default('link')
                                ->live()
                                ->required(),

                            Forms\Components\TextInput::make('url')
                                ->label('الرابط')
                                ->visible(fn (callable $get) => in_array($get('type'), ['link', 'external', 'news']))
                                ->required(fn (callable $get) => in_array($get('type'), ['link', 'external', 'news']))
                                ->maxLength(255),

                            Schema::connection('tenant')->hasColumn('menu_items', 'page_id')
                                ? Forms\Components\Select::make('page_id')
                                    ->label('الصفحة')
                                    ->options(SitePage::query()->orderBy('title')->pluck('title', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (callable $get) => $get('type') === 'page')
                                    ->required(fn (callable $get) => $get('type') === 'page')
                                : null,

                            Schema::connection('tenant')->hasColumn('menu_items', 'icon')
                                ? IconPicker::make('icon')
                                    ->label('الأيقونة')
                                    ->columnSpanFull()
                                : null,

                            Schema::connection('tenant')->hasColumn('menu_items', 'target')
                                ? Forms\Components\TextInput::make('target')
                                    ->label('target')
                                    ->default('_self')
                                : null,
                        ])),
                ]),
        ];

        return $form->statePath('data')->schema($schema);
    }

    public function addItem(): void
    {
        $state = $this->form->getState();

        if (! $this->selectedMenuId) {
            Notification::make()->title('اختر قائمة أولًا')->danger()->send();
            return;
        }

        $maxSort = MenuItem::query()
            ->where('menu_id', $this->selectedMenuId)
            ->whereNull('parent_id')
            ->max('sort_order');

        $type = $state['type'] ?? 'link';
        $url = $state['url'] ?? null;

        if ($type === 'news' && blank($url)) {
            $url = '/news';
        }

        $payload = [
            'menu_id' => $this->selectedMenuId,
            'parent_id' => null,
            'title' => $state['title'],
            'type' => $type,
            'url' => $url,
            'sort_order' => (int) ($maxSort ?? 0) + 1,
        ];

        if (Schema::connection('tenant')->hasColumn('menu_items', 'page_id')) {
            $payload['page_id'] = $state['page_id'] ?? null;
        }

        if (Schema::connection('tenant')->hasColumn('menu_items', 'icon')) {
            $payload['icon'] = $state['icon'] ?? null;
        }

        if (Schema::connection('tenant')->hasColumn('menu_items', 'target')) {
            $payload['target'] = $state['target'] ?? '_self';
        }

        MenuItem::query()->create($payload);

        $this->form->fill([
            'menu_id' => $this->selectedMenuId,
            'type' => 'link',
            'target' => '_self',
        ]);

        $this->refreshTree();

        Notification::make()
            ->title('تمت إضافة عنصر القائمة')
            ->success()
            ->send();
    }

    public function deleteItem(int $id): void
    {
        $item = MenuItem::query()->findOrFail($id);

        MenuItem::query()
            ->where('parent_id', $item->id)
            ->update(['parent_id' => $item->parent_id]);

        $item->delete();

        $this->refreshTree();

        Notification::make()
            ->title('تم حذف العنصر')
            ->success()
            ->send();
    }

    public function saveHierarchy(string $treeJson): void
    {
        if (! $this->selectedMenuId) {
            return;
        }

        $tree = json_decode($treeJson, true);

        if (! is_array($tree)) {
            Notification::make()
                ->title('تعذر حفظ ترتيب القائمة')
                ->danger()
                ->send();
            return;
        }

        $this->persistTree($tree, null);

        $this->refreshTree();

        Notification::make()
            ->title('تم حفظ ترتيب القائمة')
            ->success()
            ->send();
    }

    protected function persistTree(array $items, ?int $parentId = null): void
    {
        foreach ($items as $index => $node) {
            if (empty($node['id'])) {
                continue;
            }

            $item = MenuItem::query()
                ->where('menu_id', $this->selectedMenuId)
                ->find($node['id']);

            if (! $item) {
                continue;
            }

            $item->parent_id = $parentId;
            $item->sort_order = $index + 1;
            $item->save();

            if (! empty($node['children']) && is_array($node['children'])) {
                $this->persistTree($node['children'], $item->id);
            }
        }
    }

    protected function refreshTree(): void
    {
        $this->menuTreeJson = json_encode($this->buildTree(), JSON_UNESCAPED_UNICODE);
    }

    protected function buildTree(): array
    {
        if (! $this->selectedMenuId) {
            return [];
        }

        $query = MenuItem::query()
            ->where('menu_id', $this->selectedMenuId)
            ->orderBy('sort_order');

        if (Schema::connection('tenant')->hasColumn('menu_items', 'page_id')) {
            $query->with('page');
        }

        $items = $query->get();

        $byParent = [];
        foreach ($items as $item) {
            $byParent[$item->parent_id ?? 0][] = $item;
        }

        $make = function ($parentId = 0) use (&$make, $byParent) {
            $nodes = [];

            foreach ($byParent[$parentId] ?? [] as $item) {
                $nodes[] = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'type' => $item->type,
                    'icon' => $item->icon ?? null,
                    'url' => $item->resolved_url,
                    'children' => $make($item->id),
                ];
            }

            return $nodes;
        };

        return $make(0);
    }
}
