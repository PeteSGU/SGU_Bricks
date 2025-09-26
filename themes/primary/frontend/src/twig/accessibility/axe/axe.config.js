module.exports = {
	context: {
		results: []
	},
	default: 'static',
	variants: [
		{
			name: 'static',
			context: {
				jsonFilePath: '/axe-scans/static.json'
			}
		},
		{
			name: 'cms',
			label: 'CMS',
			context: {
				jsonFilePath: '/axe-scans/cms.json'
			}
		}
	]
};
