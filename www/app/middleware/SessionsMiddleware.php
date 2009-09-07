<?php
class SessionsMiddleware extends madeam\Middleware {
  
  static public function beforeRequest($request) {
    
    // include madeam's session class
    require 'Madeam/src/madeam/Session.php';
    
    // check if _sessionid exists. If it doesn't set it.
    if (!isset($request['_sessionid'])) {
      $request['_sessionid'] = madeam\Session::key();
      setcookie('_sessionid', $request['_sessionid']);
    }
    
    // configure sessions
    session_save_path(madeam\Framework::$pathToProject . 'etc/session');
    
    // initiate session
    madeam\Session::start($request['_sessionid']);
    
    return $request;
  }
  
}