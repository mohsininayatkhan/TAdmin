// START LIBRARIES
function loadScripts(array,callback){
    var loader = function(src,handler){
        var script = document.createElement("script");
        script.src = src;
		
		//MODERN BROWSER
		if( script.onload!==undefined ){
			script.onload = function(){
				script.onload = null;
				handler();
			}
		//IE
		}else{
			script.onreadystatechange = function(){
				var r = script.readyState;
				if (r==='loaded' || r==='complete'){
					script.onreadystatechange = null;
					handler();
				}
			}
		}
		
        var head = document.getElementsByTagName("head")[0];
        (head || document.body).appendChild( script );
    };
    (function(){
        if(array.length!=0){
        	loader(array.shift(),arguments.callee);
        }else{
        	callback && callback();
        }
    })();
}
// END LIBRARIES

var classActive = 'active',
	currentTab  = null,
	currentId = null;
$(function(){
	// Contains variables for objects
	var inpBorder = $('.inp'),
	
		tooltip = $('.tooltip'),
		
		dropdown = $('.dropdown'),
		dropdownSetter = $('.dropdownSetter'),
		
		search = $('#search'),
		
		checkAll = $('.checkAll'),
		checkBoxes = $('input[type=checkbox]'),
		radioBtns = $('input[type=radio]'),
		
		tabWrap = $('.tab_wrap'),
		tabLinks = $('.tab_link',tabWrap),
		tabContent = $('.tab_content_pad'),
		
		scrollWrap = $('.scroll_wrap'),
		
		toggleWrap = $('.toggle_wrap'),
		
	// Login
		frmLogin = $('#frmLogin'),
	// Dashboard
		dashboard = $('#dashboard');
		
	
	$('.new-btn').click(function(e){
		e.preventDefault();
		
		if (typeof $('body').find('form')[0] != 'undefined') {
			$('body').find('form')[0].reset();
		
			$('body').find('input[type=hidden]').each(function(){    
        		this.value = '';
    		});
		}
		openPopup();
	});
	
	// Change border style when element is active
	if (inpBorder.length) {
		inpBorder.each(function(){
			var el = $(this);
			el.focus(function(){
				el.parent().toggleClass(classActive)
			}).focusout(function(){
				el.parent().removeClass(classActive)
			})
		});
	}
	// Login
	if (frmLogin.length) {
		frmLogin.validate({
			rules: {
				username: "required",
				password: "required"
			},
			messages: {
				username: "Username is required",
				password: "Password is required"
			},
			errorPlacement: function(error, element){
				error.appendTo(element.parents('.row'));
			}
		});
	}
	
	// Tooltips
	if (tooltip.length) {
		tooltip.parent().mouseenter(function(){
			var el = $(this);
			if (el.parents('.left_pane').length) {
				animate = {opacity:1, left:50};
			} else {
				animate = {opacity:1, top:28};
			}
			$('.tooltip',this).stop().show().animate(animate, 300)
		}).mouseleave(function(){
			var el = $(this);
			if (el.parents('.left_pane').length) {
				animate = {opacity:0, left:60};
			} else {
				animate = {opacity:0, top:38};
			}
			$('.tooltip',this).stop().animate(animate, function(){$(this).hide()})
		})
	}
	
	// Dropdown
	if (dropdownSetter.length) {
		$('body').on('mousedown', '.dropdownSetter', function(e) {
			var el = $(this);
			e.stopPropagation();
			e.preventDefault();
			
			currentDropdown = $('#'+el.data('dropdown'));
			currentId = el.attr('id')
			
			pos = el.offset();
			defaultTop = pos.top+44;
			defaultLeft = pos.left-currentDropdown.outerWidth() + el.outerWidth();
			
			if (el.hasClass(classActive)) {
				currentDropdown.stop().animate({top:defaultTop, opacity:0}, function(){$(this).hide()});
				el.removeClass(classActive);
			} else {
				css = {left:defaultLeft, top:defaultTop};
				if (currentDropdown.is(':visible')) {
					css = {}
				}
				currentDropdown.stop().css(css).show().animate({top:pos.top+34, opacity:1}, 300);
				el.addClass(classActive)
				dropdownSetter.not(el).removeClass(classActive);
			}
			return false
		});
	}
	
	// Seach by extension
	if (search.length && $('.searchExtension').length) {
		$.expr[':'].Contains = function(a, i, m) { 
		  return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0; 
		};
		search.keyup(function(event){
			// check which key was pressed
			// var keyCode = event.which;
			var term = $(this).val();
			// hide all 
			$('.table_wrap').find('.searchExtension').parents('.row').not('.head').hide();
			$('.table_wrap').find('.searchExtension:Contains("' + term + '")').parents('.row').show();
		});
	}
	
	// Customised checkbox/radio buttons
	if (checkBoxes.length) {
		checkBoxes.each(function(){
			var el = $(this);
			checkboxWrap = el.parents('.checkbox_wrap');
			
			// add wrapper if not added
			if (!checkboxWrap.length) {
				checkboxWrap = $('<span/>', {'class':'checkbox_wrap'})
				el.wrap(checkboxWrap);
			}
			
			// checked/uncheck
			if (el.is(':checked')) {
				checkboxWrap.addClass(classActive)
			}
			
			// evaluate on change event
			// el.click(function(){
				// checkboxWrap = el.parents('.checkbox_wrap');
				// checkboxWrap.toggleClass(classActive)
			// })
			el.change(function(){
				checkboxWrap = el.parents('.checkbox_wrap');
				if (el.is(':checked')) {
					checkboxWrap.addClass(classActive)
				} else {
					checkboxWrap.removeClass(classActive)
				}
			});
			
		})
	}
	if (radioBtns.length) {
		radioBtns.each(function(){
			var el = $(this);
			checkboxWrap = el.parents('.radio_wrap');
			
			// add wrapper if not added
			if (!checkboxWrap.length) {
				checkboxWrap = $('<span/>', {'class':'radio_wrap'})
				el.wrap(checkboxWrap);
			}
			
			// checked/uncheck
			if (el.is(':checked')) {
				checkboxWrap.addClass(classActive)
			}
			
			// evaluate on change event
			el.change(function(){
				$('input[name='+el.attr('name')+']:radio').parents('.radio_wrap').removeClass(classActive);
				
				if (el.is(':checked')) {
					el.parents('.radio_wrap').addClass(classActive)
				}
			})
		})
	}
	// Checkbox check all
	if (checkAll.length) {
		checkAll.click(function(){
			var el = $(this);
			child = el.parents(el.data('parent')).find('.'+el.data('child'));
			if (el.is(':checked')) {
				child.prop("checked", true).trigger("change");
			} else {
				child.prop("checked", false).trigger("change");
			}
			// el.parents(el.data('parent')).find('.'+el.data('child')).trigger('click');
			// el.parents(el.data('parent')).find('.'+el.data('child')).attr("checked", true);
		})
	}
	
	// Toggle
	if (toggleWrap.length) {
		$('.sfield_title',toggleWrap).click(function(){
			var isActive = true,
				el = $(this),
				parent = el.parent();
				
			if (parent.hasClass('un'+classActive)) {
				parent.removeClass('un'+classActive);
				isActive = true
			} else {
				isActive = false
			}
				
			el.next().slideToggle(200).promise().done(function(){
				if (!isActive) {
					parent.addClass('un'+classActive);
				}
			});
		})
	}
	
	// Tabs
	if (tabLinks.length) {
		tabLinks.click(function(){
			tabLinks.removeClass(classActive)
			tabContent.hide();
			
			// destroy graph
			if (dashboard.length) {
				$('.cont *',currentTab).remove()
				$('.graph_viewer',currentTab).remove()
			}
			
			var el = $(this);
			currentTab = $('#'+el.data('content'));
			// if (!dashboard.length) {
				// $('#'+el.data('content')).fadeIn(200)
			// } else {
				// $('#'+el.data('content')).css({opacity:0}).show();
				
			// }
			currentTab.fadeIn(200).promise().done(function(){
				// if (el.data('action') && !el.data('loaded')) {
					// eval(el.data('action'));
					// el.data("loaded", true);
				// }
				if (dashboard.length) {
					// $('.cont',currentTab).css({opacity:0});
					eval('setupData_'+(el.data('content')).substring(3)+'()');
				}
			});
			el.addClass(classActive)
		})
	}
	
	$(document).mousedown(function(){
		dropdown.each(function(){
			var el = $(this);
			pos = el.offset();
			el.animate({top:pos.top+10, opacity:0},function(){el.hide()});
		});
		dropdownSetter.removeClass(classActive)
	});
	$(window).resize(function(){
		var ww = $(window).width();
		
		// EXCEPT IE8 and below
		if ((document.documentMode || 100) >= 9) {
			
			// TABLES WITH CUSTOMISED SCROLLBAR
			var scrollWide = $('.scroll_wrap.wide'), //2400
				scrollMedium = $('.scroll_wrap').not('.small'), //1380
				scrollSmall = $('.scroll_wrap.small'), //988
				mCustomScrollbarCls = 'mCustomScrollbar';

			if (ww <= 2400 && scrollWrap.length) {
				if (typeof(mCustomScrollbar) == 'undefined') {
					loadScripts(["/plugin/jquery.mCustomScrollbar.concat.min.js"], load_scrollbar)
				} else {
					load_scrollbar()
				}
			}
			
			function load_scrollbar() {
				if (scrollWide.length && !scrollWide.hasClass(mCustomScrollbarCls)) {
					scrollWide.mCustomScrollbar({
						scrollButtons:{enable:true, scrollType:"stepped", scrollAmount:300},
						// keyboard:{scrollType:"stepped"},
						theme:"rounded-dark",
						axis:"x",
						scrollbarPosition:"outside",
						autoExpandScrollbar:false,
						autoExpandHorizontalScroll:false
						// snapAmount:188,
						// snapOffset:65
					});
				}
				if (ww <= 1380 && scrollMedium.length && !scrollMedium.hasClass(mCustomScrollbarCls)) {
					scrollMedium.mCustomScrollbar({
						scrollButtons:{enable:true, scrollType:"stepped", scrollAmount:300},
						// keyboard:{scrollType:"stepped"},
						theme:"rounded-dark",
						axis:"x",
						scrollbarPosition:"outside",
						autoExpandScrollbar:false,
						autoExpandHorizontalScroll:false
						// snapAmount:188,
						// snapOffset:65
					});
				}
				if (ww <= 988 && scrollSmall.length && !scrollSmall.hasClass(mCustomScrollbarCls)) {
					scrollSmall.mCustomScrollbar({
						scrollButtons:{enable:true, scrollType:"stepped", scrollAmount:300},
						// keyboard:{scrollType:"stepped"},
						theme:"rounded-dark",
						axis:"x",
						scrollbarPosition:"outside",
						autoExpandScrollbar:false,
						autoExpandHorizontalScroll:false
						// snapAmount:188,
						// snapOffset:65
					});
				}
			}
		}
		
		
		
		// IE8 and below
		if ((document.documentMode || 100) < 9) {

		}
	});
	$(window).trigger('resize');
});


function hidePopup() {
	$('.floating_box').fadeOut(200);
	$('.overlay').fadeOut(300)
}
function openPopup() {
	$('.overlay').fadeIn(200);

	var fBox = $('.floating_box');
	if (!fBox.is("[style]")) {
		fBox.css({marginTop: '-'+($('.floating_box').height()/2)+'px'})
	}
	fBox.fadeIn(300)
}

/***********************************
 * THIS SECTION IS FOR GRAPHS ONLY *
 ***********************************/
 if ($('#dashboard').length) {
	currentTab = $('#tabAgentCalls');
	loadScripts(["/plugin/highcharts.js"/* , "plugin/highcharts-3d.js" */], setupData_AgentCalls)
 }
var pie = {
	chart: {
		type: 'pie',
		options3d: {
			enabled: true,
			alpha: 45,
			beta: 0
		},
		backgroundColor: '#E1F4FF',
		style: {borderRadius: '5px'}
	},
	title: {
		align: 'left',
		useHTML: true,
		style: {color: '#00334e'}
	},
	tooltip: {pointFormat: '<b>{point.percentage:.1f}%</b>'},
	plotOptions: {
		pie: {
			allowPointSelect: true,
			cursor: 'pointer',
			depth: 35,
			dataLabels: {
				enabled: true,
				// distance: 10,
				style: {
					fontSize: '10px'
				},
				formatter: function () {
					return this.point.name+'<br/>'+this.y+'('+this.percentage.toFixed(1)+'%)';
				}
			}/* ,
			size: '90%' */
		},
		series: {
			cursor: 'pointer',
			point: {
				events: {
					click: function (e){
						pdiv = $('<div/>', {
							'class': 'graph_viewer rounded',
							html: '<div>'
						}).appendTo(currentTab);
						cdiv = $('<div/>', {
							style: 'position:relative',
							html: '<strong>'+this.name+'</strong>'+
							'<table>'+
								'<tr><th>Value</th><th>Pecentage</th></tr>'+
								'<tr><td>'+this.y+'</td><td>'+this.percentage.toFixed(1)+'%</td></tr>'+
							'</table>'
						}).appendTo(pdiv);
						cspan = $('<span/>', {
							'class': 'close',
							html: 'x'
						}).appendTo(cdiv);
						cspan.click(function(){
							var p = $(this).parents('.graph_viewer');
							p.fadeOut().promise().done(function(){
								p.remove()
							})
						});
						pdiv.css({top: (e.pageY + $('.main_pane').scrollTop()) - (pdiv.height()/2) - 70, left: e.pageX - (pdiv.width()/2)}).fadeIn()
					}
				}
			}
		}
	},
	series: [{
		type: 'pie'
	}]
};
var line = {
	chart: {
		type: 'spline',
		backgroundColor: '#E1F4FF',
		// animation: Highcharts.svg, // don't animate in old IE
		// marginRight: 10,
		events: {
			/* load: function () {

				// set up the updating of the chart each second
				var series = this.series[0];
				setInterval(function () {
					var x = (new Date()).getTime(), // current time
						y = Math.random();
					series.addPoint([x, y], true, true);
				}, 1000);
			} */
		}
	},
	title: {
		align: 'left',
		useHTML: true,
		style: {color: '#00334e'}
	},
	xAxis: {
		type: 'category',
		tickPixelInterval: 150
	},
	yAxis: {
		title: {
			text: 'Total'
		},
		gridLineColor: '#AFD8F8'
		// plotLines: [{
			// value: 0,
			// width: 1,
			// color: '#808080'
		// }]
	},
	tooltip: {
		pointFormat: '<b>{point.y}%</b>'
		// formatter: function () {
			// return '<b>' + this.series.name + '</b><br/>' +
				// Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
				// Highcharts.numberFormat(this.y, 2);
		// }
	},
	plotOptions: {
		spline: {
			marker: {
				lineWidth: 2,
                lineColor: null,
                fillColor: 'white',
				symbol: 'circle'//,
				// lineColor: '#666666',
				// lineWidth: 1
			}
		}
	},
	legend: {
		// enabled: false
	},
	series: [/* {
		name: 'Random data',
		data: (function () {
			// generate an array of random data
			var data = [],
				time = (new Date()).getTime(),
				i;

			for (i = -19; i <= 0; i += 1) {
				data.push({
					x: time + i * 1000,
					y: Math.random()
				});
			}
			return data;
		}())
	} */]
}
var bar = {
	chart: {
		type: 'column',
		backgroundColor: '#E1F4FF'
	},
	title: {
		align: 'left',
		useHTML: true,
		style: {color: '#00334e'}
	},
	xAxis: {
		type: 'category',
		tickPixelInterval: 150
	},
	yAxis: {
		title: {
			text: 'Total Answered Calls'
		},
		gridLineColor: '#AFD8F8'
		// plotLines: [{
			// value: 0,
			// width: 1,
			// color: '#808080'
		// }]
	},
	tooltip: {
		pointFormat: '<b>{point.y}</b>'
		// formatter: function () {
			// return '<b>' + this.series.name + '</b><br/>' +
				// Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
				// Highcharts.numberFormat(this.y, 2);
		// }
	},
	legend: {
		enabled: false
	},
	plotOptions: {
		column: {
			allowPointSelect: true,
			cursor: 'pointer',
			dataLabels: {
				enabled: true,
				style: {
					fontSize: '10px'
				},
				formatter: function () {
					return this.y;
				}
			}/* ,
			size: '90%' */
		},
		series: {
			cursor: 'pointer',
			point: {
				events: {
					click: function (e){
						pdiv = $('<div/>', {
							'class': 'graph_viewer rounded',
							html: '<div>'
						}).appendTo(currentTab);
						cdiv = $('<div/>', {
							style: 'position:relative',
							html: '<strong>'+this.name+'</strong>'+
							'<table>'+
								'<tr><th>Total Answered Calls</th></tr>'+
								'<tr><td>'+this.y+'</td></tr>'+
							'</table>'
						}).appendTo(pdiv);
						cspan = $('<span/>', {
							'class': 'close',
							html: 'x'
						}).appendTo(cdiv);
						cspan.click(function(){
							var p = $(this).parents('.graph_viewer');
							p.fadeOut().promise().done(function(){
								p.remove()
							})
						});
						pdiv.css({top: (e.pageY + $('.main_pane').scrollTop()) - (pdiv.height()/2) - 70, left: e.pageX - (pdiv.width()/2)}).fadeIn()
					}
				}
			}
		}
	},
	series: [{
		type: 'column'
	}]
}
function setupData_AgentCalls() {
	$.ajax({
		url: "/ReportsAgentCalls/data",
		dataType:"json",
		async: false
	}).done(function(data){
		$('.cont',currentTab).each(function(){
			var el = $(this).attr('id');
			var opts = pie;
			// console.log(data[el]);
			// opts.chart.options3d.enabled = true;
			opts.title.text = '<h2 class="blue_box_title icon_arrow_blue">Agent '+el.replace(/\_/g, ' ')+'</h2>';
			// opts.tooltip.pointFormat = '<b>{point.percentage:.1f}%</b>';
			opts.series[0].data = data[el];
			$(this).highcharts(opts);
		});
	})
}
function setupData_Queues() {
	$.ajax({
		url: "/ReportsAgentCalls/data",
		dataType:"json",
		async: false
	}).done(function(data){
		$('.cont',currentTab).each(function(){
			var el = $(this).attr('id').substring(1);
			var opts = pie;
			// console.log(data[el]);
			// opts.chart.options3d.enabled = false;
			opts.title.text = '<h2 class="blue_box_title icon_arrow_blue">Agent '+el.replace(/\_/g, ' ')+'</h2>';
			// opts.tooltip.pointFormat = '<b>{point.percentage:.1f}%</b>';
			opts.series[0].data = data[el];
			$(this).highcharts(opts);
		});
	})
}
function setupData_AgentStatus() {
	$.ajax({
		url: "/ReportsAgentStatus/data",
		dataType:"json",
		async: false
	}).done(function(data){
		$.each(data, function(i, item) {
			id = i.replace(/\_/g, '');
			if (!$('#'+id).length) {
				pdiv = $('<div/>', {
					'class': 'blue_box'
				}).appendTo($('.clone_content'));
				pdiv.append('<div id="'+id+'" class="cont"></div>');
			}
			
			var opts = pie;
			// opts.chart.options3d.enabled = true;
			opts.title.text = '<h2 class="blue_box_title icon_arrow_blue">'+i.replace(/\_/g, ' ');+'</h2>';
			// opts.tooltip.pointFormat = '<b>{point.percentage:.1f}%</b>';
			opts.series[0].data = item;
			$('#'+id).highcharts(opts);
		});
	})
}
function setupData_RealtimeCalls() {
	$.ajax({
		url: "/ReportsRealtimeCalls/data",
		dataType:"json",
		async: false
	}).done(function(data){
		// console.log(data);
		var opts = line;
		opts.title.text = '<h2 class="blue_box_title icon_arrow_blue">Live number of current calls</h2>';
		opts.series = data;
		opts.chart.events.load = function(){
			var series = this.series;
			loop();
			function loop() {
				setTimeout(function(){
					$.ajax({
						url: "/req/realtime_calls.php?current=1",
						dataType:"json",
						async: false
					}).done(function(data){
						series[0].addPoint(data[0], true, true);
						series[1].addPoint(data[1], true, true);
						loop();
					})
				}, 3000)
			}
		}
		$('#currentCalls').highcharts(opts);
	})
}
function setupData_QueueCalls() {
	$.ajax({
		url: "/ReportsQueueCalls/data",
		dataType:"json",
		async: false
	}).done(function(data){
		// Queue answered calls
		var opts = bar;
		opts.title.text = '<h2 class="blue_box_title icon_arrow_blue">Queue Answered Calls</h2>';
		opts.series[0].data = data.calls;
		$('#queueAnswered').highcharts(opts);
		
		// Service level
		var opts = bar;
		opts.title.text = '<h2 class="blue_box_title icon_arrow_blue">Service Level Agreement</h2>';
		opts.series[0].data = data.calls;
		$('#queueService').highcharts(opts);
	})
}