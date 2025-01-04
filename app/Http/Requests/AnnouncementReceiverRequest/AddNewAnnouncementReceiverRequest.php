<?php

namespace App\Http\Requests\AnnouncementReceiverRequest;

use App\Enums\UserRoleEnum;
use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AddNewAnnouncementReceiverRequest extends FormRequest
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
            'title'      => 'required|string',
            'description' => 'required|string',
            'file' => 'mimes:xlsx,doc,docx,ppt,pptx,pdf,png,jpeg,jpg|nullable|max:2048',
            'author_id' => 'required|numeric|exists:users,id',

            'receiver_roles' => ['required', 'array', Rule::in([UserRoleEnum::ADMIN, UserRoleEnum::STUDENT, UserRoleEnum::TEACHER])],
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
}
