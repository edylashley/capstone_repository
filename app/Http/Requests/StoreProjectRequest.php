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

        // Use the configured academic year or fallback to current year
        $academicYearEnd = (int) \App\Models\Setting::get('academic_year', date('Y'));

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects')->where(function ($query) {
                    return $query->where('status', '!=', 'rejected');
                })
            ],
            'slug' => ['nullable','string','max:255','unique:projects,slug'],
            'abstract' => ['required','string'],
            'year' => ['required','integer','min:'.$academicYearEnd,'max:'.$academicYearEnd],
            'adviser_id' => ['required','exists:users,id'],
            'authors' => ['required','array','min:1'],
            'authors.*' => ['required','string','max:255'],

            // Metadata
            'program' => ['required', 'string', Rule::in(['BSInT', 'Com-Sci'])],
            'specialization' => ['required', 'string', Rule::in(\App\Models\Category::pluck('name')->toArray())],

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
        return [
            'title.unique' => 'A project with this exact title already exists in the system. Please revise your title.',
            'year.min' => 'Error, this is not the current year.',
            'year.max' => 'Error, this is not the current year.',
        ];
    }
}
