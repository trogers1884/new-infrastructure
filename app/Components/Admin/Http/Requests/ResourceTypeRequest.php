<?php
namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceTypeRequest extends FormRequest
{
    use WebPageAuthorization;

    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.resource-types.php';

    public function authorize(): bool
    {
        $resourceTypeId = $this->route('resource_type')?->id;
        $action = $resourceTypeId ? 'edit' : 'create';

//        $action = $this->route('resource_type') ? 'edit' : 'create';
        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $action);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.auth.tbl_resource_types', 'name')->ignore($this->route('resource_type'))
            ],
            'description' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The resource type name is required.',
            'name.unique' => 'This resource type name is already taken.',
            'name.max' => 'The resource type name cannot exceed 255 characters.'
        ];
    }
}
