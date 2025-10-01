module.exports = {
	status: 'ready',
	context: {
		title: 'Popular Pages',
		items: [
			{
				title: 'Info Sessions',
				url: '#',
				icon: 'caret_right'
			},
			{
				title: 'Apply',
				url: '#',
				icon: 'caret_right'
			},
			{
				title: 'About SGU',
				url: '#',
				icon: 'download'
			},
			{
				title: 'Directory',
				url: '#',
				icon: 'caret_right'
			},
			{
				title: 'News',
				url: '#',
				icon: 'caret_right'
			},
			{
				title: 'Events',
				url: '#',
				icon: 'external'
			},
			{
				title: 'Life at St. George\'s',
				url: '#',
				icon: 'caret_right'
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
