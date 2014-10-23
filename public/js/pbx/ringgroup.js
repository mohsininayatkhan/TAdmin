var current_page = 1;
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
	
	$('#failover_app').change(function(){
		handleFailoverApp($('#failover_appnumber'), $(this).val());
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
	
	$('.new-btn').click(function(){
		$('#form #failover_appnumber').find('option').remove();
		getNextNUm();
	});
	
});

function datagrid(page){
	
	var request = $.ajax({
		url : "/ringgroup/render",
		type : 'POST',
		dataType : 'json',
		data : {page: page, keywords: $("#search").val()}
	});
		
	request.done(function(json) {
		// console.log(json);return;
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
		}
		html += '</table>';
		
		
		var data = {current_page: page, total_rows: json.total, page_rows: json.count, num_pages:json.num_pages, start: json.start};
		pagination(data);
		
		$('#grid').html(html);
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

function openForm() {
	var request = $.ajax({
		url : "/ringgroup/get",
		type : 'POST',
		dataType : 'json',
		data : {id: currentId}
	});
		
	request.done(function(json) {
		
		if (json.status == 'ERROR' ) {
			alert(json.message);
			return;
		}
		
		if (json.count>0) {
			
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
			
			if(row['failover_app'] == 'EXTERNAL') {
				$("#form #external_failover_appnumber").val(row['failover_appnumber']);
				
			} else {
				$("#form #failover_appnumber").val(row['failover_appnumber']);	
			}
			
		} else {
			alert('Sorry! no record found.');
		}
	});
	
	$('.overlay').fadeIn(200);

	var fBox = $('.floating_box');
	if (!fBox.is("[style]")) {
		fBox.css({marginTop: '-'+($('.floating_box').height()/2)+'px'})
	}
	fBox.fadeIn(300)
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
			alert('Error occurred while fetching new ringgroup number. Error: '+json.message);
			return;
		}

		if (json.rows.length) {
			$('#ringgroup_num').val(json.rows[0]['num']);

		} else {
			alert('Sorry! No available ringgroup number.');
		}
	});
}

function saveData() {
	var request = $.ajax({
		url : "/ringgroup/save",
		type: 'post',
		dataType: 'json',
		data: $(form).serialize(),
		cache: false
	});
	
	request.success(function(json){
	
		if (json.status == 'ERROR' ) {
			$('.global.error').html(json.message);
			return;
		}
		
		// Means we are modifying a record, so we need to update table record instead of refreshing the page
		id = $('#form #ringgroup_id').val();
		if (id) {
			$('#row_'+id).find("td:eq(0)").html($('#form #name').val());
			$('#row_'+id).find("td:eq(1)").html($('#form #cli_name_prefix').val());
			$('#row_'+id).find("td:eq(2)").html($('#form #ringgroup_num').val());
			$('#row_'+id).find("td:eq(3)").html($('#form #strategy option:selected').val());
			hidePopup();
			
		} else {
			window.location.reload()
		}

	});
}