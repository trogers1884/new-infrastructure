<?php

namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceAssociationRequest extends FormRequest
{
    use WebPageAuthorization;

    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.resource-associations.php';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if this is an update request
        $isEdit = $this->route('resource_association') !== null;

        // Determine the required permission based on the request type
        $permission = $isEdit ? 'edit' : 'create';

        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $permission);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id,active,true'
            ],
            'resource_type_id' => [
                'required',
                'exists:pgsql.auth.tbl_resource_types,id'
            ],
            'role_id' => [
                'required',
                'exists:pgsql.auth.tbl_roles,id',
                Rule::unique('pgsql.auth.tbl_resource_associations')
                    ->where(function ($query) {
                        return $query->where('user_id', $this->user_id)
                            ->where('resource_type_id', $this->resource_type_id)
                            ->where('resource_id', $this->resource_id)
                            ->whereNull('deleted_at');
                    })
                    ->ignore($this->route('resource_association'))
            ],
            'resource_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value !== null) {
                        // Verify the resource exists in the mapped table
                        $mapping = \DB::table('auth.tbl_resource_type_mappings')
                            ->where('resource_type_id', $this->resource_type_id)
                            ->first();

                        if ($mapping) {
                            $exists = \DB::table("{$mapping->table_schema}.{$mapping->table_name}")
                                ->where('id', $value)
                                ->whereNull('deleted_at')
                                ->exists();

                            if (!$exists) {
                                $fail('The selected resource does not exist.');
                            }
                        } else {
                            $fail('No mapping found for the selected resource type.');
                        }
                    }
                }
            ],
            'description' => [
                'nullable',
                'string',
                'max:255'
            ]
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
            'user_id.required' => 'A user must be selected.',
            'user_id.exists' => 'The selected user must be active and valid.',
            'resource_type_id.required' => 'A resource type must be selected.',
            'resource_type_id.exists' => 'The selected resource type is invalid.',
            'role_id.required' => 'A role must be selected.',
            'role_id.exists' => 'The selected role is invalid.',
            'role_id.unique' => 'This user already has this role for this resource.',
            'resource_id.integer' => 'The resource ID must be a number.',
            'description.max' => 'The description cannot exceed 255 characters.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If no resource_id is provided, set it to null explicitly
        if ($this->input('resource_id') === '') {
            $this->merge([
                'resource_id' => null
            ]);
        }
    }
}
