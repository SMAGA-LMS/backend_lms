<?php

namespace App\Http\Requests\AttendanceRequest;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddNewAttendanceRequest extends FormRequest
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
            'students'     => 'required|array',
            'students.*.studentID' => 'required|exists:users,id',
            'students.*.classenrollmentID' => 'required|exists:class_enrollments,id',
            'students.*.dateTime' => 'required|date',
            'students.*.session' => 'required|numeric',
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
