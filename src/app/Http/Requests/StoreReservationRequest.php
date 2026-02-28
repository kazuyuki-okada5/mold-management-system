<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mold_id' => 'required|exists:molds,id',
            'reserved_start' => 'required|date|after:now',
            'reserved_end' => 'required|date|after:reserved_start',
            'purpose' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'mold_id.required' => '金型を指定してください',
            'mold_id.exists' => '指定された金型が存在しません',
            'reserved_start.required' => '予約開始日時は必須です',
            'reserved_start.after' => '予約開始日時は現在以降を指定してください',
            'reserved_end.required' => '予約終了日時は必須です',
            'reserved_end.after' => '予約終了日時は開始日時より後を指定してください',
            'purpose.required' => '使用目的は必須です',
            'purpose.max' => '使用目的は1000文字以内で入力してください',
        ];
    }
}
