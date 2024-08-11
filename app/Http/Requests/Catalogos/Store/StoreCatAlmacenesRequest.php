<?php

namespace App\Http\Requests\Catalogos\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCatAlmacenesRequest extends FormRequest
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
            'clave_almacen' => 'required|string|max:10',
            'descripcion_almacen' => 'required|string|max:200'
        ];
    }

    public function messages(): array
    {
        return [
            'clave_almacen.required' => 'El campo Clave Almacen es obligatorio.',
            'clave_almacen.string' => 'El campo Clave Almacen debe ser una cadena de texto.',
            'clave_almacen.max' => 'El campo Clave Almacen no debe exceder los 10 caracteres.',

            'descripcion_almacen.required' => 'El campo Descripcion Almacen es obligatorio.',
            'descripcion_almacen.string' => 'El campo Descripcion Almacen debe ser una cadena de texto.',
            'descripcion_almacen.max' => 'El campo Descripcion Almacen no debe exceder los 200 caracteres.',

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
