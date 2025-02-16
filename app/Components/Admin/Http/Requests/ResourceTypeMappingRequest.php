<?php

namespace App\Components\Admin\Http\Requests;

use App\Traits\WebPageAuthorization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ResourceTypeMappingRequest extends FormRequest
{
    use WebPageAuthorization;

    protected string $resourceType = 'web_pages';
    protected string $resourceValue = 'admin.resource-type-mappings.php';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $isEdit = $this->route('resource_type_mapping') !== null;
        $action = $isEdit ? 'edit' : 'create';

        return $this->checkResourcePermission($this->resourceType, $this->resourceValue, $action);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'resource_type_id' => [
                'required',
                'exists:pgsql.auth.tbl_resource_types,id',
                Rule::unique('pgsql.auth.tbl_resource_type_mappings', 'resource_type_id')
                    ->ignore($this->route('resource_type_mapping'), 'resource_type_id')
            ],
            'table_schema' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = DB::select("
                        SELECT EXISTS (
                            SELECT 1
                            FROM information_schema.schemata
                            WHERE schema_name = ?
                        ) as exists
                    ", [$value])[0]->exists;

                    if (!$exists) {
                        $fail("The selected schema does not exist.");
                    }
                }
            ],
            'table_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!$this->table_schema) {
                        return;
                    }

                    $exists = DB::select("
                        SELECT EXISTS (
                            SELECT 1
                            FROM information_schema.tables
                            WHERE table_schema = ?
                            AND table_name = ?
                        ) as exists
                    ", [$this->table_schema, $value])[0]->exists;

                    if (!$exists) {
                        $fail("The selected table does not exist in the specified schema.");
                    }
                }
            ],
            'resource_value_column' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!$this->table_schema || !$this->table_name) {
                        return;
                    }

                    $exists = DB::select("
                        SELECT EXISTS (
                            SELECT 1
                            FROM information_schema.columns
                            WHERE table_schema = ?
                            AND table_name = ?
                            AND column_name = ?
                        ) as exists
                    ", [$this->table_schema, $this->table_name, $value])[0]->exists;

                    if (!$exists) {
                        $fail("The selected column does not exist in the specified table.");
                    }
                }
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
            'resource_type_id.required' => 'A resource type must be selected.',
            'resource_type_id.exists' => 'The selected resource type is invalid.',
            'resource_type_id.unique' => 'This resource type already has a mapping.',
            'table_schema.required' => 'A database schema must be selected.',
            'table_schema.max' => 'The schema name cannot exceed 255 characters.',
            'table_name.required' => 'A database table must be selected.',
            'table_name.max' => 'The table name cannot exceed 255 characters.',
            'resource_value_column.required' => 'A value column must be selected.',
            'resource_value_column.max' => 'The column name cannot exceed 255 characters.'
        ];
    }
}
