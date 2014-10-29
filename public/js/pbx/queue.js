var current_page = 1;
var clsBordered = 'bordered';
var HTML_norecord = '<tr class="row"><td colspan="5">No record found. <span onclick="addList()" style="text-decoration:underline;cursor:pointer">Click here to add new</span></td></tr>';
$(document).ready(function() {
	
	// display the data grid for extension
	datagrid(current_page);
	
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
	
	$('body').on('click', '.pagination_wrap .btn_backward', function(e) {
		if ($(".btn_backward").is(".disabled")) {
			return;
		} 
		datagrid(--current_page);
	});
	// Main form
	$('#failover_app').change(function(){
		handleFailoverApp($('#form #failover_appnumber'), $(this).val())
	});	
	$('.toggler').change(function(){
		el = $(this);
		if (el.is(':checked')) {
			$('#wrap_'+el.data('content')).show();
			el.parents('.sfield_inside').addClass(clsBordered)
		} else {
			$('#wrap_'+el.data('content')).hide();
			el.parents('.sfield_inside').removeClass(clsBordered)
		}
	});
	$('.tab_link[data-content="tabAdd"]').click(function(){
		cleanForm();
		$('#formList #member_exten').val('')
	});
	
	// Form validation
	jQuery.validator.addMethod("isempty", function(value, element, options) {
		if (!options.parent) {
			return false
		}
		if ($('#'+options.parent).is(':checked')) {
			if (options.numeric && !(/^\d+$/.test(value))) {
				return false;
			} else if (!value) {
				return false;
			}
		}
		return true
	}, "*This is a required field.");
	$('#form').validate({
		rules: {
			exten: "required",
			dsc: "required",
			timeout: {
				required: true,
				number: true
			},
			retry: {
				required: true,
				number: true
			},
			weight: {
				required: true,
				number: true
			},
			musiconhold: "required",
			maxwait: {
				isempty: {parent: 'chk_failover', numeric: true}
			},
			failover_app: {
				isempty: {parent: 'chk_failover'}
			},
			failover_appnumber: {
				isempty: {parent: 'chk_failover'}
			},
			slf_desc: {
				isempty: {parent: 'chk_service'}
			},
			slf_str_rec: {
				isempty: {parent: 'chk_service'}
			},
			slf_end_rec: {
				isempty: {parent: 'chk_service'}
			}
		},
		messages: {
			maxwait: "Should be a valid number."
		},
		errorPlacement: function(error, element){
			error.appendTo(element.parents('.crow'));
		},
		submitHandler: saveData
	});
	// List validation
	$('#formList').validate({
		rules: {
			exten: "required",
			agent_type: "required",
			agent_level: "required",
			penalty: {required: true, number: true}
		},
		errorPlacement: function(error, element){
			error.appendTo(element.parents('.crow'));
		},
		submitHandler: saveList
	});

	$('.new-btn').click(function(){
		$('.floating_box.main .what').html('New Queue');
		resetOtherSettings();
		getNextNUm();
	});
});

function resetOtherSettings() {
	$('.floating_box.main .sfield_inside').removeClass(clsBordered);
	$('.floating_box.main .sfield_inside .hide').hide();
}
function datagrid(page){
	var request = $.ajax({
		url: "/queue/render",
		type: 'POST',
		dataType: 'json',
		data: {page: page, keywords: $("#search").val()}
	});
		
	request.done(function(json) {
		if (json.status == 'ERROR' ) {
			showLoader(json.message, true);
			return
		}

		var html = '';
		html += '<table>'
		html += '<tr class="row head">';
		html += '<td>Number</td>';
		html += '<td>Description</td>';
		html += '<td>Strategy</td>';
		html += '<td>Weight</td>';
		html += '<td>Timeout</td>';
		html += '<td width="5%"></td>';
		html += '</tr>';
		
		if (json.count>0) {
			$.each(json.rows, function(key, value){
				html += '<tr class="row" id="row_'+value.queue_id+'">';
				html += '<td>'+value.exten+'</td>';
				html += '<td>'+value.dsc+'</td>';
				html += '<td>'+value.strategy+'</td>';
				html += '<td>'+value.weight+'</td>';
				html += '<td>'+value.timeout+'</td>';
				html += '<td><a href="javascript:void(0)" id="'+value.queue_id+'" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter">Actions<i class="icon_arrow_gray right"></i></a></td>';
				html += '</tr>';
			});
			
		} else {
			html += '<tr class="row"><td colspan="6">No record found.</td></tr>';
		}
		
		html += '</table>';
		$('#grid').html(html);
		
		current_page = page;
		var data = {current_page: page, total_rows: json.total, page_rows: json.count, num_pages:json.num_pages, start: json.start};
		pagination(data);
	});
}

function getNextNUm() {
	var request = $.ajax({
		url : "/ringgroup/nextnum",
		type : 'POST',
		dataType : 'json',
		data : {minvalue: 501}
	});
		
	request.done(function(json) {
		if (json.status == 'ERROR' ) {
			 showLoader(json.message, true);
			return;
		}

		if (!json.rows.length) {
			showLoader('Sorry! No available queue number', true);
			return;
		}
		
		$('#form #exten').val(json.rows[0]['num']);
	});
}

function openForm() {
	showLoader();
	resetOtherSettings();
	
	var request = $.ajax({
		url: "/queue/get",
		type: 'POST',
		dataType: 'json',
		data: {id: currentId}
	});
		
	request.done(function(json) {
		if (json.status == 'ERROR' ) {
			showLoader(json.message, true);
			return;
		}
		
		if (!json.count){
			showLoader('Record not found!', true);
			return;
		}
			
		var row = json.rows[0];

		$('#form #queue_id').val(row['queue_id']);
		$('#form #exten').val(row['exten']);
		$('#form #dsc').val(row['dsc']);
		$('#form #cli_name_prefix').val(row['cli_name_prefix']);
		$('#form #timeout').val(row['timeout']);
		$('#form #retry').val(row['retry']);
		$('#form #weight').val(row['weight']);
		$('#form #musiconhold').val(row['musiconhold']);
		$('#form #strategy').val(row['strategy']);
		$('#form #welcome_announce').val(row['welcome_announce']);
		$('#form #agent_announce').val(row['agent_announce']);
		$('#form #thankyou').val(row['thankyou']);
		$('#form #announce_frequency').val(row['announce_frequency']);
		
		// Queue Failover
		if (row['maxwait']) {
			$('#form #maxwait').val(row['maxwait']);
			$('#form #failover_app').val(row['failover_app']);
			
			handleFailoverApp($('#failover_appnumber'), row['failover_app']);
			
			$('#form #failover_appnumber').val(row['failover_appnumber']);
			$('#chk_failover').prop("checked", true).trigger("change");
		}
		// Service Level Feedback
		if (row['slf_name']) {
			$('#form #slf_desc').val(row['slf_desc']);
			$('#form #slf_str_rec').val(row['slf_start']);
			$('#form #slf_end_rec').val(row['slf_end']);
			if (row['slf_stat']) {
				$('#form #active').prop("checked", true).trigger("change");
			}
			var validation = row['slf_valid'];
			if (validation) {
				for (var i=0; i<validation.length; i++) {
					$('#form #v_'+validation.charAt(i)).prop("checked", true).trigger("change");
				}
			}
			$('#chk_service').prop("checked", true).trigger("change");
		}
		
		hideLoader();
		
		var fBox = $('.floating_box.main');
		$('.what', fBox).html('Eidt Queue - '+row['dsc']); // Update popup title
		if (!fBox.is("[style]")) {
			fBox.css({marginTop: '-'+(fBox.height()/2)+'px'})
		}
		$('.overlay').fadeIn(200);
		fBox.fadeIn(300);
	});
}

function saveData() {
	// showLoader();
	
	if ($('#form #chk_service').is(':checked') && !$('#form input[name="slf_validate[]"]:checked').length) {
		showLoader('Please select at least one SL Valid Options', true);
		return;
	}
	
	var request = $.ajax({
		url: "/queue/save",
		type: 'post',
		dataType: 'json',
		data: $('#form').serialize(),
		cache: false
	});
	
	request.success(function(json){
		if (json.status == 'ERROR' ) {
			showLoader(json.message, true)
			return;
		}
		
		hideLoader();
		
		var id = $('#form #queue_id').val();
		if (!id) {
			window.location.reload()
		}

		//Means we are modifying a record, so we need to update table record instead of refreshing the page		
		$('#row_'+id).find("td:eq(0)").html($('#form #exten').val());
		$('#row_'+id).find("td:eq(1)").html($('#form #dsc').val());
		$('#row_'+id).find("td:eq(2)").html($('#form #strategy option:selected').val());
		$('#row_'+id).find("td:eq(3)").html($('#form #weight').val());
		$('#row_'+id).find("td:eq(4)").html($('#form #timeout').val());
		
		hidePopup();
	});
}

function deleteData() {
    if (!confirm('Are you sure you want to delete the record?')) {
        return false;
    }
	
	showLoader();
	
    var request = $.ajax({
        url: "/queue/delete",
        type: 'POST',
        dataType: 'json',
        data: {queue_id: currentId}
    });

    request.done(function(json) {
         showLoader(json.message, true);
    });
	
	request.always(function() {
        datagrid(current_page);
    });
}

/* BELOW CODE IS FOR RINGGROUP LIST */
function addList() {	
	$('.floating_box.list .tab_link.active').removeClass(classActive)
	$('#tabList').hide();
	
	$('.tab_link[data-content="tabAdd"]').addClass(classActive);
	$('#tabAdd').fadeIn(200)
}
function showList() {
	$('.floating_box.list .tab_link.active').removeClass(classActive)
	$('#tabAdd').hide();
	
	$('.tab_link[data-content="tabList"]').addClass(classActive);
	$('#tabList').fadeIn(200)
}
function openList() {
	showLoader();
	
	// Clean
	showList();
	
	var request = $.ajax({
		url: "/queue/getList",
		type: 'POST',
		dataType: 'json',
		data: {id: currentId}
	});
		
	request.done(function(json) {
		if (json.status == 'ERROR' ) {
			showLoader(json.message, true);
			return;
		}
		
		// Add Queue id
		$('#formList #queue_exten').val($('#row_'+currentId).find("td:eq(0)").text());
		
		var html = '';
		html += '<table>'
		html += '<tr class="row head">';
		html += '<td>Agent</td>';
		html += '<td>Exten</td>';
		html += '<td>Type</td>';
		html += '<td>Penalty</td>';
		html += '<td width="5%"></td>';
		html += '</tr>';
		
		if (json.total>0) {
			$.each(json.rows, function(key, value){
				html += '<tr class="row" id="row_list_'+value.member_exten+'">';
				html += '<td>'+value.name+'</td>';
				html += '<td>'+value.member_exten+'</td>';
				html += '<td>'+value.agent_type+'</td>';
				html += '<td>'+value.penalty+'</td>';
				html += '<td class="hide">'+value.agent_level+'</td>';
				html += '<td><a href="javascript:void(0)" id="list_'+value.member_exten+'" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter" data-popup=true>Actions<i class="icon_arrow_gray right"></i></a></td>';
				html += '</tr>';
			});
			
		} else {
			html += HTML_norecord;
		}
		
		html += '</table>';
		$('#gridList').html(html);
		
		hideLoader();
			
		var fBox = $('.floating_box.list');
		fBox.find('.module').text($('#row_'+currentId).find("td:eq(1)").text()); // Add current module name to title
		if (!fBox.is("[style]")) {
			fBox.css({marginTop: '-'+(fBox.height()/2)+'px'})
		}
		$('.overlay').fadeIn(200);
		fBox.fadeIn(300);
	});
}
function saveList() {
	showLoader();
	
	var request = $.ajax({
		url: "/queue/saveList",
		type: 'post',
		dataType: 'json',
		data: $('#formList').serialize(),
		cache: false
	});
	
	request.success(function(json){
		if (json.status == 'ERROR' ) {
			showLoader(json.message, true);
			return;
		}
		
		hideLoader();
		
		// Means we are modifying a record, so we need to update table record instead of refreshing the page
		var id = $('#formList #member_exten').val();
		var exten = $('#formList #exten option:selected').val();
		var type = $('#formList #agent_type option:selected').val();
		var penalty = $('#formList #penalty').val();
		var agent_level = $('#formList #agent_level option:selected').val();
		if (id) {
			$('#row_list_'+id).find("td:eq(0)").html(json.name);
			$('#row_list_'+id).find("td:eq(1)").html(exten);
			$('#row_list_'+id).find("td:eq(2)").html(type);
			$('#row_list_'+id).find("td:eq(3)").html(penalty);
			$('#row_list_'+id).find("td:eq(4)").html(agent_level);
			
		} else {
			html = $('<tr class="row" id="row_list_'+exten+'">'+
				'<td>'+json.name+'</td>'+
				'<td>'+exten+'</td>'+
				'<td>'+type+'</td>'+
				'<td>'+penalty+'</td>'+
				'<td class="hide">'+agent_level+'</td>'+
				'<td><a href="javascript:void(0)" id="list_'+exten+'" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter" data-popup=true>Actions<i class="icon_arrow_gray right"></i></a></td>'+
				'</tr>');
				
			// Clear table content if no record yet
			var table = $('#gridList table');
			if ($('tr:eq(1)', table).find('td').length <= 1) {
				$('tr:eq(1)', table).remove();
				table.append(html);
			} else {
				html.insertBefore(table.find('tbody tr:eq(1)'));
			}
		}
		
		showList();
	});
}
function editList() {
	var id = currentId.split('_');
		id = id[1];
	var exten = $('#row_list_'+id).find("td:eq(1)").text();

	$('#formList #member_exten').val(exten);
	$('#formList #exten').val(exten);
	$('#formList #agent_type').val($('#row_list_'+id).find("td:eq(2)").text());
	$('#formList #penalty').val($('#row_list_'+id).find("td:eq(3)").text());
	$('#formList #agent_level').val($('#row_list_'+id).find("td:eq(4)").text());
	
	addList();
}
function deleteList() {
	if (!confirm('Are you sure you want to delete the record?')) {
        return false;
    }
	
	showLoader();

	var id = currentId.split('_');
		id = id[1];
    var request = $.ajax({
        url: "/queue/deleteList",
        type: 'POST',
        dataType: 'json',
        data: {queue_exten: $('#formList #queue_exten').val(), member_exten: id}
    });

    request.done(function(json) {
        showLoader(json.message, true);
		
		if (json.status != 'ERROR' ) {
			$('#row_list_'+id).fadeOut(200, function(){
				$(this).remove();
				
				var table = $('#gridList table');
				if (table.find('tr').length <= 1) {
					table.append(HTML_norecord)
				}
			})
		}
		
    });
}