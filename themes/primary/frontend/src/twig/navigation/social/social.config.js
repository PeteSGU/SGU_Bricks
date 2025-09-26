let config = require(`${process.env.INIT_CWD}/config.json`);

module.exports = {
	context: {
		title: 'Social Nav Title',
		links: config.navigation.social
	}
};
