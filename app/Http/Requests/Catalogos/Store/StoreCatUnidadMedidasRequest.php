<?php

namespace App\Http\Requests\Catalogos\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCatUnidadMedidasRequest extends FormRequest
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
            'clave_unidad_medida' => 'required|string|max:5',
            'descripcion_unidad_medida' => 'required|string|max:50'
        ];
    }

    public function messages(): array
    {
        return [
            'clave_unidad_medida.required' => 'El campo Clave unidad de medida es obligatorio.',
            'clave_unidad_medida.string' => 'El campo Clave unidad de medida debe ser una cadena de texto.',
            'clave_unidad_medida.max' => 'El campo Clave unidad de medida no debe exceder los 50 caracteres.',

            'descripcion_unidad_medida.required' => 'El campo Descripcion unidad de medida es obligatorio.',
            'descripcion_unidad_medida.string' => 'El campo Descripcion unidad de medida debe ser una cadena de texto.',
            'descripcion_unidad_medida.max' => 'El campo Descripcion unidad de medida no debe exceder los 50 caracteres.',

        ];
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json(
            [
                'success' => false,
                'message' => 'Ocurrio un error al registrar',
                'data' => $validator->errors()
            ]
        ));
    }
}
