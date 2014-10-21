@section('form_title')
	<h2 class="what">Edit Extensions</h2>
@stop

@section('form_content')

	<section class="tab_wrap">
					
		<div class="tab_link_wrap">
			<span class="tab_link active" data-content="tabExtension">Extension</span>
			<span class="tab_link" data-content="tabSpeedDial">Speed Dial</span>
			
			<div class="clearfix"></div>
		</div>
		
		<div class="tab_content">
			
			<!-- Extenstion Form -->
			<form name="frmExtension" id="frmExtension">
			<div class="tab_content_pad active" id="tabExtension">
			
				@include('layout.sfield_open')

					<div class="col1">
						<h2 class="tab_content_title">Extension Detail</h2>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Extension Name', 'id' => 'name'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Extension', 'id' => 'extension'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Password', 'id' => 'password'))
						
						@include('layout.sfield_generator', array('wrap' => 'crow checkbox_parent topspace', 'type' => 'checkbox', 'label' => 'Voicemail', 'id' => 'voicemail'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Voicemail Password', 'id' => 'voicemail_password'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Voicemail Email', 'id' => 'voicemail_email'))
						
						@include('layout.sfield_generator', array('wrap' => 'crow checkbox_parent', 'type' => 'checkbox', 'label' => 'Recording', 'id' => 'recording'))
						@include('layout.sfield_generator', array('wrap' => 'crow checkbox_parent', 'type' => 'checkbox', 'label' => 'Reception', 'id' => 'reception'))
						
						<?php 
						$ring_duration = array();
						for ($i = 0; $i <= 120; $i++) {
							$ring_duration[$i] = $i;
						}
						?>

						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Ring Duration', 'id' => 'ring_duration', 'options' => $ring_duration))
						<?php 
						$musiconhold_options = array('default' => 'default');
						foreach($data['musiconhold'] as $music) {
							$musiconhold_options[$music['name']] = $music['name'];
						}
						?>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Music on Hold', 'id' => 'music_on_hold', 'options' => $musiconhold_options))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Callerid Local', 'id' => 'callerid_local'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Callerid External', 'id' => 'callerid_external'))
						
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Extension Location', 'id' => 'extension_location', 'options_default' => '', 'options' => array('' => '', '0' => 'On Premise', '1' => 'Hosted' )))
						
						<?php 
						$failover_options = array(
							'' => 'None',
							'ANNOUNCEMENT' => 'Announcement',
							'EXTEN' => 'Extension',
							'EXTERNAL' => 'External',
							'IVR' => 'IVR',
							'RINGGROUP' => 'RingGroup',
							'HANGUP' => 'Terminate Call',
							'VOICEMAIL' => 'Voicemail',
						);
						?>
						<h2 class="tab_content_title topspace">Extension Failover</h2>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover App', 'id' => 'failover_app', 'options' => $failover_options))
						<div id="fo_other">
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Failover App No.', 'id' => 'failover_app_no'))
						</div>
						<div id="fo_external">
							@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Failover App No.', 'id' => 'external_failover_app_no'))
						</div>
						
						<h2 class="tab_content_title topspace">Extension Facilities</h2>
						<?php 
						$dialplan_options = array();
						foreach($data['dialplans'] as $dialplan) {
							$dialplan_options[$dialplan['dialpatterngroup_id']] = $dialplan['groupname'];
						}
						?>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Dialplan', 'id' => 'dialplan', 'options' => $dialplan_options))
						
						<?php 
						$callgroup_options = array();
						foreach($data['callpickups'] as $callpickup) {
							$callgroup_options[$callpickup['callpickup_id']] = $callpickup['name'].' - '.$callpickup['callpickup_code'];
						}
						?>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Call Group', 'id' => 'call_group', 'options' => $callgroup_options))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Forwarding', 'id' => 'forwarding', 'options' => array('none' => 'None', 'fwd' => 'Call Forward', 'fm' => 'Folowme')))
					</div>


					<div class="col2">
						<h2 class="tab_content_title">Extension Phone Detail</h2>
						@include('layout.sfield_generator', array('wrap' => 'crow checkbox_parent topspace', 'type' => 'checkbox', 'label' => 'Autoprovisioning', 'id' => 'autoprovisioning'))
						<?php 
						$phonemodel_options = array('' => '');
						foreach($data['phonemodels'] as $model) {
							$phonemodel_options[$model['model']] = $model['model'];
						}
						?>
						
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Phone Model', 'id' => 'phone_model', 'options' => $phonemodel_options))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'MAC Address', 'id' => 'mac_address'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Extension Type', 'id' => 'extension_type', 'options' => array('none' => 'None', 'SIP' => 'SIP', 'IAX' => 'IAX')))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'DTMF Mode', 'id' => 'dtmf_mode' , 'options' => array('rfc2833' => 'RFC2833', 'inband' => 'Inband', 'auto' => 'Auto', 'info' => 'Info')))

						<div class="crow checkbox_parent">
							<label for="codecs">Codecs</label>
							
							<div class="checkbox_group regular">
								@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'name' => 'codecs[]', 'value' => 'alaw', 'text' => 'alaw', 'ischecked' => true))
								@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'name' => 'codecs[]', 'value' => 'ulaw', 'text' => 'ulaw', 'ischecked' => true))
								@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'name' => 'codecs[]', 'value' => 'gsm', 'text' => 'gsm', 'ischecked' => true))
								@include('layout.sfield_generator', array('subwrap_tag' => 'label', 'type' => 'checkbox', 'name' => 'codecs[]', 'value' => 'g729', 'text' => 'g729', 'ischecked' => true))							
								<div class="clearfix"></div>
							</div>
							
						</div>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Nat', 'id' => 'nat', 'options' => array('yes' => 'Yes', 'no' => 'No', 'never' => 'Never', 'route' => 'Route')))
						@include('layout.sfield_generator', array('wrap' => 'crow checkbox_parent', 'type' => 'checkbox', 'label' => 'Call Waiting', 'id' => 'call_waiting'))
						
						<h2 class="tab_content_title topspace">Extension Billing</h2>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Billing Type', 'id' => 'billing_type', 'options' => array('extension' => 'Extension' , 'common' => 'Common')))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Currency', 'id' => 'currency', 'options' => array('USD' => 'USD', 'GBP' => 'GBP')))
						<?php 
						$callplan_options = array('' => '');
						foreach($data['callplans'] as $plan) {
							$callplan_options[$plan['callplan_id']] = $plan['callplan_name'];
						}
						?>						
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'select', 'label' => 'Callplan', 'id' => 'callplan', 'options' => $callplan_options))

						<h2 class="tab_content_title topspace">GUI Access</h2>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'GUI Username', 'id' => 'gui_username'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'GUI Password', 'id' => 'gui_password'))
						
						<h2 class="tab_content_title topspace">Credit Alert</h2>
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Warning Alert', 'id' => 'warning_alert'))
						@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Critical Alert', 'id' => 'critical_alert'))
						
					</div>
				
				@include('layout.sfield_close')
				
				<span class="button_wrap topspace">
					<input type="submit" value="Save">
					<input type="button" value="Cancel" class="gray" onclick="hidePopup()">
				</span>
				
				<div class="clearfix"></div>
			
			</div>
			</form>
			
			<!-- End of Extenstion Form -->
			<div class="tab_content_pad" id="tabSpeedDial">
			
				<h2 class="tab_content_title">Speed Dial List</h2>
				
				<a href="javascript:void(0)" class="btn white icon_wrap">Add new speed dial <i class="icon_plus"></i></a>
				
				<span class="button_wrap">
					<input type="submit" value="Save">
					<input type="button" value="Cancel" class="gray" onclick="hidePopup()">
				</span>
				
				<div class="clearfix"></div>
				
			</div>
			
		</div>

	</section>

@stop