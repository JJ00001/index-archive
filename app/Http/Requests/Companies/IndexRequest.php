<?php

namespace App\Http\Requests\Companies;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{

    public const DEFAULT_DIRECTION = 'asc';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $columns = implode(',', self::allowedColumns());

        return [
            'sort.column' => ['nullable', 'in:'.$columns],
            'sort.direction' => ['nullable', 'in:asc,desc'],
        ];
    }

    public static function allowedColumns(): array
    {
        return [
            'name',
            'ticker',
            'isin',
        ];
    }

    public function sort(): array
    {
        $column = $this->input('sort.column');

        if ( ! $column) {
            return [];
        }

        $direction = $this->input('sort.direction', self::DEFAULT_DIRECTION);

        return [
            [
                'column' => $column,
                'direction' => $direction,
            ],
        ];
    }

}
