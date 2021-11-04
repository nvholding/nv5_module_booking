function keypress(e) {

    var keypressed = null;

    if (window.event) {
        keypressed = window.event.keyCode; //IE
    } else {
        keypressed = e.which; //NON-IE, Standard
    }

    if (keypressed < 48 || keypressed > 57) {

        if (keypressed == 8 || keypressed == 127) {
            return;
        }

        return false;
    }

}
function clearconsole() { console.log(window.console);if(window.console || window.console.firebug) {console.clear();}}
function getData(b){var c={},d=/^data\-(.+)$/;$.each(b.get(0).attributes,function(b,a){if(d.test(a.nodeName)){var e=a.nodeName.match(d)[1];c[e]=a.nodeValue}});return c};
function getRandomNum(lbound, ubound) {
	return (Math.floor(Math.random() * (ubound - lbound)) + lbound);
}

function getRandomChar() {
	var numberChars = "0123456789";
	var lowerChars = "abcdefghijklmnopqrstuvwxyz";
	//var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	//var otherChars = "`@#$%";
	var charSet = numberChars;
	charSet += lowerChars;
	//charSet += upperChars;
	//charSet += otherChars;
	return charSet.charAt(getRandomNum(0, charSet.length));
}

function getPassword() {
	length = 6;			
	var rc = '';
	if (length > 0)
	rc = rc + getRandomChar();
	for (var idx = 1; idx < length; ++idx) {
		rc = rc + getRandomChar();
	}
	return rc;
}
function tour_check_mail( email ) {
	return (email.length >= 7 && nv_mailfilter.test(email) ) ? true : false;
}
function tour_check_date( nvdate )
{
	var dRegex = new RegExp(/\b\d{1,2}[\/]\d{1,2}[\/]\d{4}\b/);
	return dRegex.test(nvdate);
}
function has_error( obj )
{
	var element = obj.parent().parent();				
	if (element.hasClass('form-group')) { element.addClass('newhas-error') }
	obj.addClass('tooltip-current');
	$('.tooltip-current').tooltip({'title': obj.attr('data-error')});
	$('.tooltip-current').tooltip('show');
}


$( 'body' ).on('focus', '#first-boxinfo input, #first-boxinfo select', function() {
	$(this).tooltip('destroy');
	$(this).removeClass('tooltip-current');
	var element = $(this).parent().parent();				
	if (element.hasClass('form-group')) { element.removeClass('newhas-error') }
})


$( 'body' ).on('click', '[data-submit="true"]', function() {
	var obj = $(this);
	if( ! obj.hasClass('disabled') )
	{	
 
		if( ! tour_check_mail( $('#input-email').val() ) )
		{
			has_error( $('#input-email') );
		}
 		else if( $('#input-mobile').val().length < 7 )
		{
			has_error( $('#input-mobile') );
		}
		else if( $('#input-fullname').val().length < 3 )
		{
			has_error( $('#input-fullname') );
		}
		else if( tour_check_date( $('#input-birthday').val() ) === false )
		{
			has_error( $('#input-birthday') );
		}
		else if( $('#input-address').val().length < 3 )
		{
			has_error( $('#input-address') );
		}
		else
		{
			$.ajax({
				url: nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=json&action=register&nocache=' + new Date().getTime(),
				type: 'post',
				dataType: 'json',
				data: $('#tour-dialog input[type=\'text\'], #tour-dialog input[type=\'password\'], #tour-dialog input[type=\'hidden\'], #tour-dialog input[type=\'checkbox\']:checked, #tour-dialog input[type=\'radio\']:checked, #tour-dialog textarea, #tour-dialog select'),
				beforeSend: function( ) {
					obj.addClass('disabled');
					$('body').prepend( '<div id="fixoverlay"></div>' );
				},	
				complete: function() {
					setTimeout(function() { obj.removeClass('disabled') }, 1000);
					$('#fixoverlay').remove();
				},
				success: function(json) {						
					if( json['error'] )
					{
						alert( json['error'] );
						change_captcha('.fcode');
					}else
					{
						 var wh = $('#tour-dialog').height() - 100;
						$('#tour-dialog .modal-content').css({ 'height': wh + 'px' });
						$('#tour-dialog').find('.modal-body').empty();
						
						var bottom = $('#tour-dialog .modal-content').position().top + $('#tour-dialog .modal-content').height() - 200;
						var log_success='<div class="alert alert-success"><i class="fa fa-check-circle"></i><div style="display: inline; margin-left: 5px;">'+json['success']+'</div></div>';
						var log_email='<div class="alert alert-success"><i class="fa fa-check-circle"></i><div style="display: inline; margin-left: 5px;">'+json['log_email']+'</div></div>';
						var log_thankyou='<div class="alert alert-success"><i class="fa fa-check-circle"></i><div style="display: inline; margin-left: 5px;">'+json['log_thankyou']+'</div></div>';
						
						var time = 0;
						$('#tour-dialog').find('.modal-body').append( log_success ).children(':last').hide().fadeIn(1000, function() {
							$('#tour-dialog').find('.modal-body').append( log_email ).children(':last').css({
								opacity: '0',
								bottom: '-'+bottom+'px'
							}).animate({bottom:0, opacity: '1'}, 1000, function() {
								
								$('#tour-dialog').find('.modal-body').append( log_thankyou ).children(':last').hide().fadeIn(1000);
								$('#tour-dialog').find('.modal-body').append( '<div class="thankyou"><img src="'+nv_base_siteurl+'themes/template0021/images/tours/thankyou.jpg" style="height:200px;max-width:100%"></div>' ).children(':last').hide().fadeIn(1000);
							} );	
							
						}); 
					}
 
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					$('#fixoverlay').remove();
				}
			});  
		}
		 
	}
	
});

$( 'body' ).on('click', '[data-close="true"]', function() {
	var key = $(this).attr('data-key');
	$('#customer-'+ key).remove();
	var stt = 1;
	$('#bodycustomer .count').each(function() {	
		$(this).text( stt );
		++stt;			
	});
});

$( 'body' ).on('click', '#addcustomer', function() {
	var stt = $('#bodycustomer .count:last').text();
	stt = parseInt(stt) + 1;
	var key = getPassword();
	var template =''+
	'<tr id="customer-'+ key +'">'+
	'	<td class="text-center"><span class="count">'+ stt +'</span></td>'+
	'	<td class="text-center">'+
	'		<input type="text" name="customer['+ key +'][fullname]" placeholder="Họ và tên" class="formbox">'+
	'	</td>'+
	'	<td class="text-center">'+
	'		<input type="text" name="customer['+ key +'][birthday]" placeholder="Ngày sinh" class="formbox">'+
	'	</td>'+
	'	<td class="text-center">'+
	'		<input type="text" name="customer['+ key +'][address]" placeholder="Địa chỉ" class="formbox">'+
	'	</td>'+
	'	<td class="text-center">'+
	'		<select name="customer['+ key +'][gender]" class="formbox">'+
	'			<option value="M">Nam</option>'+
	'			<option value="F">Nữ</option>'+
	'		</select>'+
	'	</td>'+
	'	<td class="text-center" style="width:120px">'+
	'		<select name="customer['+ key +'][country_id]" class="formbox">'+ $('#input-countryid').html() +'</select>'+
	'	</td>'+
	'	<td class="text-center">'+
	'		<select name="customer['+ key +'][age]" class="formbox">'+
	'			<option value="a">Người lớn</option>'+
	'			<option value="c">Trẻ em</option>'+
	'			<option value="k">Trẻ nhỏ</option>'+
	'		</select>'+
	'	</td>'+
	'	<td class="text-center times"><i class="fa fa-times" data-close="true" data-key="'+ key +'" title="xóa"></i></td>'+
	'</tr>';
	
	$('#bodycustomer').append( template );
})
$( 'body' ).on('click', '[data-ajax="true"]', function() {
	var obj = $(this);
	if( ! obj.hasClass('disabled') )
	{ 
		var datacontent = getData( obj );
 
		$('#tour-dialog').remove();
 
		 $.ajax({
			url: nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=json&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: datacontent,
			beforeSend: function( ) {
				obj.addClass('disabled');
			},	
			complete: function() {
				setTimeout(function() { obj.removeClass('disabled') }, 1000);
			},
			success: function(json) {		
				$('body').prepend( json['template'] );
				$('#tour-dialog').modal('show');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});  
	}
});

$( 'body' ).on('click', '[aria-controls="tour-photo"]', function() {
	$('#carousel').flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: false,
		slideshow: false,
		itemWidth: 200,
		itemMargin: 5,
		asNavFor: '#slider'
	});
	 
	$('#slider').flexslider({
		animation: "slide",
		controlNav: false,
		animationLoop: false,
		slideshow: false,
		sync: "#carousel"
	});
});
 
$(document).ready(function() {
	
	
	
	
	$(document).delegate('a[data-toggle=\'image\']', 'click', function(e) {
		e.preventDefault();	
		var element = this;
		var rel = $(this).attr('rel');	
		$(element).popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" onclick="select_image( \'input-image' + rel + '\' )" class="btn btn-primary "><i class="fa fa-pencil rmbutton" id="button-close"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
			}
		});
	
		$(element).popover('toggle');		
 
		$('#button-close').on('click', function() {
			$(element).popover('hide');
		});
		$('#button-clear').on('click', function() {
			$(element).find('img').attr('src', $(element).find('img').attr('data-placeholder'));
			
			$(element).parent().find('input').attr('value', '');
	
			$(element).popover('hide');
		});
		
	});
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});
 
	$('button[type=\'submit\']').on('click', function() {
		$("form[id*='form-']").submit();
	});
 
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();
		
		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});
	
	$('.close').on('click', function() {
		$('.alert-danger').remove();
	});
});


