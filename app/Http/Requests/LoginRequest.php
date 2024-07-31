<?php

namespace App\Http\Requests;

use App\DataTransferObjects\AuthResponseDTO;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\AuthenticationResource;
use App\Http\Resources\ValidationErrorResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Http;

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
            'username' => 'required',
            'password' => 'required'
        ];
    }

    protected $apiResponse;

    public function __construct(ApiResponseHelper $apiResponse)
    {
        parent::__construct();
        $this->apiResponse = $apiResponse;
    }

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
            message: "Authorization failed. User are already logged in.",
            errors: [],
            code: 403
        );

        throw new HttpResponseException($response);
    }
}
