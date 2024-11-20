<?php

namespace App\Http\Requests\ModuleRequest;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddNewModuleRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required',
            'file' => 'mimes:xlsx,doc,docx,ppt,pptx,pdf|nullable|max:2048',
            'courseID' => 'required|exists:courses,id'
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
