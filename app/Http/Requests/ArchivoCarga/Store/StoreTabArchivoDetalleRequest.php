<?php

namespace App\Http\Requests\ArchivoCarga\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTabArchivoDetalleRequest extends FormRequest
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
            // 'id_carga_cab' => 'required|string|max:10',
            // 'id_almacen' => 'nullable|date',
            // 'id_cat_prod' => 'nullable|date',
            // 'id_unid_med' => 'nullable|date',
            // 'id_gpo_familia' => 'nullable|integer',
            // 'Libre_utilizacion' => 'required|string|max:255',
            // 'En_control_calidad' => 'required|exists:users,id',
            // 'Bloqueado' => 'nullable|integer',
            // 'Valor_en_insp_cal' => 'nullable|integer',
            // 'reg_vobo' => 'nullable|integer',
            // 'reg_excluidos' => 'nullable|integer',
            // 'reg_incorpora' => 'nullable|integer',
            // 'id_estatus' => 'required|exists:users,id',
            // 'observaciones' => 'nullable|string',

        ];
    }

    public function messages(): array
    {
        return [
            'cve_carga.required' => 'La clave de carga es obligatoria.',
            'cve_carga.string' => 'La clave de carga debe ser una cadena de texto.',
            'cve_carga.max' => 'La clave de carga no debe exceder los 10 caracteres.',

            'fecha_asignacion.date' => 'La fecha de asignación debe ser una fecha válida.',
            'fecha_inicio_conteo.date' => 'La fecha de inicio del conteo debe ser una fecha válida.',
            'fecha_fin_conteo.date' => 'La fecha de fin del conteo debe ser una fecha válida.',

            'conteo.integer' => 'El conteo debe ser un número entero.',

            'nombre_archivo.required' => 'El nombre del archivo es obligatorio.',
            'nombre_archivo.string' => 'El nombre del archivo debe ser una cadena de texto.',
            'nombre_archivo.max' => 'El nombre del archivo no debe exceder los 255 caracteres.',

            'id_usuario.required' => 'El usuario es obligatorio.',
            'id_usuario.exists' => 'El usuario seleccionado no es válido.',

            'Reg_Archivo.integer' => 'El registro del archivo debe ser un número entero.',
            'Reg_a_Contar.integer' => 'El registro a contar debe ser un número entero.',
            'reg_vobo.integer' => 'El registro de visto bueno debe ser un número entero.',
            'reg_excluidos.integer' => 'El registro de excluidos debe ser un número entero.',
            'reg_incorpora.integer' => 'El registro de incorporados debe ser un número entero.',

            'id_estatus.required' => 'El estatus es obligatorio.',
            'id_estatus.exists' => 'El estatus seleccionado no es válido.',

            'observaciones.string' => 'Las observaciones deben ser una cadena de texto.',
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
