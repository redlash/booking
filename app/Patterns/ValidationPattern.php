<?php namespace App\Patterns;

use Illuminate\Support\Facades\Validator;

/**
 * Trait ValidationPattern
 *
 * @package App\Patterns
 */
trait ValidationPattern
{
    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return Validator
     * @throws \Exception
     */
    public function validate($data = [], $rules = [], $messages = [])
    {

        /**
         * Create the service.
         *
         * @var Validator $validator.
         */
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails() === true) {
            /**
             * Throw back the validation exceptions.
             *
             * @var \FormValidationException $error
             */
            throw new \Exception($validator->errors()->first());
        }

        return $validator;
    }
}