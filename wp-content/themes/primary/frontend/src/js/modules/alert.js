import $ from 'jquery';
import { nodeConfig, cookie } from '@/mixins';
import { settings } from '@/helpers/config';

const NAMESPACE = 'alert';
const DEFAULT_SELECTOR = '.js-alert-wrapper';
const DEFAULT_SETTINGS = {
	cookie: {
		name: `${settings.namespace}-alert`
	}
};

class Alert {
	constructor(node, config) {
		this.$node = $(node);
		this.config = config;
		this.cookie = cookie(this.config.cookie.name);

		this.$skipLink = $('.js-skip-alert').first();
	}

	get ajaxNonce() {
		return window?.wp_ajax?.nonce;
	}

	get ajaxURL() {
		if (window?.AlertURL) {
			return window.AlertURL;
		}

		let baseURL = window?.wp_ajax?.url;

		if (!baseURL) return null;

		return `${baseURL}?action=get_alert&nonce=${this.ajaxNonce}`;
	}

	get $alert() {
		return this.$node.find('.js-alert').first();
	}

	get time() {
		return this.$alert?.data('time');
	}

	get $openBtn() {
		return $('.js-alert-open').first();
	}

	get $closeBtn() {
		return this.$alert.find('.js-alert-close').first();
	}

	_fetchMarkupViaAjax(cb) {
		$.ajax(this.ajaxURL).done((response) => {
			this.$node.html(response);

			cb();
		});
	}

	onCloseClick(event) {
		event.preventDefault();

		this.cookie.append(this.time);

		this.close();

		this.$closeBtn.trigger('blur');
	}

	onOpenClick(event) {
		event.preventDefault();

		if (this.$alert.hasClass('enabled')) {
			this.$alert.trigger('focus');

			return;
		}

		let curTime = this.time;
		let newCookieValue = this.cookie.valueAsArray.reduce((carry, value) => {
			if (value === curTime) return carry;

			carry.push(value);

			return carry;
		}, []);

		this.cookie.set(newCookieValue);

		this.open();

		this.$alert.transition(
			{
				always: false,
				property: 'transform'
			},
			() => {
				this.$alert.trigger('focus');
			}
		);
	}

	close() {
		// uncomment to show open button on close
		// this.$openBtn.addClass('enabled');

		this.$alert.removeClass('enabled').attr('aria-hidden', 'true');
		this.$alert.find('a, button').attr('tabindex', '-1');
	}

	open() {
		this.$openBtn.removeClass('enabled');
		this.$alert.addClass('enabled').attr('aria-hidden', 'false');
		this.$alert.find('a, button').removeAttr('tabindex');
	}

	bindUI() {
		this.$skipLink.on('click', this.onOpenClick.bind(this));
		this.$openBtn.on('click', this.onOpenClick.bind(this));
		this.$closeBtn.on('click', this.onCloseClick.bind(this));
	}

	setup() {
		this.$alert.toggleClass('enabled', true);
		this.$openBtn.toggleClass('enabled', true);

		if (!this.cookie.valueAsArray.includes(this.time)) {
			this.open();

			return;
		}

		this.close();
	}

	init() {
		let readyCb = () => {
			this.setup();
			this.bindUI();
		};

		if (this.ajaxURL) {
			this._fetchMarkupViaAjax(readyCb.bind(this));

			return;
		}

		readyCb();
	}
}

export default function factory(selector = DEFAULT_SELECTOR, settings = {}) {
	let node = document.querySelector(selector);

	if (!node) return;

	let config = nodeConfig(node, NAMESPACE, settings, DEFAULT_SETTINGS);
	let module = new Alert(node, config);

	module.init();

	return module;
}
