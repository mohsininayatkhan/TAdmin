@extends('layout.main')

@section('main_content')
	
	@include('layout.tools_top', array('parent' => 'Call Reports'))
	
	@include('layout.sform_open')
		
		@include('layout.sfield_open', array('subtitle' => 'Search Calls', 'extra_cls' => 'bordered'))
			
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'From', 'id' => 'from', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'To', 'id' => 'to', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'Caller', 'id' => 'caller', 'options_default' => 'Anyone'))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'Call Type', 'id' => 'call_type', 'options_default' => 'Any'))
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'Destination', 'id' => 'destination'))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'Status', 'id' => 'status', 'options_default' => 'Any'))
			
			@include('layout.sfield_generator', array('wrap' => 'col1 select_parent', 'type' => 'select', 'label' => 'Duration', 'id' => ['condition', 'duration'], 'options_default' => 'Any'))
						
			@include('layout.sfield_generator', array('wrap' => 'col2 checkbox_parent', 'type' => 'checkbox', 'label' => 'Recording', 'id' => 'recording'))
			
		@include('layout.sfield_close', array('subtitle' => true, 'buttons' => true))
			
	@include('layout.sform_close')

@stop

@section('wide_content')

	<section class="table_wrap medium scroll_wrap">

		<table>
			<tr class="row head">
				<td>Call Start Time</td>
				<td>Caller/Source</td>
				<td>Call Type</td>
				<td>Destination</td>
				<td>Call Duration</td>
				<td>Status</td>
				<td width="5%">Recording</td>
			</tr>
			<?php
			$status = array("CANCEL", "ANSWERED");
			$type = array("INBOUND", "OUTBOUND");
			$rec = array("N/A", '<a href="javascript:void(0)" class="dropdownSetter btn icon_wrap_block select_wrap " data-dropdown="actionSetter"><i class="icon_record"></i></a>');
			?>
			@for ($i = 1; $i <= 5; $i++)
				<tr class="row">
					<td>2014-08-1{{ substr(str_shuffle("123456789"), 0, 1) }} 08:33:55</td>
					<td class="searchExtension">{{ ucwords(substr(str_shuffle("abcdefjhijklmnopqrstuvwxyz"), 0, 6)) }} <{{ substr(str_shuffle("0123456789"), 0, 4) }}></td>
					<td>{{ $type[array_rand($type, 1)] }}</td>
					<td>{{ substr(str_shuffle("0123456789"), 0, 11) }}</td>
					<td>00:00:2{{ substr(str_shuffle("0123456789"), 0, 1) }}</td>
					<td>{{ $status[array_rand($status, 1)] }}</td>
					<td>{{ $rec[array_rand($rec, 1)] }}</td>
				</tr>
			@endfor
		</table>
		
		<nav class="dropdown notext" id="actionSetter">
			<ul>
				<i class="triangle"></i>
				<li><a href="javascript:void(0)" class="icon_wrap_notext"><i class="icon_record"></i></a></li>
			</ul>
		</nav>
		
	</section>

@stop