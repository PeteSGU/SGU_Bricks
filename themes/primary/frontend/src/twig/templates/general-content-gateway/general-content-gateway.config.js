let config = require(`${process.env.INIT_CWD}/config.json`);

module.exports = {
	status: 'ready',
	context: {
		page: {
			theme: 'blue',
			title: 'Life on Campus',
			subNav: config.navigation.sub,
			description:
				'Study on a stunning campus with Georgian architecture and vivid Caribbean Colors.'
		}
	},
	variants: [
		{
			name: 'default',
			label: 'Blue',
			context: {
				page: {}
			}
		},
		{
			name: 'turquoise',
			context: {
				page: {
					theme: 'turquoise'
				}
			}
		},
		{
			name: 'gray',
			context: {
				page: {
					theme: 'gray'
				}
			}
		},
		{
			name: 'photo',
			context: {
				page: {
					image: '1'
				}
			}
		},
		{
			name: 'bg',
			label: 'Background',
			context: {
				page: {
					background: '1'
				}
			}
		},
		{
			name: 'cta',
			label: 'CTA',
			context: {
				page: {
					cta: {
						image: '1',
						title: 'Experience SGU Today',
						link: {
							label: 'Take a Tour',
							url: '#'
						}
					}
				}
			}
		},
		{
			name: 'alert',
			context: {
				page: {
					alert: true
				}
			}
		}
	]
};
