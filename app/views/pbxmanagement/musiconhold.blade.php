@extends('layout.main')

@section('extra_assets')
<script src="/js/pbx/musiconhold.js"></script>
@stop

@section('main_content')

	@include('layout.tools_top', array('parent' => 'Phone System Setup', 'current' => 'Music On Hold'))
	
	
	<section class="table_wrap small scroll_wrap">
		
		<!-- Grid  for extensions-->
		<div id="grid"></div>
		
		<nav class="dropdown" id="actionSetter">
			<ul>
				<li><a href="javascript:void(0)" onclick="deleteGroup()">Delete Group</a><i class="triangle"></i></li>
				<li><a href="javascript:void(0)" onclick="showFiles()">Music On Hold Files</a></li>
			</ul>
		</nav>
	</section>
	
	@include('layout.tools_bottom_open')
	
		<!-- Pagination area -->
		<div class="pagination_wrap"></div>
		
	@include('layout.tools_bottom_close')

	<!-- popup form -->
	@include('pbxmanagement.musiconholdform')

@stop