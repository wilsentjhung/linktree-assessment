<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexLinkRequest extends FormRequest
{
    /**
     * Sortable cols
     */
    public const SORTABLE_COLS = [
        'created_at',
    ];

    /**
     * Order by options
     */
    public const ORDER_BY_OPTIONS = [
        'asc',
        'desc',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     * @TODO Add user access control
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
            'userUuid' => [
                'required',
                'uuid',
            ],
            'sortBy' => [
                Rule::in(self::SORTABLE_COLS),
            ],
            'orderBy' => [
                Rule::in(self::ORDER_BY_OPTIONS),
            ],
        ];
    }
}
