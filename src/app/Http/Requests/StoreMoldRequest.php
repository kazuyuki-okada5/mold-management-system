<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMoldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'mold_number'      => 'required|string|max:50|unique:molds,mold_number',
            'name'             => 'required|string|max:255',
            'specifications'   => 'nullable|string|max:2000',
            'manufacture_date' => 'required|date',
            'warehouse'        => 'nullable|string|max:50',
            'floor'            => 'nullable|string|max:50',
            'shelf_number'     => 'nullable|string|max:50',
            'max_usage_count'  => 'nullable|integer|min:1|max:99999',
        ];
    }

    public function messages(): array
    {
        return [
            'mold_number.required'       => '金型番号は必須です。',
            'mold_number.unique'         => 'この金型番号は既に登録されています。',
            'mold_number.max'            => '金型番号は50文字以内で入力してください。',
            'name.required'              => '金型名は必須です。',
            'name.max'                   => '金型名は255文字以内で入力してください。',
            'manufacture_date.required'  => '製造日は必須です。',
            'manufacture_date.date'      => '製造日は正しい日付形式で入力してください。',
            'max_usage_count.integer'    => '最大使用回数は整数で入力してください。',
            'max_usage_count.min'        => '最大使用回数は1以上を入力してください。',
        ];
    }
}
