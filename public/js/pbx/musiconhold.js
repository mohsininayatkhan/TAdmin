var current_page = 1;
var HTML_norecord = '<tr class="row"><td colspan="2">No record found. <span onclick="addList()" style="text-decoration:underline;cursor:pointer">Click here to add new</span></td></tr>';
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
	
	$('.tab_link[data-content="tabAdd"]').click(function(){
		$('#formList #moh_file').val('');
		$('#formList .error').html('');
	});
	
	// Form validation
	$('#form').validate({
		rules: {
			name: "required"
		},
		errorPlacement: function(error, element){
			error.appendTo(element.parents('.crow'));
		},
		submitHandler: saveData
	});
	// Form validation
	$('#formList').validate({
		rules: {
			moh_file: "required"
		},
		errorPlacement: function(error, element){
			error.appendTo(element.parents('.crow'));
		},
		submitHandler: uploadFile
	});
});

function datagrid(page){
	var request = $.ajax({
		url: "/musiconhold/render",
		type: 'POST',
		dataType: 'json',
		data: {page: page, keywords: $("#search").val()}
	});
		
	request.done(function(json) {
		if (json.status == 'ERROR' ) {
			alert(json.message);
			return
		}
		
		current_page = page;				  
		var html = '';
		html += '<table>'
		html += '<tr class="row head">';
		html += '<td>Music On Hold Group</td>';
		html += '<td>Date Created</td>';
		html += '<td width="5%"></td>';
		html += '</tr>';
		
		if (json.count>0) {
			$.each( json.rows, function( key, value ) {
				html += '<tr class="row" id="row_'+value.musiconhold_id+'">';
				html += '<td>'+value.name+'</td>';
				html += '<td>'+value.stamp+'</td>';
				html += '<td><a href="javascript:void(0)" id="'+value.musiconhold_id+'" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter">Actions<i class="icon_arrow_gray right"></i></a></td>';
				html += '</tr>';
			});
		} else {
			html += '<tr class="row"><td colspan="3">No record found.</td></tr>';
		}
		html += '</table>';
		
		
		var data = {current_page: page, total_rows: json.total, page_rows: json.count, num_pages:json.num_pages, start: json.start};
		pagination(data);
		
		$('#grid').html(html);
	});
}

function saveData() {
	var request = $.ajax({
		url: "/musiconhold/save",
		type: 'post',
		dataType: 'json',
		data: $('#form').serialize(),
		cache: false
	});
	
	request.success(function(json){
		if (json.status == 'ERROR' ) {
			$('.global.error').html(json.message).show();
			return;
		}
		
		// No edit for Music On Hold
		window.location.reload()
	});
}

function deleteGroup() {
    if (!confirm('Are you sure you want to delete the record?')) {
        return false;
    }
    var request = $.ajax({
        url: "/musiconhold/delete",
        type: 'POST',
        dataType: 'json',
        data: {musiconhold_id: currentId}
    });

    request.done(function(json) {
        alert(json.message);
    });
	
	request.always(function() {
        datagrid(current_page);
    });
}

/* BELOW CODE IS FOR MOH LIST */
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
function showFiles() {
	// Clean
	showList();
	
	var request = $.ajax({
		url: "/musiconhold/getFiles",
		type: 'POST',
		dataType: 'json',
		data: {id: currentId}
	});
		
	request.done(function(json) {
		
		if (json.status == 'ERROR' ) {
			alert(json.message);
			return;
		}
		
		// Add MOH id to hidden field
		$('#formList #musiconhold_id').val(currentId);
				
		var html = '';
		html += '<table>'
		// Header Row
		html += '<tr class="row head">';
		html += '<td>Music On Hold file name</td>';
		html += '<td width="5%"></td>';
		html += '</tr>';

		if (json.total>0) {
			$.each(json.rows, function(key, value) {
				html += '<tr class="row" id="row_list_'+value.id+'">';
				html += '<td>'+value.filename+'</td>';
				html += '<td><a href="javascript:void(0)" id="list_'+value.id+'" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter" data-popup=true>Actions<i class="icon_arrow_gray right"></i></a></td>';
				html += '</tr>';
			});
			
		} else {
			html += HTML_norecord;
		}
		html += '</table>';
		
		$('#gridList').html(html);
	});
	$('.overlay').fadeIn(200);
	
	var fBox = $('.floating_box.list');
	// Add current module name to title
	fBox.find('.module').text($('#row_'+currentId).find("td:eq(0)").text());
	
	if (!fBox.is("[style]")) {
		fBox.css({marginTop: '-'+(fBox.height()/2)+'px'})
	}
	fBox.fadeIn(300)
}
function uploadFile() {
	var request = $.ajax({
		url: "/musiconhold/uploadFile",
		type: 'POST',
		dataType: 'json',
		data: new window.FormData($('#formList')[0]),
		cache: false,
		contentType: false,
		processData: false
	});
	
	request.success(function(json){
		if (json.status == 'ERROR' ) {
			$('#formList .global.error').html(json.message).show();
			return;
		}
		
		var table = $('#gridList table');
		id = new Date().getTime();//Math.random();
		html = $('<tr class="row" id="row_list_'+id+'">'+
			'<td>'+json.filename+'</td>'+
			'<td><a href="javascript:void(0)" id="list_'+id+'" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter" data-popup=true>Actions<i class="icon_arrow_gray right"></i></a></td>'+
			'</tr>');
			
		// Clear table content if no record yet
		if ($('tr:eq(1)', table).find('td').length <= 1) {
			$('tr:eq(1)', table).remove();
			table.append(html);
		} else {
			html.insertBefore(table.find('tbody tr:eq(1)'));
		}
		
		showList();
	});
}
function deleteFile() {
	if (!confirm('Are you sure you want to delete the record?')) {
        return false;
    }

    var request = $.ajax({
        url: "/musiconhold/deleteFile",
        type: 'POST',
        dataType: 'json',
        data: {
			musiconhold_id: $('#formList #musiconhold_id').val(), 
			filename: $('#row_'+currentId).find("td:eq(0)").text()
		}
    });

    request.done(function(json) {
        alert(json.message);
		if (json.status != 'ERROR') {
			var table = $('#gridList table');
			$('#row_'+currentId).fadeOut(200, function(){
				$(this).remove();
				if (table.find('tr').length <= 1) {
					table.append(HTML_norecord)
				}
			})
		}
		
    });
}
function downloadFile() {
	window.open('/musiconhold/download?musiconhold_id='+$('#formList #musiconhold_id').val()+'&filename='+$('#row_'+currentId).find("td:eq(0)").text()+'&ms='+ Math.random());
}