import $ from 'jquery';

const DEFAULT_SETTINGS = {
	path: '/',
	expires: 1000 * 365 * 24 * 60 * 60 // 365 days
};

const cookie = (name, config = {}) => {
	return {
		get value() {
			return $.cookie(name);
		},
		get valueAsArray() {
			if (!this.value) return [];

			try {
				return JSON.parse(this.value);
			} catch (error) {
				return [];
			}
		},
		erase() {
			$.cookie(name, null);

			return this.value;
		},
		append(value) {
			let curValue = [...this.valueAsArray];

			curValue.push(value);

			this.set(curValue);

			return this.value;
		},
		set(value) {
			try {
				value = JSON.stringify(value);
			} catch (error) {
				//
			}

			$.cookie(name, value, config);

			return this.value;
		}
	};
};

const MixinCookie = (name, settings = {}) => {
	if (!$.cookie) return null;

	let config = Object.assign({}, DEFAULT_SETTINGS, settings);

	return cookie(name, config);
};

export default MixinCookie;
