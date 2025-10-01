import { mergeDeep } from '@/helpers/utils';

export default class YouTubeAPI {
	constructor() {
		this.type = 'youtube';
		this.scriptUrl = 'https://www.youtube.com/iframe_api';
		this.player = null;
	}

	get optionsInline() {
		return {
			modestbranding: 1,
			rel: 0,
			playsinline: 1,
			autoplay: 0,
			controls: 1,
			loop: 0,
			origin: encodeURIComponent(window.location.protocol + '//' + window.location.hostname)
		};
	}

	get optionsBackground() {
		return {
			modestbranding: 1,
			rel: 0,
			playsinline: 1,
			autoplay: 0,
			controls: 0,
			loop: 1,
			origin: encodeURIComponent(window.location.protocol + '//' + window.location.hostname)
		};
	}

	setPlayer(config) {
		let options = config?.display === 'inline' ? this.optionsInline : this.optionsBackground;
		let playerVars = mergeDeep(options, config.playerVars, {
			playlist: config.id
		});

		return new Promise((resolve, reject) => {
			this.player = new window.YT.Player(config.iframe, {
				height: '390',
				width: '640',
				videoId: config.id,
				title: config.title,
				playerVars,
				events: {
					onReady: () => resolve(),
					onError: () => reject()
				}
			});
		});
	}

	loadPlayer(config) {
		return new Promise((resolve, reject) => {
			window.YT.ready(() => {
				this.setPlayer(config).then(resolve).catch(reject);
			});
		});
	}

	play() {
		this.player.playVideo();
	}

	pause() {
		this.player.pauseVideo();
	}

	setVolumeByPercentage(num) {
		let maxVolume = 100;
		let volumeAsPercent = maxVolume * (num / 100);

		this.player.setVolume(volumeAsPercent);
	}
}
