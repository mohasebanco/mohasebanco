(function () {
	'use strict';

	var MOBILE_MAX = 768;
	var CLEARANCE = 110;
	var Z = '100050';

	function isMobile() {
		return window.matchMedia('(max-width: ' + MOBILE_MAX + 'px)').matches;
	}

	function ensureCrispLocale() {
		window.CRISP_RUNTIME_CONFIG = window.CRISP_RUNTIME_CONFIG || {};
		window.CRISP_RUNTIME_CONFIG.locale = 'fa';
		if (!window.$crisp) {
			window.$crisp = [];
		}
		window.$crisp.push(['config', 'locale', ['fa']]);
	}

	function setCrispOpen(open) {
		document.documentElement.classList.toggle('drdr-crisp-open', !!open);
		document.documentElement.classList.toggle('drdr-crisp-closed', !open);
	}

	function bindCrispEvents() {
		if (!window.$crisp) {
			window.$crisp = [];
		}
		window.$crisp.push(['on', 'chat:opened', function () {
			setCrispOpen(true);
		}]);
		window.$crisp.push(['on', 'chat:closed', function () {
			setCrispOpen(false);
		}]);
		window.$crisp.push(['on', 'session:loaded', function () {
			window.$crisp.push(['config', 'locale', ['fa']]);
		}]);
	}

	function liftContactWidget() {
		if (!isMobile()) {
			return;
		}

		var roots = document.querySelectorAll('[id^="chaty-widget"], .chaty');
		for (var r = 0; r < roots.length; r++) {
			roots[r].style.setProperty('z-index', Z, 'important');
		}

		var widgets = document.querySelectorAll('.chaty-widget, [id^="chaty-widget"] .chaty-widget');
		for (var i = 0; i < widgets.length; i++) {
			widgets[i].style.setProperty('bottom', CLEARANCE + 'px', 'important');
			widgets[i].style.setProperty('z-index', Z, 'important');
		}

		var forms = document.querySelectorAll('.chaty-outer-forms, .chaty-chat-view');
		for (var f = 0; f < forms.length; f++) {
			forms[f].style.setProperty('bottom', CLEARANCE + 8 + 'px', 'important');
			forms[f].style.setProperty('z-index', '100051', 'important');
		}

		var tops = document.querySelectorAll('i.backtotop');
		for (var t = 0; t < tops.length; t++) {
			tops[t].style.setProperty('bottom', CLEARANCE + 'px', 'important');
			tops[t].style.setProperty('z-index', '100040', 'important');
		}
	}

	ensureCrispLocale();
	bindCrispEvents();
	document.documentElement.classList.add('drdr-crisp-closed');

	function run() {
		liftContactWidget();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', run);
	} else {
		run();
	}

	window.addEventListener('resize', run);
	window.addEventListener('orientationchange', run);

	var observer = new MutationObserver(function () {
		run();
	});
	observer.observe(document.documentElement, { childList: true, subtree: true });

	setTimeout(run, 600);
	setTimeout(run, 1500);
	setTimeout(run, 3500);
}());
