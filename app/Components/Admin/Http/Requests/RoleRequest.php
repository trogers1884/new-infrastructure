<?php

namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    use WebPageAuthorization;

    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.roles.php';

    public function authorize(): bool
    {
        // Determine if this is a create or update request
        $roleId = $this->route('role')?->id;
        $action = $roleId ? 'edit' : 'create';

        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $action);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.auth.tbl_roles', 'name')
                    ->ignore($this->route('role'))
            ],
            'description' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The role name is required.',
            'name.unique' => 'This role name is already taken.',
            'name.max' => 'The role name cannot exceed 255 characters.'
        ];
    }
}
