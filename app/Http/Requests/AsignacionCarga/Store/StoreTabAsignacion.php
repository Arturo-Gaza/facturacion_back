<?php

namespace App\Http\Requests\AsignacionCarga\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTabAsignacion extends FormRequest
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
            'id_carga' => 'required|exists:tab_detalle_cargas,id',
            'id_usuario' => 'required|exists:users,id',
            'id_estatus' => 'nullable|exists:cat_estatuses,id',
            'conteo' => 'required|integer|max:3',
            'fecha_asignacion' => 'nullable|date',
            'fecha_inicio_conteo'  => 'nullable|date',
            'fecha_fin_conteo'  => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'id_carga.required' => 'El campo id carga es obligatorio.',
            'id_carga.exists' => 'El valor para id carga no existe, ingrese otra id',

            'id_usuario.required' => 'El campo id usuario es obligatorio.',
            'id_usuario.exists' => 'El valor para id usuario no existe, ingrese otra id',

            'id_estatus.required' => 'El campo id estatus es obligatorio.',
            'id_estatus.exists' => 'El valor para id estatus no existe, ingrese otra id',

            'conteo.required' => 'El campo conteo es obligatorio.',
            'conteo.integer' => 'El conteo debe ser un número entero.',
            'conteo.max' => 'El campo conteo no debe ser mayor que 3',

            'fecha_asignacion.date' => 'La fecha de asignación del conteo debe ser una fecha válida.',

            'fecha_inicio_conteo.date' => 'La fecha de inicio del conteo debe ser una fecha válida.',

            'fecha_fin_conteo.date' => 'La fecha de fin del conteo debe ser una fecha válida.',


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
