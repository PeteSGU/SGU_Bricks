import $ from 'jquery';

const DEFAULT_SETTINGS = {
	scroll: {
		offsetAmount: 20
	}
};

class Page {
	constructor(config) {
		this.config = config;

		this.$header = $('.js-header').first();
		this.$toggle = $('.js-toggle').not('.js-bound');
		this.$scrollTo = $('.js-scroll-to').not('.js-bound');
		this.$videoEmbedsInType = $(
			`iframe[src*='vimeo.com'], iframe[src*='youtube.com']`,
			'.typography'
		);
		this.$tablesInType = $('table', '.typography');
		this.$window = $(window);
		this.$html = $('html');
		this.$body = $('body');
	}

	get $tableWrappers() {
		return $('.table_wrapper');
	}

	get headerHeight() {
		return this.$header.outerHeight(true);
	}

	scrollToPosition(position) {
		let scrollTop = position;

		if (this.config.useFixedHeader) {
			scrollTop -= this.headerHeight;
		}

		$('html, body').animate({
			scrollTop: scrollTop - this.config.scroll.offsetAmount
		});
	}

	scrollToElement(selector) {
		let offset = $(selector).offset();

		if (!offset) return;

		this.scrollToPosition(offset.top);
	}

	onToggleClick(event) {
		let $target = $(event.delegateTarget);

		event.preventDefault();
		event.stopPropogation();

		$target.toggleClass('.ks-toggle-active');
	}

	updateTableAttributes() {
		this.$tableWrappers.each((index, node) => {
			let $node = $(node);

			let $inner = $node.find('.table_wrapper_inner');
			let scrollWidth = $inner.get(0).scrollWidth;
			let clientWidth = $inner.get(0).clientWidth;

			if (scrollWidth > clientWidth) {
				$node.addClass('table_wrapper_overflow').attr({ tabindex: '0', role: 'group' });
			} else {
				$node.removeClass('table_wrapper_overflow').removeAttr('tabindex role');
			}
		});
	}

	onScrollToClick(event) {
		let $target = $(event.delegateTarget).attr('href');

		event.preventDefault();
		event.stopPropogation();

		this.scrollToElement($target);
	}

	onFormstoneReady() {
		this.$body.removeClass('preload').addClass('loaded');
		this.$window.trigger('resize');
	}

	onWindowResize() {
		this.updateTableAttributes();
	}

	bindUI() {
		this.$toggle.on('click', '.js-toggle-handle', this.onToggleClick.bind(this));
		this.$scrollTo.on('click', this.onScrollToClick.bind(this));

		window.Formstone.Ready(this.onFormstoneReady.bind(this));

		window.addEventListener('resize', this.onWindowResize.bind(this));
	}

	setup() {
		this.$toggle.addClass('js-bound');
		this.$scrollTo.addClass('js-bound');

		this.$videoEmbedsInType.removeAttr('style').wrap('<div class="video_frame"></div>');

		this.$tablesInType.wrap(
			'<div class="table_wrapper"><div class="table_wrapper_inner"></div></div>'
		);
		this.updateTableAttributes();
	}

	init() {
		this.setup();
		this.bindUI();
	}
}

export default function factory() {
	let module = new Page(DEFAULT_SETTINGS);

	module.init();

	return module;
}
