<?php

namespace App\Http\Requests\Catalogos\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCatProductosRequest extends FormRequest
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
            'clave_producto' => 'required|string|max:255',
            'descripcion_producto_material' => 'required|string|max:500',
            'id_cat_almacenes' => 'required|exists:cat_almacenes,id',
            'id_unidad_medida' => 'required|exists:cat_unidad_medidas,id',
            'id_gpo_familia' => 'required|exists:cat_gpo_familias,id',
            
        ];
    }

    public function messages(): array
    {
        return [
            'clave_producto.required' => 'El campo clave producto es obligatorio.',
            'clave_producto.string' => 'El campo clave producto debe ser una cadena de texto.',
            'clave_producto.max' => 'El campo clave producto no puede tener más de 255 caracteres.',
            'descripcion_producto_material.required' => 'El campo descripción producto material es obligatorio.',
            'descripcion_producto_material.string' => 'El campo descripción producto material debe ser una cadena de texto.',
            'descripcion_producto_material.max' => 'El campo descripción producto material no puede tener más de 500 caracteres.',
            'id_cat_almacenes.required' => 'El campo id cat almacenes es obligatorio.',
            'id_cat_almacenes.exists' => 'El valor para id cat almacenes no existe en la tabla cat_almacenes.',
            'id_unidad_medida.required' => 'El campo id unidad medida es obligatorio.',
            'id_unidad_medida.exists' => 'El valor para id unidad medida no existe en la tabla cat_unidad_medidas.',
            'id_gpo_familia.required' => 'El campo id gpo familia es obligatorio.',
            'id_gpo_familia.exists' => 'El valor para id gpo familia no existe en la tabla cat_gpo_familias.',

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
