<?php namespace Forge;

/**
 * Very simple view loader
 *
 * @author Ashley Wilson
 */

class ViewNotFound extends \Exception {};

class View
{
   /**
    * Load a view
    * <code>
    * \Forge\View::load('erorr.404');
    *
    * \Forge\View::load('user.profile', array('username' => 'Foo', 'password' => 'bar'));
    * </code>
    *
    * @param string $view View location
    * @param array $data Data to be passed to a view
    */
   public static function load($view, $data = array())
   {
      if (preg_match("~^(.*)\.tpl$~", $view)) self::makeTpl($view, $data);

      $view = str_replace('.', '/', $view);
      
      if (strstr($view, '::'))
      {
         $exp = explode('::', $view, 2);
         $path = Module::path(array_get($exp, 0)) .'views' .DIRECTORY_SEPARATOR . array_get($exp, 1) .'.php';
      } else {
         $path = Core::path('app') .'views'. DIRECTORY_SEPARATOR . $view .'.php';
      }

      if (file_exists($path) === false) throw new ViewNotFound("No view found at path [$path]");

      // Capture all output
      ob_start();

      // Turn the data $key/$val into starndard PHP vars
      extract((array) $data, EXTR_OVERWRITE);

      require ($path);

      $file_output = ob_get_contents();
      ob_end_clean();

      if (ob_get_level() > 1)
      {
         // If there are multiple levels of object buffering then it's most likely a
         // nested view making the call.. Print the output as it will be captured
         echo $file_output;
         return;
      }

      return $file_output;
   }

   /**
    *  Back compatibility
    *  load the data on to the RC object
    *  the v1 template engine is restricted to the locations
    *  from where it is able to load the templates
    */

   private static function makeTpl($view, array $data = array())
   {
      global $RC;
      $RC->view->current_module = strtolower(get_class());
      $RC->output = array_merge($RC->output, $data['tpl_data']);
      $RC->output['title'] = $data['tpl_outline']["page_title"]; 


      if ($data['tpl_outline']['return']) {
	      return $RC->view->show($view, 
                                     $data['tpl_outline']['page_module'], 
                                     $data['tpl_outline']['template_name'], 
                                     $data['tpl_outline']['template_entity'], 
                                     $data['tpl_outline']['page_entity'], 
                                     $data['tpl_outline']['use_cache'], 
                                     $data['tpl_outline']['return']);
      }
      
      $RC->view->show($view,
		      $data['tpl_outline']['page_module'],
		      $data['tpl_outline']['template_name'],
		      $data['tpl_outline']['template_entity'],
		      $data['tpl_outline']['page_entity'],
		      $data['tpl_outline']['use_cache'],
		      $data['tpl_outline']['return']);

   }
}
