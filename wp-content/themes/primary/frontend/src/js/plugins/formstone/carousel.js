import { icon } from '@/helpers/utils';

const DEFAULT_SETTINGS = {
	selector: '.js-carousel',
	icons: {
		close: icon('close'),
		prev: icon('caret_left'),
		next: icon('caret_right')
	}
};

class FormstoneCarousel {
	constructor(node, config) {
		this.$node = $(node);
		this.config = config;
	}

	get $visibleTabable() {
		return this.$node.find(
			'.fs-carousel-item.fs-carousel-visible a, .fs-carousel-item.fs-carousel-visible button'
		);
	}

	onUpdate() {
		this.$node.find('.fs-carousel-control').attr('disabled', '');

		this.$node.find('.fs-carousel-item a, .fs-carousel-item button').attr('tabindex', '-1');

		setTimeout(() => {
			this.$node.find('.fs-carousel-control.fs-carousel-visible').removeAttr('disabled');

			this.$visibleTabable.removeAttr('tabindex');
		}, 0);
	}

	getBtnMarkup(icon, text) {
		return `
			<span class="fs-carousel-control-icon">${icon}</span>
			<span class="fs-carousel-control-label">${text}</span>
		`;
	}

	scaffold() {
		let $nodeBtnPrev = this.$node.find('.fs-carousel-control_previous');
		let btnPrevText = $nodeBtnPrev.text();
		let $nodeBtnNext = this.$node.find('.fs-carousel-control_next');
		let btnNextText = $nodeBtnNext.text();
		let $listGalleryItems = this.$node.find('.fs-carousel-item');

		$nodeBtnPrev
			.attr('disabled', '')
			.html(this.getBtnMarkup(this.config.icons.prev, btnPrevText));
		$nodeBtnNext
			.attr('disabled', '')
			.html(this.getBtnMarkup(this.config.icons.next, btnNextText));

		if ($nodeBtnPrev.is('.fs-carousel-visible')) {
			$nodeBtnPrev.removeAttr('disabled');
		}

		if ($nodeBtnNext.is('.fs-carousel-visible')) {
			$nodeBtnNext.removeAttr('disabled');
		}

		let anyParentIsEnabled = $listGalleryItems
			.parents('.fs-carousel')
			.hasClass('fs-carousel-enabled');

		if (anyParentIsEnabled) {
			$listGalleryItems.find('a, button').attr('tabindex', -1);
		}

		this.$visibleTabable.removeAttr('tabindex');
	}

	bindUI() {
		this.$node.on('update.carousel', this.onUpdate.bind(this));
	}

	setup() {
		this.$node.carousel();

		this.scaffold();
	}

	init() {
		this.setup();
		this.bindUI();
	}
}

export default function factory(settings = {}) {
	let config = Object.assign({}, DEFAULT_SETTINGS, settings);
	let listNode = document.querySelectorAll(config.selector);

	if (!listNode.length) return;

	return Array.from(listNode).map((node) => {
		return new FormstoneCarousel(node, config).init();
	});
}
