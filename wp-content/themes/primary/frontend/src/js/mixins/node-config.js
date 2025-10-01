import $ from 'jquery';
import { mergeDeep } from '@/helpers/utils';

const normalizeDataset = (dataset, namespace) => {
	if (!(dataset instanceof DOMStringMap)) return {};

	return Object.entries(dataset).reduce((carry, [key, value]) => {
		key = key.toLowerCase();

		if (!key.includes(namespace)) return carry;

		key = key.replace(namespace, '');

		try {
			value = JSON.parse(value);
		} catch (error) {
			//
		}

		/**
		 * if it's the root namespace
		 * i.e. - data-accordion="" vs data-accordion-key=""
		 */
		if (!key.length) {
			carry = mergeDeep({}, carry, value);

			return carry;
		}

		carry[key] = value;

		return carry;
	}, {});
};

const MixinNodeConfig = (node, namespace, ...settings) => {
	if (node instanceof $) {
		node = node[0];
	}

	if (!(node instanceof HTMLElement)) return {};

	let namespaceReplacements = [
		{
			regexp: /^\.js-/,
			newSubstr: ''
		},
		{
			regexp: /[^a-zA-Z0-9]/g,
			newSubstr: ''
		}
	];

	let normalizedNamespace = namespaceReplacements.reduce(
		(carry, entry) => carry.replace(entry.regexp, entry.newSubstr),
		namespace
	);

	let dataSettings = normalizeDataset(node.dataset, normalizedNamespace);

	return mergeDeep({}, ...settings, dataSettings);
};

export default MixinNodeConfig;
