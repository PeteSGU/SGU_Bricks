let img = require(`${process.env.INIT_CWD}/image-sizes.json`);

module.exports = {
	context: {
		class: 'media',
		image: '1',
		alt: '',
		loading: 'lazy',
		default: img.square.xsml,
		sources: {
			'1200px': img.wide.med,
			'500px': img.wide.sml
		}
	}
};
