@extends('layout.main')

@section('main_content')

	@include('layout.tools_top', array('parent' => 'Phone System Setup', 'current' => 'Black List'))
	
	
	<section class="table_wrap small scroll_wrap">
		
		<!-- Grid  for call group-->
		<div id="tb_callgroup"></div>
		
		<nav class="dropdown" id="actionSetter">
			<ul>
				<li><a href="javascript:void(0)" onClick="openBlacklistForm()">Edit</a><i class="triangle"></i></li>
				<li><a href="javascript:void(0)" onClick="deleteBlacklist()">Delete</a></li>
			</ul>
		</nav>
	</section>
	
	@include('layout.tools_bottom_open')
	
		<!-- Pagination area -->
		<div class="pagination_wrap"></div>
		
	@include('layout.tools_bottom_close')
	@include('assets.blacklist')
	
	<!-- popup form -->
	@include('pbxmanagement.extra_telephone_features.black_list_form')

@stop