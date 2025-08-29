<?php

namespace App\Http\Requests\SistemaTickets\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTabCotizacionesDetalleRequest extends FormRequest
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
            'id_solicitud_detalle' => 'required',
            'id_usuario' => 'required',
            'nombre_cotizacion' => 'required|string',
            'archivo_cotizacion' => 'required|string',


        ];
    }

    public function messages(): array
    {
        return [
            'id_solicitud_detalle.required' => 'El campo Solicitud detalle es obligatorio.',

            'id_usuario.required' => 'El campo Usuario es obligatorio.',

            'nombre_cotizacion.required' => 'El campo nombre de cotización es obligatorio.',
            'nombre_cotizacion.string'  => 'El campo nombre de cotización debe ser una cadena de texto.',

            'archivo_cotizacion.required' => 'El campo nombre de cotización es obligatorio.',
            'archivo_cotizacion.string'  => 'El campo nombre de cotización debe ser una cadena de texto.',        ];
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
