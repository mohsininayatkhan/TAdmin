@extends('layout.main')

@section('main_content')
	<div class="space_remover"></div>
@stop

@section('wide_content')
	
	@include('layout.tools_top', array('no_tools' => true))
	
	<section class="tab_wrap" id="dashboard">
		
		<div class="tab_link_wrap">
			<span class="tab_link active" data-content="tabAgentCalls">Agent Calls</span>
			<span class="tab_link" data-content="tabAgentStatus">Agent Status</span>
			<span class="tab_link" data-content="tabRealtimeCalls">Realtime Calls</span>
			<span class="tab_link" data-content="tabQueues">Queues</span>
			<span class="tab_link" data-content="tabNotAnswered">Not Answered</span>
			<span class="tab_link" data-content="tabQueueCalls">Queue Calls</span>
			<span class="tab_link" data-content="tabCallsInProgress">Calls in Progress</span>
			<span class="tab_link" data-content="tabCallsPerHour">Calls per Hour</span>
			
			<div class="clearfix"></div>
		</div>
		
		<div class="tab_content">

			<div class="tab_content_pad active" id="tabAgentCalls">
			
				<div class="blue_box">
					<div class="cont" id="inbound_calls_count"></div>
				</div>
				<div class="blue_box">
					<div class="cont" id="outbound_calls_count"></div>
				</div>
				<div class="blue_box">
					<div class="cont" id="internal_calls_count"></div>
				</div>
										
				<div class="clearfix"></div>
			
			</div>
			
			<div class="tab_content_pad" id="tabAgentStatus">
				<div class="clone_content">
				
				</div>
				
				<div class="clearfix"></div>
				
			</div>
			
			<div class="tab_content_pad nf" id="tabRealtimeCalls">
				<div class="blue_box">
					<div class="cont" id="currentCalls"></div>
				</div>
										
				<div class="clearfix"></div>
				
			</div>
			
			<div class="tab_content_pad" id="tabQueues">
				<div class="blue_box">
					<div class="cont" id="ainbound_calls_count"></div>
				</div>
				<div class="blue_box">
					<div class="cont" id="aoutbound_calls_count"></div>
				</div>
				<div class="blue_box">
					<div class="cont" id="ainternal_calls_count"></div>
				</div>
										
				<div class="clearfix"></div>
				
			</div>
			
			<div class="tab_content_pad" id="tabNotAnswered">
			
										
				<div class="clearfix"></div>
				
			</div>
			
			<div class="tab_content_pad nf" id="tabQueueCalls">
				<div class="blue_box">
					<div class="cont" id="queueAnswered"></div>
				</div>
				<div class="blue_box">
					<div class="cont" id="queueService"></div>
				</div>
										
				<div class="clearfix"></div>
				
			</div>
			
			<div class="tab_content_pad" id="tabCallsInProgress">
			
										
				<div class="clearfix"></div>
				
			</div>
			
			<div class="tab_content_pad" id="tabCallsPerHour">
			
										
				<div class="clearfix"></div>
				
			</div>
			
		</div>

	</section>
	
@stop