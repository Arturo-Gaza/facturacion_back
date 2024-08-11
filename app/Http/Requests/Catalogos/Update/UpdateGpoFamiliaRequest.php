<?php

namespace App\Http\Requests\Catalogos\Update;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateGpoFamiliaRequest extends FormRequest
{
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
            'clave_gpo_familia' => 'required|string|max:10',
            'descripcion_gpo_familia' => 'required|string|max:200'
        ];
    }

    public function messages(): array
    {
        return [
            'clave_gpo_familia.required' => 'El campo clave grupo familia es obligatorio.',
            'clave_gpo_familia.string' => 'El campo clave grupo familia debe ser una cadena de texto.',
            'clave_gpo_familia.max' => 'El campo clave grupo familia no debe exceder los 10 caracteres.',

            'descripcion_gpo_familia.required' => 'El campo descripcion grupo familia es obligatorio.',
            'descripcion_gpo_familia.string' => 'El campo descripcion grupo familia debe ser una cadena de texto.',
            'descripcion_gpo_familia.max' => 'El campo descripcion grupo familia no debe exceder los 200 caracteres.',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'errors' => $errors
        ], 422));
    }
}
