@section('form_title')
	<h2 class="what">Edit Call Group</h2>
@stop

@section('form_content')
	
	<section class="tab_wrap no_tab">
	
		<div class="tab_content_pad active">
			
			<form name="form" id="form" class="validate">
				@include('layout.sfield_open')
					
					<input type="hidden" name="ringgroup_id" id="ringgroup_id" value="" />
					<div class="col1">
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Name', 'id' => 'name'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'CLI Prefix', 'id' => 'cli_name_prefix'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'number', 'label' => 'Ring Duration', 'id' => 'ringtime'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Ringgroup Strategy', 'id' => 'strategy', 'options' => array('RINGALL' => 'Ringall', 'HUNT' => 'Hunt', 'MEMORYHUNT' => 'Memory Hunt', 'FIRSTAVAILABLE' => 'First Available')))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Ringgroup Number', 'id' => 'ringgroup_num', 'maxlength' => 6, 'hint' => 'This is auto-generated, change only if you want to use custom number'))
					</div>
					<div class="col2">
						<?php 
						$failovermsg = array('' => '');
						foreach($data['failovermsg'] as $dd) {
							$failovermsg[$dd['announcement_number']] = $dd['name'].' - '.$dd['announcement_number'];
						}
						?>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover Message', 'id' => 'failover_announcement_no', 'options' => $failovermsg))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover App', 'id' => 'failover_app', 'options' => array('' => 'None', 'ANNOUNCEMENT' => 'Announcement', 'EXTEN' => 'Extension', 'EXTERNAL' => 'External', 'QUEUE' => 'Queue', 'RINGGROUP' => 'RingGroup', 'HANGUP' => 'Terminate Call', 'VOICEMAIL' => 'Voicemail')))						
						<div id="fo_other">
							@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover App No.', 'id' => 'failover_appnumber'))
						</div>
						<div id="fo_external" style="display:none">
							@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Failover App No.', 'id' => 'external_failover_appnumber'))
						</div>
					</div>
					
					
				@include('layout.sfield_close')
				
				<span class="button_wrap topspace">
					<input type="submit" value="Save">
					<input type="button" value="Cancel" class="gray" onclick="hidePopup()">
				</span>
				<i class="global error"></i>
				<div class="clearfix"></div>
			</form>
		</div>
		
	</section>
	
@stop