<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUsuarioRequest extends FormRequest
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
        $userId = $this->route('id');
        return [
            'name' => ['required', 'string' ],
            'apellidoP' => ['required', 'string'  ],
            'apellidoM' => ['required', 'string' ],
            'user' => ['required', 'string', 'min:6', 'max:20', Rule::unique('users', 'user')
                ->ignore($userId) // Ignora el email del usuario actual en la validación de unicidad
                ->where(function ($query) {
                    $query->where('habilitado', 1); // Condición adicional: solo usuarios con status 'active'
                })],
            'email' => ['nullable', 'string', 'lowercase', 'email' , 'unique:users,email,' . $userId],


            // 'user' => [
            //     'required',
            //     'string',
            //     'min:6',
            //     'max:10',
            //     'sometimes' => [
            //         'unique:users,user,' . $userId,
            //         'habilitado' => 'true',
            //     ],
            // ], ejemplo 2 de user

        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'apellidoP.required' => 'El campo apellido paterno es obligatorio.',
            'apellidoP.string' => 'El campo apellido paterno debe ser una cadena de texto.',
            'apellidoM.required' => 'El campo apellido materno es obligatorio.',
            'apellidoM.string' => 'El campo apellido materno debe ser una cadena de texto.',
            'email.string' => 'El campo correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El campo correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'user.required' => 'El campo usuario es obligatorio.',
            'user.string' => 'El campo usuario debe ser una cadena de texto.',
            'user.min' => 'El campo usuario debe tener mínimo 6 caracteres.',
            'user.max' => 'El campo usuario no debe de exceder los 20 caracteres.',
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
