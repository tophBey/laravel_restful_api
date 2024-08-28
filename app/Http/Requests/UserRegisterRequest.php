<?php

namespace App\Http\Requests;

use Carbon\Traits\Mixin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @mixin \App\Models\User
 */
class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;//siapapun bloh registrasi
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "username" => ["required","max:100"],
            "password"=> ["required","max:100"],
            "name"=> ["required","max:100"],
        ];
    }

    // custom error sesuai api spek
    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ],400));
    }
}
