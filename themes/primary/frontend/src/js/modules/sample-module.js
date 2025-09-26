/**
 * 1. Dependencies
 *
 * This section can pull in mixins, other modules, helpers, etc
 *
 * 2. Configuration
 *
 * This section defines default options for this module.
 * For settings, the order is as follows (ordered highest priority to lowest):
 *
 * Namespace - data-[namespace] key for grabbing inline options
 *
 * - data-[namespace]-key attributes on the node
 * - data-[namespace] attribute on the node
 * - user passed in settings (into the factory function)
 * - default settings
 *
 * 3. Module
 *
 * This section defines the code for your module.
 * It should always be scoped to one node.
 *
 * 4. Factory function
 *
 * This section defines the default exported function for your module.
 * This code should new up an instance of your module for each node that will consume it.
 */

// 1. Dependencies
import $ from 'jquery';
import { events, nodeConfig } from '@/mixins';

// 2. Configuration
const NAMESPACE = 'sample-module';
const DEFAULT_SELECTOR = '.js-sample-module';
const DEFAULT_SETTINGS = {
	option: 'value',
	anotherOption: 'some-value',
	active: true
};

// 3. Module
class SampleModule {
	constructor(node, config) {
		this.$node = $(node);
		this.config = config;
	}

	/**
	 * getter
	 * use like a variable - this.isActive
	 */
	get isActive() {
		return this.$node.hasClass('active');
	}

	onClick(event) {
		//
	}

	onResize(event) {
		//
	}

	bindUI() {
		this.$node.on('click', this.onClick.bind(this));

		events.window.subscribe('resize', this.onResize.bind(this));
	}

	init() {
		this.bindUI();
	}
}

// 4. Factory Function
export default function factory(selector = DEFAULT_SELECTOR, settings = {}) {
	let listNode = document.querySelectorAll(selector);

	if (!listNode.length) return;

	return Array.from(listNode).map((node) => {
		let config = nodeConfig(node, NAMESPACE, settings, DEFAULT_SETTINGS);
		let module = new SampleModule(node, config);

		module.init();

		return module;
	});
}
