<?php

namespace App\Http\Requests\API\V1\DataValue;

use App\Domain\Entities\Column;
use App\Domain\Entities\DataValue;
use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['required', 'exists:schools,id'],
            'updates' => ['required', 'array'],
            'updates.*.column_id' => ['required', 'exists:columns,id'],
            'updates.*.value' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'school_id.required' => 'Məktəb ID-si tələb olunur',
            'school_id.exists' => 'Məktəb tapılmadı',
            'updates.required' => 'Yeniləmələr tələb olunur',
            'updates.array' => 'Yeniləmələr array formatında olmalıdır',
            'updates.*.column_id.required' => 'Sütun ID-si tələb olunur',
            'updates.*.column_id.exists' => 'Sütun tapılmadı',
            'updates.*.value.required' => 'Dəyər tələb olunur',
            'updates.*.value.string' => 'Dəyər mətn formatında olmalıdır'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->updates as $index => $update) {
                $column = Column::find($update['column_id']);
                
                if ($column) {
                    // Məlumatın tipinə görə yoxlama
                    switch ($column->data_type) {
                        case 'number':
                            if (!is_numeric($update['value'])) {
                                $validator->errors()->add(
                                    "updates.{$index}.value",
                                    "Sətir {$index}: Ədədi qiymət tələb olunur"
                                );
                            }
                            break;
                        case 'text':
                            if (!is_string($update['value'])) {
                                $validator->errors()->add(
                                    "updates.{$index}.value",
                                    "Sətir {$index}: Mətn formatı tələb olunur"
                                );
                            }
                            break;
                        case 'date':
                            if (!$this->isValidDate($update['value'])) {
                                $validator->errors()->add(
                                    "updates.{$index}.value",
                                    "Sətir {$index}: Tarix formatı düzgün deyil"
                                );
                            }
                            break;
                    }
                }
            }
        }); 
    }

// Tarix formatını yoxlayan köməkçi metod
    private function isValidDate($value): bool
    {
        try {
            \Carbon\Carbon::parse($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}