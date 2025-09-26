import $ from 'jquery';
import { nodeConfig } from '@/mixins';
import { getVideoAPIByType } from '@/modules/api/video';
import { appendScriptToBody } from '@/helpers/utils';

const NAMESPACE = 'background-video';
const DEFAULT_SELECTOR = '.js-background-video-wrapper';
const DEFAULT_SETTINGS = {
	//
};

class BackgroundVideo {
	constructor(node, config) {
		this.$node = $(node);
		this.node = node;
		this.config = config;

		this.$iframeTarget = this.$node.find('.js-iframe-target').first();
		this.$playBtn = this.$node.find('.js-hero-video-play-button');
		this.$pauseBtn = this.$node.find('.js-hero-video-pause-button');

		this.api = null;
	}

	get $iframe() {
		return this.$node.find('iframe').first();
	}

	setApi(api) {
		this.api = api;
	}

	toggleBtnState(isPlaying = true) {
		this.$playBtn
			.toggleClass('pressed', isPlaying)
			.attr('aria-pressed', isPlaying ? 'true' : 'false');
		this.$pauseBtn
			.toggleClass('pressed', !isPlaying)
			.attr('aria-pressed', isPlaying ? 'false' : 'true');
	}

	muteAndPlay() {
		this.api.setVolumeByPercentage(0);
		this.play();
	}

	play() {
		this.api.play();

		this.toggleBtnState(true);
	}

	pause() {
		this.api.pause();

		this.toggleBtnState(false);
	}

	setVolumeByPercentage(num) {
		this.api.setVolumeByPercentage(num);
	}

	resize() {
		if (!this.$iframe.length) return;

		let width = this.$node.width();
		let height = this.$node.height();
		const ratio = 0.5625;

		if (height / width <= ratio) {
			this.$iframe.css({
				width: '100%',
				height: `${width * ratio}`
			});

			return;
		}

		this.$iframe.css({
			width: `${height / ratio}`,
			height: '100%'
		});
	}

	bindUI() {
		this.$playBtn.on('click', this.play.bind(this));
		this.$pauseBtn.on('click', this.pause.bind(this));

		window.addEventListener('resize', this.resize.bind(this));
	}

	onPlayerReady() {
		this.$node.toggleClass('loaded', true);

		this.resize();
		this.bindUI();
	}

	onScriptLoaded() {
		let config = Object.assign({}, this.config, {
			iframe: this.$iframeTarget[0],
			display: 'background'
		});

		return this.api.loadPlayer(config).then(this.onPlayerReady.bind(this));
	}

	onIntersecting() {
		if (this.config.autoplay) {
			return appendScriptToBody(this.api.scriptUrl)
				.then(this.onScriptLoaded.bind(this))
				.then(this.muteAndPlay.bind(this));
		}

		return appendScriptToBody(this.api.scriptUrl).then(this.onScriptLoaded.bind(this));
	}

	setup() {
		this.toggleBtnState(false);

		let observer = new window.IntersectionObserver(
			(entries) => {
				let entry = entries[0];

				if (!entry.isIntersecting) return;

				this.onIntersecting();

				observer.unobserve(entry.target);
			},
			{
				rootMargin: '200px'
			}
		);

		observer.observe(this.node);
	}

	init() {
		if (!this.api) throw Error('An API must be provided');

		this.setup();
	}
}

export default function factory(selector = DEFAULT_SELECTOR, settings = {}) {
	let listNode = document.querySelectorAll(selector);

	if (!listNode.length) return;

	return Array.from(listNode).map((node) => {
		let config = nodeConfig(node, NAMESPACE, settings, DEFAULT_SETTINGS);

		if (!config.id || !config.type) return;

		let apiCtr = getVideoAPIByType(config.type);

		if (!apiCtr) {
			node.style.display = 'none';

			return;
		}

		let module = new BackgroundVideo(node, config);

		module.setApi(new apiCtr());

		module.init();

		return module;
	});
}
