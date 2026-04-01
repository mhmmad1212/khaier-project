<?php

namespace App\Http\Controllers;

use App\Models\SiteForm;
use App\Models\SiteFormSubmission;
use App\Models\SiteFormSubmissionMessage;
use Illuminate\Http\Request;

class FrontendFormController extends Controller
{
    public function show(string $slug)
    {
        $form = SiteForm::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['fields' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('id')])
            ->firstOrFail();

        return view('themes.default.forms.show', compact('form'));
    }

    public function submit(Request $request, string $slug)
    {
        $form = SiteForm::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['fields' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('id')])
            ->firstOrFail();

        $rules = [
            'phone' => ['required', 'string', 'max:30'],
        ];

        foreach ($form->fields as $field) {
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($field->type === 'file') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'max:5120';
            } else {
                $fieldRules[] = match ($field->type) {
                    'email' => 'email',
                    'number' => 'numeric',
                    'url' => 'url',
                    'date' => 'date',
                    default => 'string',
                };
            }

            $rules[$field->name] = $fieldRules;
        }

        $validated = $request->validate($rules);

        $payload = [
            'رقم الجوال' => $validated['phone'],
        ];

        foreach ($form->fields as $field) {
            if ($field->type === 'file' && $request->hasFile($field->name)) {
                $path = $request->file($field->name)->store('form-submissions', 'public');
                $payload[$field->label] = $path;
            } else {
                $payload[$field->label] = $validated[$field->name] ?? null;
            }
        }

        $submission = SiteFormSubmission::create([
            'site_form_id' => $form->id,
            'phone' => $validated['phone'],
            'status' => 'new',
            'allow_customer_reply' => false,
            'data' => $payload,
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'submitted_at' => now(),
        ]);

        $submission->update([
            'reference_number' => 'REQ-' . now()->format('Ymd') . '-' . str_pad((string) $submission->id, 6, '0', STR_PAD_LEFT),
        ]);

        return back()->with('success', $form->success_message ?: 'تم إرسال الطلب بنجاح')
            ->with('reference_number', $submission->reference_number);
    }

    public function track(string $slug)
    {
        $form = SiteForm::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('themes.default.forms.track', compact('form'));
    }

    public function lookup(Request $request, string $slug)
    {
        $request->validate([
            'reference_number' => ['required', 'string'],
            'phone' => ['required', 'string'],
        ]);

        $form = SiteForm::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $submission = SiteFormSubmission::query()
            ->where('site_form_id', $form->id)
            ->where('reference_number', $request->reference_number)
            ->where('phone', $request->phone)
            ->with('messages')
            ->first();

        return view('themes.default.forms.track', compact('form', 'submission'));
    }

    public function customerReply(Request $request, string $slug, int $submissionId)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'message' => ['required', 'string'],
        ]);

        $form = SiteForm::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $submission = SiteFormSubmission::query()
            ->where('site_form_id', $form->id)
            ->where('id', $submissionId)
            ->where('phone', $request->phone)
            ->firstOrFail();

        abort_unless($submission->allow_customer_reply, 403);

        SiteFormSubmissionMessage::create([
            'site_form_submission_id' => $submission->id,
            'message' => $request->message,
            'type' => 'customer_reply',
            'is_visible_to_customer' => true,
            'created_by_type' => 'customer',
        ]);

        return back()->with('success', 'تم إرسال ردك بنجاح');
    }
}
