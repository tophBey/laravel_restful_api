<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Auth\User;
// use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @mixin \App\Models\User
 */

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // siapa yang bisa melakukan update
        // return $this->user != null;
        return auth()->check();

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'max:100'],
            'password' => ['nullable', 'max:100']
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ],400));
    }
}
