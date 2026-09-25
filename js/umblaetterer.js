/**
 * Umblätterer
 *
 * Three small jobs, none of which the page depends on: fold the running head
 * on narrow screens, draw the reading rule along the top edge of an article,
 * and remember which edition the reader prefers.
 *
 * Everything here degrades to a perfectly readable page if it never runs.
 */

(function () {
	'use strict';

	/* ---------------------------------------------------------------
	 * The running head, folded
	 * ------------------------------------------------------------- */

	function initNavigation() {
		const nav = document.getElementById('site-navigation');

		if (!nav) {
			return;
		}

		const button = nav.querySelector('.menu-toggle');
		const menu = nav.querySelector('ul');

		if (!button || !menu) {
			return;
		}

		if (!menu.id) {
			menu.id = 'primary-menu';
		}

		button.setAttribute('aria-controls', menu.id);

		button.addEventListener('click', function () {
			const open = nav.classList.toggle('toggled');
			button.setAttribute('aria-expanded', open ? 'true' : 'false');
		});

		// Close the fold when focus leaves it, so tabbing does not strand the reader.
		document.addEventListener('click', function (event) {
			if (!nav.classList.contains('toggled') || nav.contains(event.target)) {
				return;
			}

			nav.classList.remove('toggled');
			button.setAttribute('aria-expanded', 'false');
		});

		document.addEventListener('keydown', function (event) {
			if ('Escape' === event.key && nav.classList.contains('toggled')) {
				nav.classList.remove('toggled');
				button.setAttribute('aria-expanded', 'false');
				button.focus();
			}
		});
	}

	/* ---------------------------------------------------------------
	 * The reading rule
	 *
	 * A thread of ink along the top edge, measured against the article
	 * itself rather than the whole document — the masthead and the
	 * correspondence column are not part of the piece, and counting them
	 * makes the rule lie about how much is left.
	 * ------------------------------------------------------------- */

	function initReadingRule() {
		const rule = document.getElementById('reading-rule');
		const article = document.querySelector('.single .entry-content, .page .entry-content');

		if (!rule || !article) {
			return;
		}

		let ticking = false;

		function draw() {
			const box = article.getBoundingClientRect();
			const start = box.top + window.scrollY;
			const span = article.offsetHeight - window.innerHeight;
			const progress = span > 0 ? (window.scrollY - start) / span : 0;

			rule.style.transform = 'scaleX(' + Math.min(1, Math.max(0, progress)) + ')';
			ticking = false;
		}

		function onScroll() {
			if (!ticking) {
				window.requestAnimationFrame(draw);
				ticking = true;
			}
		}

		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', onScroll, { passive: true });
		draw();
	}

	/* ---------------------------------------------------------------
	 * Morgenblatt / Abendblatt
	 *
	 * The morning edition and the evening edition. The morning edition is
	 * always what a reader gets first: the ivory is the point of the theme,
	 * and a dark operating system is not a request for a dark newspaper.
	 * The night edition is reached only through this button, and the choice
	 * is then remembered. The attribute is set in the document head before
	 * first paint, so a returning reader never sees the wrong edition flash.
	 * ------------------------------------------------------------- */

	function initEdition() {
		const button = document.getElementById('edition-toggle');

		if (!button) {
			return;
		}

		const root = document.documentElement;
		const chrome = document.querySelector('meta[name="theme-color"]');

		function isNight() {
			return 'nacht' === root.getAttribute('data-edition');
		}

		function label() {
			const night = isNight();

			button.textContent = night ? 'Morgenblatt' : 'Abendblatt';
			button.setAttribute('aria-pressed', night ? 'true' : 'false');

			// Keep the browser chrome on the same stock as the page.
			if (chrome) {
				chrome.setAttribute('content', night ? '#11141b' : '#f4efe1');
			}
		}

		button.addEventListener('click', function () {
			const next = isNight() ? 'tag' : 'nacht';

			root.setAttribute('data-edition', next);

			try {
				localStorage.setItem('umbl-edition', next);
			} catch (err) {
				// A reader with storage blocked still gets the switch, just not the memory.
			}

			label();
		});

		label();
	}

	function init() {
		initNavigation();
		initReadingRule();
		initEdition();
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
}());
