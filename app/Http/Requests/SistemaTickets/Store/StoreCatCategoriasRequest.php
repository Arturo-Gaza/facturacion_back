<?php

namespace App\Http\Requests\SistemaTickets\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCatCategoriasRequest extends FormRequest
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
            'descripcion_categoria' => 'required|string|unique:cat_categorias,descripcion_categoria',
            'habilitado' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion_categoria.required' => 'El campo descripción departamento es obligatorio.',
            'descripcion_categoria.string' => 'El campo descripción departamento debe ser una cadena de texto.',
             'descripcion_categoria.unique' => 'La categoria ya existe',

            'habilitado' => 'El campo habilitado es obligatorio',
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
