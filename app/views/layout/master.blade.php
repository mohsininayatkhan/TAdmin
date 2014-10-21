<!DOCTYPE html>
<!--[if lte IE 7]><html class="oldie"><![endif]-->
<!--[if lte IE 8]><html class="ie"><![endif]-->
<!--[if gte IE 9]><!--><html><!--<![endif]-->
<head>
<title>Tpad Web</title>
<meta name="description" content="">
<meta name="keywords" content="">
<!-- Optimized mobile viewport -->
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes"/>
<meta name="apple-mobile-web-app-capable" content="yes">

@include('assets.css')
@include('assets.scripts')

@yield('extra_assets')
</head>
<body>
@yield('content')

@include('assets.main_scripts')
</body>
</html>