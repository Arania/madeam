<?php
require_once 'Bootstrap.php';
class Madeam_EnvironmentTest extends PHPUnit_Framework_TestCase {
 
  public function testPHPVersionIs520OrGreater() {
    $version = explode('.', phpversion());
    
    if ((int) $version[0] == 5 && (int) $version[1] >= 2) {
      $this->assertTrue(true);
    } else {
      $this->fail('Invalid version of PHP');
    }
  }
  
  public function testRequestOrder() {
    //$this->assertEquals('GPC', ini_get('request_order')); // PHP 5.3
    //$this->assertEquals('GPC', ini_get('gpc_order'), 'The get, post, cookie order should equal "GPC"');
  }
  
}