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
class Madeam_Exception extends Exception {

  const THROW_VIEW_MISSING = 100;

  const THROW_CONTORLLER_MISSING = 101;

  const THROW_ACTION_MISSING = 102;

  const THROW_CLASS_MISSING = 103;

  const THROW_FILE_MISSING = 104;

  const THROW_METHOD_MISSING = 105;

  public function __construct($message, $code = 0) {
    $date = date('M d o H:i:s');
    $file = basename($this->getFile());
    $line = $this->getLine();
    $exception = substr(get_class($this), 17);
    //$message = sprintf("%1$.20s | %2$-28s | %3$-4s | %4$-10s | %5$0s", $date, $file, $line, $exception, $message);
    parent::__construct($message, $code);
  }

  public function setMessage($message) {
    $this->message = $message;
  }

  public function setLine($line) {
    $this->line = $line;
  }

  public function setFile($file) {
    $this->file = $file;
  }
}
