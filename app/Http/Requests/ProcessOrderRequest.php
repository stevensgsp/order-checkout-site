<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_name'   => ['required', 'string', 'min:2', 'max:80'],
            'customer_email'  => ['required', 'email', 'max:120'],
            'customer_mobile' => ['required', 'string', 'min:7', 'max:40'],
            'product_id'      => ['required', 'exists:products,id'],
        ];
    }
}
