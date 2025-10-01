import $ from 'jquery';
import { nodeConfig } from '@/mixins';
import { keyCodes } from '@/helpers/config';

const NAMESPACE = 'nav-toggle';
const DEFAULT_SELECTOR = '.js-nav-toggle-group';
const DEFAULT_SETTINGS = {
	//
};

class NavToggle {
	constructor(node, config) {
		this.$node = $(node);
		this.config = config;

		this.$trigger = this.$node.find('.js-nav-toggle-button');
		this.$panel = this.$node.find('.js-nav-toggle-panel');
		this.$link = this.$node.find('.js-nav-toggle-link');
		this.linkFocusDirection = 'first';
	}

	openPopup() {
		let panelStyle = window.getComputedStyle(this.$panel[0], null).getPropertyValue("transition");

		this.$trigger.attr('aria-expanded', 'true');
		this.$link.removeAttr('tabindex');
		this.$panel.attr('aria-hidden', 'false');

		if (panelStyle != "all 0s ease 0s") {
			this.$panel.transition({ always: false, property: 'opacity' }, () => {
				this.linkFocusDirection == 'first'
					? this.$link.first().trigger('focus')
					: this.$link.last().trigger('focus');
			});
		} else {
			this.linkFocusDirection == 'first'
				? this.$link.first().trigger('focus')
				: this.$link.last().trigger('focus');
		}
	}

	closePopup() {
		this.$trigger.attr('aria-expanded', 'false').trigger('focus');
		this.$link.attr('tabindex', '-1');
		this.$panel.attr('aria-hidden', 'true');
	}

	onTriggerActivate() {
		this.openPopup();
	}

	onTriggerDeactivate() {
		this.closePopup();
	}

	focusFirst() {
		this.$node.find('.js-nav-toggle-item:first-of-type .js-nav-toggle-link').trigger('focus');
	}

	focusLast() {
		this.$node.find('.js-nav-toggle-item:last-of-type .js-nav-toggle-link').trigger('focus');
	}

	focusNext($node) {
		$node.next().find('.js-nav-toggle-link').trigger('focus');
	}

	focusPrev($node) {
		$node.prev().find('.js-nav-toggle-link').trigger('focus');
	}

	onTriggerKeydown(event) {
		let keyCode = event.keyCode;

		if (this.$trigger.attr('aria-expanded') == 'true') return;

		if (![keyCodes.upArrow, keyCodes.downArrow].includes(keyCode)) return;

		event.preventDefault();

		let lookup = {
			[keyCodes.upArrow]: () => {
				this.linkFocusDirection = 'last';
				this.$trigger.swap('activate');
				return;
			},
			[keyCodes.downArrow]: () => {
				this.linkFocusDirection = 'first';
				this.$trigger.swap('activate');
				return;
			}
		};

		if (keyCode in lookup) {
			lookup[keyCode]();
		}
	}

	onTriggerClick() {
		this.focusFirst(this.$node.closest('.js-nav-toggle-item'));
	}

	onPanelKeydown(event) {
		let keyCode = event.keyCode;

		if (
			![
				keyCodes.tab,
				keyCodes.escape,
				keyCodes.home,
				keyCodes.end,
				keyCodes.upArrow,
				keyCodes.downArrow
			].includes(keyCode)
		)
			return;

		if (![keyCodes.tab].includes(keyCode)) event.preventDefault();

		let $focusedElement = $(':focus');
		let $focusedItem = $focusedElement.closest('.js-nav-toggle-item');

		let lookup = {
			[keyCodes.tab]: () => {
				this.$trigger.swap('deactivate');
			},
			[keyCodes.escape]: () => {
				this.$trigger.swap('deactivate');
			},
			[keyCodes.home]: () => {
				this.focusFirst();
			},
			[keyCodes.end]: () => {
				this.focusLast();
			},
			[keyCodes.upArrow]: () => {
				if ($focusedItem.prev().length) {
					this.focusPrev($focusedItem);

					return;
				}

				this.focusLast();
			},
			[keyCodes.downArrow]: () => {
				if ($focusedItem.next().length) {
					this.focusNext($focusedItem);

					return;
				}

				this.focusFirst();
			}
		};

		if (keyCode in lookup) {
			lookup[keyCode]();
		}
	}

	bindUI() {
		this.$trigger.on({
			'activate.swap': this.onTriggerActivate.bind(this),
			'deactivate.swap': this.onTriggerDeactivate.bind(this),
			keydown: this.onTriggerKeydown.bind(this),
			click: this.onTriggerClick.bind(this)
		});
		this.$panel.on({
			keydown: this.onPanelKeydown.bind(this)
		});
	}

	setup() {
		this.$trigger.attr({
			'aria-haspopup': 'true',
			'aria-expanded': 'false'
		});
		this.$panel.attr('aria-hidden', 'true');
		this.$link.attr('tabindex', '-1');
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
		let module = new NavToggle(node, config);

		module.init();

		return module;
	});
}
