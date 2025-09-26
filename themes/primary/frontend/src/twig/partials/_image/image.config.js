let img = require(`${process.env.INIT_CWD}/image-sizes.json`);

module.exports = {
	context: {
		class: 'media',
		alt: '',
		image: '1',
		loading: 'lazy',
		sources: [img.wide.med, img.wide.sml, img.wide.xsml, img.wide.xxsml],
		sizes: ''
	}
};
