<?php

namespace App\Http\Requests\SistemaFacturacion\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTabContactoRequest extends FormRequest
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
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'id_tipo_contacto' => 'required|exists:cat_tipos_contacto,id_tipo_contacto',
            'lada' => 'required|string|max:5',
            'valor' => 'required|string|max:250',
            'principal' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'id_cliente.required' => 'El cliente es obligatorio',
            'id_cliente.exists' => 'El cliente no existe',
            'id_tipo_contacto.required' => 'El tipo de contacto es obligatorio',
            'id_tipo_contacto.exists' => 'El tipo de contacto no existe',
            'lada.required' => 'La lada es obligatoria',
            'valor.required' => 'El valor es obligatorio',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'errors' => $validator->errors()
        ], 422));
    }
}
