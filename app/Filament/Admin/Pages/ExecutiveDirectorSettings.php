<?php

namespace App\Filament\Admin\Pages;

use App\Forms\Components\MediaPicker;
use App\Models\ExecutiveDirectorProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ExecutiveDirectorSettings extends Page implements HasForms
{
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?int $navigationSort = 9;
    protected static ?string $slug = 'executive-director';
    protected static bool $shouldRegisterNavigation = true;


    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'المدير التنفيذي';
    protected static ?string $title = 'المدير التنفيذي';

    protected static string $view = 'filament.admin.pages.executive-director-settings';

    public ?array $data = [];
    public ?ExecutiveDirectorProfile $record = null;

    public function mount(): void
    {
        $this->record = ExecutiveDirectorProfile::query()->first();

        $this->form->fill(
            $this->record?->toArray() ?? [
                'name' => null,
                'phone' => null,
                'email' => null,
                'bio' => null,
                'image' => null,
                'image_media_id' => null,
            ]
        );
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات المدير التنفيذي')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم المدير التنفيذي')
                            ->required()
                            ->maxLength(255),

                        MediaPicker::make('image_media_id')
                            ->label('صورة المدير التنفيذي')
                            ->default(fn () => request('selected_media_id'))
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('image')
                            ->default(fn () => request('selected_media_file')),

                        Forms\Components\TextInput::make('phone')
                            ->label('رقم التواصل')
                            ->tel()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('bio')
                            ->label('نبذة عن المدير التنفيذي')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                                'blockquote',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $this->record ??= new ExecutiveDirectorProfile();
        $this->record->fill($state);
        $this->record->save();

        $this->form->fill($this->record->fresh()->toArray());

        Notification::make()
            ->title('تم حفظ بيانات المدير التنفيذي بنجاح')
            ->success()
            ->send();
    }
}
