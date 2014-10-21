@extends('layout.main')

@section('main_content')

	@include('layout.tools_top', array('parent' => 'Extension Management', 'current' => 'Extension'))
	
	
	<section class="table_wrap small scroll_wrap">
		
		<!-- Grid  for extensions-->
		<div id="tb_extension"></div>
		
		<nav class="dropdown" id="actionSetter">
			<ul>
				<li><a href="javascript:void(0)" onClick="openExtensionForm()">Edit</a><i class="triangle"></i></li>
				<li><a href="javascript:void(0)">Delete</a></li>
			</ul>
		</nav>
	</section>
	
	@include('layout.tools_bottom_open')
	
		<!-- Pagination area -->
		<div class="pagination_wrap"></div>
		
	@include('layout.tools_bottom_close')
	@include('assets.extension')
	
	<!-- popup form -->
	@include('pbxmanagement.extension_management.extensions_form')

@stop