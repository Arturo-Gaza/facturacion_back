<?php

namespace App\Http\Requests\Catalogos\Update;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCatUbicacionesRequest extends FormRequest
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
            'clave_ubicacion' => 'required|string|max:10|unique:cat_ubicaciones,clave_ubicacion',
            'descripcion_ubicacion' => 'required|string|max:200'
        ];
    }

    public function messages(): array
    {
        return [
            'clave_ubicaion.required' => 'El campo clave ubicaion es obligatorio.',
            'clave_ubicaion.string' => 'El campo clave ubicaion debe ser una cadena de texto.',
            'clave_ubicaion.max' => 'El campo clave ubicaion no debe exceder los 10 caracteres.',

            'descripcion_ubicaion.required' => 'El campo descripcion ubicaion es obligatorio.',
            'descripcion_ubicaion.string' => 'El campo descripcion ubicaion debe ser una cadena de texto.',
            'descripcion_ubicaion.max' => 'El campo descripcion ubicaion no debe exceder los 200 caracteres.',

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
