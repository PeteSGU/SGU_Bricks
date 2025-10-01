import $ from 'jquery';
import { nodeConfig } from '@/mixins';
import { keyCodes } from '@/helpers/config';

const NAMESPACE = 'accordion';
const DEFAULT_SELECTOR = '.js-accordion';
const DEFAULT_SETTINGS = {
	//
};

class Accordion {
	constructor(node, config) {
		this.$node = $(node);
		this.config = config;

		this.$content = this.$node.find('.js-accordion-content');
		this.$swap = this.$node.find('.js-accordion-swap');
		this.$item = this.$node.find('.js-accordion-item');
	}

	get hasActive() {
		return this.$node.find('.fs-swap-active').length;
	}

	onTriggerActivate(event) {
		let $target = $(event.currentTarget);
		let swapTarget = $target.data('swapTarget');

		$target.attr('aria-expanded', 'true');

		this.$item.filter(swapTarget).attr('aria-hidden', 'false');
		setTimeout(() => {
			$target.parents('.js-accordion-item').find('.js-accordion-content').addClass('active');
		}, 100);
	}

	onTriggerDeactivate(event) {
		let $target = $(event.currentTarget);
		let swapTarget = $target.data('swapTarget');

		$target.attr('aria-expanded', 'false');

		this.$item.filter(swapTarget).attr('aria-hidden', 'true');
		$target.parents('.js-accordion-item').find('.js-accordion-content').removeClass('active');
	}

	focusFirst() {
		this.$node.find('.js-accordion-item:first-of-type .js-accordion-swap').trigger('focus');
	}

	focusLast() {
		this.$node.find('.js-accordion-item:last-of-type .js-accordion-swap').trigger('focus');
	}

	focusNext($node) {
		$node.next().find('.js-accordion-swap').trigger('focus');
	}

	focusPrev($node) {
		$node.prev().find('.js-accordion-swap').trigger('focus');
	}

	onTriggerKeydown(event) {
		let keyCode = event.keyCode;

		if (![keyCodes.home, keyCodes.end, keyCodes.upArrow, keyCodes.downArrow].includes(keyCode))
			return;

		let $focusedElement = $(':focus');
		let $focusedItem = $focusedElement.closest('.js-accordion-item');

		event.preventDefault();

		let lookup = {
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
		this.$swap.on({
			'activate.swap': this.onTriggerActivate.bind(this),
			'deactivate.swap': this.onTriggerDeactivate.bind(this),
			keydown: this.onTriggerKeydown.bind(this)
		});
	}

	setup() {
		if (this.hasActive) {
			this.$content.first().attr('aria-hidden', 'false');
			this.$item.first().find('.js-accordion-swap').attr('aria-expanded', 'true');
		}
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
		let module = new Accordion(node, config);

		module.init();

		return module;
	});
}
