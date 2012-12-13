/*
option 1: inline call from an element
<element class="lightbox" lightbox="addboarder-loader" lightbox-width="100" />
where attributes:
lightbox - id of div element as content to insert in lightbox
lightbox-width - width of the lightbox
note: be sure to add class="lightbox"

option 2 : call via function
runLightbox(a,b)
where:
a - id of div element as content to insert in lightbox
b - lightbox width


sample:

css:
div.lightbox-content{
	display:none;
	font-family:Calibri;
	font-size:12px;
	background-color:#efefef;
	border:1px solid #ccc;
	margin:0 auto;
	text-align:center;
	padding:5px;
	border-radius:5px;
}

html:
<div id="addboarder-loader">
	<div class="lightbox-content">
		<!-- you lightbox content place here -->
	</div>
</div>

javascript:
a. inline call:
<a class="lightbox" lightbox="addboarder-loader" lightbox-width="100" />Click here to show lightbox</a>

b. function call
<script>
function showlightbox(){
	runLightbox('addboarder-loader',100);
}
</script>
*/

$(document).ready(function(){
	$('body').prepend('<div id="popup-overlay"></div><div id="popup-inner"></div>');
	initLightbox();
	
	$('.lightbox').each(function(){
		$(this).bind('click',function(){
			var lcontent = $('#'+$(this).attr('lightbox')).html();
			insertLightboxContents(lcontent);
			$('div.lighbox-content').css('width',$(this).attr('lightbox-width'));
			adjustLightboxVertically();
			showLightbox();
		});
	});
	
});

function runLightbox(a,b){
	var lcontent = $('#'+a).html();
	insertLightboxContents(lcontent);
	$('div.lighbox-content').css('width',b);
	adjustLightboxVertically();
	showLightbox();
}

function initLightbox(){
	$('#popup-overlay').css({
												position:'fixed',
												left:0,
												top:0,
												width:'100%',
												opacity:'0.2',
												backgroundColor:'#000',
												display:'none',
												'-ms-filter':'"progid:DXImageTransform.Microsoft.Alpha(Opacity=20)"',
												'filter':'alpha(opacity=20)',
												'z-index':1,
												height:$(document).height()
										   });
	$('#popup-inner').css({
											position:'fixed',
											left:0,
											top:0,
											width:'100%',
											display:'none',
											'z-index':2,
											height:$(document).height()
									    });					
}

function showLightbox(){
	$('#popup-overlay').show();
	$('#popup-inner').show();
	$('div#popup-inner div.lighbox-content').show();
}

function hideLightbox(){
	$('#popup-overlay').hide();
	$('#popup-inner').hide();
}

function insertLightboxContents(c){	
	$('#popup-inner').html('');
	$('#popup-inner').html(c);
}

function adjustLightboxVertically(){
	var ptop = ( $(window).height() - $('div.lighbox-content').height() ) / 2;
	$('div.lighbox-content').css('margin-top', ptop);
}