<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_id'               => 'required|exists:sales,id',
            'return_date'           => 'required|date',
            'reason'                => 'nullable|string|max:1000',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.unit_price'    => 'required|numeric|min:0',
            'items.*.return_reason' => 'required|in:damaged,wrong_item,customer_changed_mind,defective,expired,other',
            'items.*.condition'     => 'required|in:good,damaged,defective',
            'items.*.notes'         => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'sale_id.required'             => 'Please select the original sale.',
            'sale_id.exists'               => 'Selected sale does not exist.',
            'return_date.required'         => 'Return date is required.',
            'items.required'               => 'At least one item must be selected for return.',
            'items.min'                    => 'At least one item must be selected for return.',
            'items.*.quantity.min'         => 'Return quantity must be at least 1.',
            'items.*.unit_price.min'       => 'Unit price cannot be negative.',
        ];
    }
}
