@extends('layout.main')

@section('main_content')

	<div class="centered">
	
		<h2 class="title">PBX Management</h2>
				
		<?php
		$menus = array(
			// title, description, link, icon class
			["user, user group &amp; queues", "Lorem ipsum dolor", "user-group-queues"], 
			["credit management", "Lorem ipsum dolor sit amet"], 
			["phone registration", "Lorem ipsum dolor sit amet"], 
			["features", "Lorem ipsum dolor sit amet"],
			["call group", "Lorem ipsum dolor sit amet"],
			["voice conference", "Lorem ipsum dolor sit amet"],
			["paging / intercom", "Lorem ipsum dolor sit amet"],
			["blacklist", "Lorem ipsum dolor sit amet"],
			["outbound pin dialing", "Lorem ipsum dolor sit amet"],
			["auto provisioning admin", "Lorem ipsum dolor"],
			["ring groups", "Lorem ipsum dolor sit amet"],
			["music on hold", "Lorem ipsum dolor sit amet"],
			["announcement", "Lorem ipsum dolor sit amet"],
			["queues", "Lorem ipsum dolor sit amet"],
			["IVR", "Lorem ipsum dolor sit amet"],
			["inbound numbers", "Lorem ipsum dolor sit amet"]
		);
		?>
		@include('layout.menu_generator', array('title' => 'Phone System Setup', 'cssref' => 'pbx1', 'menus' => $menus))
		
		<hr class="hr_gray" />
					
		<?php
		$menus = array(
			["auto day &amp; night", "Lorem ipsum dolor sit amet", "auto-day-night"],
			["manual day &amp; night", "Lorem ipsum dolor sit amet", "manual-day-night"]
		);
		?>
		@include('layout.menu_generator', array('title' => 'Day &amp; Night Settings', 'cssref' => 'pbx2', 'menus' => $menus))
		
		<hr class="hr_gray" />
		
		<?php
		$menus = array(
			["agent group", "Lorem ipsum dolor sit amet"],
			["queue group", "Lorem ipsum dolor sit amet"],
			["agent shift", "Lorem ipsum dolor sit amet"]
		);
		?>
		@include('layout.menu_generator', array('title' => 'User &amp; User Group', 'cssref' => 'pbx3', 'menus' => $menus))
		
	</div>

@stop