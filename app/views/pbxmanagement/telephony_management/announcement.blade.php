@extends('layout.main')

@section('main_content')

	@include('layout.tools_top', array('parent' => 'Phone System Setup', 'current' => 'Announcement'))
	
	
	<section class="table_wrap small scroll_wrap">
		
		<!-- Grid  for announcement-->
		<div id="tb_announcement"></div>
		
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
	@include('assets.announcement')
	
	<!-- popup form -->
	@include('pbxmanagement.telephony_management.announcement_form')

@stop