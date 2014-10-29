var current_page = 1;

$(document).ready(function() {

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

    $("#search").keyup(function(e) {
        datagrid(1);
    });

    $("#frmBlacklist").validate({
        rules : {
            exten : "required",
            phonenumber : "required"
        },errorPlacement: function(error, element){
			error.appendTo(element.parents('.crow'));
		}
    });
	
	$('#sbtBtn').click(function() {

        if ($("#frmBlacklist").valid()) {
            var request = $.ajax({
                url : "/blacklist/save",
                type : 'POST',
                dataType : 'json',
                data : $('#frmBlacklist').serialize()
            });

            request.done(function(json) {
                if (json.status == 'ERROR') {
                    alert(json.message);
                    return;
                }
                alert(json.message);
                datagrid(current_page);
                hidePopup();
            });

            request.fail(function() {
                return false;
            });

            request.always(function() {
            });
        }
    });

});

function datagrid(page) {

    var request = $.ajax({
        url : "/blacklist/render",
        type : 'POST',
        dataType : 'json',
        data : {
            page : page,
            keywords : $("#search").val()
        }
    });

    request.done(function(json) {

        if (json.status == 'ERROR') {
            alert(json.message);
            return
        }

        current_page = page;
        var html = '';
        html += '<table>'
        // Header Row
        html += '<tr class="row head">';
        html += '<td>Extension</td>';
        html += '<td>Blacklist Phone Number</td>';
        html += '<td width="5%"></td>';
        html += '</tr>';

        if (json.count > 0) {
            $.each(json.rows, function(key, value) {
                html += '<tr class="row">';
                html += '<td>' + value.extennumber + '</td>';
                html += '<td>' + value.phonenumber + '</td>';
                html += '<td><a href="javascript:void(0)" id="' + value.blacklist_id + '" class="dropdownSetter btn gray icon_wrap_block icon_gear_small" data-dropdown="actionSetter">Actions<i class="icon_arrow_gray right"></i></a></td>';
                html += '</tr>';
            });
        }
        html += '</table>';

        var data = {
            current_page : page,
            total_rows : json.total,
            page_rows : json.count,
            num_pages : json.num_pages,
            start : json.start
        };
        pagination(data);

        $('#tb_callgroup').html(html);
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
    html += '<span id="records_count" class="float text">' + data.start + '-' + parseInt(data.start + data.page_rows) + ' of ' + data.total_rows + ' items</span>';

    var backword_status = '';
    if (current_page == 1) {
        backword_status = 'disabled';
    }

    html += '<a class="btn gradient icon_wrap_notext btn_backward ' + backword_status + '"><i class="icon_backward"></i></a>';

    for ( i = 1; i <= data.num_pages; i++) {
        var active = '';
        if (data.current_page == i) {
            active = 'active';
        }
        html += '<a class="btn page gradient ' + active + '">' + i + '</a>';
    }

    var forward_status = '';
    if (current_page == data.num_pages) {
        forward_status = 'disabled';
    }

    html += '<a class="btn gradient icon_wrap_notext btn_forward ' + forward_status + '"><i class="icon_forward"></i></a>';
    $('.pagination_wrap').html(html);
}

function deleteBlacklist() {

    if (!confirm('Are you sure you want to delete the record?')) {
        return false;
    }
    var request = $.ajax({
        url : "/blacklist/delete",
        type : 'POST',
        dataType : 'json',
        data : {
            blacklist_id : currentId
        }
    });

    request.done(function(json) {

        if (json.status == 'ERROR') {
            alert(json.message);
            return;
        }
        alert(json.message);
    });

    request.fail(function() {
        return false;
    });

    request.always(function() {
        datagrid(current_page);
    });
}

function openBlacklistForm() {

    var validator = $("#frmBlacklist").validate();
    validator.resetForm();
	$("#frmBlacklist").find(".error").removeClass("error");

    var request = $.ajax({
        url : "/blacklist/get",
        type : 'POST',
        dataType : 'json',
        data : {
            blacklist_id : currentId
        }
    });

    request.done(function(json) {

        if (json.status == 'ERROR') {
            alert(json.message);
            return;
        }

        if (json.count > 0) {
			console.log(json.rows[0]['account_id']);
            $('#frmBlacklist #exten').val(json.rows[0]['account_id']);
            $('#frmBlacklist #phonenumber').val(json.rows[0]['phonenumber']);
            $('#frmBlacklist #blacklist_id').val(json.rows[0]['blacklist_id']);
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

    var fBox = $('.floating_box.main');
    if (!fBox.is("[style]")) {
        fBox.css({
            marginTop : '-' + ($('.floating_box').height() / 2) + 'px'
        })
    }
    fBox.fadeIn(300)
}