<?php

namespace App\Http\Requests\SistemaFacturacion\Store;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTabClientesRequest extends FormRequest
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
            'usuario' => 'required|string|max:100|unique:clientes,usuario',
            'password' => 'required|string|min:6|max:100',
            'email' => 'required|email|unique:clientes,email',
            'habilitado' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'usuario.required' => 'El usuario es obligatorio',
            'usuario.string' => 'El usuario debe ser una cadena de texto',
            'usuario.max' => 'El usuario no debe exceder de 100 caracteres',
            'usuario.unique' => 'El usuario ya existe',

            'password.required' => 'La contraseña es obligatoria',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.max' => 'La contraseña no debe exceder de 100 caracteres',

            'email.required' => 'El email es obligatorio',
            'email.email' => 'Debe ser un email válido',
            'email.unique' => 'El email ya está registrado',

            'habilitado.boolean' => 'El campo habilitado debe ser verdadero o falso',
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
