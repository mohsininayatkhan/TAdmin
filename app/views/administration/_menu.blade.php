@extends('layout.main')

@section('main_content')

	<div class="centered">
		
		<h2 class="title">Administration</h2>
		
		<?php
		$menus = array(
			["Lorem ipsum", "Lorem ipsum dolor sit amet"], 
			["user access", "Lorem ipsum dolor sit amet"], 
			["menu admin", "Lorem ipsum dolor sit amet"]
		);
		?>
		@include('layout.menu_generator', array('title' => 'User Admin', 'cssref' => 'ad1', 'menus' => $menus))
		
		<hr class="hr_gray" />
		
		<?php
		$menus = array(
			["BLF Clients", "Lorem ipsum dolor sit amet"], 
			["BLF Servers", "Lorem ipsum dolor sit amet"], 
			["Extend Panel Access Groups", "Lorem ipsum dolor sit amet"],
			["BLF Admin Access Groups", "Lorem ipsum dolor sit amet"]
		);
		?>
		@include('layout.menu_generator', array('title' => 'BLF', 'cssref' => 'ad2', 'menus' => $menus))
		
		<hr class="hr_gray" />
		
		<?php
		$menus = array(
			["Provider", "Lorem ipsum dolor sit amet"],
			["Trunk", "Lorem ipsum dolor sit amet"],
			["Trunk Group", "Lorem ipsum dolor sit amet"],
			["Call Plan", "Lorem ipsum dolor sit amet"],
			["Rate Plan", "Lorem ipsum dolor sit amet"],
			["Rates", "Lorem ipsum dolor sit amet"],
			["Rate Period", "Lorem ipsum dolor sit amet"],
			["Discount Period", "Lorem ipsum dolor sit amet"],
			["Dial Patterns", "Lorem ipsum dolor sit amet"],
			["Dial Pattern Groups", "Lorem ipsum dolor sit amet"],
			["Tenant", "Lorem ipsum dolor sit amet"],
			["Credit Management", "Lorem ipsum dolor sit amet"],
			["Parking Managemen", "Lorem ipsum dolor sit amet"],
			["Email Management", "Lorem ipsum dolor sit amet"]
		);
		?>
		@include('layout.menu_generator', array('title' => 'Tenant', 'cssref' => 'ad3', 'menus' => $menus))
		
	</div>

@stop