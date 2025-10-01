let config = require(`${process.env.INIT_CWD}/config.json`);

module.exports = {
	status: 'ready',
	context: {
		page: {
			theme: 'blue',
			metaImage: '/images/temp/social-wide.jpg',
			metaImageWidth: '',
			metaImageHeight: '',
			title: 'Doctor of Medicine',
			description: 'From Aspiring Student To Practicing MD',
			subNav: config.navigation.sub,
			classes: ['layout_detail'],
			cta: {
				image: '1',
				title: 'Attend an Info Session',
				link: {
					label: 'View Next Session',
					url: '#'
				}
			}
		}
	}
};
