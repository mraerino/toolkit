<?php

/**
 *
 * V
 *
 * Validators
 *
 * @package   Kirby Toolkit
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class V {

  // an array with all installed validators
  static public $validators = array();

  /**
   * Return the list of all validators
   *
   * @return array
   */
  static public function validators() {
    return static::$validators;
  }

  /**
   * Calls an installed validator and passes all arguments
   *
   * @param string $method
   * @param array $arguments
   * @return boolean
   */
  static public function __callStatic($method, $arguments) {

    // check for missing validators
    if(!isset(static::$validators[$method])) throw new Exception('The validator does not exist: ' . $method);

    return call_user_func_array(static::$validators[$method], $arguments);

  }

}


/**
 * Default set of validators
 */
v::$validators = array(
  'accepted' => function($value) {
    return v::in($value, array(1, true, 'yes', 'true', '1', 'on'));
  },
  'alpha' => function($value) {
    return v::match($value, '/^([a-z])+$/i');
  },
  'alphanum' => function($value) {
    return v::match($value, '/^[a-z0-9]+$/i');
  },
  'between' => function($value, $min, $max) {
    return v::min($value, $min) and v::max($value, $max);
  },
  'date' => function($value) {
    $time = strtotime($value);
    if(!$time) return false;

    $year  = date('Y', $time);
    $month = date('m', $time);
    $day   = date('d', $time);

    return checkdate($month, $day, $year);

  },
  'different' => function($value, $other) {
    return $value !== $other;
  },
  'email' => function($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
  },
  'filename' => function($value) {
    return v::match($value, '/^[a-z0-9@._-]+$/i') and v::min($value, 2);
  },
  'in' => function($value, $in) {
    return in_array($value, $in, true);
  },
  'integer' => function($value) {
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
  },
  'ip' => function($value) {
    return filter_var($value, FILTER_VALIDATE_IP) !== false;
  },
  'match' => function($value, $preg) {
    return preg_match($preg, $value) > 0;
  },
  'max' => function($value, $max) {
    return size($value) <= $max;
  },
  'min' => function($value, $min) {
    return size($value) >= $min;
  },
  'notIn' => function($value, $notIn) {
    return !v::in($value, $notIn);
  },
  'num' => function($value) {
    return is_numeric($value);
  },
  'required' => function($key, $array) {
    return !empty($array[$key]);
  },
  'same' => function($value, $other) {
    return $value === $other;
  },
  'size' => function($value, $size) {
    return size($value) == $size;
  },
  'url' => function($value) {
    // In search for the perfect regular expression: https://mathiasbynens.be/demo/url-regex
    $regex = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iu';
    return preg_match($regex, $value);    
  }
);