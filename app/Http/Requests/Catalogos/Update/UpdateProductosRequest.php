<?php

namespace App\Http\Requests\Catalogos\Update;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductosRequest extends FormRequest
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
            'clave_producto' => 'required|string|max:9',
            'descripcion_producto_material' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'clave_producto.required' => 'El campo clave producto es obligatorio.',
            'clave_producto.string' => 'El campo clave producto es numerica.',
            'clave_producto.max' => 'El campo clave producto no debe exceder los 9 caracteres.',

            'descripcion_producto_material.required' => 'El campo descripcion producto material es obligatorio.',
            'descripcion_producto_material.string' => 'El campo descripcion producto material debe ser una cadena de texto.',
            'descripcion_producto_material.max' => 'El campo descripcion producto material no debe exceder los 50 caracteres.',

        ];
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json(
            [
                'success' => false,
                'message' => 'Error de validacion',
                'data' => $validator->errors()
            ]
        ));
    }
}
