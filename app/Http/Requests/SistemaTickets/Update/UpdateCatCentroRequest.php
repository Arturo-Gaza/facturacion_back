<?php

namespace App\Http\Requests\SistemaTickets\Update;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCatCentroRequest extends FormRequest
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
            'clave_centro' => 'required|string|max:5',
            'descripcion_centro' => 'required|string|max:50',
            'habilitado' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'clave_centro.required' => 'El campo clave centro es obligatorio.',
            'clave_centro.string' => 'El campo clave centro debe ser una cadena de texto.',
            'clave_centro.max' => 'El campo clave centro no debe exceder los 5 caracteres.',
            
            'descripcion_centro.required' => 'El campo descripción centro es obligatorio.',
            'descripcion_centro.string' => 'El campo descripción centro debe ser una cadena de texto.',
            'descripcion_centro.max' => 'El campo descripción centro no debe exceder los 50 caracteres.',

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
