<?php

/**
 * Madeam :  Rapid Development MVC Framework <http://www.madeam.com/>
 * Copyright (c)	2006, Joshua Davey
 *								24 Ridley Gardens, Toronto, Ontario, Canada
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright		Copyright (c) 2006, Joshua Davey
 * @link				http://www.madeam.com
 * @package			madeam
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class Madeam_Cache {

  public static $path = false;

  public static $openCaches = array();

  /**
   * Enter description here...
   *
   * @param unknown_type $id
   * @param unknown_type $life_time
   * @return unknown
   */
  public static function read($id, $life_time = 0, $ignore = false) {
    
    if ($ignore === true) { return false; }
    
    // check registry first
    if (Madeam_Registry::exists($id)) {
      return Madeam_Registry::get($id);
    }

    // set file name
    $file = self::$path . $id;
    if (file_exists($file)) {
      if ((time() - filemtime($file)) <= $life_time || $life_time == - 1) {
        // get cache from file and unserialize
        return unserialize(file_get_contents($file));
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  /**
   * Enter description here...
   *
   * @param unknown_type $id
   * @param unknown_type $value
   * @param unknown_type $store_in_registry
   * @return unknown
   */
  public static function save($id, $value, $store_in_registry = false) {
    // store in registry
    if ($store_in_registry === true) {
      Madeam_Registry::set($id, $value);
    }

    // set file name
    $file = self::$path . $id;

    // save serialization to file
    file_put_contents($file, serialize($value));
  }

  /**
   * Enter description here...
   *
   * @param unknown_type $id
   * @param unknown_type $life_time
   * @return unknown
   */
  public static function start($id, $life_time = 0) {
    // check if inline cache is enabled
    if (Madeam_Config::get('cache_inline') === false) {
      return false;
    }

    if (! $cache = self::read($id, $life_time)) {
      ob_start();
      self::$openCaches[] = $id;
      return false;
    } else {
      echo $cache;
      return true;
    }
  }

  /**
   * Enter description here...
   *
   * @return unknown
   */
  public static function stop() {
    // check if inline cache is enabled
    if (Madeam_Config::get('cache_inline') === false) {
      return false;
    }

    $id = array_shift(self::$openCaches);
    $cache = ob_get_contents();
    self::save($id, $cache);
    //ob_clean();
    ob_end_clean();
    echo $cache;
  }

  /**
   * Enter description here...
   *
   * @param unknown_type $id
   */
  public static function clear($id) {
    // set file name
    $file = self::$path . $id;

    // save serialization to file
    file_put_contents($file, null);
  }

  /**
   * Check to see if a cache exists
   *
   * @param string $id
   * @return boolean
   */
  public static function check($id) {
    // check registry first
    if (Madeam_Registry::get($id)) {
      return true;
    }

    // check file system cache
    $file = self::$path . $id;
    if (file_exists($file)) {
      return true;
    }
    return false;
  }
}
