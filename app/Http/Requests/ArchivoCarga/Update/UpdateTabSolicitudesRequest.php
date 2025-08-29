<?php

namespace App\Http\Requests\ArchivoCarga\Update;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTabSolicitudesRequest extends FormRequest
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
            'justificacion' => 'string ',
            'prioridad' => 'integer',
            'justificacion_prioridad' => 'nullable |string ',
        ];

        if ($this->input('prioridad') === '1') {
            $rules['justificacion_prioridad'][] = 'required';
        }
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'La descripción solicitud es obligatoria.',
            'descripcion.string' => 'La descripción solicitud debe ser una cadena de texto.',

            'justificacion.string' => 'La justificación solicitud debe ser una cadena de texto.',

            'prioridad.required' => 'La prioridad solicitud es obligatoria.',
            'prioridad.integer' => 'El campo prioridad solicitud debe ser un número entero.',
            'justificacion_prioridad.string' => 'La justificación de la prioridad debe ser una cadena de texto..',
            'justificacion_prioridad.required' => 'Debes justificar la prioridad si es alta.',
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
