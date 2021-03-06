<?php
class Madeam_Console_Script_Locs extends Madeam_Console_Script {
  
  /**
   * undocumented 
   *
   * @author Joshua Davey
   */
  public function all() {
    $totalLocs =  (int) $this->_countLOCs(Madeam::$pathToRoot, array(
      Madeam::$pathToEtc,
      Madeam::$pathToRoot . 'README',
      Madeam::$pathToRoot . 'LICENSE',
      Madeam::$pathToRoot . 'index.php',
      Madeam::$pathToRoot . 'env.php',
      Madeam::$pathToPub . 'index.php',
      Madeam::$pathToPub . 'dispatcher.php'
    ));
    Madeam_Console_CLI::out(sprintf("------------------------------"));
    Madeam_Console_CLI::out(sprintf("%-20s%10d", 'Project', $totalLocs));
    Madeam_Console_CLI::out(sprintf("------------------------------"));
  }
  
  /**
   * undocumented 
   *
   * @author Joshua Davey
   */
  public function madeam() {
    $madeamLocs = (int) $this->_countLOCs(Madeam::$pathToLib . 'Madeam' . DS . 'src');
    Madeam_Console_CLI::out(sprintf("------------------------------"));
    Madeam_Console_CLI::out(sprintf("%-20s%10d", 'Madeam', $madeamLocs));
    
    $madeamTestLocs = (int) $this->_countLOCs(Madeam::$pathToLib . 'Madeam' . DS . 'tests' . DS);
    Madeam_Console_CLI::out(sprintf("------------------------------"));
    Madeam_Console_CLI::out(sprintf("%-20s%10d", 'Madeam Tests', $madeamTestLocs));
    
    $totalLocs = $madeamLocs + $madeamTestLocs;
    Madeam_Console_CLI::out(sprintf("=============================="));
    Madeam_Console_CLI::out(sprintf("%-20s%10d", 'Total', $totalLocs));
  }
  
  /**
   * undocumented 
   *
   * @author Joshua Davey
   */
  public function app() {
    $appLocs = (int) $this->_countLOCs(Madeam::$pathToApp . 'src' . DS);
    Madeam_Console_CLI::out(sprintf("------------------------------"));
    Madeam_Console_CLI::out(sprintf("%-20s%10d", 'Application', $appLocs));
    
    $testLocs = (int) $this->_countLOCs(Madeam::$pathToApp . 'tests' . DS);
    Madeam_Console_CLI::out(sprintf("------------------------------"));
    Madeam_Console_CLI::out(sprintf("%-20s%10d", 'Tests', $testLocs));
    
    $libLocs = (int) $this->_countLOCs(Madeam::$pathToLib, array(
      Madeam::$pathToLib . 'Madeam' . DS
    ));
    Madeam_Console_CLI::out(sprintf("------------------------------"));
    Madeam_Console_CLI::out(sprintf("%-20s%10d", 'Library', $libLocs));
    
    $staticLocs = (int) $this->_countLOCs(Madeam::$pathToPub, array(
      Madeam::$pathToPub . 'dispatcher.php',
      Madeam::$pathToPub . 'index.php'
    ));
    Madeam_Console_CLI::out(sprintf("------------------------------"));
    Madeam_Console_CLI::out(sprintf("%-20s%10d", 'Static', $staticLocs));
    
    $totalLocs = $appLocs + $testLocs + $libLocs + $staticLocs;
    Madeam_Console_CLI::out(sprintf("=============================="));
    Madeam_Console_CLI::out(sprintf("%-20s%10d", 'Total', $totalLocs));
  }
    
  /**
   * undocumented
   * @author Joshua Davey
   */
  private function _countLOCs($path, $ignore = array(), $exts = array('php', 'html', 'xml', 'css', 'js', 'inc')) {
    $locs = 0;
    foreach (new DirectoryIterator($path) as $file) {
      if ($file->isDir()) {
        $filePath = $file->getPathname() . DS;
      } else {
        $filePath = $file->getPathname();
      }
      
      $ext = substr($file->getFilename(), strrpos($file->getFilename(), '.') + 1);
      
      if (!$file->isLink() && !$file->isDot() && substr($file->getFilename(), 0, 1) != '.' && !in_array($filePath, $ignore)) {
        if ($file->isDir()) {
          $locs += $this->_countLocs($filePath);
        } elseif (in_array($ext, $exts)) {
          $lines = file($file->getPathname(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
          $locs += count($lines);
        }
      }
    }
    return $locs;
  }
  
}