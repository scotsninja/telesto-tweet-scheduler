// Copyright 20xx Productions

/* Global Variables */

$MONTHS = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
$MONTHS_SHORT = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
AJAX_URL_OUTPUT = '/src/ajax/ajax_output.php';
AJAX_URL_REQUEST = '/src/ajax/ajax_requests.php';

/* Global Functions */

function trackEvent(cat, act, lab) {
	if ('_gaq' in window) {
		_gaq.push(['_trackEvent', cat, act, lab]);
	}
}

function replaceURLs(text) {
	var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
	return text.replace(exp, '<a href="$1" target="_blank">$1</a>');
}

function openMask() {
	var maskHeight = $(document).height();
	var maskWidth = $(document).width();
	
	$('#mask').css({'width':maskWidth,'height':maskHeight});
	
	$('#mask').fadeIn(500);
	$('#mask').fadeTo("slow", 0.8);
}

function closeMask() {
	$('#mask').fadeOut();
}

function centerWindow(sel) {
	var winH = $(window).height();
	var winW = $(window).width();
	
	$(sel).css("top", winH/2-$(sel).height()/2);
	$(sel).css("left", winW/2-$(sel).width()/2);
}

function initTooltips() {
	if (gTooltips) {
		$('.tip').tooltip({
			placement: 'right'
		});
		$('.img-tip').tooltip({
			placement: 'bottom'
		});
	}
}

function getCookie(name) {
	var i, x, cookieArr = document.cookie.split(';');

	for (i=0; i<cookieArr.length;i++) {
		x = cookieArr[i].substr(0,cookieArr[i].indexOf('='));
		x=x.replace(/^\s+|\s+$/g,'');
		if (x == name) {
			return cookieArr[i].substr(cookieArr[i].indexOf('=')+1);
		}
	}
}

function setCookie(name, value, time) {
	var exDate = new Date(), cValue;
	exDate.setDate(exDate.getDate()+(Math.round(time/86400)));
	
	cValue = name + '=' + escape(value)+'; ';
	cValue += 'expires='+exDate.toUTCString()+'; ';
	cValue += 'path=/';

	document.cookie = cValue;
}

function deleteCookie(name) {
	setCookie(name, null, -1);
}

function setPageNum(field, page) {
	$('#'+field).val(page);
}

function init() {
	initTooltips();

	// store last page viewed
	if (window.storePage == undefined || window.storePage == true) {
		setCookie('last-page-viewed', window.location.pathname, 86400);
	}

	// benchmark page load
	var url = AJAX_URL_REQUEST,
		dataArr = {
			"requireLogin": 0,
			"action": "log-pageload",
			"start": bmStartTime,
			"pageId": bmPageId,
			"page": bmPage,
			"vars": bmVars
		};
	
	if (bmLevel > 1) {
		$.post(url, dataArr);
	
		// benchmark page render
		$(window).load(function () {
			var url = AJAX_URL_REQUEST,
				dataArr = {
					"requireLogin": 0,
					"action": "log-pagerender",
					"start": bmStartTime,
					"pageId": bmPageId,
					"page": bmPage,
					"vars": bmVars
				};

			$.post(url, dataArr);
		});
	}
}