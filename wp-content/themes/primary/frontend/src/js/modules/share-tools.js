import $ from 'jquery';
import { nodeConfig } from '@/mixins';
import { keyCodes } from '@/helpers/config';

const NAMESPACE = 'share-tools';
const DEFAULT_SELECTOR = '.js-share-tools';
const DEFAULT_SETTINGS = {
	//
};

class ShareTools {
	constructor(node, config) {
		this.$node = $(node);
		this.config = config;

		this.url = window.location.href;
		this.title = encodeURIComponent($('title').text());
		this.description = $('meta[property="og:description"]').attr('content');

		this.$option = this.$node.find('.js-share-tools-list').first();
		this.$toggle = this.$node.find('.js-share-tools-toggle');
		this.$tools = this.$node.find('.js-share-tool');
		this.$toolItems = this.$node.find('.js-share-tool-item');

		this.$share = {
			facebook: this.$node.find('.js-share-facebook'),
			twitter: this.$node.find('.js-share-twitter'),
			linkedin: this.$node.find('.js-share-linkedin')
		};
	}

	get supportsNavigator() {
		return typeof navigator.share !== 'undefined';
	}

	get isActive() {
		return this.$node.hasClass('fs-swap-active');
	}

	closeOptions() {
		this.$toggle.attr('aria-expanded', 'false').trigger('focus');
		this.$option.attr('aria-hidden', 'true').find('.js-share-tool').attr('tabindex', '-1');
	}

	openOptions() {
		this.$toggle.attr('aria-expanded', 'true');
		this.$option.attr('aria-hidden', 'false').find('.js-share-tool').removeAttr('tabindex');
	}

	shareViaNavigator() {
		navigator.share({
			title: this.title,
			text: this.description,
			url: this.url
		});
	}

	shareViaPopout() {
		if (!this.isActive) {
			this.closeOptions();

			return;
		}

		this.$toolItems.first().transition({ always: false, property: 'opacity' }, () => {
			this.$option.first().find(this.$tools).first().focus();
		});
	}

	onToggleClick() {
		if (this.supportsNavigator) {
			this.shareViaNavigator();

			return;
		}

		this.shareViaPopout();
	}

	onToggleKeydown(event) {
		if (this.supportsNavigator) return;

		let keyCode = event.keyCode;

		if (![keyCodes.upArrow, keyCodes.downArrow].includes(keyCode)) return;

		event.preventDefault();

		if (!this.isActive) {
			this.$toggle.swap('activate');

			this.openOptions();
		}

		let lookup = {
			[keyCodes.upArrow]: () => {
				this.$toolItems.first().transition({ always: false, property: 'opacity' }, () => {
					this.$option.find(this.$tools).last().trigger('focus');
				});
			},
			[keyCodes.downArrow]: () => {
				this.$toolItems.last().transition({ always: false, property: 'opacity' }, () => {
					this.$option.find(this.$tools).first().trigger('focus');
				});
			}
		};

		if (!this.isActive) return;

		if (keyCode in lookup) {
			lookup[keyCode]();
		}
	}

	onOptionsKeydown(event) {
		let keyCode = event.keyCode;

		if (
			![
				keyCodes.escape,
				keyCodes.upArrow,
				keyCodes.downArrow,
				keyCodes.home,
				keyCodes.end
			].includes(keyCode)
		) {
			return;
		}

		event.preventDefault();

		let $focused = $(':focus').closest(this.$toolItems);
		let focusedIndex = $focused.index();
		let lookup = {
			[keyCodes.escape]: () => {
				this.$toggle.swap('deactivate').trigger('focus');
			},
			[keyCodes.upArrow]: () => {
				if (focusedIndex > 0) {
					this.$tools.eq(focusedIndex - 1).trigger('focus');

					return;
				}

				this.$tools.last().trigger('focus');
			},
			[keyCodes.downArrow]: () => {
				if (!$focused.is(':last-of-type')) {
					this.$tools.eq(focusedIndex + 1).trigger('focus');

					return;
				}

				this.$tools.first().trigger('focus');
			},
			[keyCodes.home]: () => {
				this.$tools.first().trigger('focus');
			},
			[keyCodes.end]: () => {
				this.$tools.last().trigger('focus');
			}
		};

		if (keyCode in lookup) {
			lookup[keyCode]();
		}
	}

	bindUI() {
		this.$toggle.on({
			click: this.onToggleClick.bind(this),
			keydown: this.onToggleKeydown.bind(this)
		});
	}

	navigatorShareCheck() {
		if (this.supportsNavigator) return;

		this.$toggle.attr({ 'aria-haspopup': 'true', 'aria-expanded': 'false' }).swap();
		this.$option.attr('aria-hidden', 'true').on('keydown', this.onOptionsKeydown.bind(this));
		this.$tools.attr('tabindex', '-1');
	}

	setup() {
		this.$share.facebook.attr(
			'href',
			`https://www.facebook.com/sharer/sharer.php?u=${this.url}`
		);
		this.$share.twitter.attr(
			'href',
			`https://twitter.com/intent/tweet?text=${this.title}&url=${this.url}`
		);
		this.$share.linkedin.attr(
			'href',
			`https://www.linkedin.com/shareArticle?mini=true&url=${this.url}&title=${this.title}`
		);

		this.navigatorShareCheck();
	}

	init() {
		this.setup();
		this.bindUI();
	}
}

export default function factory(selector = DEFAULT_SELECTOR, settings = {}) {
	let listNode = document.querySelectorAll(selector);

	if (!listNode.length) return;

	return Array.from(listNode).map((node) => {
		let config = nodeConfig(node, NAMESPACE, settings, DEFAULT_SETTINGS);
		let module = new ShareTools(node, config);

		module.init();

		return module;
	});
}
