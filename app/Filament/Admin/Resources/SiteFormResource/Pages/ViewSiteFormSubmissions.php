<?php

namespace App\Filament\Admin\Resources\SiteFormResource\Pages;

use App\Filament\Admin\Resources\SiteFormResource;
use App\Models\SiteForm;
use Filament\Resources\Pages\Page;
use Livewire\WithPagination;

class ViewSiteFormSubmissions extends Page
{
    use WithPagination;

    protected static string $resource = SiteFormResource::class;
    protected static string $view = 'filament.admin.resources.site-form-resource.pages.view-site-form-submissions';

    protected static ?string $title = 'طلبات النموذج';
    protected static ?string $breadcrumb = 'طلبات النموذج';
    protected static ?string $navigationLabel = 'طلبات النموذج';

    public SiteForm $record;

    public string $search = '';
    public string $statusFilter = '';

    protected $paginationTheme = 'tailwind';

    public function mount(SiteForm $record): void
    {
        $this->record = $record;
    }

    public function getHeading(): string
    {
        return 'طلبات النموذج';
    }

    public function getSubheading(): ?string
    {
        return 'إدارة ومتابعة الطلبات المرسلة لنموذج: ' . $this->record->name;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function getSubmissionsProperty()
    {
        return $this->record->submissions()
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('reference_number', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest('id')
            ->paginate(10);
    }

    public function exportCsv()
    {
        $rows = $this->record->submissions()
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('reference_number', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest('id')
            ->get();

        $statusMap = [
            'new' => 'جديد',
            'under_review' => 'قيد المراجعة',
            'awaiting_completion' => 'بانتظار الاستكمال',
            'replied' => 'تم الرد',
            'completed' => 'مكتمل',
            'rejected' => 'مرفوض',
        ];

        $fileName = 'طلبات-النموذج-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->stream(function () use ($rows, $statusMap) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['رقم الطلب', 'رقم الجوال', 'الحالة', 'تاريخ الإرسال']);

            foreach ($rows as $row) {
                fputcsv($file, [
                    $row->reference_number,
                    $row->phone,
                    $statusMap[$row->status] ?? $row->status,
                    optional($row->submitted_at)->format('Y-m-d h:i A') ?: optional($row->created_at)->format('Y-m-d h:i A'),
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}
