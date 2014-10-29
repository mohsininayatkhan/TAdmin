@section('form_title')
	<h2 class="what">Edit Call Group</h2>
@stop

@section('form_content')
	
	<section class="tab_wrap no_tab">
	
		<div class="tab_content_pad active">
			
			<form name="form" id="form" class="validate">
				@include('layout.sfield_open')
					
					<input type="hidden" name="queue_id" id="queue_id" value="" />
					<div class="col1">
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Queue Number', 'id' => 'exten'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Description', 'id' => 'dsc'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'CLI Prefix', 'id' => 'cli_name_prefix'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Timeout', 'id' => 'timeout'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Retry', 'id' => 'retry'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Weight', 'id' => 'weight'))
						<?php 
						$moh = array('' => '-- Select --');
						foreach($data['moh'] as $dd) {
							$moh[$dd['name']] = $dd['name'];
						}
						?>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Music On Hold', 'id' => 'musiconhold', 'options' => $moh))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Strategy', 'id' => 'strategy', 'options' => array('ringall' => 'ringall', 'leastrecent' => 'leastrecent', 'fewestcalls' => 'fewestcalls', 'random' => 'random', 'rrmemory' => 'rrmemory', 'linear' => 'linear', 'wrandom' => 'wrandom')))
						<?php 
						$annc = array('' => '-- Select --');
						foreach($data['annc'] as $dd) {
							$annc[$dd['announcement_number']] = $dd['name'].' - '.$dd['announcement_number'];
						}
						?>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Join Announcement', 'id' => 'welcome_announce', 'options' => $annc))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Agent Announcement', 'id' => 'agent_announce', 'options' => $annc))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Frequent Announcement', 'id' => 'thankyou', 'options' => $annc))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Announcement Frequency', 'id' => 'announce_frequency'))
					</div>
					<div class="col2">
						<div class="sfield_inside top">
							<h2 class="sfield_title"><label class="title_label" for="chk_failover"><span class="checkbox_wrap"><input type="checkbox" class="toggler" id="chk_failover" name="chk_failover" data-content="failover" /></span>Queue Failover</label></h2>
							<div id="wrap_failover" class="hide">
								@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'number', 'label' => 'Maximum Callwait', 'id' => 'maxwait', 'default' => 3600, 'min' => 10, 'max' => 9999))
								@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover Routing', 'id' => 'failover_app', 'options' => array('' => '--Select--', 'ANNOUNCEMENT' => 'Announcement', 'DAYNIGHT' => 'Day Night', 'EXTEN' => 'Extension', 'IVR' => 'IVR', 'QUEUE' => 'Queue', 'RINGGROUP' => 'RingGroup', 'HANGUP' => 'Terminate Call', 'MEETME' => 'Voice Conference', 'VOICEMAIL' => 'Voicemail')))
								@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover Value', 'id' => 'failover_appnumber'))
							</div>
						</div>
						
						<div class="sfield_inside">
							<h2 class="sfield_title"><label class="title_label" for="chk_service"><span class="checkbox_wrap"><input type="checkbox" class="toggler" id="chk_service" name="chk_service" data-content="service" /></span>Service Level Feedback</label></h2>
							<div id="wrap_service" class="hide">
								@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'SL Description', 'id' => 'slf_desc'))
								@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Start SL Recording', 'id' => 'slf_str_rec', 'options' => $annc))
								@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'End SL Recording', 'id' => 'slf_end_rec', 'options' => $annc))
								
								<div class="crow checkbox_parent">
									<label for="slf_validate">SL Valid Options</label>
									<div class="checkbox_group regular narrow">
										@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'id' => 'v_0', 'name' => 'slf_validate[]', 'value' => '0', 'text' => '0', 'isLabelTop' => true))
										@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'id' => 'v_1', 'name' => 'slf_validate[]', 'value' => '1', 'text' => '1', 'isLabelTop' => true))
										@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'id' => 'v_2', 'name' => 'slf_validate[]', 'value' => '2', 'text' => '2', 'isLabelTop' => true))
										@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'id' => 'v_3', 'name' => 'slf_validate[]', 'value' => '3', 'text' => '3', 'isLabelTop' => true))
										@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'id' => 'v_4', 'name' => 'slf_validate[]', 'value' => '4', 'text' => '4', 'isLabelTop' => true))
										@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'id' => 'v_5', 'name' => 'slf_validate[]', 'value' => '5', 'text' => '5', 'isLabelTop' => true))
										@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'id' => 'v_6', 'name' => 'slf_validate[]', 'value' => '6', 'text' => '6', 'isLabelTop' => true))
										@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'id' => 'v_7', 'name' => 'slf_validate[]', 'value' => '7', 'text' => '7', 'isLabelTop' => true))
										@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'id' => 'v_8', 'name' => 'slf_validate[]', 'value' => '8', 'text' => '8', 'isLabelTop' => true))
										@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'id' => 'v_9', 'name' => 'slf_validate[]', 'value' => '9', 'text' => '9', 'isLabelTop' => true))
										<div class="clearfix"></div>
									</div>
								</div>
								
								@include('layout.sfield_generator', array('wrap' => 'crow checkbox_parent', 'type' => 'checkbox', 'label' => 'Active', 'id' => 'active', 'ischecked' => true, 'value' => 1))
							</div>
						</div>
					</div>
					
					
				@include('layout.sfield_close')
				
				<span class="button_wrap topspace">
					<input type="submit" value="Save" />
					<input type="button" value="Cancel" class="gray" onclick="hidePopup()" />
				</span>
				<i class="global error"></i>
				<div class="clearfix"></div>
			</form>
		</div>
		
	</section>
	
@stop


<!-- popup form -->
<div class="floating_box list hidden">

	<div class="container sform_wrap">

		<div class="sform_pad">

			<h2 class="what">Queue Members - <span class="module"></span></h2>

			<section class="tab_wrap">
					
				<div class="tab_link_wrap">
					<span class="tab_link active" data-content="tabList">List</span>
					<span class="tab_link" data-content="tabAdd">Add New/Edit</span>
					
					<div class="clearfix"></div>
				</div>
				
				<div class="tab_content">
					
					<div class="tab_content_pad active" id="tabList">
						
						<section class="table_wrap">
							<div id="gridList"></div>
							
							<nav class="dropdown popup">
								<ul>
									<li><a href="javascript:void(0)" onclick="editList()">Edit</a><i class="triangle"></i></li>
									<li><a href="javascript:void(0)" onclick="deleteList()">Delete</a></li>
								</ul>
							</nav>
						</section>
						
						<span class="button_wrap topspace">
							<input type="button" value="Close" class="gray" onclick="hidePopup()">
						</span>
						
						<div class="clearfix"></div>
				
					</div>

					
					<div class="tab_content_pad" id="tabAdd">
						<form name="formList" id="formList" class="validate">
							
							<h2 class="tab_content_title">Add to Queue Members List</h2>
							
							@include('layout.sfield_open')
								<!--<input type="hidden" name="queue_id" id="queue_id" value="" />-->
								<input type="hidden" name="queue_exten" id="queue_exten" value="" />
								<input type="hidden" name="member_exten" id="member_exten" value="" />
								<div class="col_wide">
									<?php
									$extension = array('' => '-- Select --');
									foreach($data['extension'] as $dd) {
										$extension[$dd['extennumber']] = $dd['name'].' - '.$dd['extennumber'];
									}
									?>
									@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Agent Extension', 'id' => 'exten', 'options' => $extension))
									@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Agent Type', 'id' => 'agent_type', 'options' => array('DYNAMIC' => 'Dynamic', 'STATIC' => 'Static')))
									@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Agent Level', 'id' => 'agent_level', 'options' => array('0' => 'Agent', '1' => 'Supervisor', '2' => 'Manager', '3' => 'Administrator')))
									@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'number', 'label' => 'Penalty', 'id' => 'penalty', 'default' => 1, 'min' => 1, 'max' => 25))
								</div>
							
							@include('layout.sfield_close')
							
							<span class="button_wrap topspace">
								<input type="submit" value="Save" />
								<input type="button" value="Close" class="gray" onclick="hidePopup()" />
							</span>
							<i class="global error"></i>
							<div class="clearfix"></div>
							
						</form>
					</div>
					
				</div>

			</section>
			
		</div>

		<span class="ie_shadow_top"></span>
		<span class="ie_shadow_bottom"></span>
		<span class="ie_shadow_left"></span>
		<span class="ie_shadow_right"></span>
	</div>
	
</div> <!-- end floating_box -->