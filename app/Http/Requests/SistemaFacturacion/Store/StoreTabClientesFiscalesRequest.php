<?php

namespace App\Http\Requests\SistemaFacturacion\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTabClientesFiscalesRequest extends FormRequest
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
            'nombre_razon' => 'required|string|max:250',
            'nombre_comercial' => 'nullable|string|max:250',
            'es_persona_moral' => 'nullable|boolean',
            'rfc' => 'nullable|string|max:20|unique:clientes_fiscales,rfc',
            'curp' => 'nullable|string|max:20|unique:clientes_fiscales,curp',
            'id_regimen' => 'required|exists:cat_regimenes_fiscales,id_regimen',
            'fecha_inicio_op' => 'nullable|date',
            'id_estatus_sat' => 'required|exists:cat_estatuses_sat,id_estatus_sat',
            'datos_extra' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id_cliente.required' => 'El cliente es obligatorio',
            'id_cliente.exists' => 'El cliente no existe',
            'nombre_razon.required' => 'El nombre o razón social es obligatorio',
            'rfc.unique' => 'El RFC ya existe',
            'curp.unique' => 'La CURP ya existe',
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
