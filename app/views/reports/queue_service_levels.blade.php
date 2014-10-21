@extends('layout.main')

@section('main_content')
	
	@include('layout.tools_top', array('parent' => 'Queue Reports'))
	
	@include('layout.sform_open')
		
		@include('layout.sfield_open', array('subtitle' => 'Search Service Level History', 'extra_cls' => 'bordered'))
			
			@include('layout.sfield_generator', array('wrap' => 'col1', 'type' => 'select', 'label' => 'From', 'id' => 'from', 'options' => array('2014-08-15' => '2014-08-15')))
			@include('layout.sfield_generator', array('wrap' => 'col2', 'type' => 'select', 'label' => 'To', 'id' => 'to', 'options' => array('2014-08-15' => '2014-08-15')))						
			@include('layout.sfield_generator', array('wrap' => 'col1 checkbox_parent', 'type' => 'checkbox', 'label' => 'Summary', 'id' => 'summary'))
			
		@include('layout.sfield_close', array('subtitle' => true, 'buttons' => true))
		
		<div class="table_wrap small scroll_wrap topspace">
			<table>
				<tr class="row head">
					<td width="140">Queue</td>
					<td>Date</td>
					<td>Service Level</td>
					<td>Ofered</td>
					<td>Answered</td>
					<td>Abandoned</td>
					<td>Avg Abandoned</td>
					<td>Calls Duration</td>
				</tr>
				<tr class="row">
					<td>QD Queue</td>
					<td>2014-08-18</td>
					<td>
						<div class="bar_graph_wrap">
							<span class="fill" style="width:50%"></span>
							<span class="text">50%</span>
						</div>
					</td>
					<td>2</td>
					<td>1</td>
					<td>1</td>
					<td>00:00:01</td>
					<td>00:00:53</td>
				</tr>
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