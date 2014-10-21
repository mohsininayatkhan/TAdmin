@extends('layout.main')

@section('main_content')
	<div class="space_remover"></div>
@stop

@section('wide_content')
	
	@include('layout.tools_top', array('parent' => 'Queue Reports'))
	
	@include('layout.sform_open')
		
		@include('layout.sfield_open', array('subtitle' => 'Queue Report', 'extra_cls' => 'bordered toggle_wrap'))
			
			<section class="table_wrap medium scroll_wrap">
				<table>
					<tr class="row head">
						<td width="140">Queue</td>
						<td>Total Agents</td>
						<td>Ready Agents</td>
						<td>On Pause</td>
						<td>Busy Agents</td>
						<td>Calls Answered</td>
						<td>Calls Dropped</td>
						<td width="100">Service Level</td>
						<td>Live Calls</td>
						<td>Waiting Calls</td>
						<td>Longest Wait</td>
						<td>Avg Wait Time</td>
						<td>Avg Talk Time</td>
					</tr>
					<?php
					$queue = array("Rep Contact", "Internal BDE", "QD Queue");
					?>
					@for ($i = 1; $i <= 5; $i++)
						<?php $percent = substr(str_shuffle("0123456789"), 0, 2); ?>
						<tr class="row">
							<td>{{ $queue[array_rand($queue, 1)] }}</td>
							<td>{{ substr(str_shuffle("0123456789"), 0, 2) }}</td>
							<td>{{ substr(str_shuffle("0123456789"), 0, 2) }}</td>
							<td>{{ substr(str_shuffle("0123456789"), 0, 2) }}</td>
							<td>{{ substr(str_shuffle("0123456789"), 0, 1) }}</td>
							<td>{{ substr(str_shuffle("0123456789"), 0, 1) }}</td>
							<td>{{ substr(str_shuffle("0123456789"), 0, 1) }}</td>
							<td>
								<div class="bar_graph_wrap">
									<span class="fill" style="width:{{ $percent }}%"></span>
									<span class="text">{{ $percent }}%</span>
								</div>
							</td>
							<td>0</td>
							<td>0</td>
							<td>-</td>
							<td>00:00:00</td>
							<td>00:00:00</td>
						</tr>
					@endfor
				</table>
			</section>
				
		@include('layout.sfield_close')
		
		@include('layout.sfield_open', array('subtitle' => 'Queue Calls in Progress', 'extra_cls' => 'bordered toggle_wrap'))
			
			<section class="scroll_wrap table_wrap small">
				<table>
					<tr class="row head">
						<td>Lorem Ipsum</td>
					</tr>
					<tr class="row">
						<td>Lorem Ipsum</td>
					</tr>
				</table>
			</section>
					
		@include('layout.sfield_close')
		
		@include('layout.sfield_open', array('subtitle' => 'PBX Calls in Progress', 'extra_cls' => 'bordered toggle_wrap'))
			
			<section class="scroll_wrap table_wrap small">
				<table>
					<tr class="row head">
						<td>Lorem Ipsum</td>
					</tr>
					<tr class="row">
						<td>Lorem Ipsum</td>
					</tr>
				</table>
			</section>
					
		@include('layout.sfield_close')
		
		@include('layout.sfield_open', array('subtitle' => 'Logged in Agents', 'extra_cls' => 'bordered toggle_wrap'))
		
			<section class="scroll_wrap table_wrap small">
				<table>
					<tr class="row head">
						<td>Agent</td>
						<td>Extension</td>
						<td>Status</td>
						<td>On Queue</td>
					</tr>
					<tr class="row">
						<td>xSpare-1030</td>
						<td class="col2 searchExtension">1030</td>
						<td>Logged in</td>
						<td>Rep Contact</td>
					</tr>
				</table>
			</section>
			
		@include('layout.sfield_close')
			
	@include('layout.sform_close')

@stop