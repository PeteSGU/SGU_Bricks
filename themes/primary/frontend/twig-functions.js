let twig_counter = 0;
let twig_counter_namespaces = {};

module.exports = {
	icon: function (name) {
		return `
			<svg class="icon icon_${name}">
				<use href="/images/icons.svg#${name}" />
			</svg>
		`;
	},
	tel: function (number) {
		return number.replace(/[^0-9\.]+/g, '');
	},
	uniqid: function (namespace) {
		if (namespace) {
			if (typeof twig_counter_namespaces[namespace] === 'undefined') {
				twig_counter_namespaces[namespace] = 1;
			} else {
				twig_counter_namespaces[namespace]++;
			}

			return namespace + '-' + twig_counter_namespaces[namespace];
		}

		twig_counter++;

		return twig_counter;
	},
	init_uniqid: function () {
		twig_counter = 0;
		twig_counter_namespaces = {};

		return '';
	}
};
