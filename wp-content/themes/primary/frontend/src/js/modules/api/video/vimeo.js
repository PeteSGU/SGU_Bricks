import { mergeDeep } from '@/helpers/utils';

export default class VimeoAPI {
	constructor() {
		this.type = 'vimeo';
		this.scriptUrl = 'https://player.vimeo.com/api/player.js';
		this.player = null;
	}

	get optionsInline() {
		return {
			background: false,
			controls: true,
			autoplay: false,
			loop: false
		};
	}

	get optionsBackground() {
		return {
			background: true,
			controls: false,
			autoplay: false,
			loop: true
		};
	}

	setPlayer(config) {
		let options = config?.display === 'inline' ? this.optionsInline : this.optionsBackground;

		options = mergeDeep(options, config.playerVars, {
			id: config?.id || null,
			title: config?.title || null,
			height: '390',
			width: '640'
		});

		return new Promise((resolve, reject) => {
			this.player = new window.Vimeo.Player(config.iframe, options);

			this.player.ready().then(resolve).catch(reject);
		});
	}

	loadPlayer(config) {
		return new Promise((resolve, reject) => {
			this.setPlayer(config).then(resolve).catch(reject);
		});
	}

	play(player = null) {
		player = player || this.player;

		player.play();
	}

	pause(player = null) {
		player = player || this.player;

		player.pause();
	}

	setVolumeByPercentage(num) {
		let maxVolume = 1;
		let volumeAsPercent = maxVolume * (num / 100);

		this.player.setVolume(volumeAsPercent);
	}
}
