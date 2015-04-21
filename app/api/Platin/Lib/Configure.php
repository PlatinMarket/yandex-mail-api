<?php

namespace Platin\Lib;

use Platin\Lib\Hash;

class Configure {

/**
 * Array of values currently stored in Configure.
 *
 * @var array
 */
  protected static $_values = array(
    'debug' => 0
  );

/**
 * Configured reader classes, used to load config files from resources
 *
 * @var array
 * @see Configure::load()
 */
  protected static $_readers = array();

/**
 * Used to store a dynamic variable in Configure.
 *
 * Usage:
 * ```
 * Configure::write('One.key1', 'value of the Configure::One[key1]');
 * Configure::write(array('One.key1' => 'value of the Configure::One[key1]'));
 * Configure::write('One', array(
 *     'key1' => 'value of the Configure::One[key1]',
 *     'key2' => 'value of the Configure::One[key2]'
 * );
 *
 * Configure::write(array(
 *     'One.key1' => 'value of the Configure::One[key1]',
 *     'One.key2' => 'value of the Configure::One[key2]'
 * ));
 * ```
 *
 * @param string|array $config The key to write, can be a dot notation value.
 * Alternatively can be an array containing key(s) and value(s).
 * @param mixed $value Value to set for var
 * @return bool True if write was successful
 * @link http://book.cakephp.org/2.0/en/development/configuration.html#Configure::write
 */
  public static function write($config, $value = null) {
    if (!is_array($config)) {
      $config = array($config => $value);
    }

    foreach ($config as $name => $value) {
      self::$_values = Hash::insert(self::$_values, $name, $value);
    }

    if (isset($config['debug']) && function_exists('ini_set')) {
      if (self::$_values['debug']) {
        ini_set('display_errors', 1);
      } else {
        ini_set('display_errors', 0);
      }
    }
    return true;
  }

/**
 * Used to read information stored in Configure. It's not
 * possible to store `null` values in Configure.
 *
 * Usage:
 * ```
 * Configure::read('Name'); will return all values for Name
 * Configure::read('Name.key'); will return only the value of Configure::Name[key]
 * ```
 *
 * @param string $var Variable to obtain. Use '.' to access array elements.
 * @return mixed value stored in configure, or null.
 * @link http://book.cakephp.org/2.0/en/development/configuration.html#Configure::read
 */
  public static function read($var = null) {
    if ($var === null) {
      return self::$_values;
    }
    return Hash::get(self::$_values, $var);
  }

/**
 * Returns true if given variable is set in Configure.
 *
 * @param string $var Variable name to check for
 * @return bool True if variable is there
 */
  public static function check($var = null) {
    if (empty($var)) {
      return false;
    }
    return Hash::get(self::$_values, $var) !== null;
  }

/**
 * Used to delete a variable from Configure.
 *
 * Usage:
 * ```
 * Configure::delete('Name'); will delete the entire Configure::Name
 * Configure::delete('Name.key'); will delete only the Configure::Name[key]
 * ```
 *
 * @param string $var the var to be deleted
 * @return void
 * @link http://book.cakephp.org/2.0/en/development/configuration.html#Configure::delete
 */
  public static function delete($var = null) {
    self::$_values = Hash::remove(self::$_values, $var);
  }

/**
 * Add a new reader to Configure. Readers allow you to read configuration
 * files in various formats/storage locations. CakePHP comes with two built-in readers
 * PhpReader and IniReader. You can also implement your own reader classes in your application.
 *
 * To add a new reader to Configure:
 *
 * `Configure::config('ini', new IniReader());`
 *
 * @param string $name The name of the reader being configured. This alias is used later to
 *   read values from a specific reader.
 * @param ConfigReaderInterface $reader The reader to append.
 * @return void
 */
  public static function config($name, ConfigReaderInterface $reader) {
    self::$_readers[$name] = $reader;
  }

/**
 * Gets the names of the configured reader objects.
 *
 * @param string $name Name to check. If null returns all configured reader names.
 * @return array Array of the configured reader objects.
 */
  public static function configured($name = null) {
    if ($name) {
      return isset(self::$_readers[$name]);
    }
    return array_keys(self::$_readers);
  }

/**
 * Remove a configured reader. This will unset the reader
 * and make any future attempts to use it cause an Exception.
 *
 * @param string $name Name of the reader to drop.
 * @return bool Success
 */
  public static function drop($name) {
    if (!isset(self::$_readers[$name])) {
      return false;
    }
    unset(self::$_readers[$name]);
    return true;
  }

  public static function self(){
    return self;
  }

}
