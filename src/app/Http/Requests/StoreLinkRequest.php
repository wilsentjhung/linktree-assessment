<?php

namespace App\Http\Requests;

use App\Models\ClassicLink;
use App\Models\Link;
use App\Rules\SublinkRule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLinkRequest extends FormRequest
{
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
        $rules = $this->baseRules();

        $type = $this->request->get('type') ?? null;

        if (empty($type)) {
            return $rules;
        }

        $linkRules = $this->linkRules($type);

        if (is_null($linkRules)) {
            return $rules;
        }

        $rules = [...$rules, ...$linkRules];

        $sublinkRules = $this->sublinkRules($type);

        if (is_null($sublinkRules)) {
            return $rules;
        }

        $rules = [...$rules, ...$sublinkRules];

        return $rules;
    }

    /**
     * Get base rules for all link types.
     *
     * @return array
     */
    protected function baseRules()
    {
        return [
            'userUuid' => [
                'required',
                'uuid',
            ],
            'type' => [
                'required',
                Rule::in(Link::TYPE_LIST),
            ],
        ];
    }

    /**
     * Get link rules by link type.
     *
     * @param $type
     * @return array|null
     */
    protected function linkRules(string $type)
    {
        /**
         * Link type to link rules mapping
         *
         * @TODO Add more rules for cols related to music base link (if any)
         * @TODO Add more rules for cols related to shows base link (if any)
         */
        $ruleMapping = [
            Link::TYPE_CLASSIC_LINK => [
                'title' => [
                    'required',
                    'string',
                    'max:' . ClassicLink::LEN_TITLE,
                ],
                'url' => [
                    'required',
                    'url',
                ],
            ],
            Link::TYPE_MUSIC_LINK => [],
            Link::TYPE_SHOWS_LINK => [],
        ];

        return $ruleMapping[$type] ?? null;
    }

    /**
     * Get sublink rules by link type.
     *
     * @param $type
     * @return array|null
     */
    protected function sublinkRules(string $type)
    {
        if (!in_array($type, Link::TYPE_LIST_WITH_SUBLINKS)) {
            return null;
        }

        return [
            'sublinks' => [
                'required',
                'nullable',
                'array',
            ],
            'sublinks.*' => [
                'required',
                'json',
                new SublinkRule($type),
            ],
        ];
    }
}
