class CSSCustomProperties {
	constructor() {
		this.root = document.documentElement;
		this.nodeHeader = document.querySelector('.js-header');
	}

	setDefaultProps() {
		if (this.nodeHeader) {
			let height = Math.round(this.nodeHeader.offsetHeight);

			this.root.style.setProperty('--header-height', `${height}px`);
		}
	}

	setProps() {
		this.setDefaultProps();
	}

	bindUI() {
		window.addEventListener('resize', this.setProps.bind(this));
	}

	init() {
		this.setProps();
		this.bindUI();
	}
}

export default function factory() {
	let module = new CSSCustomProperties();

	module.init();

	return module;
}
