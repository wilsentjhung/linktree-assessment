<?php

namespace App\Rules;

use App\Models\Link;
use App\Models\ShowsSublink;

use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SublinkRule implements RuleContract
{
    /**
     * @var string
     */
    protected $type = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $json = json_decode($value, true);

        if (empty($json)) {
            return false;
        }

        $rules = $this->rules();

        if (is_null($rules)) {
            return false;
        }

        $this->validator = Validator::make($json, $this->rules());

        return $this->validator->passes();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return !empty($this->validator)
            ? $this->validator->errors()->all()
            : "The :attribute error must be a valid $this->type sublink JSON string";
    }

    /**
     * Get the rules.
     *
     * @return array
     */
    protected function rules()
    {
        /**
         * Link type to sublink rules mapping
         */
        $ruleMapping = [
            Link::TYPE_MUSIC_LINK => [
                'name' => [
                    'required',
                    'string',
                ],
                'url' => [
                    'required',
                    'url',
                ],
            ],
            Link::TYPE_SHOWS_LINK => [
                'name' => [
                    'required',
                    'string',
                ],
                'url' => [
                    'required',
                    'url',
                ],
                'status' => [
                    'required',
                    Rule::in(ShowsSublink::STATUS_LIST),
                ],
                'date' => [
                    'required',
                    'date',
                ],
                'venue' => [
                    'required',
                    'string',
                ],
            ],
        ];

        return $ruleMapping[$this->type] ?? null;
    }
}
