<?php

namespace App\Http\Requests\AttendanceRequest;

use App\Enums\AttendanceStatus;
use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'class_enrollment_id' => 'required|numeric|exists:class_enrollments,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'date_time' => 'required|date',

            'students'     => 'required|array',
            'students.*.student_id' => 'required|numeric|exists:users,id',
            'students.*.status' => ['required', Rule::in([AttendanceStatus::PRESENT, AttendanceStatus::ABSENT, AttendanceStatus::SICK, AttendanceStatus::PERMIT, AttendanceStatus::OTHER])],

            // 'students.*.class_enrollment_id' => 'required|exists:class_enrollments,id', // cukup kirim class_enrollment_id sekali aja, ga perlu di setiap student (karena pasti sama untuk batch store student ini)
            // 'students.*.date_time' => 'required|date',
            // 'students.*.session' => 'required|numeric',  // ini ga jadi dipakai
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
