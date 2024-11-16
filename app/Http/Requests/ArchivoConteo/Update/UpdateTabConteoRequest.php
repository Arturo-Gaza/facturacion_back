<?php

namespace App\Http\Requests\ArchivoConteo\Update;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTabConteoRequest extends FormRequest
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
        'codigo' => 'required|string|max:20',
        'descripcion' => 'required|string|max:255',
        'ume' => 'required|string|max:10',
        'cantidad' => 'required',
        'ubicacion' => 'required|string|max:100',
        'observaciones' => 'nullable|string|max:500',
        'conteo'=> 'required|integer',
    ];
}

public function messages(): array
{
    return [
        'codigo.required' => 'El campo Código es obligatorio.',
        'codigo.string' => 'El campo Código debe ser una cadena de texto.',
        'codigo.max' => 'El campo Código no debe exceder los 20 caracteres.',
        'codigo.unique' => 'El campo Código debe ser único en la tabla.',

        'descripcion.required' => 'El campo Descripción es obligatorio.',
        'descripcion.string' => 'El campo Descripción debe ser una cadena de texto.',
        'descripcion.max' => 'El campo Descripción no debe exceder los 255 caracteres.',

        'ume.required' => 'El campo UME es obligatorio.',
        'ume.string' => 'El campo UME debe ser una cadena de texto.',
        'ume.max' => 'El campo UME no debe exceder los 10 caracteres.',

        'cantidad.required' => 'El campo Cantidad es obligatorio.',
        'cantidad.min' => 'El campo Cantidad debe ser al menos 1.',

        'ubicacion.required' => 'El campo Ubicación es obligatorio.',
        'ubicacion.string' => 'El campo Ubicación debe ser una cadena de texto.',
        'ubicacion.max' => 'El campo Ubicación no debe exceder los 100 caracteres.',

        'observaciones.string' => 'El campo Observaciones debe ser una cadena de texto.',
        'observaciones.max' => 'El campo Observaciones no debe exceder los 500 caracteres.',

        'conteo.required' => 'El campo Conteo debe ser reqierido',
        'conteo.integer' => 'El campo Conteo debe ser un entero',
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
