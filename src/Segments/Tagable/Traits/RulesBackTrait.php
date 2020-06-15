<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits;

use Lar\LteAdmin\Controllers\Controller;

/**
 * Trait FieldRulesTrait
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait RulesBackTrait {

    /**
     * The field under validation must be yes, on, 1, or true.
     * This is useful for validating "Terms of Service" acceptance.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function accepted(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
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
    public function active_url(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be a value after a given date.
     * The dates will be passed into the strtotime PHP function.
     *
     * Instead of passing a date string to be evaluated by strtotime,
     * you may specify another field to compare against the date
     *
     * @param $date
     * @param  string|null  $message
     * @return $this
     */
    public function after(string $date, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$date], $message);
    }

    /**
     * The field under validation must be a value after or equal to the
     * given date. For more information, see the after rule.
     *
     * @param $date
     * @param  string|null  $message
     * @return $this
     */
    public function after_or_equal(string $date, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$date], $message);
    }

    /**
     * The field under validation must be entirely alphabetic characters.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function alpha(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation may have alpha-numeric characters, as
     * well as dashes and underscores.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function alpha_dash(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be entirely alpha-numeric characters.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function alpha_num(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be a PHP array.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function array(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * Stop running validation rules after the first validation failure.
     *
     * @return $this
     */
    public function bail()
    {
        return  $this->rule(__FUNCTION__);
    }

    /**
     * The field under validation must be a value preceding the given date.
     * The dates will be passed into the PHP strtotime function. In addition,
     * like the after rule, the name of another field under validation may
     * be supplied as the value of date.
     *
     * @param $date
     * @param  string|null  $message
     * @return $this
     */
    public function before(string $date, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$date], $message);
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
    public function before_or_equal(string $date, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$date], $message);
    }

    /**
     * The field under validation must have a size between the given min and max.
     * Strings, numerics, arrays, and files are evaluated in the same fashion
     * as the size rule.
     *
     * @param string|int $min
     * @param string|int $max
     * @param  string|null  $message
     * @return $this
     */
    public function between($min, $max, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$min,$max], $message);
    }

    /**
     * The field under validation must be able to be cast as a boolean.
     * Accepted input are true, false, 1, 0, "1", and "0".
     *
     * @param  string|null  $message
     * @return $this
     */
    public function boolean(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must have a matching field of foo_confirmation.
     * For example, if the field under validation is password, a matching
     * password_confirmation field must be present in the input.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function confirmed(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be a valid, non-relative date according
     * to the strtotime PHP function.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function date(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be equal to the given date. The dates
     * will be passed into the PHP strtotime function.
     *
     * @param  string  $date
     * @param  string|null  $message
     * @return $this
     */
    public function date_equals(string $date, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$date], $message);
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
    public function date_format(string $format, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$format], $message);
    }

    /**
     * The field under validation must have a different value than field.
     *
     * @param  string  $field
     * @param  string|null  $message
     * @return $this
     */
    public function different(string $field, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$field], $message);
    }

    /**
     * The field under validation must be numeric and must have an
     * exact length of value.
     *
     * @param  string  $value
     * @param  string|null  $message
     * @return $this
     */
    public function digits(string $value, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * The field under validation must be numeric and must have a length
     * between the given min and max.
     *
     * @param string|int $min
     * @param string|int $max
     * @param  string|null  $message
     * @return $this
     */
    public function digits_between($min, $max, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$min, $max], $message);
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
    public function dimensions(array $params, string $message = null)
    {
        return  $this->_n_rule(__FUNCTION__, $params, $message);
    }

    /**
     * When working with arrays, the field under validation must not have
     * any duplicate values.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function distinct(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be formatted as an e-mail address.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function email(string $message = null)
    {
        $this->_front_rule_email();
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must end with one of the given values.
     *
     * @param  array  $values
     * @param  string|null  $message
     * @return $this
     */
    public function ends_with(array $values, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $values, $message);
    }

    /**
     * The field under validation must exist on a given database table.
     *
     * @param  string  $table
     * @param  string|null  $column
     * @param  string|null  $message
     * @return $this
     */
    public function exists(string $table, string $column = null, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$table, $column], $message);
    }

    /**
     * The field under validation must be a successfully uploaded file.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function file(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must not be empty when it is present.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function filled(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
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
    public function gt(string $field, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$field], $message);
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
    public function gte(string $field, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$field], $message);
    }

    /**
     * The file under validation must be an image
     * (jpeg, png, bmp, gif, svg, or webp)
     *
     * @param  string|null  $message
     * @return $this
     */
    public function image(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be included in the given list of values.
     * Since this rule often requires you to implode an array.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function in(array $params, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * The field under validation must exist in anotherfield's values.
     *
     * @param  string  $field
     * @param  string|null  $message
     * @return $this
     */
    public function in_array(string $field, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$field], $message);
    }

    /**
     * The field under validation must be an IP address.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function ip(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be an IPv4 address.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function ipv4(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be an IPv6 address.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function ipv6(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be a valid JSON string.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function json(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
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
    public function lt(string $field, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$field], $message);
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
    public function lte(string $field, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$field], $message);
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
    public function max(int $value, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * The file under validation must match one of the given MIME types
     *
     * @param  array  $mimetypes
     * @param  string|null  $message
     * @return $this
     */
    public function mimetypes(array $mimetypes, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $mimetypes, $message);
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
    public function mimes(array $mimes, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $mimes, $message);
    }

    /**
     * The field under validation must have a minimum value. Strings, numerics,
     * arrays, and files are evaluated in the same fashion as the size rule.
     *
     * @param  int  $value
     * @param  string|null  $message
     * @return $this
     */
    public function min(int $value, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * The field under validation must be an integer.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function integer(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must not be included in the given list of values
     *
     * @param  array  $values
     * @param  string|null  $message
     * @return $this
     */
    public function not_in(array $values, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $values, $message);
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
    public function not_regex(string $pattern, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$pattern], $message);
    }

    /**
     * The field under validation may be null. This is particularly useful when validating
     * primitive such as strings and integers that can contain null values.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function nullable(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be numeric.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function numeric(string $message = null)
    {
        $this->_front_rule_number();
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must match the authenticated user's password.
     * You may specify an authentication guard using the rule's first parameter.
     *
     * @param  string  $guard
     * @param  string|null  $message
     * @return $this
     */
    public function password(string $guard, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$guard], $message);
    }

    /**
     * The field under validation must be present in the input data but can be empty.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function present(string $message = null)
    {
        return  $this->rule(__FUNCTION__, $message);
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
    public function regex(string $pattern, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$pattern], $message);
    }

    /**
     * The field under validation must be present in the input data and not empty.
     * A field is considered "empty" if one of the following conditions are true
     *
     * @param  string|null  $message
     * @return $this
     */
    public function required(string $message = null)
    {
        $this->_front_rule_required();
        return  $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be present and not empty if the anotherfield
     * field is equal to any value.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_if(array $params, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * The field under validation must be present and not empty unless the anotherfield
     * field is equal to any value.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_unless(array $params, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * The field under validation must be present and not empty only if any of the other
     * specified fields are present.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_with(array $params, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * The field under validation must be present and not empty only if all of the other
     * specified fields are present.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_with_all(array $params, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * The field under validation must be present and not empty only when any of the other
     * specified fields are not present.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_without(array $params, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * The field under validation must be present and not empty only when all of the other
     * specified fields are not present.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function required_without_all(array $params, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * The given field must match the field under validation.
     *
     * @param  string  $field
     * @param  string|null  $message
     * @return $this
     */
    public function same(string $field, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$field], $message);
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
    public function size(int $value, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, [$value], $message);
    }

    /**
     * The field under validation must start with one of the given values.
     *
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    public function starts_with(array $params, string $message = null)
    {
        return  $this->_rule(__FUNCTION__, $params, $message);
    }

    /**
     * The field under validation must be a string. If you would like to allow
     * the field to also be null, you should assign the nullable rule to the field.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function string(string $message = null)
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be a valid timezone identifier according
     * to the timezone_identifiers_list PHP function.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function timezone(string $message = null)
    {
        return $this->rule(__FUNCTION__, $message);
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
    public function unique(string $table, string $column = null, $except = null, $idColumn = null, string $message = null)
    {
        return $this->_rule(__FUNCTION__, [$table,$column,$except,$idColumn], $message);
    }

    /**
     * The field under validation must be a valid URL.
     *
     * @param  string|null  $message
     * @return $this
     */
    public function url(string $message = null)
    {
        $this->_front_rule_url();
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * The field under validation must be a valid RFC 4122 (version 1, 3, 4, or 5)
     * universally unique identifier (UUID).
     *
     * @param  string|null  $message
     * @return $this
     */
    public function uuid(string $message = null)
    {
        return $this->rule(__FUNCTION__, $message);
    }

    /**
     * @param  \Closure|string|object  $rule
     * @param  string|null  $message
     * @return $this
     */
    public function rule($rule, string $message = null)
    {
        if ($this->admin_controller) {

            /** @var Controller $controller */
            $controller = $this->controller;
            $controller::$rules[$this->path][] = $rule;
            if ($message && is_string($rule)) {
                $controller::$rule_messages["{$this->path}.{$rule}"] = $message;
            }
        }

        return $this;
    }

    /**
     * @param  string  $rule
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    protected function _rule(string $rule, array $params = [], string $message = null) {

        $params = trim(implode(',', $params), ',');
        if ($params) { $rule .= ":{$params}"; }
        return $this->rule($rule, $message);
    }

    /**
     * @param  string  $rule
     * @param  array  $params
     * @param  string|null  $message
     * @return $this
     */
    protected function _n_rule(string $rule, array $params = [], string $message = null) {

        $new_params = [];

        foreach ($params as $key => $param) {

            $new_params[] = "{$key}={$param}";
        }

        return $this->_rule($rule, $new_params, $message);
    }
}