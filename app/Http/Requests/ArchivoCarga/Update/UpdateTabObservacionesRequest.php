<?php

namespace App\Http\Requests\ArchivoCarga\Update;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTabObservacionesRequest extends FormRequest
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
            'id_usuario' => 'required|exists:users,id',
            'id_detalle_carga' => 'required|exists:tab_detalle_cargas,id',
            'observaciones' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [

            'id_usuario.required' => 'El usuario es obligatorio.',
            'id_usuario.exists' => 'El usuario seleccionado no es válido.',

            'id_detalle_carga.required' => 'La carga es obligatoria.',
            'id_detalle_carga.exists' => 'la carga seleccionada no es válido.',

            'observaciones.string' => 'Las observaciones deben ser una cadena de texto.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new  HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Errores de validación',
            'errors' => $errors
        ], 422));
    }
}
