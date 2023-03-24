<?php


namespace Raadaapartners\Raadaabase\Kuda\Helpers;


use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

trait ValidationHelper
{
    public function validateRequest(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'success'   => false,
                'message' => 'Whoops! Validation errors',
                'errors' => $validator->errors()
            ], 422));
        }
    }
}