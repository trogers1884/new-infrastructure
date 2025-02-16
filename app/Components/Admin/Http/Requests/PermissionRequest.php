<?php

namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
{
    use WebPageAuthorization;

    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.permissions.php';

    public function authorize(): bool
    {
//        dd($this->route('permission'));

        $permissionId = $this->route('permission')?->id;
        $action = $permissionId ? 'edit' : 'create';

        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $action);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.auth.tbl_permissions', 'name')
                    ->ignore($this->route('permission')),
            ],
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The permission name is required',
            'name.unique' => 'This permission name already exists',
            'name.max' => 'The permission name cannot exceed 255 characters',
            'description.max' => 'The description cannot exceed 1000 characters',
        ];
    }
}
