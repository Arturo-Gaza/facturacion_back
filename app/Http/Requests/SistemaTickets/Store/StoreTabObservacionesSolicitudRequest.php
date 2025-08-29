<?php

namespace App\Http\Requests\SistemaTickets\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTabObservacionesSolicitudRequest extends FormRequest
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
            'id_solicitud' => 'required',
            'id_usuario' => 'required',
            'observacion' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id_solicitud.required' => 'El campo solicitud es obligatorio.',

            'id_usuario' => 'El campo Usuario es obligatorio',

            'observacion' => 'El campo observación es obligatorio',
            'observacion.string' => 'El campo observación departamento debe ser una cadena de texto.',

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
