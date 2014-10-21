<html>
   <head>
      <title>Something went wrong!</title>
      <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css" rel="stylesheet">
      
      <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
      <script src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/js/bootstrap.min.js"></script>
      
      <style>
         body {
            background-color: #177df0;
         }
         
         .wrapper {
            margin-top:50px;
            padding: 10px 10px 0px 10px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            background-color: #dcebfc;
            border-radius: 5px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
            -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
         }
      </style>
   </head>
   <body>
      <div class="container">
         <div class='wrapper row span8 offset2'>
            <div class='span7'>
               <ul class="breadcrumb span7" style="margin-left:-10px;">
                  <li><?php echo (isset($error) ? $error : "Report this problem at once..."); ?></li>
               </ul>
            </div>
         </div>
         <?php if (isset($exception)): ?>
         <div class="wrapper row span8 offset2">
            <h3>Report: </h3>
            <pre><?php echo $exception->getTraceAsString(); ?></pre>
         </div>
         <?php endif; ?>
      </div>
   </body>
</html>