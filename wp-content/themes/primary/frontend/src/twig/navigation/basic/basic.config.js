let config = require(`${process.env.INIT_CWD}/config.json`);

module.exports = {
	context: {
		class: 'secondary',
		modifier: 'demo',
		title: 'Basic',
		active_index: '',
		links: config.navigation.secondary,
		icon: '',
		child_icon: ''
	}
};
