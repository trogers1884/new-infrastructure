<?php

namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class MigrationRequest extends FormRequest
{
    use WebPageAuthorization;

    /**
     * The resource type for authorization.
     *
     * @var string
     */
    protected string $resourceType = 'web_pages';

    /**
     * The resource value for authorization.
     *
     * @var string
     */
    protected string $resourceValue = 'admin.migrations.php';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // We'll only allow view permission since migrations shouldn't be modified through the UI
        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, 'view');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sort' => ['sometimes', 'string', 'in:id,migration,batch'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
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
            'sort.in' => 'The sort field must be either id, migration, or batch.',
            'direction.in' => 'The direction must be either ascending or descending.',
            'search.max' => 'The search term cannot exceed 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'sort' => 'sort field',
            'direction' => 'sort direction',
            'search' => 'search term',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Ensure sort and direction have default values if not provided
        $this->merge([
            'sort' => $this->input('sort', 'id'),
            'direction' => $this->input('direction', 'desc'),
        ]);
    }
}
