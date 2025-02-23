<?php
// app/Http/Requests/SchoolAdmin/DataEntryRequest.php

namespace App\Http\Requests\SchoolAdmin;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Entities\Column;

class DataEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $column = Column::find($this->column_id);
        
        $rules = [
            'column_id' => 'required|exists:columns,id',
            'value' => 'required'
        ];

        // Sütun tipinə görə əlavə validasiya
        if ($column) {
            switch ($column->data_type) {
                case 'number':
                    $rules['value'] .= '|numeric';
                    break;
                case 'date':
                    $rules['value'] .= '|date';
                    break;
                case 'select':
                    $rules['value'] .= '|in:' . implode(',', $column->options);
                    break;
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'column_id.required' => 'Sütun ID-si tələb olunur',
            'column_id.exists' => 'Yanlış sütun ID-si',
            'value.required' => 'Dəyər tələb olunur',
            'value.numeric' => 'Dəyər rəqəm olmalıdır',
            'value.date' => 'Yanlış tarix formatı',
            'value.in' => 'Yanlış seçim'
        ];
    }
}