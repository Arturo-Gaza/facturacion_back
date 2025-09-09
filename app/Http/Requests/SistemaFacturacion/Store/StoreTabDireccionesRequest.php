<?php

namespace App\Http\Requests\SistemaFacturacion\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTabDireccionesRequest extends FormRequest
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
            'id_tipo_direccion' => 'required|exists:cat_tipos_direccion,id_tipo_direccion',
            'calle' => 'required|string|max:250',
            'num_exterior' => 'required|string|max:50',
            'num_interior' => 'nullable|string|max:50',
            'colonia' => 'required|string|max:150',
            'localidad' => 'nullable|string|max:150',
            'municipio' => 'nullable|string|max:150',
            'estado' => 'required|string|max:150',
            'codigo_postal' => 'nullable|string|max:10',
            'pais' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'id_cliente.required' => 'El cliente es obligatorio',
            'id_cliente.exists' => 'El cliente no existe',
            'id_tipo_direccion.required' => 'El tipo de dirección es obligatorio',
            'id_tipo_direccion.exists' => 'El tipo de dirección no existe',
            'calle.required' => 'La calle es obligatoria',
            'num_exterior.required' => 'El número exterior es obligatorio',
            'colonia.required' => 'La colonia es obligatoria',
            'estado.required' => 'El estado es obligatorio',
            'pais.required' => 'El país es obligatorio',
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
