<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendNotificationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'recipient' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'channel' => 'required|in:email,sms,push,telegram',
            'priority' => 'sometimes|in:low,medium,high',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'recipient.required' => 'Recipient is required for notification',
            'message.required' => 'Message content cannot be empty',
            'message.max' => 'Message is too long (maximum 1000 characters)',
            'channel.required' => 'Notification channel must be specified',
            'channel.in' => 'Invalid notification channel. Available: email, sms, push, telegram',
            'priority.in' => 'Priority must be one of: low, medium, high',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Notification validation failed',
                'errors' => $validator->errors(),
                'request_data' => $this->getValidationData(),
                'status' => 422
            ], 422)
        );
    }

    /**
     * Get data for validation (excluding sensitive fields)
     */
    protected function getValidationData(): array
    {
        return $this->only(['channel', 'priority',]);
    }
}
