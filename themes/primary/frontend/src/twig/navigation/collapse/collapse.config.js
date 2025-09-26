let config = require(`${process.env.INIT_CWD}/config.json`);

module.exports = {
	context: {
		class: 'main_collapse',
		modifier: 'demo',
		title: 'collapse',
		toggle_icon: 'caret_down',
		active_index: '2',
		links: config.navigation.main,
		child_icon: ''
	}
};
