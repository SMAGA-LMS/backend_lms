<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentEnrollmentResource extends JsonResource
{
    //  //define properti
    //  public $status;
    //  public $message;

    //  /**
    //   * __construct
    //   *
    //   * @param  mixed $status
    //   * @param  mixed $message
    //   * @param  mixed $resource
    //   * @return void
    //   */
    //  public function __construct($status, $message, $resource)
    //  {
    //      parent::__construct($resource);
    //      $this->status  = $status;
    //      $this->message = $message;
    //  }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'student_enrollment_id' => $this->student_enrollment_id,
            'user' => [
                'id' => $this->user_id,
                'user_code' => $this->user_code,
                'full_name' => $this->user_full_name,
                'avatar' => $this->user_avatar
            ]
        ];
    }
}
