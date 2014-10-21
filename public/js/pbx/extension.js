var current_page = 1;
$( document ).ready(function() {
	
	// display the data grid for extension
	datagrid(current_page);
	
	$('#frmExtension #fo_external').hide();
	
	
	$('body').on('click', '.pagination_wrap .page', function(e) {
		if ($(e.target).is(".active")) {
			return
		}
		var txt = $(e.target).text();
		current_page = parseInt(txt);
		datagrid(txt);
	});
	
	$('body').on('click', '.pagination_wrap .btn_forward', function(e) {
		if ($(".btn_forward").is(".disabled")) {
			return;
		} 
		datagrid(++current_page);
	});
	
	$('#frmExtension #failover_app').change(function () {
		
		var act = $(this).val();
		handleFailoverApp(act);
		
		/*$('select[name="failover_app_no"]').find('option').remove();
		
		if(act=='EXTERNAL') {
			$('#frmExtension #fo_external').show();
			$('#frmExtension #fo_other').hide();
		} else {
			$('#frmExtension #fo_external').hide();
			$('#frmExtension #fo_other').show();
			getFailoverApp(act);
		}*/
	});	
	
	
	$('body').on('click', '.pagination_wrap .btn_backward', function(e) {
		if ($(".btn_backward").is(".disabled")) {
			return;
		} 
		datagrid(--current_page);
	});
	
	$("#search").keyup(function(e) {
		datagrid(1);
	});
	
});

function handleFailoverApp(act) {
	
	$('select[name="failover_app_no"]').find('option').remove();
		
	if(act=='EXTERNAL') {
		$('#frmExtension #fo_external').show();
		$('#frmExtension #fo_other').hide();
	} else {
		$('#frmExtension #fo_external').hide();
		$('#frmExtension #fo_other').show();
		getFailoverApp(act);
	}
}

function getFailoverApp(type) {
	
	var request = $.ajax({
		url : '/extension/getFailoverApp',
		type : 'POST',
		dataType : 'json',
		async: false,
		data : {type: type}
	});
	
	request.done(function(json) {
		if (json.status == 'ERROR' ) {
			alert(json.message);
			return false;
		}
		
		if(json.length <= 0) {
			return;
		}
		if (type == 'ANNOUNCEMENT') {
			$.each( json, function( key, value ) {
				$('#failover_app_no').append($('<option></option>').val(value.announcement_id).html(value.name));
			});
		} else if(type == 'EXTEN') {
			$.each( json, function( key, value ) {
				$('#failover_app_no').append($('<option></option>').val(value.account_id).html(value.name+' - '+value.extennumber));
			});
		} else if(type == 'IVR') {
			$.each( json, function( key, value ) {
				$('#failover_app_no').append($('<option></option>').val(value.ivr_number).html(value.ivr_name+' - '+value.ivr_number));
			});
		} else if(type == 'RINGGROUP') {
			$.each( json, function( key, value ) {
				$('#failover_app_no').append($('<option></option>').val(value.ringgroup_id).html(value.name+' - '+value.ringgroup_num));
			});
		} else if(type == 'HANGUP') {
			$('#failover_app_no').append($('<option></option>').val('Hangup').html('Hangup'));
		} else if(type == 'VOICEMAIL') {
			$.each( json, function( key, value ) {
				$('#failover_app_no').append($('<option></option>').val(value.exten).html(value.fullname+' - '+value.exten));
			});
		} 
		
		return;
	});
	
	request.fail(function() {
		return false;
	});
}


function datagrid(page){
	
	var request = $.ajax({
		url : "/extension/render",
		type : 'POST',
		dataType : 'json',
		data : {page: page, keywords: $("#search").val()}
	});
		
	request.done(function(json) {
						  
		if (json.status == 'ERROR' ) {
			alert(json.message);
			return
		}
		
		current_page = page;				  
		var html = '';
		html += '<table>'
		// Header Row
		html += '<tr class="row head">';
		html += '<td>Name</td>';
		html += '<td>Extension</td>';
		html += '<td>User</td>';
		html += '<td>Credit</td>';
		html += '<td width="5%"></td>';
		html += '</tr>';
		
		if (json.count>0) {
			$.each( json.rows, function( key, value ) {
				html += '<tr class="row">';
				html += '<td>'+value.name+'</td>';
				html += '<td>'+value.extennumber+'</td>';
				html += '<td>'+value.account_id+'</td>';
				html += '<td>'+value.credit+'</td>';
				html += '<td><a href="javascript:void(0)" id="'+value.account_id+'" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter">Actions<i class="icon_arrow_gray right"></i></a></td>';
				html += '</tr>';
			});
		}
		html += '</table>';
		
		
		var data = {current_page: page, total_rows: json.total, page_rows: json.count, num_pages:json.num_pages, start: json.start};
		pagination(data);
		
		$('#tb_extension').html(html);
		return true;
	});

	request.fail(function() {
		return false;
	});

	request.always(function() {
		//alert('test');
	}); 
}

function pagination(data) {
	
	var html = '';
	html +='<span id="records_count" class="float text">'+data.start+'-'+parseInt(data.start+data.page_rows)+' of '+data.total_rows+' items</span>';
	
	var backword_status = '';
	if(current_page == 1) {
		backword_status = 'disabled';
	}
	
	html +='<a class="btn gradient icon_wrap_notext btn_backward '+backword_status+'"><i class="icon_backward"></i></a>';
	
	for(i=1; i<=data.num_pages; i++){
		var active = '';
		if (data.current_page == i) {
			active = 'active';
		}
		html +='<a class="btn page gradient '+active+'">'+i+'</a>';
	}
	
	var forward_status = '';
	if(current_page == data.num_pages) {
		forward_status = 'disabled';
	}
	
	html +='<a class="btn gradient icon_wrap_notext btn_forward '+forward_status+'"><i class="icon_forward"></i></a>';
	$('.pagination_wrap').html(html);
}

function openExtensionForm() {
	
	var request = $.ajax({
		url : "/extension/get",
		type : 'POST',
		dataType : 'json',
		data : {account_id: currentId}
	});
		
	request.done(function(json) {
						  
		if (json.status == 'ERROR' ) {
			alert(json.message);
			return;
		}
		
		if (json.count>0) {
			
			var row = json.rows[0];
			
			// extension name
			$('#frmExtension #name').val(row['name']);
			
			// extension number
			$('#frmExtension #extension').val(row['extennumber']);
			
			// password
			$('#frmExtension #password').val(row['secret']);
			
			// voicemail checkbox
			if ( row['voicemail'] ==1 ) {
				$('#frmExtension #voicemail').closest('.checkbox_wrap').addClass('active');
				$('#frmExtension #voicemail').prop('checked', true);
			} else {
				$('#frmExtension #voicemail').closest('.checkbox_wrap').removeClass('active');
				$('#frmExtension #voicemail').prop('checked', false);
			}
			$('#frmExtension #voicemail').val(row['voicemail']);
			
			// voicemail password
			$('#frmExtension #voicemail_password').val(row['voicemail_passwd']);			
			
			// voicemail email
			$('#frmExtension #voicemail_email').val(row['email']);		
			
			// recording checkbox
			if ( row['recording'] ==1 ) {
				$('#frmExtension #recording').closest('.checkbox_wrap').addClass('active');
				$('#frmExtension #recording').prop('checked', true);
			} else {
				$('#frmExtension #recording').closest('.checkbox_wrap').removeClass('active');
				$('#frmExtension #recording').prop('checked', false);
			}
			$('#frmExtension #recording').val(row['recording']);			
			
			
			// reception checkbox
			if ( row['reception'] ==1 ) {
				$('#frmExtension #reception').closest('.checkbox_wrap').addClass('active');
				$('#frmExtension #reception').prop('checked', true);
			} else {
				$('#frmExtension #reception').closest('.checkbox_wrap').removeClass('active');
				$('#frmExtension #reception').prop('checked', false);
			}
			$('#frmExtension #reception').val(row['reception']);
			
			// ring duration
			var ringtime = row['ringtime']  == '' ? 0 : row['ringtime'];
			$('#frmExtension #ring_duration').val(ringtime);
			
			//music on hold
			$('#music_on_hold').val(row['musiconhold']);
			
			
			// caller id local			
			$('#frmExtension #calleridlocal').val(row['calleridlocal']);
			
			// caller id external
			$('#frmExtension #callerid_external').val(row['calleridexternal']);
			
			// extension location
			$("#extension_location").val(row['hosted']);
			
			// failover app
			$("#frmExtension #extension_location").val(row['failover_app']);
			handleFailoverApp(row['failover_app']);
			
			
			// failover app no.
			if(row['failover_app'] == 'EXTERNAL') {
				$("#frmExtension #external_failover_app_no").val(row['failover_appnumber']);
				
			} else {
				$("#frmExtension #failover_app_no").val(row['failover_appnumber']);	
			}
			
			
			// dialup plan
			$('#frmExtension #dialplan').val(row['dialpatterngroup_id']);
			
			// call group
			$('#frmExtension #call_group').val(row['callgroup']);
			
			//forwading
			if (row['callforward']=='1') {
				$('#frmExtension #forwarding').val('fwd');
			} else if (row['followme']=='1') {
				$('#frmExtension #forwarding').val('fm');
			} else {
				$('#frmExtension #forwarding').val('none');
			}
			
			// auto provisioning checkbox
			if ( row['autoprovisioning'] ==1 ) {
				$('#frmExtension #autoprovisioning').closest('.checkbox_wrap').addClass('active');
				$('#frmExtension #autoprovisioning').prop('checked', true);
			} else {
				$('#frmExtension #autoprovisioning').closest('.checkbox_wrap').removeClass('active');
				$('#frmExtension #autoprovisioning').prop('checked', false);
			}
			$('#frmExtension #autoprovisioning').val(row['autoprovisioning']);
			
			
			// phone model
			$('#frmExtension #phone_model').val(row['phone_model']);
			
			// mac address
			$('#frmExtension #mac_address').val(row['macadd']);
			
			// extension type
			var extension_type = '';
			if (row['iaxuser'] == '1') {
				extension_type = 'IAX';
			} else if (row['sipuser'] == '1') {
				extension_type = 'SIP';
			}
			$('#frmExtension #extension_type').val(extension_type);
			
			// dtmf mode
			$('#frmExtension #dtmf_mode').val(row['dtmfmode']);
			
			// codecs
			var codecs = row['allow'].split(',');
			
			if (codecs.length>0) {
				$("input[name='codecs[]']").each(function(){
					$(this).closest('.checkbox_wrap').removeClass('active');
					$(this).prop('checked', false);
					
					if ($.inArray( $(this).val(), codecs )!==-1) {
						$(this).prop('checked', true);
						$(this).closest('.checkbox_wrap').addClass('active');
					}
				});
			}
			
			//nat
			$('#frmExtension #nat').val(row['macadd']);
			
			// call waiting			
			
			// billing type 
			var billing_type = row['billing_type']  == '' ? 'extension' : row['billing_type'];
			$('#frmExtension #billing_type').val(billing_type);
			
			// currency
			$('#frmExtension #currency').val(row['currency']);			
			
			// call plan
			$('#frmExtension #callplan').val(row['callplan_id']);
			
			// GUI username
			$('#frmExtension #gui_username').val(row['guiuser']);
			
			// GUI password
			$('#frmExtension #gui_password').val(row['guisecret']);
			
			// Warning Alert
			$('#frmExtension #warning_alert').val(row['w_credit']);
			
			// Critical Alert
			$('#frmExtension #critical_alert').val(row['u_credit']);
			
			console.log(row['phone_model']);
			
		} else {
			alert('Sorry! no record found.');
		}
	});

	request.fail(function() {
		return false;
	});

	request.always(function() {
		//alert('test');
	});
	
	$('.overlay').fadeIn(200);

	var fBox = $('.floating_box');
	if (!fBox.is("[style]")) {
		fBox.css({marginTop: '-'+($('.floating_box').height()/2)+'px'})
	}
	fBox.fadeIn(300)
}


function populateForm(account_id) {
	
	var request = $.ajax({
		url : "/pbx-management/ajax_get_account",
		type : 'POST',
		dataType : 'json',
		data : {account_id: account_id}
	});
		
	request.done(function(json) {
						  
		if (json.status == 'ERROR' ) {
			alert(json.message);
			return;
		}
	});

	request.fail(function() {
		return false;
	});

	request.always(function() {
		//alert('test');
	}); 
}