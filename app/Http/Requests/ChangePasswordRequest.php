<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(User $user): bool
    {
        return Auth::user()->isSuperAdmin() || Auth::id() == $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isProfile = request()->routeIs('user.profile.*');
        $newPassword = ['required', Password::defaults()];
        $isProfile && array_push($newPassword, 'different:password');
        array_push($newPassword, 'confirmed:new_password_confirmation');

        return [
            'password' => ['required', 'current_password'],
            'new_password' => $newPassword
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $isProfile = request()->routeIs('user.profile.*');
        return [
            'password' => $isProfile ? 'old password' : 'your password',
        ];
    }
}
