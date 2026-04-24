<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Debug logging for tests (temporary)
        if (app()->runningUnitTests()) {
            $allowed = (bool) ($user && $user->is_active && ($user->isStudent() || $user->isAdmin()));
            \Log::debug('StoreProjectRequest authorize check', ['user' => $user ? $user->toArray() : null, 'allowed' => $allowed]);
            return $allowed;
        }

    // Allow students and admins to submit. Advisers verify.
        return $user && $user->is_active && ($user->isStudent() || $user->isAdmin());
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('authors') && is_array($this->authors)) {
            // Remove empty string inputs so they don't fail the 'required' check
            $filteredAuthors = array_filter($this->authors, function ($value) {
                return !is_null($value) && trim($value) !== '';
            });
            $this->merge([
                'authors' => array_values($filteredAuthors)
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Convert MB from settings to KB for validation
        $maxManuscriptMb = (int) \App\Models\Setting::get('max_upload_size', '10');
        $maxManuscriptKb = $maxManuscriptMb * 1024;
        
        $maxAttachmentMb = (int) \App\Models\Setting::get('max_attachment_size', '50');
        $maxAttachmentKb = $maxAttachmentMb * 1024;
        
        $allowedTypes = str_replace(' ', '', \App\Models\Setting::get('allowed_file_types', 'pdf,zip,doc,docx,ppt,pptx,xls,xlsx,mp4,avi,mov,sql,txt,csv,json,xml,jpg,jpeg,png,gif,md,rar,7z'));

        // Smart Year Validation: If setting is blank, allow any year. If set, force that year.
        $yearSetting = \App\Models\Setting::get('academic_year');
        $yearRules = ['required', 'integer'];
        
        if ($yearSetting) {
            $yearRules[] = 'min:' . $yearSetting;
            $yearRules[] = 'max:' . $yearSetting;
        }
        
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->where(function ($query) {
                    return $query->where('status', '!=', 'rejected')
                                 ->whereNull('deleted_at');
                })
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('projects', 'slug')->whereNull('deleted_at')
            ],
            'abstract' => ['required','string'],
            'year' => $yearRules,
            'adviser_name' => ['required','string','max:255'],
            'authors' => ['required','array','min:1'],
            'authors.*' => ['required','string','max:255'],

            // Metadata
            'program' => [
                'required', 
                'string', 
                auth()->user()->isAdmin() 
                    ? Rule::in(\App\Models\Program::pluck('abbreviation')->toArray())
                    : Rule::in([auth()->user()->program])
            ],
            'categories' => ['required_without:other_category', 'array'],
            'categories.*' => ['exists:categories,id'],
            'other_category' => ['nullable', 'string', 'max:50', 'required_if:other_category_trigger,on'],

            // Main manuscript: PDF only
            'manuscript' => ['required','file','mimes:pdf','max:'.$maxManuscriptKb], 

            // Optional attachments — broad types for capstone research submissions
            'attachments' => ['nullable','array'],
            'attachments.*' => [
                'file',
                'max:'.$maxAttachmentKb,
                'extensions:'.$allowedTypes,
            ],

            // Policy acknowledgment
            'acknowledge_policy' => ['accepted'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $yearSetting = \App\Models\Setting::get('academic_year');
        $message = $yearSetting 
            ? "Submissions are currently restricted to the {$yearSetting} academic year."
            : "Please enter a valid academic year (e.g., 2020 - " . (date('Y')) . ").";

        return [
            'title.unique' => 'A project with this exact title already exists in the system. Please revise your title.',
            'year.min' => $message,
            'year.max' => $message,
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Log the failure to the database for the Admin
        \App\Models\ActivityLog::create([
            'user_id' => $this->user() ? $this->user()->id : null,
            'action' => 'submission_validation_failed',
            'ip' => $this->ip(),
            'meta' => [
                'errors' => $validator->errors()->toArray(),
                'title' => $this->title ?? 'Untitled',
                'files' => [
                    'manuscript' => $this->hasFile('manuscript') ? [
                        'name' => $this->file('manuscript')->getClientOriginalName(),
                        'size' => round($this->file('manuscript')->getSize() / 1024 / 1024, 2) . ' MB',
                        'mime' => $this->file('manuscript')->getMimeType(),
                    ] : 'missing'
                ]
            ]
        ]);

        parent::failedValidation($validator);
    }
}
