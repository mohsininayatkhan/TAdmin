@extends('layout.main')

@section('main_content')
	
	@include('layout.tools_top', array('parent' => 'Queue Reports'))
	
	@include('layout.sform_open')
		
		@include('layout.sfield_open', array('subtitle' => 'Search Agent Missed Calls', 'extra_cls' => 'bordered'))
					
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'From', 'id' => 'from', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'To', 'id' => 'to', 'options' => array('2014-08-15' => '2014-08-15')))
			
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => ['radio', 'select'], 'id' => ['ragent', 'agent'], 'name' => ['radio_agent', 'agent'], 'options_default' => 'Anyone', 'text' => 'Agent', 'ischecked' => true))
			
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => ['radio', 'select'], 'id' => ['ragent_group', 'agent_group'], 'name' => ['radio_agent', 'agent_group'], 'text' => 'Agent Group', 'options' => array('Quickdox Ltd' => 'Quickdox Ltd')))
				
		@include('layout.sfield_close', array('subtitle' => true, 'buttons' => true))
		
		<div class="table_wrap small scroll_wrap topspace">

			<table>
				<tr class="row head">
					<td>Datetime</td>
					<td>Agent</td>
					<td>Extension</td>
					<td>Queue</td>
					<td>Caller</td>
				</tr>
				<?php
				$type = array("INBOUND", "OUTBOUND");
				?>
				@for ($i = 1; $i <= 5; $i++)
					<tr class="row">
						<td>2014-08-1{{ substr(str_shuffle("123456789"), 0, 1) }}</td>
						<td>{{ ucwords(substr(str_shuffle("abcdefjhijklmnopqrstuvwxyz"), 0, 6)) }}</td>
						<td class="searchExtension">{{ substr(str_shuffle("0123456789"), 0, 4) }}</td>
						<td>QD Queue</td>
						<td>{{ substr(str_shuffle("123456789"), 0, 11) }}</td>
					</tr>
				@endfor
			</table>

		</div>
		
		@include('layout.tools_bottom_open')
		
			<div class="button_wrap">
				<input type="button" value="Export CSV" class="gray" />
				<input type="button" value="Export PDF" class="gray" />
				<input type="button" value="Print" class="gray" />
			</div>
			
			<div class="pagination_wrap">
				<span class="float text">1-20 of 50 items</span>
				<a href="" class="btn gradient icon_wrap_notext disabled"><i class="icon_backward"></i></a>
				<a href="" class="btn gradient active">1</a>
				<a href="" class="btn gradient">2</a>
				<a href="" class="btn gradient">3</a>
				<a href="" class="btn gradient icon_wrap_notext"><i class="icon_forward"></i></a>
			</div>
		
		@include('layout.tools_bottom_close')
			
	@include('layout.sform_close')
	
@stop