<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
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
            'username' => ['sometimes', 'string', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'company.name' => ['nullable', 'string', 'max:255'],
            'company.logo' => ['nullable', 'image', 'max:2048'],
            'company.address' => ['nullable', 'string', 'max:255'],
            'company.city' => ['nullable', 'string', 'max:255'],
            'company.postal_code' => ['nullable', 'string', 'max:255'],
            'company.nip' => ['nullable', 'string', 'max:255'],
            'company.regon' => ['nullable', 'string', 'max:255'],
            'company.phone' => ['nullable', 'string', 'max:255'],
            'company.email' => ['nullable', 'email', 'max:255'],
            'company.website' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Nazwa użytkownika jest wymagana',
            'username.unique' => 'Ta nazwa użytkownika jest już zajęta',
            'first_name.required' => 'Imię jest wymagane',
            'last_name.required' => 'Nazwisko jest wymagane',
            'email.required' => 'Adres email jest wymagany',
            'email.email' => 'Podaj prawidłowy adres email',
            'email.unique' => 'Ten adres email jest już zajęty',
            'avatar.image' => 'Plik musi być obrazem',
            'avatar.max' => 'Maksymalny rozmiar avatara to 1MB',
            'company.logo.image' => 'Plik musi być obrazem',
            'company.logo.max' => 'Maksymalny rozmiar logo to 2MB',
            'company.website.url' => 'Podaj prawidłowy adres strony internetowej',
        ];
    }

    protected function prepareForValidation()
    {
        // Usuń puste pola z walidacji
        $this->replace(
            collect($this->all())
                ->filter(function ($value) {
                    return $value !== null && $value !== '';
                })
                ->toArray()
        );
    }
}
