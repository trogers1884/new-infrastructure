<?php

namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRoleRequest extends FormRequest
{
    use WebPageAuthorization;

    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.user-roles.php';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $isEdit = $this->route('userRole') !== null;
        $action = $isEdit ? 'edit' : 'create';

        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $action);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('pgsql.auth.tbl_user_roles', 'user_id')
                    ->where('role_id', $this->input('role_id'))
                    ->ignore($this->route('userRole'))
            ],
            'role_id' => [
                'required',
                'integer',
                'exists:pgsql.auth.tbl_roles,id'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'A user must be selected.',
            'user_id.exists' => 'The selected user is invalid.',
            'user_id.unique' => 'This user already has been assigned this role.',
            'role_id.required' => 'A role must be selected.',
            'role_id.exists' => 'The selected role is invalid.',
            'description.max' => 'The description cannot exceed 1000 characters.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'user',
            'role_id' => 'role',
            'description' => 'description'
        ];
    }
}
