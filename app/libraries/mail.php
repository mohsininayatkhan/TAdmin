<?php

/**
 * Wrapper for PHPMailer to make sending emails that much simpler.
 * Each method supports method chaining, for ease of use!
 * @author Ashley Wilson
 */
class Mail
{
   /**
    * PHPMailer instance
    *
    * @var object
    */
   private $_mailer;

   private $token;
   private $to_name;
   private $to_email;

   private $from_email;

   private $reference;
   private $customer_id;

   /**
    * Create a new instance and set some defaults
    */
   public function __construct($token = true)
   {
      $this->_mailer = new PHPMailer(true);
      $this->_mailer->IsSMTP();
      $this->_mailer->IsHTML(true);

      // Identify this app - This is super important for bounce messages!
      $this->customHeader('X-MailerApp', 'Nest2.0');

      // Setup some defaults
      $this->host();
      $this->port();

      if ($token) $this->token();
   }

   /**
    * Generate the completed version of the message
    * @access public
    * @return mixed
    */
   public function export()
   {
      $this->_mailer->PreSend();
      return $this->_mailer->GetSentMIMEMessage();
   }

   /**
    * Add a custom token for mail tracking
    * @access public
    * @param int $length
    */
   public function token($length = 32)
   {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $this->token = '';

      for ($i = 0; $i < $length; $i++) {
         $this->token .= $characters[rand(0, strlen($characters) - 1)];
      }

      $this->customHeader('X-Token', $this->token);

      return $this;
   }

   /**
    * Fetch the token
    * @access public
    * @return string
    */
   public function getToken()
   {
      return $this->token;
   }

   /**
    * This is purely for logging!
    * @access public
    * @param int $customer_id
    * @param string $reference
    */
   public function reference($customer_id = '', $reference = '')
   {
      if ($customer_id instanceof \Ad\Sop\Customer) {
          $customer = $customer_id;
      } elseif (is_numeric($customer_id)) {
         $customer = \Ad\Sop\Customer::find($customer_id);
      } else {
         $customer = \Ad\Sop\Customer::where('customer', '=', $customer_id)->first();
      }

      if (is_null($customer)) throw new \Exception("Invalid customer [{$customer_id}]");

      $this->customer_id = $customer->customer_id;
      $this->reference = $reference;

      return $this;
   }

   /**
    * Adds custom header values.
    * @access public
    * @param string $name
    * @param mixed $value
    */
   public function customHeader($name = '', $value = null)
   {
      $this->_mailer->AddCustomHeader($name, $value);

      return $this;
   }

   /**
    * Set the host name
    * @access public
    * @param string $host
    */
   public function host($host = 'localhost')
   {

      if (is_null(\Forge\Config::get('app.mail.host')) === false) {
           $this->_mailer->Host =  \Forge\Config::get('app.mail.host');
      } else {
          $this->_mailer->Host = $host;
      }

      return $this;
   }

   /**
    * Set the port number
    * @access public
    * @param int $port
    */
   public function port($port = 25)
   {
      $this->_mailer->Port = $port;

      return $this;
   }

   /**
    * Set the message body
    * Added decode function to convert non-UTF8 chars
    * @access public
    * @param string $text
    */
   public function body($text)
   {
      $this->_mailer->MsgHtml(utf8_decode($text));

      return $this;
   }

   /**
    * Set the alt message text
    * @access public
    * @param string $text
    */
   public function altBody($text)
   {
      $this->_mailer->AltBody = $text;

      return $this;
   }

   /**
    * Set the message subject
    * @access public
    * @param string $subject
    */
   public function subject($subject)
   {
      $this->_mailer->Subject = utf8_decode($subject);

      return $this;
   }

   /**
    * Set the email from fields
    * @access public
    * @param string $email
    * @param string $name
    */
   public function from($email = '', $name = '')
   {
      $this->_mailer->SetFrom($email, $name);

      $this->from_email = $email;

      return $this;
   }

   /**
    * Set the email reply fields
    * @access public
    * @param string $email
    * @param string $name
    */
   public function reply($email = '', $name = '')
   {
      $this->_mailer->AddReplyTo($email, $name);

      return $this;
   }

   /**
    * Add a to address. Call more than once to add multiple 'to' address's
    * @access public
    * @param string $email
    * @param string $name
    */
   public function to($email = '', $name = '')
   {
      $this->_mailer->AddAddress($email, $name);

      $this->to_name = $name;
      $this->to_email = $email;

      return $this;
   }

   /**
    * Attach a file
    * @access public
    * @param string $file_path
    */
   public function attach($file_path)
   {
      $this->_mailer->AddAttachment($file_path);

      return $this;
   }

   /**
    * Embed an image in the email
    * @access public
    * @param string $image_path The path to the image
    * @param string $cid The reference to this image in the email body
    * @param string $desc The 'title' attribute for this image
    * @param string $encoding Encoding type (default: base64)
    * @param string $mime Set the mime type. Leave black for automatic detection (where possible)
    */
   public function embed($image_path, $cid = '', $desc = '', $encoding = 'base64', $mime = null)
   {
      $real_path = realpath($image_path);

      if (is_null($mime))
      {
         $data = getimagesize($real_path);
         $mime = $data['mime'];
      }

      $this->_mailer->AddEmbeddedImage($real_path, $cid, $desc, $encoding, $mime);

      return $this;
   }

   /**
    * Wrapper to grab the latest error message from the class
    * @access public
    * @return mixed;
    */
   public function getError()
   {
      return $this->_mailer->ErrorInfo;
   }

   /**
    * Send the mail
    * @access public
    */
   public function send($logging = true)
   {
      if ($logging) $this->log();

      return $this->_mailer->Send();
   }

   /**
    * Log the outgoing mail
    * @access private
    */
   private function log()
   {
       /*
        * Commented out due to lack of DB
      try {
         \Ad\System\Mail::create(array(
            'customer_id' => $this->customer_id,
            'reference' => $this->reference,
            'to_email' => $this->to_email,
            'to_name' => $this->to_name,
            'from_email' => $this->from_email,
            'subject' => $this->_mailer->Subject,
            'body' => $this->_mailer->Body,
            'token' => $this->token,
            'status' => 'sent',
            'created_by' => \Forge\Auth::user()->user_id
            //'created_by' => 1
         ));
      } catch (\Exception $e) {
         // ?
      }
        */
   }
}
