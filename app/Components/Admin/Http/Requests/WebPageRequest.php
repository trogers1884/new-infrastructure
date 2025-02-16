<?php
namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebPageRequest extends FormRequest
{
    use WebPageAuthorization;
    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.web-pages.php';

    public function authorize(): bool
    {
//        return true;
        $webPageId = $this->route('web_page')?->id;
        $action = $webPageId ? 'edit' : 'create';

        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $action);
    }

    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pgsql.config.tbl_web_pages', 'url')->ignore($this->route('web_page'))
            ],
            'description' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'The URL is required.',
            'url.max' => 'The URL cannot exceed 255 characters.',
            'url.unique' => 'This URL is already in use.',
        ];
    }
}
