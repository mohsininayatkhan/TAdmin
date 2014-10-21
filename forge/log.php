<?php namespace Forge;

/**
 * Re-made Forge logger class
 * 
 * @author Ashley Wilson
 */
class Log
{
   const FATAL    = 10; // fatal errors
   const CRITICAL = 20; // critical errors
   const ERROR    = 30; // general errors
   const WARNING  = 40; // warning
   const NOTICE   = 50; // notice
   const DEBUG    = 100; // debug mode (write all errors and messages)
   
   private $file;
   
   private static $log_id;
   private static $writer;
   
   /**
    * Create the file handle - A daily log file will be written to avoid large file sizes!
    * @throws \Excetpion
    */
   public function __construct()
   {
      $path = Core::path('logs'). Config::get('error.log.file', date('Ymd').'log');
      $this->file = fopen($path, 'a');
      
      if (is_resource($this->file) === false) throw new \Exception("Unable to open log file [$path]");
   }
   
   /**
    * Save the formatted string. Fire an event here to allow other writers to catch the message
    * @param string $str
    */
   public function save($str)
   {
      Event::fire("log.write", array('line' => $str));
      
      fwrite($this->file, $str);
   }
   
   public static function open($logFile) {
      $path = Core::path('logs') . $logFile;
      
      if (File::exists($path) === false) throw new \Exception("Cannot find log file [{$logFile}]");
      
      $content = File::get($path);
      
      $data = explode("\n\n", $content);
      
      $output = array();
      
      foreach ($data as $line) {
          if (!$line) continue;
          $split = explode('->', $line, 3);
          
          $output[] = array(
              'date' => $split[0],
              'message' => array_get($split, 1),
              'trace' => array_get($split, 2)
          );
      }
      
      return array_reverse($output);
   }
   
   /**
    * Cleanup - Close the file handle when done
    */
   public function __destruct()
   {
      if (is_resource($this->file))
      {
         fclose($this->file);
      }
   }
   
   /**
    * Write a message to the log
    * @param const $level Log constant
    * @param string|array $message Item to log
    * @param string $sender User we're logging against
    * @return bool
    */
   public static function write($level, $message, $sender = null)
   {
      if ($level < Config::get('error.log.level')) return;
      
      if (is_null(self::$writer)) self::$writer = new static;
      //if (is_null(self::$log_id)) self::$log_id = substr((int)(microtime()*100000+time()), -6);
      if (is_null(self::$log_id)) {
          if (\Forge\Auth::check()) {
              self::$log_id = \Forge\Auth::user()->user_id;
              $sender = \Forge\Auth::user()->fullname;
          } else {
              self::$log_id = substr((int)(microtime()*100000+time()), -6);
          }
      }
      if (is_null($sender)) $sender = Config::get('error.log.sender');
      
      $str = Config::get('error.log.format') . print_r($message, true) ."\n\n";
      
      $search = array(
         '/{sender}/',
         '/{LEVEL}/i',
         '/{ID}/i',
         '/{IP}/i',
         '/{dTS}/i',
         '/{dR}/i',
         '/{dY}/i',
         '/{dM}/i',
         '/{dD}/i',
         '/{dH}/i',
         '/{dI}/i',
         '/{dS}/i',
         '/{dZ}/i',
         '/{SRC}/i',
      );
      
      $replace = array(
         $sender,
         $level,
         self::$log_id,
         Url::full(),
         time(),
         date('r'),
         date('Y'),
         date('M'),
         date('d'),
         date('H'),
         date('i'),
         date('s'),
         date('O'),
         $sender,
      );
      
      $str = preg_replace($search, $replace, $str);
      
      self::$writer->save($str);
      
      return true;
   }
}