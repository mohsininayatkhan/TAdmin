<?php
/**
 * A new PDF class with fewer bugs and easier use.
 * Main problems faced with html2pdf class:
 * - Random blank pages in middle of documents
 * - Doesn't support many css 2.0 attributes
 * - HTML tables don't always render correctly (namely borders/tables)
 *
 * This class adds an easier way to setup header/footer/page numbers/paging
 *
 * Example:
 * $_pdf = new pdf('a4', 'portrait');
 * $_pdf->set_option('pages', TRUE);
 * $_pdf->header(array('type' => 'image', 'image' => '/path/to/image/'));
 * $_pdf->set_option('style', ".class { color: red; }");
 *
 * $_pdf->add_page("<div>Page One</div>");
 * $_pdf->add_page("<div>Page Two</div>");
 *
 * $_pdf->output(); // Return HTML string
 * $_pdf->output('steam'); // Stream the file to the browser
 * $_pdf->output('file', '/path/to/file/'); // Save to a file
 *
 * PORTED FROM EKALAVYA
 * @author Ashley Wilson
 */

require_once(\Forge\Core::path('vendor') .'dompdf/dompdf/dompdf_config.inc.php');
ini_set('mbstring.internal_encoding','UTF-8'); // Stops line breaks in the middle of words

// Needed to stop complaints about fonts???
//error_reporting(0);

class Pdf {
    private $_dompdf;

    private $pages = array();

    private $header_type = array('text', 'image', 'custom');
    private $header_image;
    private $haeder_text;
    private $header_custom;

    private $header_text_size = "10";

    private $footer_type = array('text', 'image', 'custom');
    private $footer_image;
    private $footer_text;
    private $footer_custom;

    private $footer_text_size = "10";
    private $page_margin_bottom = "0";

   private $custom_style;

   private $output_type = array('stream', 'file', 'display');

   private $show_pages = FALSE;
   private $page_margin = '40';
   private $page_height = '80';

   private $font_family = 'helvetica';

   private $orientation = array('portrait', 'landscape');

   public function __construct($type = 'a4', $orientation = 'portrait') {
       if (in_array($orientation, $this->orientation) === false) {
           throw new Exception("Invalid orientation set [{$orientation}]");
       }

       $this->_dompdf = new DOMPDF();
       $this->_dompdf->set_paper($type, $orientation);
   }

   public function get_instance() {
       return $this->_dompdf;
   }

   public function get_option($key) {
       switch ($key) {
           case 'pages':
               return $this->show_pages;
           case 'margin':
               return $this->page_margin;
           case 'style':
               return $this->custom_style;
           case 'font':
               return $this->font_family;
           case 'pageheight':
               return $this->page_height;
           default:
               throw new Exception("Invalid option [$key]");
       }
   }

   public function set_option($key, $value) {
       switch ($key) {
         case 'pages':
            if (is_bool($value) === FALSE) throw new Exception('Invalid option value');
            else $this->show_pages = $value;
            break;
         case 'margin':
            if (is_numeric($value) === FALSE) throw new Exception('Invalid option value');
            else $this->page_margin = (int) $value;
            break;
        case 'margin_bottom':
            if (is_numeric($value) === FALSE) throw new Exception('Invalid option value');
            else $this->page_margin_bottom = (int) $value;
            break;
         case 'style':
            if (empty($value)) throw new Exception('Invalid style attributes');
            else $this->custom_style = $value;
            break;
         case 'font':
            if (empty($value)) throw new Exception('Invalid font value');
            else $this->font_family = $value;
            break;
         case 'pageheight':
            if (empty($value)) throw new Exception('Invalid font value');
            else $this->page_height = (int) $value;
            break;
         default:
            throw new Exception("Invalid option [$key]");
      }
   }

   public function current_page_num()
   {
      return count($this->pages) + 1;
   }

   public function add_page($content = '')
   {
      if (empty($content)) return;

      $this->pages[] = $content;
   }

   public function header(array $obj = array())
   {
      if (isset($obj['type']) === FALSE) throw new Exception('Missing header type');
      if (in_array($obj['type'], $this->header_type) === FALSE) throw new Exception('Invalid header type');

      if ($obj['type'] == 'image')
      {
         if (isset($obj['image']) === FALSE) throw new Exception('No header image defined');
         else $this->header_image = $obj['image'];
      }

      if ($obj['type'] == 'custom')
      {
         $this->header_custom = $obj['data'];
      }

      if (isset($obj['text'])) $this->header_text = $obj['text'];
      if (isset($obj['text_size'])) $this->header_text_size = $obj['text_size'];
   }

   public function footer(array $obj = array())
   {
      if (isset($obj['type']) === FALSE) throw new Exception('Missing footer type');
      if (in_array($obj['type'], $this->footer_type) === FALSE) throw new Exception('Invalid footer type');

      if ($obj['type'] == 'image')
      {
         if (isset($obj['image']) === FALSE) throw new Exception('No footer image defined');
         else $this->footer_image = $obj['image'];
      }
      
      if ($obj['type'] == 'custom')
      {
         $this->footer_custom = $obj['data'];
      }

      if (isset($obj['text']))
      {
         $this->footer_text = (array) $obj['text'];
      }

      if (isset($obj['text_size'])) $this->footer_text_size = $obj['text_size'];
   }

   private function show_pages()
   {
      $output = '';

      if ($this->show_pages === TRUE)
      {
         $output = '$font = Font_Metrics::get_font("'. $this->font_family .'"); ';
         $output .= '$fontsize = '. $this->footer_text_size .'; ';
         $output .= '$fontcolor = array(0.4,0.4,0.4);';
         $output .= '$text_width = Font_Metrics::get_text_width("Page 1 of 1", $font, $fontsize); ';
         $output .= '$pdf->page_text($w - $text_width, $h - 40, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, $fontsize, $fontcolor);';
      }

      return $output;
   }

   private function footer_text()
   {
      $output = '';

      if (empty($this->footer_text) === FALSE)
      {
         $line_height = 40;

         foreach ($this->footer_text as $key => $text)
         {
            $output .= '$font = Font_Metrics::get_font("'. $this->font_family .'"); ';
            $output .= '$fontsize = '. $this->footer_text_size .'; ';
            $output .= '$fontcolor = array(0.4,0.4,0.4); ';
            $output .= '$text_width = Font_Metrics::get_text_width("'. $text .'", $font, $fontsize); ';
            $output .= '$pdf->page_text(($w - $text_width) / 2, $h - '. $line_height .', "'. $text .'", $font, $fontsize, $fontcolor);';

            $line_height -= $this->footer_text_size;
         }
      }

      return $output;
   }
   
   private function footer_custom()
   {
      $output = empty($this->footer_custom) ? '' : $this->footer_custom;

      return $output;
   }

   private function header_image()
   {
      $output = '';

      if (empty($this->header_image) === FALSE)
      {
         if (is_file($this->header_image) === FALSE) throw new Exception('Cannot open header image : '. $this->header_image);

         $parts = pathinfo($this->header_image);
         $size  = getimagesize($this->header_image);

         // Seems to blow up images? Reduce here
         $width = $size[0] * .70;
         $height = $size[1] * .70;

         $this->page_height += $height;

         $output = '$pdf->image("'. $this->header_image .'", $w - ('. $width .' + '. $this->page_margin .'), '. $this->page_margin .', '. $width .', '. $height .');';

         //$output = '$pdf->image("'. $this->header_image .'", "'. $parts["extension"] .'", $w - ('. $width .' + '. $this->page_margin .'), '. $this->page_margin .', '. $width .', '. $height .');';
      }

      return $output;
   }

   private function header_text()
   {
      $output = '';

      if (empty($this->header_text) === FALSE)
      {
         $line_height = 40;

         foreach ($this->header_text as $key => $text)
         {
            $output .= '$font = Font_Metrics::get_font("'. $this->font_family .'"); ';
            $output .= '$fontsize = '. $this->header_text_size .'; ';
            $output .= '$fontcolor = array(0.4,0.4,0.4); ';
            $output .= '$text_width = Font_Metrics::get_text_width("'. $text .'", $font, $fontsize); ';
            $output .= '$pdf->page_text(($w - $text_width) / 2, '. ($this->page_margin - $line_height) .', "'. $text .'", $font, $fontsize, $fontcolor);';

            $line_height -= $this->header_text_size;
         }
      }

      return $output;
   }

   private function header_custom()
   {
      $output = empty($this->header_custom) ? '' : $this->header_custom;

      return $output;
   }

   public function output($type = 'display', $filename = 'sample', $override = false)
   {
      if (in_array($type, $this->output_type) === FALSE) throw new Exception('Invalid output type');

      $output = 'if (isset($pdf)) { $obj = $pdf->open_object(); $h = $pdf->get_height(); $w = $pdf->get_width();';

      $output .= $this->header_custom();
      $output .= $this->header_image();
      $output .= $this->header_text();

      $output .= $this->show_pages();
      $output .= $this->footer_custom();
      $output .= $this->footer_text();

      $output .= ' $pdf->close_object(); $pdf->add_object($obj, "all"); }';

      $html = <<<HTML
<html>
   <head>
      <style>
         @page {
             margin: {$this->page_margin}px;
             margin-top: {$this->page_height}px;
             page-break-inside: avoid;
         }

         .page {
            /*margin-top: {$this->page_height}px;*/
            margin-bottom: {$this->page_margin_bottom}px;
            font-family: '{$this->font_family}';
            page-break-inside: avoid;
         }

         .page-one {
            page-break-after: always;
         }

         .page-break {
            page-break-after: always;
         }

         {$this->custom_style}

      </style>
   </head>
   <body>
      <script type='text/php'>
         $output
      </script>
      <div class='page'>
HTML;
      $html .= implode("<div class='page-break'></div>", $this->pages);

      $html .= <<<HTML
      </div>
   </body>
</html>
HTML;

      if ($type == 'stream')
      {
         $html = utf8_decode($html);
         $this->_dompdf->load_html($html);
         $this->_dompdf->render();

         $this->_dompdf->stream($filename .".pdf");
      } elseif ($type == 'file') {
         $html = utf8_decode($html);
         $this->_dompdf->load_html($html);
         $this->_dompdf->render();

         // Save the file, output the result
         $file = strstr($filename ,".pdf") ? $filename : $filename . ".pdf";

         if (is_writable(dirname($file)) !== TRUE) throw new Exception('Cannot write to this folder location');

         if (file_exists($file) && $override === false) throw new Exception('A file already exists in this location!');

         $output = $this->_dompdf->output();

         if (empty($output)) throw new Exception('There was a problem generating your PDF');

         if (file_put_contents($file, $output) === FALSE) throw new Exception('Unable to write the PDF file');

         return TRUE;
      } else {
         return $html;
      }
   }
}
