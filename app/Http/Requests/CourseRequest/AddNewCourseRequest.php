<?php

namespace App\Http\Requests\CourseRequest;

use App\Enums\GradeEnum;
use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AddNewCourseRequest extends FormRequest
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
            'name'     => 'required|string',
            'user_id' => 'numeric|nullable',
            'grade' => ['required', Rule::in([GradeEnum::X, GradeEnum::XI, GradeEnum::XII])],
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
