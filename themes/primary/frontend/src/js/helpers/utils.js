import { dirs } from '@/helpers/config';

export const icon = function (icon) {
	return `
		<svg class="icon icon_${icon}">
			<use href="${dirs.STATIC_ICONS_PATH}#${icon}" />
		</svg>
	`;
};

export const singletonFactory = function (createInstanceFn) {
	let instance = null;

	return {
		getInstance() {
			if (!instance) {
				instance = createInstanceFn();
			}

			return instance;
		}
	};
};

export const debounce = (func, delay) => {
	let debounceTimer;

	return function () {
		const context = this;
		const args = arguments;
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(() => func.apply(context, args), delay);
	};
};

export const getScrollbarWidth = () => {
	let $outer = $('<div>')
		.css({
			visibility: 'hidden',
			width: '100px',
			msOverflowStyle: 'scrollbar'
		})
		.appendTo('body');

	let no_scroll_width = $outer.outerWidth();

	// force scrollbars
	$outer.css({ overflow: 'scroll' });

	// add inner div and calculate width difference
	let $inner = $('<div>').css({ width: '100%' }).appendTo($outer);
	let width = no_scroll_width - $inner.outerWidth();

	// remove divs
	$outer.remove();

	return width;
};

export const isObject = (item) => {
	return item && typeof item === 'object' && !Array.isArray(item);
};

export const mergeDeep = (target, ...sources) => {
	if (!sources.length) return target;

	const source = sources.shift();

	if (isObject(target) && isObject(source)) {
		for (const key in source) {
			if (isObject(source[key])) {
				if (!target[key])
					Object.assign(target, {
						[key]: {}
					});
				mergeDeep(target[key], source[key]);
			} else {
				Object.assign(target, {
					[key]: source[key]
				});
			}
		}
	}

	return mergeDeep(target, ...sources);
};

export const appendScriptToBody = (src) => {
	let curTag = document.querySelector(`script[src*="${src}"]`);

	if (curTag && curTag.dataset.loaded) return Promise.resolve(curTag);

	return new Promise((resolve, reject) => {
		let tag = curTag || document.createElement('script');

		tag.addEventListener('load', () => resolve(tag), {
			once: true
		});
		tag.addEventListener('error', () => reject('Error loading script'), {
			once: true
		});

		if (curTag) return;

		tag.addEventListener(
			'load',
			() => {
				tag.dataset.loaded = true;
			},
			{
				once: true
			}
		);

		tag.setAttribute('src', src);
		tag.setAttribute('defer', 'defer');

		if (!curTag) {
			document.body.append(tag);
		}
	});
};

export const onWindowLoaded = (cb) => {
	if (typeof cb !== 'function') return;

	if (document.readyState === 'complete') return cb(window);

	window.addEventListener('load', cb, {
		once: true
	});
};
