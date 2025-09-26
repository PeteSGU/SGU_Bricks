import $ from 'jquery';
import { nodeConfig } from '@/mixins';
import { keyCodes } from '@/helpers/config';
import { getScrollbarWidth } from '@/helpers/utils';

const NAMESPACE = 'menu';
const DEFAULT_SELECTOR = '.js-menu';
const DEFAULT_SETTINGS = {
	//
};

class Menu {
	constructor(node, config) {
		this.$node = $(node);
		this.config = config;

		this.$toggle = $('.js-menu-toggle').first();
		this.$tabable = this.$node.find('.js-nav-link, button, input');
		this.$btnClose = this.$node.find('.js-menu-close-toggle').first();
		this.$shiftingElements = $('.header, .page, .footer');

		this.$document = $(document);
		this.$body = $('body');

		this.classLookup = {
			lock: 'fs-page-lock'
		};
	}

	get isFocused() {
		return this.$node.is(':focus');
	}

	onNodeTransitionEnd(event) {
		let propertyName = event?.originalEvent?.propertyName || false;

		if (propertyName !== 'opacity') return;

		this.$node.trigger('focus');
	}

	onKeyup(event) {
		let keyCode = event.keyCode;

		if (keyCode !== keyCodes.escape) return;
		if (!this.isFocused) return;

		this.$toggle.swap('deactivate');
	}

	onKeydown(event) {
		if ([!this.isFocused, event.keyCode !== keyCodes.tab, !event.shiftKey].some(Boolean))
			return;

		event.preventDefault();

		this.$btnClose.trigger('focus');
	}

	onSwapActivate() {
		let scrollbarWidth = getScrollbarWidth();

		this.$body.toggleClass(this.classLookup.lock, true);

		this.$toggle.attr('aria-expanded', 'true');

		this.$node.attr({ 'aria-hidden': 'false', tabindex: '0' });

		this.$tabable.removeAttr('tabindex');

		this.$shiftingElements.css({
			paddingRight: scrollbarWidth
		});

		this.$node.css({
			marginRight: '',
			width: ''
		});
	}

	onSwapDeactivate() {
		let scrollbarWidth = getScrollbarWidth();

		this.$body.toggleClass(this.classLookup.lock, false);

		this.$node.attr('aria-hidden', 'true').removeAttr('tabindex');

		this.$tabable.attr('aria-expanded', 'false');

		this.$toggle.trigger('focus');

		this.$shiftingElements.css({
			paddingRight: ''
		});

		this.$node.css({
			marginRight: scrollbarWidth * -1,
			width: `calc(100% + ${scrollbarWidth}px)`
		});
	}

	onSwapEnable() {
		setTimeout(function () {
			$('.js-menu').addClass('transitions');
		}, 0);
	}

	onCloseKeydown(event) {
		if ([event.keyCode !== keyCodes.tab, event.shiftKey].some(Boolean)) return;

		this.$node.trigger('focus');
	}

	onDocumentClick(event) {
		let $target = $(event.target);
		let bodyHasLockClass = this.$body.hasClass(this.classLookup.lock);
		let eventOccuredInsideMenu = !!$target.closest(this.$node).length;

		if (!bodyHasLockClass || eventOccuredInsideMenu) return;

		this.$toggle.swap('deactivate');
	}

	bindUI() {
		this.$node.on({
			keydown: this.onKeydown.bind(this),
			keyup: this.onKeyup.bind(this),
			transitionend: this.onNodeTransitionEnd.bind(this)
		});

		this.$toggle.on({
			'activate.swap': this.onSwapActivate.bind(this),
			'deactivate.swap': this.onSwapDeactivate.bind(this)
		});

		this.$toggle.swap().on({ 'enable.swap': this.onSwapEnable() });

		this.$btnClose.on({
			keydown: this.onCloseKeydown.bind(this),
			click: this.onSwapDeactivate.bind(this)
		});

		this.$document.on('click touchstart', this.onDocumentClick.bind(this));
	}

	setup() {
		this.$node.attr({
			role: 'dialog',
			'aria-modal': 'true',
			'aria-hidden': 'true'
		});

		this.$tabable.attr('tabindex', '-1');

		this.$toggle.attr({ 'aria-expanded': 'false', role: 'button' });
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
		let module = new Menu(node, config);

		module.init();

		return module;
	});
}
