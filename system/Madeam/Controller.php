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
class Madeam_Controller {
	public    $output      = null;
  
  protected $scaffold     = false;
  protected $layout       = 'master';  
  protected $represent    = false;
  
  protected $view;
  protected $isRendered   = false;
  protected $viewParser;

  protected $scaffoldController;
  protected $scaffoldKey;

  public function __construct($params) {
    // load represented model
    if (is_string($this->represent)) {
      $this->represent = Madeam_Inflector::model_nameize($this->represent);
    }

    // assign params
    foreach ($params as $name => $value) {
      if (!isset($this->$name)) {
        $this->$name = $value;
      }
    }

    // scaffold config
    if ($this->scaffold == true && $this->represent == true) {
      $this->scaffoldController  = $this->controller;
      $this->scaffoldKey         = $this->{$this->represent}->get_primary_key();
    }

    // set view
    $this->setView($this->controller . '/' . $this->action);

    // set layout
    // check to see if the layout param is set to true or false. If it's false then don't render the layout
    if ($this->showLayout == '0' || $this->showLayout == 'false') {
      $this->setLayout(false);
    } else {
      $this->setLayout($this->layout);
    }
  }


  /**
   * Handle variable gets.
   * This magic method exists to handle instances of models and components when they're called.
   * If the instance of a model or component doesn't exist we create it here.
   * This is so we don't need to pre-load all of them.
   *
   * @param string $name
   * @return object
   */
  public function __get($name) {
    $match = array();
    if (preg_match("/^[A-Z]{1}/", $name, $match)) {
       // set model class name
      $comp_class = 'Component_' . $name;

      // create component instance
      $inst = new $comp_class($this);
      $this->$name = $inst;
      return $inst;
    }
  }

  public function __call($name, $args) {
    if (!file_exists($this->view)) {
      throw new Madeam_Exception('Missing Action <b>' . $name . '</b> in <b>' . get_class($this) . '</b> controller', Madeam_Exception::ERR_ACTION_MISSING);
    }
  }

  /**
   * Final methods. (Actions cannot have the same name as these methods)
   * =======================================================================
   */

  final public function callback($callback) {
    return $this->$callback();
  }

  final protected function callAction($uri) {
    return Madeam::callAction($uri);
  }

  final protected function callPartial($partial_path, $data = array(), $start = 0, $limit = false) {
    if (!empty($data)) {
      // internal counter can be accessed in the view
      $_num = $start;

      // get partial name
      $partial = explode('/', $partial_path);
      $partial_name = array_pop($partial);

      // splice array so that it is within the range defined by $start and $limit
      if ($limit !== false) {
        $data = array_splice($data, $start, $limit);
      } else {
        $data = array_splice($data, $start);
      }

      // set variables
      if (is_list($data)) {
        foreach ($data as $key => $$partial_name) {
          $_num++;
          if (count($partial) > 0) {
            include(PATH_TO_VIEW . implode(DS, $partial) . DS . '_' . $partial_name . '.' . $this->format);
          } else {
            include(PATH_TO_VIEW . str_replace('/', DS, $this->controller) . DS . '_' . implode($partial) . '.' . $this->format);
          }
        }
      } else {
        $$partial_name = $data;
        $_num++;
        if (count($partial) > 0) {
          include(PATH_TO_VIEW . implode(DS, $partial) . DS . '_' . $partial_name . '.' . $this->format);
        } else {
          include(PATH_TO_VIEW . str_replace('/', DS, $this->controller) . DS . '_' . implode($partial) . '.' . $this->format);
        }
      }
    }

    return false;
  }

  /**
   * This takes the full path to the view.
   * 
   * For example: "posts/show" and not "show"
   *
   * @param string $view
   */
  final protected function setView($view) {
    $this->view = PATH_TO_VIEW . str_replace('/', DS, low($view)) . '.' . $this->format;
  }

  /**
   * Enter description here...
   *
   * @param string/boolean/array $layouts
   */
  final protected function setLayout($layouts) {
    $this->layout = array();

    if (func_num_args() < 2) {
      if (is_string($layouts)) {
        $this->layout[] = PATH_TO_LAYOUT . $layouts . '.layout.' . $this->format;
      } elseif (is_array($layouts)) {
        foreach ($layouts as $layout) {
          $this->layout[] = PATH_TO_LAYOUT . $layout . '.layout.' . $this->format;
        }
      } else {
        $this->layout = false;
      }
    } else {
      foreach (func_get_args() as $layout) {
        $this->layout[] = PATH_TO_LAYOUT . $layout . '.layout.' . $this->format;
      }
    }
  }

  final protected function set($name, $value) {
    $this->data[$name] = $value;

    /*
    $parser = "parser_' . $this->format;
    if (class_exists($parser)) {
    	if ($this->_parser == false) { $this->_parser = new $parser; }
    	$this->_parser->set($name, $value);
  	}
  	*/
  }

  final protected function render($data = true, $rendered = true) {
    // sometimes the developer may want to tell the view not to render from the controller's action
    if ($data === false) { $this->isRendered = true; }
    
    // consider: checking if it's rendered based on if there is anything in the output buffer? does that make sense?
    if ($this->isRendered === false) {
      // output buffering
      ob_start();

      foreach($this as $key => $value) { $$key = $value; }
      //extract($this->data, EXTR_OVERWRITE); // which one is faster?
      

      if ($data === true) {
        // include view's template file
        if (file_exists($this->view)) {
          include($this->view);
        } else {
          throw new Madeam_Exception('Missing View <b>' . substr($this->view, strlen(PATH_TO_VIEW)) . '</b>', Madeam_Exception::ERR_VIEW_MISSING);
        }
        
				/*
				$parser = $this->format;
				if (method_exists('madeamParser', $parser)) {
					unset($this->data['header_for_layout']);
					unset($this->data['params']);
					madeamParser::$parser($this->view, $this->data);
				}
				*/
				
        // grab result of inclusion
        $content_for_layout = ob_get_contents();
        // clear output
        ob_clean();
      } elseif (is_string($data)) { // this needs to change
        // set $content_for_layout to $data which is just a string
				$content_for_layout = $data;
				/*
				$parser = $this->format;
        if (method_exists('madeamParser', $parser)) {
					$content_for_layout = madeamParser::$parser($this->view, $data);
				} else {
					$content_for_layout = null;
				}
				*/
      }

      // loop through layouts
      // the layouts are rendered in order they are in the array
      if (is_array($this->layout)) {
        foreach ($this->layout as $layout) {
          if ($layout && file_exists($layout)) {
            // include layout if it exists
            include($layout);
          } else {
            // otherwise just output the content
            echo $content_for_layout;
          }

          // get contents of output buffering
          $content_for_layout = ob_get_contents();

          // clean ob
          ob_clean();
        }
      }

      // end ouptut buffering
      ob_end_clean();

      // mark view as rendered
      $this->isRendered = $rendered;

      $this->output = $content_for_layout;
      
      return $this->output;
    }

    return false;
  }

  /**
   * Scaffold Actions
   * =======================================================================
   */

  public function _scaffold_index() {
    include(SCAFFOLD_PATH . $this->scaffold . '/action/index.php');
  }

  public function _scaffold_show() {
    include(SCAFFOLD_PATH . $this->scaffold . '/action/show.php');
  }

  public function _scaffold_add() {
    include(SCAFFOLD_PATH . $this->scaffold . '/action/add.php');
  }

  public function _scaffold_edit() {
    include(SCAFFOLD_PATH . $this->scaffold . '/action/edit.php');
  }

  public function _scaffold_delete() {
    include(SCAFFOLD_PATH . $this->scaffold . '/action/delete.php');
  }


  /**
   * Callback functions
   * =======================================================================
   */

  /* what about re-naming these like this: _beforeAction() or before_action()? */
  /* come up with a better naming convention for these methods */
  protected function beforeAction() {
  }
  
  protected function beforeRender() {
  }

  protected function afterRender() {
  }
}
?>