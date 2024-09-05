<?php

namespace App\Http\Requests\Catalogos\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCatUbicacionesRequest extends FormRequest
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
            'clave_ubicacion.required' => 'El campo clave ubicacion es obligatorio.',
            'clave_ubicacion.string' => 'El campo clave ubicacion debe ser una cadena de texto.',
            'clave_ubicacion.max' => 'El campo clave ubicacion no debe exceder los 10 caracteres.',

            'descripcion_ubicacion.required' => 'El campo descripcion ubicacion es obligatorio.',
            'descripcion_ubicacion.string' => 'El campo descripcion ubicacion debe ser una cadena de texto.',
            'descripcion_ubicacion.max' => 'El campo descripcion ubicacion no debe exceder los 200 caracteres.',

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
