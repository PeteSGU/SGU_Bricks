module.exports = {
	status: 'ready',
	context: {
		title: 'Explore Our Campus Map',
		description:
			'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.',
		image: '2',
		video: 'https://www.youtube.com/watch?v=RmS2O2kpfVM',
		link: {
			label: 'View the Map',
			url: '#',
			icon: 'caret_right'
		}
	},
	variants: [
		{
			name: 'two columns',
			preview: '@preview-gutenberg-column',
			context: {
				gutenberg_column_count: 2
			}
		},
		{
			name: 'three columns',
			preview: '@preview-gutenberg-column',
			context: {
				gutenberg_column_count: 3
			}
		}
	]
};
