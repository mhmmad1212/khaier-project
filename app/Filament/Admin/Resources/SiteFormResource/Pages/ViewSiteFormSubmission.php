<?php

namespace App\Filament\Admin\Resources\SiteFormResource\Pages;

use App\Filament\Admin\Resources\SiteFormResource;
use App\Models\SiteForm;
use App\Models\SiteFormSubmission;
use App\Models\SiteFormSubmissionMessage;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ViewSiteFormSubmission extends Page
{
    protected static string $resource = SiteFormResource::class;
    protected static string $view = 'filament.admin.resources.site-form-resource.pages.view-site-form-submission';

    public SiteForm $record;
    public SiteFormSubmission $submission;

    public string $status = 'new';
    public bool $allow_customer_reply = false;
    public string $message = '';
    public string $message_type = 'staff_reply';
    public bool $message_visible = true;

    public function mount(SiteForm $record, SiteFormSubmission $submission): void
    {
        $this->record = $record;
        $this->submission = $submission->load('messages');

        $this->status = (string) $this->submission->status;
        $this->allow_customer_reply = (bool) $this->submission->allow_customer_reply;
    }

    public function saveMeta(): void
    {
        $this->submission->update([
            'status' => $this->status,
            'allow_customer_reply' => $this->allow_customer_reply,
        ]);

        Notification::make()->title('تم تحديث الطلب')->success()->send();
    }

    public function addMessage(): void
    {
        if (blank($this->message)) {
            Notification::make()->title('اكتب الرد أو الملاحظة أولاً')->danger()->send();
            return;
        }

        $type = $this->message_type === 'staff_reply' ? 'staff_reply' : 'internal_note';
        $isVisible = $type === 'staff_reply';

        SiteFormSubmissionMessage::create([
            'site_form_submission_id' => $this->submission->id,
            'message' => $this->message,
            'type' => $type,
            'is_visible_to_customer' => $isVisible,
            'created_by_type' => 'staff',
            'created_by_user_id' => Auth::id(),
        ]);

        if ($type === 'staff_reply') {
            $this->submission->update([
                'status' => 'replied',
            ]);
            $this->status = 'replied';
        }

        $this->message = '';
        $this->submission = $this->submission->fresh('messages');

        Notification::make()->title('تمت إضافة الرسالة')->success()->send();
    }
}
