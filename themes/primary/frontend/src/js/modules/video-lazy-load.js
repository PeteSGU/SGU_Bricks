import $ from 'jquery';
import { nodeConfig } from '@/mixins';
import { getVideoAPIByType } from '@/modules/api/video';
import { appendScriptToBody } from '@/helpers/utils';

const NAMESPACE = 'video-lazy-load';
const DEFAULT_SELECTOR = '.js-video-lazy-load';
const DEFAULT_SETTINGS = {
	//
};

class VideoLazyLoad {
	constructor(node, config) {
		this.node = node;
		this.$node = $(node);
		this.config = config;

		this.api = null;
		this.scriptLoaded = false;

		this.$iframe = this.$node.find('.js-iframe-target').first();
		this.$playBtn = this.$node.find('.js-play-btn').first();
	}

	setApi(api) {
		this.api = api;
	}

	play() {
		this.api.play();
	}

	pause() {
		this.api.pause();
	}

	muteAndPlay() {
		this.node.classList.toggle('ready', true);

		this.api.setVolumeByPercentage(0);
		this.play();
	}

	playWithAudio(volumePercentage = 25) {
		this.node.classList.toggle('ready', true);

		this.setVolumeByPercentage(volumePercentage);
		this.play();
	}

	setVolumeByPercentage(num) {
		this.api.setVolumeByPercentage(num);
	}

	loadPlayer() {
		if (!this.scriptLoaded) return Promise.reject(`Script hasn't loaded`);

		let config = Object.assign({}, this.config, {
			iframe: this.$iframe[0]
		});

		return new Promise((resolve, reject) => {
			this.api.loadPlayer(config).then(resolve).catch(reject);
		});
	}

	onScriptLoaded() {
		this.scriptLoaded = true;
	}

	onIntersecting() {
		if (this.config.autoplay) {
			return appendScriptToBody(this.api.scriptUrl)
				.then(this.onScriptLoaded.bind(this))
				.then(this.loadPlayer.bind(this))
				.then(this.muteAndPlay.bind(this));
		}

		return appendScriptToBody(this.api.scriptUrl).then(this.onScriptLoaded.bind(this));
	}

	onPlayBtnClick(event) {
		event.preventDefault();

		return this.loadPlayer().then(this.playWithAudio.bind(this));
	}

	bindUI() {
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

		if (!this.config.autoplay) {
			this.$playBtn.one('click', this.onPlayBtnClick.bind(this));
		}
	}

	setup() {
		this.$playBtn.attr('role', 'button');
	}

	init() {
		if (!this.api) throw Error('An API must be provided');

		this.setup();
		this.bindUI();
	}
}

export default function factory(selector = DEFAULT_SELECTOR, settings = {}) {
	let listNode = document.querySelectorAll(selector);

	if (!listNode.length) return;

	return Array.from(listNode).map((node) => {
		let config = nodeConfig(node, NAMESPACE, settings, DEFAULT_SETTINGS);

		if (!config.id || !config.type) return;

		let apiCtr = getVideoAPIByType(config.type.toLowerCase());

		if (!apiCtr) return;

		let module = new VideoLazyLoad(node, config);

		module.setApi(new apiCtr());

		module.init();

		return module;
	});
}
