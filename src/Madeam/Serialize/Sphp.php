<?php
class Madeam_Serialize_Sphp {
  
  public static function encode($data) {
    return serialize($data);
  }
  
  public static function decode($data) {
    return unserialize($data);
  }
  
}