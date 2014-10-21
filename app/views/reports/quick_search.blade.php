@extends('layout.main')

@section('main_content')
	
	@include('layout.tools_top', array('parent' => 'Call Reports'))
	
	@include('layout.sform_open')
		
		@include('layout.sfield_open', array('subtitle' => 'Search Calls', 'extra_cls' => 'bordered'))
			
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'From', 'id' => 'from', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'To', 'id' => 'to', 'options' => array('2014-08-15' => '2014-08-15')))
			
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'text', 'label' => 'Number', 'id' => 'number', 'hint' => 'Minimum 5 digits'))
			
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
				<td>Destination Name</td>
				<td>Call Duration</td>
				<td>Status</td>
				<td>Queue Name</td>
				<td width="5%">Recording</td>
			</tr>
			<tr class="row no_record">
				<td colspan="9">No Calls</td>
			</tr>
		</table>
		
		<nav class="dropdown notext" id="actionSetter">
			<ul>
				<i class="triangle"></i>
				<li><a href="javascript:void(0)" class="icon_wrap_notext"><i class="icon_record"></i></a></li>
			</ul>
		</nav>

	</section>

@stop