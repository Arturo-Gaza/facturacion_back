<?php

namespace App\Http\Requests\Catalogos\Update;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateCatEstatusSat extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
        'clave' => 'required|string|max:50|unique:cat_estatuses_sat,clave,' . $this->route('id') . ',id_estatus_sat',
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
            'descripcion.required' => 'El Campo descripcion es obligatorio',
            'descripcion.max' => 'La descripcion no debe exceder de 250 caracteres',
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
