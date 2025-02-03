<?php

namespace App\Http\Requests\API\V1\DataValue;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Entities\Column;
use App\Domain\Entities\DataValue;

class StoreDataValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['required', 'exists:schools,id'],
            'column_id' => ['required', 'exists:columns,id'],
            'value' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'school_id.required' => 'Məktəb seçilməlidir',
            'school_id.exists' => 'Seçilmiş məktəb mövcud deyil',
            'column_id.required' => 'Sütun seçilməlidir',
            'column_id.exists' => 'Seçilmiş sütun mövcud deyil',
            'value.required' => 'Dəyər tələb olunur'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $column = Column::find($this->column_id);
            
            if ($column && $this->value) {
                $dataValue = new DataValue([
                    'column_id' => $column->id,
                    'value' => $this->value
                ]);

                if (!$dataValue->isValidValue()) {
                    $validator->errors()->add(
                        'value', 
                        'Daxil edilən məlumat sütunun tipinə uyğun deyil'
                    );
                }
            }
        });
    }
}