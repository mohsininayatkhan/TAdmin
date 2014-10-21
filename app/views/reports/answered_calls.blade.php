@extends('layout.main')

@section('main_content')
	
	@include('layout.tools_top', array('parent' => 'Queue Reports'))
	
	@include('layout.sform_open')
		
		@include('layout.sfield_open', array('subtitle' => 'Agents on Queue', 'extra_cls' => 'bordered toggle_wrap'))
			
			<section class="table_wrap small scroll_wrap">
				<table>
					<tr class="row head">
						<td>Queue</td>
						<td width="20%">Number of Calls</td>
						<td width="20%">Progress</td>
					</tr>
					<tr class="row head">
						<td>Kelly (1029)</td>
						<td>1</td>
						<td>
							<div class="bar_graph_wrap">
								<span class="fill" style="width:100%"></span>
								<span class="text">100%</span>
							</div>
						</td>
					</tr>
				</table>
			</section>
				
		@include('layout.sfield_close')
		
		@include('layout.sfield_open', array('subtitle' => 'Service Level Agreement', 'extra_cls' => 'bordered toggle_wrap'))
		
			<section class="table_wrap small scroll_wrap">
				<table>
					<tr class="row head">
						<td>Answered Within</td>
						<td width="20%">Number of Calls</td>
						<td width="20%">Delta</td>
						<td width="20%">Progress</td>
					</tr>
					@for ($i = 1; $i <= 5; $i++)
						<?php $percent = substr(str_shuffle("0123456789"), 0, 2); ?>
						<tr class="row">
							<td><= {{ substr(str_shuffle("0123456789"), 0, 2) }} seconds</td>
							<td>{{ substr(str_shuffle("0123456789"), 0, 1) }}</td>
							<td>{{ substr(str_shuffle("0123456789"), 0, 1) }}</td>
							<td>
								<div class="bar_graph_wrap">
									<span class="fill" style="width:{{ $percent }}%"></span>
									<span class="text">{{ $percent }}%</span>
								</div>
							</td>
						</tr>
					@endfor
					<tr class="row">
						<td>Total Answered Calls</td>
						<td>1</td>
						<td>0</td>
						<td>
							<div class="bar_graph_wrap">
								<span class="fill" style="width:10%"></span>
								<span class="text">10%</span>
							</div>
						</td>
					</tr>
				</table>
			</section>
		
		@include('layout.sfield_close')
		
		@include('layout.tools_bottom_open')
		
			<div class="button_wrap">
				<input type="button" value="Export CSV" class="gray" />
				<input type="button" value="Export PDF" class="gray" />
				<input type="button" value="Print" class="gray" />
			</div>
		
		@include('layout.tools_bottom_close')
			
	@include('layout.sform_close')

@stop