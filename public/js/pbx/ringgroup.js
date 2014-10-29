var current_page = 1;
var HTML_norecord = '<tr class="row"><td colspan="3">No record found. <span onclick="addList()" style="text-decoration:underline;cursor:pointer">Click here to add new</span></td></tr>';
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
	$('.new-btn').click(function(){
		$('.floating_box.main .what').html('New RingGroup');
	});
	$('#failover_app').change(function(){
		handleFailoverApp($('#failover_appnumber'), $(this).val());
	});	
	// List
	$('#extentype').change(function(){
		handleExten($(this).val());
	});	
	$('.tab_link[data-content="tabAdd"]').click(function(){
		cleanForm();
		handleExten();
		$('#formList #ringgrouplist_id').val('')
	});
	
	// Form validation
	$('#form').validate({
		rules: {
			name: "required",
			ringgroup_num: {
				required: true,
				number: true
			}
		},
		errorPlacement: function(error, element){
			error.appendTo(element.parents('.crow'));
		},
		submitHandler: saveData
	});
	// List validation
	jQuery.validator.addMethod("externaldstnumber", function(value, element) {
		if ($('#extentype option:selected').val() == 'EXTERNAL' && !value.length) {
			return false
		}
		return true
	}, "");
	$('#formList').validate({
		rules: {
			external_dst_number: "externaldstnumber"
		},
		errorPlacement: function(error, element){
			error.appendTo(element.parents('.crow'));
		},
		messages: {
				external_dst_number: "Please enter a valid number",
		},
		submitHandler: saveList
	});
	
	$('.new-btn').click(function(){
		$('#form #failover_appnumber').find('option').remove();
		getNextNUm();
	});
	
});

function datagrid(page){
	var request = $.ajax({
		url: "/ringgroup/render",
		type: 'POST',
		dataType: 'json',
		data: {page: page, keywords: $("#search").val()}
	});
		
	request.done(function(json) {
		if (json.status == 'ERROR' ) {
			showLoader(json.message, true);
			return;
		}
		
		var html = '';
		html += '<table>'
		html += '<tr class="row head">';
		html += '<td>Name</td>';
		html += '<td>CLI Prefix</td>';
		html += '<td>Number</td>';
		html += '<td>Strategy</td>';
		html += '<td width="5%"></td>';
		html += '</tr>';
		
		if (json.count>0) {
			$.each( json.rows, function( key, value ) {
				html += '<tr class="row" id="row_'+value.ringgroup_id+'">';
				html += '<td>'+value.name+'</td>';
				html += '<td>'+(value.cli_name_prefix ? value.cli_name_prefix : '')+'</td>';
				html += '<td>'+value.ringgroup_num+'</td>';
				html += '<td>'+value.strategy+'</td>';
				html += '<td><a href="javascript:void(0)" id="'+value.ringgroup_id+'" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter">Actions<i class="icon_arrow_gray right"></i></a></td>';
				html += '</tr>';
			});
			
		} else {
			html += '<tr class="row"><td colspan="5">No record found.</td></tr>';
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
		data : {minvalue: 8000}
	});
		
	request.done(function(json) {
		if (json.status == 'ERROR' ) {
			 showLoader(json.message, true);
			return;
		}

		if (!json.rows.length) {
			showLoader('Sorry! No available ringgroup number', true);
			return;
		}
		
		$('#ringgroup_num').val(json.rows[0]['num']);
	});
}

function openForm() {
	showLoader();
	
	var request = $.ajax({
		url: "/ringgroup/get",
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

		$('#form #ringgroup_id').val(row['ringgroup_id']);
		$('#form #name').val(row['name']);
		$('#form #cli_name_prefix').val(row['cli_name_prefix']);
		$('#form #ringtime').val(row['ringtime']);
		$('#form #strategy').val(row['strategy']);
		$('#form #ringgroup_num').val(row['ringgroup_num']);
		
		$('#form #failover_announcement_no').val(row['failover_announcement_no']);
		$('#form #failover_app').val(row['failover_app']);

		handleFailoverApp($('#failover_appnumber'), row['failover_app']);
		
		if (row['failover_app'] == 'EXTERNAL') {
			$("#form #external_failover_appnumber").val(row['failover_appnumber']);
			
		} else {
			$("#form #failover_appnumber").val(row['failover_appnumber']);	
		}
		
		hideLoader();
		
		var fBox = $('.floating_box.main');
		$('.what', fBox).html('Eidt RingGroup - '+row['name']); // Update popup title
		if (!fBox.is("[style]")) {
			fBox.css({marginTop: '-'+(fBox.height()/2)+'px'})
		}
		$('.overlay').fadeIn(200);
		fBox.fadeIn(300);
	});
}

function saveData() {
	showLoader();
	
	var request = $.ajax({
		url: "/ringgroup/save",
		type: 'post',
		dataType: 'json',
		data: $('#form').serialize(),
		cache: false
	});
	
	request.success(function(json){
		if (json.status == 'ERROR' ) {
			showLoader(json.message).show();
			return;
		}
		
		hideLoader();
		
		id = $('#form #ringgroup_id').val();
		if (!id) {
			window.location.reload()
		}
		
		// Means we are modifying a record, so we need to update table record instead of refreshing the page		
		$('#row_'+id).find("td:eq(0)").html($('#form #name').val());
		$('#row_'+id).find("td:eq(1)").html($('#form #cli_name_prefix').val());
		$('#row_'+id).find("td:eq(2)").html($('#form #ringgroup_num').val());
		$('#row_'+id).find("td:eq(3)").html($('#form #strategy option:selected').val());
		hidePopup();
	});
}

function deleteData() {
    if (!confirm('Are you sure you want to delete the record?')) {
        return false;
    }
	
	showLoader();
	
    var request = $.ajax({
        url: "/ringgroup/delete",
        type: 'POST',
        dataType: 'json',
        data: {ringgroup_id: currentId}
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
function handleExten(value) {
	if (value == 'EXTERNAL') {
		$('#dst_extension').hide();
		$('#dst_external').show();
	} else {
		$('#dst_external').hide();
		$('#dst_extension').show();
	}
}
function openList() {
	showLoader();
	
	// Clean
	showList();
	
	var request = $.ajax({
		url: "/ringgroup/getList",
		type: 'POST',
		dataType: 'json',
		data: {id: currentId}
	});
		
	request.done(function(json) {
		if (json.status == 'ERROR' ) {
			showLoader(json.message, true);
			return;
		}
		
		// Add Ringgroup id
		$('#formList #ringgroup_id').val(currentId);
		
		var html = '';
		html += '<table>'
		html += '<tr class="row head">';
		html += '<td>Type</td>';
		html += '<td>Destination</td>';
		html += '<td width="5%"></td>';
		html += '</tr>';
		
		if (json.total>0) {
			$.each(json.rows, function( key, value) {
				html += '<tr class="row" id="row_list_'+value.ringgrouplist_id+'">';
				html += '<td>'+value.extentype+'</td>';
				html += '<td>'+value.dst_number+'</td>';
				html += '<td><a href="javascript:void(0)" id="list_'+value.ringgrouplist_id+'" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter" data-popup=true>Actions<i class="icon_arrow_gray right"></i></a></td>';
				html += '</tr>';
			});
			
		} else {
			html += HTML_norecord;
		}
		
		html += '</table>';
		$('#gridList').html(html);
		
		hideLoader();
			
		var fBox = $('.floating_box.list');
		fBox.find('.module').text($('#row_'+currentId).find("td:eq(0)").text()); // Add current module name to title
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
		url: "/ringgroup/saveList",
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
		id = $('#formList #ringgrouplist_id').val();
		extentype = $('#formList #extentype option:selected').val();
		dst_number = $('#formList #extentype option:selected').val() == 'EXTERNAL' ? $('#external_dst_number').val() : $('#dst_number option:selected').val();
		if (id) {
			$('#row_list_'+id).find("td:eq(0)").html(extentype);
			$('#row_list_'+id).find("td:eq(1)").html(dst_number);
			
		} else {
			html = $('<tr class="row" id="row_list_'+json.id+'">'+
				'<td>'+extentype+'</td>'+
				'<td>'+dst_number+'</td>'+
				'<td><a href="javascript:void(0)" id="list_'+json.id+'" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter" data-popup=true>Actions<i class="icon_arrow_gray right"></i></a></td>'+
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
	var extentype = $('#row_list_'+id).find("td:eq(0)").text();
	var dst_number = $('#row_list_'+id).find("td:eq(1)").text();

	$('#formList #ringgrouplist_id').val(id);
	$('#formList #extentype').val(extentype);
	$('#formList #dst_number').val(dst_number);
	$('#formList #external_dst_number').val(dst_number);
	
	addList();	
	handleExten(extentype);
}
function deleteList() {
	if (!confirm('Are you sure you want to delete the record?')) {
        return false;
    }
	
	showLoader();
	
	var id = currentId.split('_');
		id = id[1];
    var request = $.ajax({
        url: "/ringgroup/deleteList",
        type: 'POST',
        dataType: 'json',
        data: {ringgroup_id: $('#formList #ringgroup_id').val(), ringgrouplist_id: id}
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