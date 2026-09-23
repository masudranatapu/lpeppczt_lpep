<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentStockTransferRequest extends FormRequest
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
            'product_id' => 'required',
            'product_id.*' => 'required',
            'quantity' => 'required',
            'quantity.*' => 'required|numeric|min:1',
            'purchase_product_id' => 'required',
            'purchase_product_id.*' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'product_id' => 'Product is required.',
            'product_id.*' => 'Product is required.',
            'quantity' => 'Quantity is required.',
            'quantity.*' => 'Transfer Quantity is required.',
            'purchase_product_id' => 'Batch is required.',
            'purchase_product_id.*' => 'Batch is required.',
        ];
    }
}
