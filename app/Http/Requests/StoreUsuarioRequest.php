<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class StoreUsuarioRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'apellidoP' => ['required', 'string', 'max:255'],
            'apellidoM' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'user' => ['required', 'string', 'min:6', 'max:20', Rule::unique('users', 'user')
                ->where(function ($query) {
                    $query->where('habilitado', 1); // Condición adicional: solo usuarios con status 'active'
                })],
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
            'name.max' => 'El campo nombre no debe exceder los 255 caracteres.',
            'apellidoP.required' => 'El campo apellido paterno es obligatorio.',
            'apellidoP.string' => 'El campo apellido paterno debe ser una cadena de texto.',
            'apellidoP.max' => 'El campo apellido paterno no debe exceder los 255 caracteres.',
            'apellidoM.required' => 'El campo apellido materno es obligatorio.',
            'apellidoM.string' => 'El campo apellido materno debe ser una cadena de texto.',
            'apellidoM.max' => 'El campo apellido materno no debe exceder los 255 caracteres.',
            'email.string' => 'El campo correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El campo correo electrónico debe ser una dirección válida.',
            'email.max' => 'El campo correo electrónico no debe exceder los 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'user.required' => 'El campo usuario es obligatorio.',
            'user.string' => 'El campo usuario debe ser una cadena de texto.',
            'user.min' => 'El campo usuario debe tener al menos 6 caracteres.',
            'user.unique' => 'El nombre de usuario ya está registrado.',
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
