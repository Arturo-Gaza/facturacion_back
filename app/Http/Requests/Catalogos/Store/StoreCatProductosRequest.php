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
            'id_cat_almacenes' => 'required|exists:cat_almacenes,id',
            'clave_producto' => 'required|string|max:9',
            'descripcion_producto_material' => 'required|string|max:50',
            'id_unidad_medida' => 'required|exists:cat_unidad_medidas,id',
            'id_gpo_familia' => 'required|exists:cat_gpo_familias,id',
            'habilitado' => 'required|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'id_cat_almacenes.required' => 'El campo id_cat_almacenes es obligatorio.',
            'id_cat_almacenes.exists' => 'El id_cat_almacenes seleccionado no existe.',
    
            'clave_producto.required' => 'El campo clave producto es obligatorio.',
            'clave_producto.string' => 'El campo clave producto debe ser una cadena de texto.',
            'clave_producto.max' => 'El campo clave producto no debe exceder los 9 caracteres.',
    
            'descripcion_producto_material.required' => 'El campo descripcion producto material es obligatorio.',
            'descripcion_producto_material.string' => 'El campo descripcion producto material debe ser una cadena de texto.',
            'descripcion_producto_material.max' => 'El campo descripcion producto material no debe exceder los 50 caracteres.',
    
            'id_unidad_medida.required' => 'El campo id_unidad_medida es obligatorio.',
            'id_unidad_medida.exists' => 'El id_unidad_medida seleccionado no existe.',
    
            'id_gpo_familia.required' => 'El campo id_gpo_familia es obligatorio.',
            'id_gpo_familia.exists' => 'El id_gpo_familia seleccionado no existe.',

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


