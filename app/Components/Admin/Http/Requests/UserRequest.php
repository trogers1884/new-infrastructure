<?php

namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    use WebPageAuthorization;

    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.users.php';

    public function authorize(): bool
    {
        $modelId = $this->route('user')?->id;
        $action = $modelId ? 'edit' : 'create';

        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $action);
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
            ],
            'active' => ['boolean'],
        ];

        // Add password rules for create
        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        // Modify password rules for update
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
            $rules['email'][4] = Rule::unique('users', 'email')->ignore($this->route('user')->id);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'A name is required',
            'name.max' => 'The name cannot be longer than 255 characters',
            'email.required' => 'An email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email address is already in use',
            'password.required' => 'A password is required',
            'password.min' => 'The password must be at least 8 characters',
            'password.confirmed' => 'The password confirmation does not match',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active')
        ]);
    }

    /**
     * Get the error messages that apply to the request parameters.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => 'user name',
            'email' => 'email address',
            'password' => 'password',
            'active' => 'active status',
        ];
    }
}
