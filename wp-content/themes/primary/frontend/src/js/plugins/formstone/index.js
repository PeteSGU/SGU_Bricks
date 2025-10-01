import $ from 'jquery';
import 'formstone/src/js/core';
import 'formstone/src/js/mediaquery';
import 'formstone/src/js/analytics';
import 'formstone/src/js/carousel';
import 'formstone/src/js/checkpoint';
import 'formstone/src/js/cookie';
import 'formstone/src/js/equalize';
import 'formstone/src/js/lightbox';
import 'formstone/src/js/sticky';
import 'formstone/src/js/swap';
import 'formstone/src/js/touch';
import 'formstone/src/js/transition';
import 'formstone/src/js/viewer';
import formstoneCarousel from '@/plugins/formstone/carousel';
import { icon } from '@/helpers/utils';
import { settings } from '@/helpers/config';

const DEFAULT_SETTINGS = {
	icons: {
		close: icon('close'),
		prev: icon('caret_left'),
		next: icon('caret_right')
	}
};

class Formstone {
	constructor(config) {
		this.config = config;
	}

	get shouldInit() {
		return window.Formstone;
	}

	get lightboxConfig() {
		return {
			videoWidth: 1000,
			labels: {
				close: `<span class='fs-lightbox-icon-close'>${this.config.icons.close}</span>`,
				previous: `<span class='fs-lightbox-icon-previous'>${this.config.icons.prev}</span>`,
				count: `<span class='fs-lightbox-meta-divider'></span>`,
				next: `<span class='fs-lightbox-icon-next'>${this.config.icons.next}</span>`
			}
		};
	}

	setup() {
		$('.js-equalize').equalize();
		$('.js-lightbox').lightbox(this.lightboxConfig);
		$('.js-swap').swap();

		if (typeof $.mediaquery !== 'undefined') {
			$.mediaquery({
				minWidth: Object.values(settings.breakpoints)
			});
		}

		if (typeof $.cookie !== 'undefined') {
			$.cookie({ path: '/' });
		}

		formstoneCarousel();
	}

	init() {
		if (!this.shouldInit) return;

		this.setup();
	}
}

export default function factory(settings = {}) {
	let config = Object.assign({}, DEFAULT_SETTINGS, settings);

	let module = new Formstone(config);

	module.init();

	return module;
}
