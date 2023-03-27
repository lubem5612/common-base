<?php


namespace Raadaapartners\Raadaabase\Kuda\Helpers;


use Illuminate\Support\Facades\Validator;

trait ValidationHelper
{
    public $validationFails = false;
    public $validationErrors = [];

    public function validateRequest(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);
        $this->validationFails = $validator->fails();
        $this->validationErrors = [
            'success' => false,
            'errors' =>$validator->errors(),
            'message' => 'Whoops, validation errors'
        ];
    }
}
