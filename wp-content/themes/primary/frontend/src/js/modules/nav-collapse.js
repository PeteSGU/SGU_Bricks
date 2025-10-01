import $ from 'jquery';
import { nodeConfig } from '@/mixins';
import { keyCodes } from '@/helpers/config';

const NAMESPACE = 'nav-collapse';
const DEFAULT_SELECTOR = '.js-nav-collapse-item';
const DEFAULT_SETTINGS = {
	//
};

class NavCollapse {
	constructor(node, config) {
		this.$node = $(node);
		this.config = config;

		this.$trigger = this.$node.find('.js-nav-collapse-toggle');
		this.$children = this.$node.find('.js-nav-collapse-children');
		this.$childLinks = this.$node.find('.js-nav-collapse-child-link');
	}

	get hasActive() {
		return this.$node.find('.fs-swap-active').length;
	}

	onTriggerActivate(event) {
		let $target = $(event.currentTarget);
		let swapTarget = $target.data('swapTarget');

		$target.attr('aria-expanded', 'true');

		this.$node
			.filter(swapTarget)
			.find('.js-nav-collapse-children')
			.attr('aria-hidden', 'false')
			.find('.js-nav-collapse-child-link')
			.removeAttr('tabindex');
	}

	onTriggerDeactivate(event) {
		let $target = $(event.currentTarget);
		let swapTarget = $target.data('swapTarget');

		$target.attr('aria-expanded', 'false');

		this.$node
			.filter(swapTarget)
			.find('.js-nav-collapse-children')
			.attr('aria-hidden', 'true')
			.find('.js-nav-collapse-child-link')
			.attr('tabindex', '-1');
	}

	focusFirst() {
		this.$node
			.find('.js-nav-collapse-child-item:first-of-type .js-nav-collapse-child-link')
			.trigger('focus');
	}

	focusLast() {
		this.$node
			.find('.js-nav-collapse-child-item:last-of-type .js-nav-collapse-child-link')
			.trigger('focus');
	}

	focusNext($node) {
		$node.next().find('.js-nav-collapse-child-link').trigger('focus');
	}

	focusPrev($node) {
		$node.prev().find('.js-nav-collapse-child-link').trigger('focus');
	}

	onTriggerKeydown(event) {
		let childStyle = window.getComputedStyle(this.$children[0], null).getPropertyValue("transition");

		let keyCode = event.keyCode;

		if (![keyCodes.upArrow, keyCodes.downArrow].includes(keyCode)) return;

		let $focusedElement = $(':focus');
		let $focusedItem = $focusedElement.closest('.js-nav-collapse-item');

		event.preventDefault();

		if (!$(this).hasClass('fs-swap-active')) {
			$focusedElement.swap('activate');
		}

		let lookup = {
			[keyCodes.upArrow]: () => {
				if (childStyle != "all 0s ease 0s") {
					this.$children.transition({ always: false, property: 'opacity' }, () => {
						this.focusLast($focusedItem);
					});
				} else {
					this.focusLast($focusedItem);
				}
			},
			[keyCodes.downArrow]: () => {

				if (childStyle != "all 0s ease 0s") {
					this.$children.transition({ always: false, property: 'opacity' }, () => {
						this.focusFirst($focusedItem);
					});
				} else {
					this.focusFirst($focusedItem);
				}
			}
		};

		if (keyCode in lookup) {
			lookup[keyCode]();
		}
	}

	onTriggerClick() {
		let childStyle = window.getComputedStyle(this.$children[0], null).getPropertyValue("transition");

		if (childStyle != "all 0s ease 0s") {
			this.$children.transition({ always: false, property: 'opacity' }, () => {
				this.focusFirst(this.$node.closest('.js-nav-collapse-item'));
			});
		} else {
			this.focusFirst(this.$node.closest('.js-nav-collapse-item'));
		}
	}

	onNavKeydown(event) {
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
		let $focusedItem = $focusedElement.closest('.js-nav-collapse-child-item');

		let lookup = {
			[keyCodes.tab]: () => {
				$focusedElement
					.closest('.js-nav-collapse-item')
					.find('.js-nav-collapse-toggle')
					.swap('deactivate');
			},
			[keyCodes.escape]: () => {
				$focusedElement
					.closest('.js-nav-collapse-item')
					.find('.js-nav-collapse-toggle')
					.swap('deactivate')
					.focus();
			},
			[keyCodes.home]: () => {
				this.focusFirst($focusedItem);
			},
			[keyCodes.end]: () => {
				this.focusLast($focusedItem);
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
		this.$children.on('keydown', this.onNavKeydown.bind(this));
	}

	setup() {
		this.$trigger.attr({
			'aria-haspopup': 'true',
			'aria-expanded': 'false'
		});
		this.$children.attr('aria-hidden', 'true');
		this.$childLinks.attr('tabindex', '-1');
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
		let module = new NavCollapse(node, config);

		module.init();

		return module;
	});
}
