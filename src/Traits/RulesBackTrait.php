<?php

declare(strict_types=1);

namespace Admin\Traits;

use Admin\Components\InputGroupComponent;
use Admin\Controllers\Controller;

/**
 * Trade with backend validation form rules.
 */
trait RulesBackTrait
{
    /**
     * Duplicate rules to the front end rules. (Only for support rules)
     *
     * @var bool
     */
    protected bool $print_front = true;

    /**
     * Rules for the current component.
     *
     * @var array
     */
    protected array $backRules = [];

    /**
     * Rules messages for the current component.
     *
     * @var array
     */
    protected array $backRuleMessages = [];

    /**
     * Add custom rule.
     *
     * @param  object|string  $rule
     * @param  string|null  $message
     * @return $this
     */
    public function rule(object|string $rule, string $message = null): static
    {
        if ($this->admin_controller) {
            /** @var Controller $controller */
            $arr = str_ends_with($this->name, '[]') ? '.' : '';
            $deepPaths = $this->deepPaths();
            $ruleKey = trim(implode('.', $deepPaths).$arr, '.');
            $controller = $this->controller;

            $controller::addGlobalRule($ruleKey, $rule);
            $controller::addGlobalRuleMessage($ruleKey, $rule, $message);

            if (is_string($rule) && isset($this->backRules[$ruleKey])) {
                if (!in_array($rule, $this->backRules[$ruleKey])) {
                    $this->backRules[$ruleKey][] = $rule;
                }
            } else {
                $this->backRules[$ruleKey][] = $rule;
            }

            if ($message && is_string($rule)) {
                $this->backRuleMessages["{$ruleKey}.{$rule}"] = $message;
            }
        }

        return $this;
    }

    /**
     * Add rule "accepted" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function accepted_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->accepted($message) : $this;
    }

    /**
     * The field under validation must be yes, on, 1, or true.
     * This is useful for validating "Terms of Service" acceptance.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function accepted(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "active_url" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function active_url_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->active_url($message) : $this;
    }

    /**
     * The field under validation must have a valid A or AAAA record
     * according to the dns_get_record PHP function. The hostname of
     * the provided URL is extracted using the parse_url PHP function
     * before being passed to dns_get_record.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function active_url(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "after" if condition is true.
     *
     * @param $condition
     * @param  string  $date
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function after_if($condition, string $date, string $message = null): InputGroupComponent
    {
        return $condition ? $this->after($date, $message) : $this;
    }

    /**
     * The field under validation must be a value after a given date.
     * The dates will be passed into the strtotime PHP function.
     *
     * Instead of passing a date string to be evaluated by strtotime,
     * you may specify another field to compare against the date
     *
     * @param  string  $date
     * @param  string|null  $message
     * @return $this
     */
    public function after(string $date, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$date], $message);
    }

    /**
     * @param  string  $rule
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    protected function _rule(string $rule, array $params = [], string $message = null): static
    {
        $params = trim(implode(',', $params), ',');
        if ($params) {
            $rule .= ":{$params}";
        }

        return $this->rule($rule, $message);
    }

    /**
     * Add rule "after_or_equal" if condition is true.
     *
     * @param $condition
     * @param  string  $date
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function after_or_equal_if($condition, string $date, string $message = null): InputGroupComponent
    {
        return $condition ? $this->after_or_equal($date, $message) : $this;
    }

    /**
     * The field under validation must be a value after or equal to the
     * given date. For more information, see the after rule.
     *
     * @param  string  $date
     * @param  string|null  $message
     * @return $this
     */
    public function after_or_equal(string $date, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$date], $message);
    }

    /**
     * Add rule "alpha" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function alpha_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->alpha($message) : $this;
    }

    /**
     * The field under validation must be entirely alphabetic characters.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function alpha(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "alpha_dash" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function alpha_dash_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->alpha_dash($message) : $this;
    }

    /**
     * The field under validation may have alpha-numeric characters, as
     * well as dashes and underscores.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function alpha_dash(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "alpha_num" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function alpha_num_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->alpha_num($message) : $this;
    }

    /**
     * The field under validation must be entirely alpha-numeric characters.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function alpha_num(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "array" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function array_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->array($message) : $this;
    }

    /**
     * The field under validation must be a PHP array.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function array(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "bail" if condition is true.
     *
     * @param $condition
     * @return InputGroupComponent
     */
    public function bail_if($condition): InputGroupComponent
    {
        return $condition ? $this->bail() : $this;
    }

    /**
     * Stop running validation rules after the first validation failure.
     *
     * @return $this
     */
    public function bail(): static
    {
        return $this->rule(__FUNCTION__);
    }

    /**
     * Add rule "before" if condition is true.
     *
     * @param $condition
     * @param  string  $date
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function before_if($condition, string $date, string $message = null): InputGroupComponent
    {
        return $condition ? $this->before($date, $message) : $this;
    }

    /**
     * The field under validation must be a value preceding the given date.
     * The dates will be passed into the PHP strtotime function. In addition,
     * like the after rule, the name of another field under validation may
     * be supplied as the value of date.
     *
     * @param  string  $date
     * @param  string|null  $message
     * @return $this
     */
    public function before(string $date, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$date], $message);
    }

    /**
     * Add rule "before_or_equal" if condition is true.
     *
     * @param $condition
     * @param  string  $date
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function before_or_equal_if($condition, string $date, string $message = null): InputGroupComponent
    {
        return $condition ? $this->before_or_equal($date, $message) : $this;
    }

    /**
     * The field under validation must be a value preceding or equal to the given
     * date. The dates will be passed into the PHP strtotime function. In addition,
     * like the after rule, the name of another field under validation may be
     * supplied as the value of date.
     *
     * @param  string  $date
     * @param  string|null  $message
     * @return $this
     */
    public function before_or_equal(string $date, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$date], $message);
    }

    /**
     * Add rule "between" if condition is true.
     *
     * @param $condition
     * @param $min
     * @param $max
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function between_if($condition, $min, $max, string $message = null): InputGroupComponent
    {
        return $condition ? $this->between($min, $max, $message) : $this;
    }

    /**
     * The field under validation must have a size between the given min and max.
     * Strings, numerics, arrays, and files are evaluated in the same fashion
     * as the size rule.
     *
     * @param  int|string  $min
     * @param  int|string  $max
     * @param  string|null  $message
     * @return $this
     */
    public function between(int|string $min, int|string $max, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$min, $max], $message);
    }

    /**
     * Add rule "boolean" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function boolean_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->boolean($message) : $this;
    }

    /**
     * The field under validation must be able to be cast as a boolean.
     * Accepted input are true, false, 1, 0, "1", and "0".
     *
     * @param  string|null  $message
     * @return $this
     */
    public function boolean(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "confirmed" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function confirmed_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->confirmed($message) : $this;
    }

    /**
     * The field under validation must have a matching field of foo_confirmation.
     * For example, if the field under validation is password, a matching
     * password_confirmation field must be present in the input.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function confirmed(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "date" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function date_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->date($message) : $this;
    }

    /**
     * The field under validation must be a valid, non-relative date according
     * to the strtotime PHP function.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function date(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "date_equals" if condition is true.
     *
     * @param $condition
     * @param  string  $date
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function date_equals_if($condition, string $date, string $message = null): InputGroupComponent
    {
        return $condition ? $this->date_equals($date, $message) : $this;
    }

    /**
     * The field under validation must be equal to the given date. The dates
     * will be passed into the PHP strtotime function.
     *
     * @param  string  $date
     * @param  string|null  $message
     * @return $this
     */
    public function date_equals(string $date, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$date], $message);
    }

    /**
     * Add rule "date_format" if condition is true.
     *
     * @param $condition
     * @param  string  $format
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function date_format_if($condition, string $format, string $message = null): InputGroupComponent
    {
        return $condition ? $this->date_format($format, $message) : $this;
    }

    /**
     * The field under validation must match the given format. You should use
     * either date or date_format when validating a field, not both.
     * This validation rule supports all formats supported by PHP's DateTime class.
     *
     * @param  string  $format
     * @param  string|null  $message
     * @return $this
     */
    public function date_format(string $format, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$format], $message);
    }

    /**
     * Add rule "different" if condition is true.
     *
     * @param $condition
     * @param  string  $field
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function different_if($condition, string $field, string $message = null): InputGroupComponent
    {
        return $condition ? $this->different($field, $message) : $this;
    }

    /**
     * The field under validation must have a different value than field.
     *
     * @param  string  $field
     * @param  string|null  $message
     * @return $this
     */
    public function different(string $field, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$field], $message);
    }

    /**
     * Add rule "digits" if condition is true.
     *
     * @param $condition
     * @param  string  $value
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function digits_if($condition, string $value, string $message = null): InputGroupComponent
    {
        return $condition ? $this->digits($value, $message) : $this;
    }

    /**
     * The field under validation must be numeric and must have an
     * exact length of value.
     *
     * @param  string  $value
     * @param  string|null  $message
     * @return $this
     */
    public function digits(string $value, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * Add rule "digits_between" if condition is true.
     *
     * @param $condition
     * @param $min
     * @param $max
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function digits_between_if($condition, $min, $max, string $message = null): InputGroupComponent
    {
        return $condition ? $this->digits_between($min, $max, $message) : $this;
    }

    /**
     * The field under validation must be numeric and must have a length
     * between the given min and max.
     *
     * @param  int|string  $min
     * @param  int|string  $max
     * @param  string|null  $message
     * @return $this
     */
    public function digits_between(int|string $min, int|string $max, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$min, $max], $message);
    }

    /**
     * Add rule "dimensions" if condition is true.
     *
     * @param $condition
     * @param  array  $params
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function dimensions_if($condition, array $params, string $message = null): InputGroupComponent
    {
        return $condition ? $this->dimensions($params, $message) : $this;
    }

    /**
     * The file under validation must be an image meeting the dimension
     * constraints as specified by the rule's parameters.
     *
     * Available constraints are:
     * min_width, max_width, min_height, max_height, width, height, ratio.
     *
     * A ratio constraint should be represented as width divided by height.
     * This can be specified either by a statement like 3/2 or a float like 1.5
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function dimensions(array $params, string $message = null): static
    {
        return $this->_n_rule(__FUNCTION__, $params, $message);
    }

    /**
     * System new rule with parameters.
     *
     * @param  string  $rule
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    protected function _n_rule(string $rule, array $params = [], string $message = null): static
    {
        $new_params = [];

        foreach ($params as $key => $param) {
            $new_params[] = "{$key}={$param}";
        }

        return $this->_rule($rule, $new_params, $message);
    }

    /**
     * Add rule "distinct" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function distinct_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->distinct($message) : $this;
    }

    /**
     * When working with arrays, the field under validation must not have
     * any duplicate values.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function distinct(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "email" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function email_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->email($message) : $this;
    }

    /**
     * The field under validation must be formatted as an e-mail address.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function email(string $message = null): static
    {
        if ($this->print_front) {
            $this->is_email();
        }

        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "ends_with" if condition is true.
     *
     * @param $condition
     * @param  array  $values
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function ends_with_if($condition, array $values, string $message = null): InputGroupComponent
    {
        return $condition ? $this->ends_with($values, $message) : $this;
    }

    /**
     * The field under validation must end with one of the given values.
     *
     * @param  array  $values
     * @param  string|null  $message
     * @return $this
     */
    public function ends_with(array $values, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $values, $message);
    }

    /**
     * Add rule "exists" if condition is true.
     *
     * @param $condition
     * @param  string  $table
     * @param  string|null  $column
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function exists_if(
        $condition,
        string $table,
        string $column = null,
        string $message = null
    ): InputGroupComponent {
        return $condition ? $this->exists($table, $column, $message) : $this;
    }

    /**
     * The field under validation must exist on a given database table.
     *
     * @param  string  $table
     * @param  string|null  $column
     * @param  string|null  $message
     * @return $this
     */
    public function exists(string $table, string $column = null, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$table, $column], $message);
    }

    /**
     * Add rule "file" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function file_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->file($message) : $this;
    }

    /**
     * The field under validation must be a successfully uploaded file.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function file(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "filled" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function filled_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->filled($message) : $this;
    }

    /**
     * The field under validation must not be empty when it is present.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function filled(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "gt" if condition is true.
     *
     * @param $condition
     * @param  string  $field
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function gt_if($condition, string $field, string $message = null): InputGroupComponent
    {
        return $condition ? $this->gt($field, $message) : $this;
    }

    /**
     * The field under validation must be greater than the given field.
     * The two fields must be of the same type. Strings, numerics, arrays,
     * and files are evaluated using the same conventions as the size rule.
     *
     * @param  string  $field
     * @param  string|null  $message
     * @return $this
     */
    public function gt(string $field, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$field], $message);
    }

    /**
     * Add rule "gte" if condition is true.
     *
     * @param $condition
     * @param  string  $field
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function gte_if($condition, string $field, string $message = null): InputGroupComponent
    {
        return $condition ? $this->gte($field, $message) : $this;
    }

    /**
     * The field under validation must be greater than or equal to the given
     * field. The two fields must be of the same type. Strings, numerics,
     * arrays, and files are evaluated using the same conventions
     * as the size rule.
     *
     * @param  string  $field
     * @param  string|null  $message
     * @return $this
     */
    public function gte(string $field, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$field], $message);
    }

    /**
     * Add rule "image" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function image_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->image($message) : $this;
    }

    /**
     * The file under validation must be an image
     * (jpeg, png, bmp, gif, svg, or webp).
     *
     * @param  string|null  $message
     * @return $this
     */
    public function image(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "in" if condition is true.
     *
     * @param $condition
     * @param  array  $params
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function in_if($condition, array $params, string $message = null): InputGroupComponent
    {
        return $condition ? $this->in($params, $message) : $this;
    }

    /**
     * The field under validation must be included in the given list of values.
     * Since this rule often requires you to implode an array.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function in(array $params, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * Add rule "in_array" if condition is true.
     *
     * @param $condition
     * @param  string  $field
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function in_array_if($condition, string $field, string $message = null): InputGroupComponent
    {
        return $condition ? $this->in_array($field, $message) : $this;
    }

    /**
     * The field under validation must exist in anotherfield's values.
     *
     * @param  string  $field
     * @param  string|null  $message
     * @return $this
     */
    public function in_array(string $field, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$field], $message);
    }

    /**
     * Add rule "ip" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function ip_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->ip($message) : $this;
    }

    /**
     * The field under validation must be an IP address.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function ip(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "ipv4" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function ipv4_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->ipv4($message) : $this;
    }

    /**
     * The field under validation must be an IPv4 address.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function ipv4(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "ipv6" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function ipv6_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->ipv6($message) : $this;
    }

    /**
     * The field under validation must be an IPv6 address.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function ipv6(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "json" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function json_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->json($message) : $this;
    }

    /**
     * The field under validation must be a valid JSON string.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function json(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "lt" if condition is true.
     *
     * @param $condition
     * @param  string  $field
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function lt_if($condition, string $field, string $message = null): InputGroupComponent
    {
        return $condition ? $this->lt($field, $message) : $this;
    }

    /**
     * The field under validation must be less than the given field.
     * The two fields must be of the same type. Strings, numerics, arrays,
     * and files are evaluated using the same conventions as the size rule.
     *
     * @param  string  $field
     * @param  string|null  $message
     * @return $this
     */
    public function lt(string $field, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$field], $message);
    }

    /**
     * Add rule "lte" if condition is true.
     *
     * @param $condition
     * @param  string  $field
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function lte_if($condition, string $field, string $message = null): InputGroupComponent
    {
        return $condition ? $this->lte($field, $message) : $this;
    }

    /**
     * The field under validation must be less than or equal to the given field.
     * The two fields must be of the same type. Strings, numerics, arrays, and
     * files are evaluated using the same conventions as the size rule.
     *
     * @param  string  $field
     * @param  string|null  $message
     * @return $this
     */
    public function lte(string $field, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$field], $message);
    }

    /**
     * Add rule "max" if condition is true.
     *
     * @param $condition
     * @param  int  $value
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function max_if($condition, int $value, string $message = null): InputGroupComponent
    {
        return $condition ? $this->max($value, $message) : $this;
    }

    /**
     * The field under validation must be less than or equal to a maximum value.
     * Strings, numerics, arrays, and files are evaluated in the same fashion as
     * the size rule.
     *
     * @param  int  $value
     * @param  string|null  $message
     * @return $this
     */
    public function max(int $value, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * Add rule "mimetypes" if condition is true.
     *
     * @param $condition
     * @param  array  $mimetypes
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function mimetypes_if($condition, array $mimetypes, string $message = null): InputGroupComponent
    {
        return $condition ? $this->mimetypes($mimetypes, $message) : $this;
    }

    /**
     * The file under validation must match one of the given MIME types.
     *
     * @param  array  $mimetypes
     * @param  string|null  $message
     * @return $this
     */
    public function mimetypes(array $mimetypes, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $mimetypes, $message);
    }

    /**
     * Add rule "mimes" if condition is true.
     *
     * @param $condition
     * @param  array  $mimes
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function mimes_if($condition, array $mimes, string $message = null): InputGroupComponent
    {
        return $condition ? $this->mimes($mimes, $message) : $this;
    }

    /**
     * The file under validation must have a MIME type corresponding to
     * one of the listed extensions.
     *
     * Even though you only need to specify the extensions, this rule
     * actually validates against the MIME type of the file by reading
     * the file's contents and guessing its MIME type
     *
     * A full listing of MIME types and their corresponding extensions
     * may be found at the following location:
     * https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
     *
     * @param  array  $mimes
     * @param  string|null  $message
     * @return $this
     */
    public function mimes(array $mimes, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $mimes, $message);
    }

    /**
     * Add rule "min" if condition is true.
     *
     * @param $condition
     * @param  int  $value
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function min_if($condition, int $value, string $message = null): InputGroupComponent
    {
        return $condition ? $this->min($value, $message) : $this;
    }

    /**
     * The field under validation must have a minimum value. Strings, numerics,
     * arrays, and files are evaluated in the same fashion as the size rule.
     *
     * @param  int  $value
     * @param  string|null  $message
     * @return $this
     */
    public function min(int $value, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * Add rule "integer" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function integer_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->integer($message) : $this;
    }

    /**
     * The field under validation must be an integer.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function integer(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "not_in" if condition is true.
     *
     * @param $condition
     * @param  array  $values
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function not_in_if($condition, array $values, string $message = null): InputGroupComponent
    {
        return $condition ? $this->not_in($values, $message) : $this;
    }

    /**
     * The field under validation must not be included in the given list of values.
     *
     * @param  array  $values
     * @param  string|null  $message
     * @return $this
     */
    public function not_in(array $values, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $values, $message);
    }

    /**
     * Add rule "not_regex" if condition is true.
     *
     * @param $condition
     * @param  string  $pattern
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function not_regex_if($condition, string $pattern, string $message = null): InputGroupComponent
    {
        return $condition ? $this->not_regex($pattern, $message) : $this;
    }

    /**
     * The field under validation must not match the given regular expression.
     *
     * Internally, this rule uses the PHP preg_match function. The pattern specified
     * should obey the same formatting required by preg_match and thus also include
     * valid delimiters. For example: 'email' => 'not_regex:/^.+$/i'.
     *
     * Note: When using the regex / not_regex patterns, it may be necessary to specify
     * rules in an array instead of using pipe delimiters, especially if the regular
     * expression contains a pipe character.
     *
     * @param  string  $pattern
     * @param  string|null  $message
     * @return $this
     */
    public function not_regex(string $pattern, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$pattern], $message);
    }

    /**
     * Add rule "nullable" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function nullable_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->nullable($message) : $this;
    }

    /**
     * The field under validation may be null. This is particularly useful when validating
     * primitive such as strings and integers that can contain null values.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function nullable(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "numeric" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function numeric_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->numeric($message) : $this;
    }

    /**
     * The field under validation must be numeric.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function numeric(string $message = null): static
    {
        if ($this->print_front) {
            $this->is_number();
        }

        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "password" if condition is true.
     *
     * @param $condition
     * @param  string  $guard
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function password_if($condition, string $guard, string $message = null): InputGroupComponent
    {
        return $condition ? $this->password($guard, $message) : $this;
    }

    /**
     * The field under validation must match the authenticated user's password.
     * You may specify an authentication guard using the rule's first parameter.
     *
     * @param  string  $guard
     * @param  string|null  $message
     * @return $this
     */
    public function password(string $guard, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$guard], $message);
    }

    /**
     * Add rule "present" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function present_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->present($message) : $this;
    }

    /**
     * The field under validation must be present in the input data but can be empty.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function present(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "regex" if condition is true.
     *
     * @param $condition
     * @param  string  $pattern
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function regex_if($condition, string $pattern, string $message = null): InputGroupComponent
    {
        return $condition ? $this->regex($pattern, $message) : $this;
    }

    /**
     * The field under validation must match the given regular expression.
     *
     * Internally, this rule uses the PHP preg_match function. The pattern specified
     * should obey the same formatting required by preg_match and thus also include
     * valid delimiters. For example: 'email' => 'regex:/^.+@.+$/i'.
     *
     * Note: When using the regex / not_regex patterns, it may be necessary to specify
     * rules in an array instead of using pipe delimiters, especially if the regular
     * expression contains a pipe character.
     *
     * @param  string  $pattern
     * @param  string|null  $message
     * @return $this
     */
    public function regex(string $pattern, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$pattern], $message);
    }

    /**
     * Add rule "required" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function required_condition($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->required($message) : $this;
    }

    /**
     * The field under validation must be present in the input data and not empty.
     * A field is considered "empty" if one of the following conditions are true.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function required(string $message = null): static
    {
        if ($this->print_front) {
            $this->is_required();
        }

        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "required" if condition is true.
     *
     * @param $condition
     * @param  array  $params
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function required_if_condition($condition, array $params, string $message = null): InputGroupComponent
    {
        return $condition ? $this->required_if($params, $message) : $this;
    }

    /**
     * The field under validation must be present and not empty if the anotherfield
     * field is equal to any value.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_if(array $params, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * Add rule "required_unless" if condition is true.
     *
     * @param $condition
     * @param  array  $params
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function required_unless_if($condition, array $params, string $message = null): InputGroupComponent
    {
        return $condition ? $this->required_unless($params, $message) : $this;
    }

    /**
     * The field under validation must be present and not empty unless the anotherfield
     * field is equal to any value.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_unless(array $params, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * Add rule "required_with" if condition is true.
     *
     * @param $condition
     * @param  array  $params
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function required_with_if($condition, array $params, string $message = null): InputGroupComponent
    {
        return $condition ? $this->required_with($params, $message) : $this;
    }

    /**
     * The field under validation must be present and not empty only if any of the other
     * specified fields are present.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_with(array $params, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * Add rule "required_with_all" if condition is true.
     *
     * @param $condition
     * @param  array  $params
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function required_with_all_if($condition, array $params, string $message = null): InputGroupComponent
    {
        return $condition ? $this->required_with_all($params, $message) : $this;
    }

    /**
     * The field under validation must be present and not empty only if all of the other
     * specified fields are present.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_with_all(array $params, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * Add rule "required_without" if condition is true.
     *
     * @param $condition
     * @param  array  $params
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function required_without_if($condition, array $params, string $message = null): InputGroupComponent
    {
        return $condition ? $this->required_without($params, $message) : $this;
    }

    /**
     * The field under validation must be present and not empty only when any of the other
     * specified fields are not present.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_without(array $params, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * Add rule "required_without_all" if condition is true.
     *
     * @param $condition
     * @param  array  $params
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function required_without_all_if($condition, array $params, string $message = null): InputGroupComponent
    {
        return $condition ? $this->required_without_all($params, $message) : $this;
    }

    /**
     * The field under validation must be present and not empty only when all of the other
     * specified fields are not present.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_without_all(array $params, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * Add rule "same" if condition is true.
     *
     * @param $condition
     * @param  string  $field
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function same_if($condition, string $field, string $message = null): InputGroupComponent
    {
        return $condition ? $this->same($field, $message) : $this;
    }

    /**
     * The given field must match the field under validation.
     *
     * @param  string  $field
     * @param  string|null  $message
     * @return $this
     */
    public function same(string $field, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$field], $message);
    }

    /**
     * Add rule "size" if condition is true.
     *
     * @param $condition
     * @param  int  $value
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function size_if($condition, int $value, string $message = null): InputGroupComponent
    {
        return $condition ? $this->size($value, $message) : $this;
    }

    /**
     * The field under validation must have a size matching the given value.
     * For string data, value corresponds to the number of characters. For numeric data,
     * value corresponds to a given integer value (the attribute must also have the
     * numeric or integer rule). For an array, size corresponds to the count of the
     * array. For files, size corresponds to the file size in kilobytes.
     *
     * @param  int  $value
     * @param  string|null  $message
     * @return $this
     */
    public function size(int $value, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * Add rule "starts_with" if condition is true.
     *
     * @param $condition
     * @param  array  $params
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function starts_with_if($condition, array $params, string $message = null): InputGroupComponent
    {
        return $condition ? $this->starts_with($params, $message) : $this;
    }

    /**
     * The field under validation must start with one of the given values.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function starts_with(array $params, string $message = null): static
    {
        return $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * Add rule "string" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function string_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->string($message) : $this;
    }

    /**
     * The field under validation must be a string. If you would like to allow
     * the field to also be null, you should assign the nullable rule to the field.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function string(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "timezone" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function timezone_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->timezone($message) : $this;
    }

    /**
     * The field under validation must be a valid timezone identifier according
     * to the timezone_identifiers_list PHP function.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function timezone(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "unique" if condition is true.
     *
     * @param $condition
     * @param  string  $table
     * @param  string|null  $column
     * @param $except
     * @param $idColumn
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function unique_if(
        $condition,
        string $table,
        string $column = null,
        $except = null,
        $idColumn = null,
        string $message = null
    ): InputGroupComponent {
        return $condition ? $this->unique($table, $column, $except, $idColumn, $message) : $this;
    }

    /**
     * The field under validation must not exist within the given database table.
     *
     * @param  string  $table
     * @param  string|null  $column
     * @param  null  $except
     * @param  null  $idColumn
     * @param  string|null  $message
     * @return $this
     */
    public function unique(
        string $table,
        string $column = null,
        $except = null,
        $idColumn = null,
        string $message = null
    ): static {
        return $this->_rule(__FUNCTION__, [$table, $column, $except, $idColumn], $message);
    }

    /**
     * Add rule "url" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function url_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->url($message) : $this;
    }

    /**
     * The field under validation must be a valid URL.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function url(string $message = null): static
    {
        if ($this->print_front) {
            $this->is_url();
        }

        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * Add rule "uuid" if condition is true.
     *
     * @param $condition
     * @param  string|null  $message
     * @return InputGroupComponent
     */
    public function uuid_if($condition, string $message = null): InputGroupComponent
    {
        return $condition ? $this->uuid($message) : $this;
    }

    /**
     * The field under validation must be a valid RFC 4122 (version 1, 3, 4, or 5)
     * universally unique identifier (UUID).
     *
     * @param  string|null  $message
     * @return $this
     */
    public function uuid(string $message = null): static
    {
        return $this->rule(__FUNCTION__, $message);
    }
}
