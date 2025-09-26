
'use strict';
/* eslint-disable */ //TODO: be aware of unused vars
class Trigger {
	constructor(element, options = {}) {
		this.element = element;
		this.options = this.parseOptions(options);
		this.init();
	}

	parseOptions(options) {
		options = options || {};

		if (!('toggleClass' in options)) {
			options.targetSelector = null;
			options.classToToggle = null;
			return options;
		}
		const normalizedTarget = this.normalizeTargetSelector(options.targetSelector);
		options.targetSelector = normalizedTarget ? normalizedTarget.selector : null;
		options.targetSelectorType = normalizedTarget ? normalizedTarget.targetSelectorType : null;
		options.classToToggle = this.normalizeClassToToggle(options.classToToggle);

		return options;
	}

	normalizeTargetSelector(selector) {
		if (typeof selector !== 'string' || !selector.trim()) {
			console.error('Missing targetSelector in options.', this.options);
			return null;
		}

		selector = selector.replace(/^"|"$/g, '').replace(/\\"/g, '"').trim();

		if (selector.startsWith('#')) {
			return { selector, targetSelectorType: 'id' };
		}

		if (selector.startsWith('.')) {
			return { selector, targetSelectorType: 'class' };
		}

		if (/^[a-zA-Z0-9_-]+$/.test(selector)) {
			return { selector: `.${selector}`, targetSelectorType: 'class' };
		}

		if (selector.startsWith('[')) {
			return { selector, targetSelectorType: 'data' };
		}

		return { selector, targetSelectorType: 'class' };
	}

	normalizeClassToToggle(className) {
		if (typeof className !== 'string' || !className.trim()) {
			console.error('Invalid classToToggle:', className);
			return null;
		}
		className = className.startsWith('.') ? className.slice(1) : className.trim();

		return className.length > 0 ? className : null;
	}


	init() {
		this.element.addEventListener('click', () => {
			this.toggleMenu();
		});

		this.element.addEventListener('keydown', (e) => {
			if (e.key === 'Enter') {
				e.preventDefault();
				this.toggleMenu();
			}
		});
	}

	toggleMenu() {
		if (this.isBurgerType()) {
        	this.element.classList.toggle('fr-hamburger--active');
		} else if (this.isButtonType()) {
			this.element.classList.toggle('fr-button-trigger--active');
			this.toggleButtonText();
		}

		if (this.options.classToToggle) {
			this.toggleTargetClass();
		}

		this.element.setAttribute('aria-expanded',
			this.element.getAttribute('aria-expanded') === 'true' ? 'false' : 'true'
		);
	}

	toggleButtonText() {
		if (this.options.useActiveText) {
			if (this.element.classList.contains('fr-button-trigger--active')) {
				this.element.querySelector('.fr-button-trigger__text').innerHTML = this.options.buttonActiveText;
			} else {
				this.element.querySelector('.fr-button-trigger__text').innerHTML = this.options.buttonText;
			}
		}

	}

	isBurgerType() {
		return this.element.classList.contains('fr-hamburger');
	}

	isButtonType() {
		return this.element.classList.contains('fr-button-trigger');
	}

	toggleTargetClass() {
		if (!this.options.targetSelector || !this.options.classToToggle) {
			console.error(`
				Cant find the selector or the 'targetSelector' or 'classToToggle' not found in options.
				`, this.options);
			return;
		}

		let targetElements = [];
		const { targetSelector, targetSelectorType } = this.options;

		if (targetSelectorType === 'id') {
			const element = document.getElementById(targetSelector.slice(1));
			if (element) targetElements.push(element);
		} else {
			targetElements = [...document.querySelectorAll(targetSelector)];
		}

		if (targetElements.length === 0) {
			console.error('Selector incorrect or not found:', targetSelector);
			return;
		}

		targetElements.forEach((element) => element.classList.toggle(this.options.classToToggle));
	}

}

function trigger_script() {

	const triggers = document.querySelectorAll('.brxe-fr-trigger');
	triggers.forEach((trigger) => {
		let options = {};
		if (trigger.dataset.frTriggerOptions) {
			try {
				options = JSON.parse(trigger.dataset.frTriggerOptions);
			} catch (error) {
				console.error('Error parsing frTriggerOptions', error);
			}
		}
		new Trigger(trigger, options);
	});

}

document.addEventListener('DOMContentLoaded', function (e) {
	bricksIsFrontend && trigger_script();
});
