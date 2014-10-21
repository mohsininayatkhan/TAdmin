@extends('layout.main')

@section('main_content')
	
	@include('layout.tools_top', array('parent' => 'Admin Reports'))
	
	@include('layout.sform_open')
		
		@include('layout.sfield_open', array('subtitle' => 'Search Outbound Calls', 'extra_cls' => 'bordered'))
					
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'From', 'id' => 'from', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'To', 'id' => 'to', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'Caller', 'id' => 'caller', 'options_default' => 'Anyone'))
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'Trunk', 'id' => 'trunk', 'options_default' => 'Any'))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'Provider', 'id' => 'provider', 'options_default' => 'Any'))
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'Call Plan', 'id' => 'call_plan', 'options_default' => 'Any'))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'Rate Plan', 'id' => 'rate_plan', 'options_default' => 'Any'))
					
			@include('layout.sfield_generator', array('wrap' => 'col1 select_parent', 'type' => 'select', 'label' => 'Duration', 'id' => ['condition', 'duration'], 'options_default' => 'Any'))
			
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'Status', 'id' => 'status', 'options_default' => 'Any'))
					
		@include('layout.sfield_close', array('subtitle' => true, 'buttons' => true))
			
	@include('layout.sform_close')

@stop

@section('wide_content')

	<section class="table_wrap medium scroll_wrap">

		<table>
			<tr class="row head">
				<td>Call Start Time</td>
				<td>Caller/Source</td>
				<td>Destination</td>
				<td>Trunk</td>
				<td>Call Plan</td>
				<td>Rate Plan</td>
				<td>Duration</td>
				<td>Buy Cost</td>
				<td>Sell Cost</td>
			</tr>
			@for ($i = 1; $i <= 2; $i++)
				<tr class="row">
					<td>QD Queue</td>
					<td class="searchExtension">{{ ucwords(substr(str_shuffle("abcdefjhijklmnopqrstuvwxyz"), 0, 6)) }} <{{ substr(str_shuffle("0123456789"), 0, 4) }}></td>
					<td>00{{ substr(str_shuffle("0123456789"), 0, 12) }} (UK Mobile - fm{{ $i + 1 }})</td>
					<td class="searchExtension">Tpad Bill ITwo</td>
					<td>dox2you</td>
					<td>SS7RatedPlan</td>
					<td>00:00:00</td>
					<td>Buy Cost</td>
					<td>Sell Cost</td>
				</tr>
			@endfor
		</table>

	</section>

@stop