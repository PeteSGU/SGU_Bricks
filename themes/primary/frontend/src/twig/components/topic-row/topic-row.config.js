module.exports = {
	status: 'ready',
	context: {
		title: 'Topic Group',
		description: 'Praesent commodo cursus magna, vel scelerisque nisl consectetur et.',
		items: [
			{
				title: 'Explore MD Programs',
				image: '1',
				description:
					'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.',
				links: [
					{
						title: 'Learn More',
						url: '#',
						icon: 'caret_right'
					},
					{
						title: 'Take a Tour',
						url: '#',
						icon: 'caret_right'
					}
				]
			},
			{
				title: 'Life on Campus',
				image: '2',
				description:
					'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.',
				links: [
					{
						title: 'Explore Campus Life',
						url: '#',
						icon: 'caret_right'
					}
				]
			},
			{
				title: 'About SGU',
				image: '',
				description:
					'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.',
				links: [
					{
						title: 'Learn More',
						url: '#',
						icon: 'caret_right'
					}
				]
			},
			{
				title: 'Ipsum',
				image: '2',
				description: 'Praesent',
				links: ''
			}
		]
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
