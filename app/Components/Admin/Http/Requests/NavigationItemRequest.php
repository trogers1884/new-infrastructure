<?php

namespace App\Components\Admin\Http\Requests;

use App\Helpers\IconHelper;
use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NavigationItemRequest extends FormRequest
{
    use WebPageAuthorization;

    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.navigation-items.php';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Determine the action based on whether we're updating an existing item
        $isEdit = $this->route('navigation_item') !== null;
        $action = $isEdit ? 'edit' : 'create';

        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $action);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'menu_type_id' => [
                'required',
                Rule::exists('pgsql.config.tbl_menu_types', 'id')
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'route' => [
                'required',
                'string',
                'max:255'
            ],
            'icon' => [
                'nullable',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && !IconHelper::isValidIcon($value)) {
                        $fail('The selected icon is invalid.');
                    }
                }
            ],
            'order_index' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'parent_id' => [
                'nullable',
                Rule::exists('pgsql.config.tbl_navigation_items', 'id')
                    ->whereNull('deleted_at')
                    ->where(function ($query) {
                        $query->whereNull('parent_id')
                            ->when($this->route('navigation_item'), function ($query) {
                                $query->where('id', '!=', $this->route('navigation_item')->id);
                            });
                    })
            ],
            'is_active' => [
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'menu_type_id.required' => 'The menu type is required.',
            'menu_type_id.exists' => 'The selected menu type is invalid.',
            'name.required' => 'The navigation item name is required.',
            'name.max' => 'The navigation item name cannot exceed 255 characters.',
            'route.required' => 'The route is required.',
            'route.max' => 'The route cannot exceed 255 characters.',
            'icon.max' => 'The icon cannot exceed 100 characters.',
            'order_index.integer' => 'The order index must be a number.',
            'order_index.min' => 'The order index must be 0 or greater.',
            'parent_id.exists' => 'The selected parent item is invalid.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'order_index' => $this->input('order_index') ?? 0,
        ]);
    }
}
