<?php

namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuTypeRequest extends FormRequest
{
    use WebPageAuthorization;

    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.menu-types.php';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $menuTypeId = $this->route('menu_type')?->id;
        $action = $menuTypeId ? 'edit' : 'create';
        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $action);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.config.tbl_menu_types', 'name')
                    ->ignore($this->route('menu_type')),
                'regex:/^[\w\s-]+$/'
            ],
            'description' => ['nullable', 'string', 'max:255']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A menu type name is required',
            'name.max' => 'The menu type name cannot be longer than 100 characters',
            'name.regex' => 'The name may only contain letters, numbers, spaces, hyphens, and underscores',
            'name.unique' => 'This menu type name is already in use',
            'description.max' => 'The description cannot be longer than 255 characters'
        ];
    }
}
