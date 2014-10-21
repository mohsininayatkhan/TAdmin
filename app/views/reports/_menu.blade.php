@extends('layout.main')

@section('main_content')

	<div class="centered">
	
		<h2 class="title">Reports</h2>
		
		<?php
		$menus = array(
			["Outbound Report", "Lorem ipsum dolor sit amet"], 
			["Trunk Group Reports", "Lorem ipsum dolor sit amet"]
		);
		?>
		@include('layout.menu_generator', array('title' => 'Admin Reports', 'cssref' => 're1', 'menus' => $menus))
		
		<hr class="hr_gray" />
		
		<?php
		$menus = array(
			["Call Reports", "Lorem ipsum dolor sit amet"], 
			["Call Summary", "Lorem ipsum dolor sit amet"], 
			["Queue Call Reports", "Lorem ipsum dolor sit amet"],
			["Quick Search", "Lorem ipsum dolor sit amet"]
		);
		?>
		@include('layout.menu_generator', array('title' => 'Call Reports', 'cssref' => 're2', 'menus' => $menus))
		
		<hr class="hr_gray" />
		
		<?php
		$menus = array(
			["Real Time Queue Monitoring", "Lorem ipsum dolor sit amet"], 
			["Queue Service Levels", "Lorem ipsum dolor sit amet"], 
			["Answered Calls", "Lorem ipsum dolor sit amet"],
			["Agent Statistics", "Lorem ipsum dolor sit amet"],
			["Agent Log Statistics", "Lorem ipsum dolor sit amet"],
			["No Answer by Agents", "Lorem ipsum dolor sit amet"],
			["Wallboard", "Lorem ipsum dolor sit amet"],
			["Live Report", "Lorem ipsum dolor sit amet"]
		);
		?>
		@include('layout.menu_generator', array('title' => 'Queue Reports', 'cssref' => 're3', 'menus' => $menus))
		
	</div>

@stop