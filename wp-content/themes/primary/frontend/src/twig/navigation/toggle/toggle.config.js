let config = require(`${process.env.INIT_CWD}/config.json`);

module.exports = {
	context: {
		class: 'audience',
		modifier: 'demo',
		title: 'Audience',
		links: config.navigation.audience,
		icon: '',
		child_icon: '',
		toggle: {
			title: 'Audience',
			icon: 'plus',
			title_active: 'Close',
			icon_active: 'minus',
			swap_target: '.js-audience-nav-group',
			swap_link: 'audience'
		}
	}
};
