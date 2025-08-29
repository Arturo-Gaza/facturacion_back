<?php

namespace App\Http\Requests\SistemaTickets\Update;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSolicitudDetalleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_producto'   => 'required|integer|exists:cat_productos,id',
            'id_solicitud'  => 'required|integer|exists:tab_solicitudes,id',
            'descripcion'   => 'required|string ',
            'marca'         => 'nullable|string ',
            'modelo'        => 'nullable|string ',
            'cantidad'      => 'required|numeric|min:1',
            'observacion'   => 'nullable|string ',
        ];
    }

    public function messages(): array
    {
        return [
            'id_producto.required'  => 'El campo producto es obligatorio.',
            'id_producto.integer'   => 'El campo producto debe ser un número entero.',
            'id_producto.exists'    => 'El producto seleccionado no existe.',

            'id_solicitud.required' => 'El campo solicitud es obligatorio.',
            'id_solicitud.integer'  => 'El campo solicitud debe ser un número entero.',
            'id_solicitud.exists'   => 'La solicitud seleccionada no existe.',

            'descripcion.required'  => 'La descripción es obligatoria.',
            'descripcion.string'    => 'La descripción debe ser una cadena de texto.',

            'marca.string'          => 'La marca debe ser una cadena de texto.',

            'modelo.string'         => 'El modelo debe ser una cadena de texto.',

            'cantidad.required'     => 'La cantidad es obligatoria.',
            'cantidad.numeric'      => 'La cantidad debe ser un número.',
            'cantidad.min'          => 'La cantidad debe ser al menos 1.',

            'observacion.string'    => 'La observación debe ser una cadena de texto.',
         ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'errors'  => $errors,
        ], 422));
    }
}
