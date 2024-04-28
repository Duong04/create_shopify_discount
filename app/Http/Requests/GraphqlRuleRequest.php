<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GraphqlRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'discount_status' => 'required',
            'discount_value' => 'required',
            'discount_type' => 'required',
            'name' => ['required', 'unique:graphql_rules,name,' . $this->route('id')]
        ];
    }
}
