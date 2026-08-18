(function () {
	'use strict';

	var MOBILE_MAX = 768;
	var CLEARANCE = 96;

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
		liftCrispLauncher();
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
			liftCrispLauncher();
		}]);
	}

	function applyFixedBottom(el, bottom) {
		if (!el || !el.style) {
			return;
		}

		el.style.setProperty('bottom', bottom + 'px', 'important');
	}

	function liftCrispLauncher() {
		if (!isMobile() || document.documentElement.classList.contains('drdr-crisp-open')) {
			return;
		}

		var root = document.querySelector('.crisp-client');
		if (!root) {
			return;
		}

		var nodes = root.querySelectorAll('*');
		for (var i = 0; i < nodes.length; i++) {
			var el = nodes[i];
			var style = window.getComputedStyle(el);
			if (style.position !== 'fixed') {
				continue;
			}

			var bottom = parseFloat(style.bottom);
			if (isNaN(bottom) || bottom > 140) {
				continue;
			}

			applyFixedBottom(el, CLEARANCE);
		}
	}

	ensureCrispLocale();
	bindCrispEvents();
	document.documentElement.classList.add('drdr-crisp-closed');

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', liftCrispLauncher);
	} else {
		liftCrispLauncher();
	}

	window.addEventListener('resize', liftCrispLauncher);
	window.addEventListener('orientationchange', liftCrispLauncher);

	var observer = new MutationObserver(function () {
		liftCrispLauncher();
	});

	observer.observe(document.documentElement, {
		childList: true,
		subtree: true
	});

	setTimeout(liftCrispLauncher, 800);
	setTimeout(liftCrispLauncher, 2500);
}());
