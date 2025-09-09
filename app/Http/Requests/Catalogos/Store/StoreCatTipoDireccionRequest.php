<?php

namespace App\Http\Requests\Catalogos\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use phpDocumentor\Reflection\Types\True_;

class StoreCatTipoDireccionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'clave' => 'required|string|max:50|unique:cat_tipos_direccion,clave',
            'descripcion' => 'required|string|max:250',
            'habilitado' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'clave.required' => 'La Clave es obligatoria',
            'clave.string' => 'La Clave debe ser una cadena de texto',
            'clave.max' => 'La Clave no debe exceder de 50 caracteres',
            'clave.unique' => 'La Clave ya existe',
            'descripcion.required' => 'El Campo descripción es obligatorio',
            'descripcion.max' => 'La descripción no debe exceder de 250 caracteres',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'errors' => $validator->errors()
        ], 422));
    }
}
