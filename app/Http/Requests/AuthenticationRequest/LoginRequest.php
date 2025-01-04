<?php

namespace App\Http\Requests\AuthenticationRequest;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // just the guest user can login, user that logged in can't request this end point
        return !auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'id'      => 'required',
            // CHANGE: id to username
            'username'      => 'required',
            'password'     => 'required',
            'device_name' => 'nullable'
        ];
    }

    // buat bantuan bikin response biar seragam sesuai template,
    // bisa dibuka file ApiResponseHelper, ada yang untuk success dan error
    // udah dicoba buat kayak error laravel kalo gagal validasi, keluar array errors
    protected $apiResponse;
    public function __construct(ApiResponseHelper $apiResponse)
    {
        parent::__construct();
        $this->apiResponse = $apiResponse;
    }

    // cuma check validasi yang ada di rules aja, belum ke validasi credential ke database
    protected function failedValidation(Validator $validator)
    {
        $response = $this->apiResponse->errorResponse(
            message: "Validation error.",
            errors: $validator->errors()->toArray(),
            codeResponse: 422
        );

        throw new HttpResponseException($response);
    }

    protected function failedAuthorization()
    {
        $response = $this->apiResponse->errorResponse(
            message: "Authorization failed.",
            errors: [
                "User are already logged in."
            ],
            codeResponse: 401
        );

        throw new HttpResponseException($response);
    }
}
