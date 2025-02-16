<?php

namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AutogroupRequest extends FormRequest
{
    use WebPageAuthorization;

    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.autogroups.php';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $autogroupId = $this->route('autogroup')?->id;
        $action = $autogroupId ? 'edit' : 'create';

        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $action);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $autogroupId = $this->route('autogroup')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.core.tbl_autogroups', 'name')
                    ->ignore($autogroupId, 'id')
                    ->whereNull('deleted_at')
            ],
            'description' => [
                'nullable',
                'string',
                'max:255'
            ],
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
            'name.required' => 'The autogroup name is required.',
            'name.unique' => 'This autogroup name is already in use.',
            'name.max' => 'The autogroup name cannot exceed 255 characters.',
            'description.max' => 'The description cannot exceed 255 characters.',
        ];
    }
}
