import $ from 'jquery';
import { nodeConfig } from '@/mixins';

const NAMESPACE = 'video-appender';
const DEFAULT_SELECTOR = '.js-video-appender';
const DEFAULT_SETTINGS = {
	//
};

class VideoAppender {
	constructor(node, config) {
		this.$node = $(node);
		this.config = config;
	}

	parse_vimeo_url(url) {
		const regExp = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/;
		let parsedUrl = regExp.exec(url);

		return parsedUrl[5];
	}

	parse_youtube_url(url) {
		const regExp = /.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/;
		let match = url.match(regExp);

		return (match&&match[1].length==11) ? match[1] : false;
	}

	insertVideo(event) {
		let embed = null;
		let url = this.$node.attr('href');
		let video_id = null;

		event.preventDefault();

		if (url.indexOf("youtu") !== -1) {
			video_id = this.parse_youtube_url(url);

			if (video_id) {
				embed = "<iframe class='video_item_iframe' src='https://www.youtube.com/embed/" + video_id + "?rel=0&amp;showinfo=0&amp;autoplay=1&amp;playsinline=1' style='position:absolute;top:0;left:0;width:100%;height:100%;' frameborder='0' allow='autoplay; fullscreen; encrypted-media' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";
			}
		} else if (url.indexOf("vimeo") !== -1) {
			video_id = this.parse_vimeo_url(url);

			if (video_id) {
				embed = "<iframe class='video_item_iframe' src='https://player.vimeo.com/video/" + video_id + "?autoplay=1&title=0&byline=0&portrait=0' style='position:absolute;top:0;left:0;width:100%;height:100%;' frameborder='0' allow='autoplay; fullscreen; encrypted-media' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><script src='https://player.vimeo.com/api/player.js'></script>";
			}
		}

		if (embed) {
			event.preventDefault();
			this.$node.after(embed).remove();
		}
	}

	bindUI() {
		this.$node.on('click', this.insertVideo.bind(this));
	}

	init() {
		this.bindUI();
	}
}

export default function factory(selector = DEFAULT_SELECTOR, settings = {}) {
	let listNode = document.querySelectorAll(selector);

	if (!listNode.length) return;

	return Array.from(listNode).map((node) => {
		let config = nodeConfig(node, NAMESPACE, settings, DEFAULT_SETTINGS);
		let module = new VideoAppender(node, config);

		module.init();

		return module;
	});
}
