<?php

namespace App\Http\Requests\SistemaTickets\Update;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCatMonedaRequest extends FormRequest
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
            'clave_moneda' => 'required|string',
            'descripcion_moneda' => 'required|string',
            'habilitado' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'clave_moneda.required' => 'El campo clave moneda es obligatorio.',
            'clave_moneda.string' => 'El campo clave moneda debe ser una cadena de texto.',
            
            'descripcion_moneda.required' => 'El campo descripción moneda es obligatorio.',
            'descripcion_moneda.string' => 'El campo descripción moneda debe ser una cadena de texto.',
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
