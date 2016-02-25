<?php

namespace Artisangang\Validator;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\Validator as LaravelValidator;

class Validator
{

    /**
     * @var array
     */
    protected $original_rules;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $jquery_rules;

    /**
     * @var string
     */
    protected $form_identity = 'form';

    /**
     * @var array
     */
    protected $custom_messages = [];

    /**
     * @var array
     */
    protected $jquery_messages = [];

    /**
     * @param $rules
     * @param array $messages
     */
    public function __construct(array $rules = [], array $messages = [])
    {
        $this->original_rules = $rules;
        $this->rules = $this->explodeRules($rules);
        $this->custom_messages = $messages;
        view()->share('jquery', $this);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function make(array $data = [])
    {
        return LaravelValidator::make($data, $this->original_rules, $this->custom_messages);
    }

    /**
     * @param $rules
     * @return mixed
     */
    protected function explodeRules($rules)
    {
        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        return $rules;
    }

    /**
     * @param $rules
     * @return array
     */
    protected function parseRule($rules)
    {

        $parameters = [];

        if (strpos($rules, ':') !== false) {
            list($rules, $parameter) = explode(':', $rules, 2);

            $parameters = $this->parseParameters($rules, $parameter);
        }

        $rules = [Str::studly(trim($rules)), $parameters];

        $rules[0] = $this->normalizeRule($rules[0]);

        return $rules;
    }

    /**
     * @param $rule
     * @param $parameter
     * @return array
     */
    protected function parseParameters($rule, $parameter)
    {
        if (strtolower($rule) == 'regex') {
            return [$parameter];
        }

        return str_getcsv($parameter);
    }

    /**
     * @param $rule
     * @return string
     */
    protected function normalizeRule($rule)
    {
        switch ($rule) {
            case 'Int':
                return 'Integer';
            case 'Bool':
                return 'Boolean';
            default:
                return $rule;
        }
    }

    /**
     * @param $attribute
     * @param $lowerRule
     * @return mixed
     */
    protected function parseMessage($attribute, $lowerRule)
    {
        $keys = ["{$attribute}.{$lowerRule}", $lowerRule];

        foreach ($keys as $key) {
            if (isset($this->custom_messages[$key])) {
                return $this->custom_messages[$key];
            }
        }
    }

    /**
     * @return array
     */
    protected function validateRequired()
    {
        return ['required' => true];
    }

    /**
     * @return array
     */
    protected function validateAccepted()
    {
        return ['accepted' => true];
    }

    /**
     * @return array
     */
    protected function validateActiveUrl()
    {
        return $this->validateUrl();
    }

    /**
     * @return array
     */
    protected function validateUrl()
    {
        return ['url' => true];
    }

    /**
     * @return array
     */
    protected function validateAlpha()
    {
        return ['alpha' => true];
    }

    /**
     * @return array
     */
    protected function validateAlphaDash()
    {
        return ['alpha_dash' => true];
    }

    /**
     * @return array
     */
    protected function validateAlphaNum()
    {
        return ['alpha_num' => true];
    }

    /**
     * @return array
     */
    protected function validateArray()
    {
        return ['array' => true];
    }

    /**
     * @param $min
     * @param $max
     * @return array
     */
    protected function validateBetween($min, $max)
    {
        return ['between' => [$min, $max]];
    }

    /**
     * @return array
     */
    protected function validateBoolean()
    {
        return ['boolean' => true];
    }

    /**
     * @return array
     */
    protected function validateConfirmed()
    {
        return ['equalTo' => ''];
    }

    /**
     * @return array
     */
    protected function validateIn()
    {
        return ['in' => func_get_args()];
    }

    /**
     * @param $min
     * @return array
     */
    protected function validateMin($min)
    {

        return ['min' => [$min]];
    }

    /**
     * @param $max
     * @return array
     */
    protected function validateMax($max)
    {

        return ['max' => [$max]];
    }

    /**
     * @param $field
     * @param null $value
     * @return array
     */
    public function validateRequiredIf($field, $value = null)
    {
        return ['required_if' => ["{$this->form_identity}#{$field}", $value]];
    }

    /**
     * @return array
     */
    protected function validateEmail()
    {
        return ['email' => true];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->validate();
    }

    /**
     * @param $attribute
     * @param $rules
     */
    public function writeRules($attribute, $rules)
    {

        foreach ($rules as $rule) {
            list($rule, $parameters) = $this->parseRule($rule);

            if ($rule == '') {
                return;
            }

            $method = "validate{$rule}";

            $out = call_user_func_array([$this, $method], $parameters);

            $this->jquery_rules[$attribute] = isset($this->jquery_rules[$attribute]) ? array_merge($this->jquery_rules[$attribute], $out) : (array)$out;
            $lowerRule = strtolower($rule);
            $message = $this->parseMessage($attribute, $lowerRule);

            if (!empty($message)) {
                $this->jquery_messages[$attribute][$lowerRule] = $message;
            }

        }
    }

    /**
     * @return string
     */
    protected function writeJavascript()
    {

        ob_start();

        ?>
        <script>
            $(function () {
                $("<?= $this->form_identity ?>").validate({
                    rules: <?= json_encode($this->jquery_rules) ?>,
                    messages: <?= json_encode($this->jquery_messages) ?>

                });
            });
        </script>
        <?php
        $s = ob_get_contents();
        ob_get_clean();

        return $s;

    }

    /**
     * @param $attribute
     * @param array $rules
     * @param array $messages
     */
    public function append($attribute, array $rules = [], array $messages = [])
    {

        if (is_array($attribute)) {
            array_merge($this->jquery_rules, $attribute);
        }

        if (is_string($attribute) && !empty($rules)) {

            if (isset($this->jquery_rules[$attribute]) && is_array($this->jquery_rules[$attribute])) {
                $this->jquery_rules[$attribute] = $rules;
            }

        }

        if (!empty($messages)) {
            array_merge($this->jquery_messages, $messages);
        }

    }

    /**
     * @param $attribute
     * @param null $rule
     * @param null $message
     */
    public function addMessage($attribute, $rule = null, $message = null)
    {

        if (is_array($rule)) {
            $this->jquery_messages[$attribute] = $rule;
        } else {

            $this->jquery_messages[$attribute][$rule] = $message;
        }
    }

    /**
     * @param $form
     * @return string
     */
    public function validate($form)
    {

        $this->form_identity = $form;

        foreach ($this->rules as $attribute => $rule) {
            $this->writeRules($attribute, $rule);
        }

        return $this->writeJavascript();
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        if (is_callable([$this, $method])) {
            return call_user_func_array([$this, $method], $params);
        }
    }


}