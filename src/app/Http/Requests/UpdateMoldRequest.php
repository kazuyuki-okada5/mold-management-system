<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMoldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        // 自身のmold_numberはuniqueから除外
        $moldId = $this->route('mold')->id;

        return [
            'mold_number'      => "required|string|max:50|unique:molds,mold_number,{$moldId}",
            'name'             => 'required|string|max:255',
            'specifications'   => 'nullable|string|max:2000',
            'manufacture_date' => 'required|date',
            'status'           => 'required|in:待機中,使用中,予約済み,メンテナンス中',
            'warehouse'        => 'nullable|string|max:50',
            'floor'            => 'nullable|string|max:50',
            'shelf_number'     => 'nullable|string|max:50',
            'max_usage_count'  => 'nullable|integer|min:1|max:99999',
        ];
    }

    public function messages(): array
    {
        return [
            'mold_number.required'      => '金型番号は必須です。',
            'mold_number.unique'        => 'この金型番号は既に登録されています。',
            'name.required'             => '金型名は必須です。',
            'manufacture_date.required' => '製造日は必須です。',
            'manufacture_date.date'     => '製造日は正しい日付形式で入力してください。',
            'status.required'           => '状態は必須です。',
            'status.in'                 => '状態の値が不正です。',
            'max_usage_count.integer'   => '最大使用回数は整数で入力してください。',
            'max_usage_count.min'       => '最大使用回数は1以上を入力してください。',
        ];
    }
}
