@section('form_title')
	<h2 class="what">Edit Announcement</h2>
@stop

@section('form_content')
	
	<form name="frmAnnouncement" id="frmAnnouncement" enctype="multipart/form-data" target="upload_target" action="/announcement/save" method="post">
	<input type="hidden" name="announcement_id" id="announcement_id" value="" />
	<section class="tab_wrap no_tab">
	
		<div class="tab_content_pad active">
			
			@include('layout.sfield_open')
				
				<div class="col_wide">
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Announcement Name', 'id' => 'name'))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Announcement No.', 'id' => 'number'))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'text', 'label' => 'Description', 'id' => 'description'))
					@include('layout.sfield_generator', array('wrap' => 'crow', 'type' => 'file', 'label' => 'Recording file', 'id' => 'rec_file', 'name' => 'rec_file', 'hint' => 'Leave this empty if no change to recording'))
				</div>
				
			@include('layout.sfield_close')
				
			<span class="button_wrap topspace">
				<input id="sbtBtn" name="sbtBtn" type="button" value="Save">
				<input type="button" value="Cancel" class="gray" onclick="hidePopup()">
			</span>
			
			<div class="clearfix"></div>
		</div>
		
	</section>
	</form>
	
@stop