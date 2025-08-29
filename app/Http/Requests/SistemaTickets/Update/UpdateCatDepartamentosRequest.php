<?php

namespace App\Http\Requests\SistemaTickets\Update;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCatDepartamentosRequest extends FormRequest
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
            'descripcion' => 'required|string ',
            'habilitado' => 'required',
            'id_usuario_responsable_compras' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'El campo descripción departamento es obligatorio.',
            'descripcion.string' => 'El campo descripción departamento debe ser una cadena de texto.',

            'habilitado' => 'El campo habilitado es obligatorio',
            'id_usuario_responsable_compras.required' => 'El usuario responsable es obligatorio.',

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
