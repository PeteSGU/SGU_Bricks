let items = [
	{
		image: '1',
		title: 'Admissions & Applying',
		description:
			'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor.',
		link: {
			label: 'Admissions Info',
			url: '#',
			icon: 'caret_right'
		}
	},
	{
		image: '2',
		title: 'Tuition & Scholarhsips',
		description:
			'Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.',
		link: {
			label: 'Financial Services',
			url: '#',
			icon: 'caret_right'
		}
	},
	{
		image: '3',
		title: 'Student Support',
		description:
			'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor.',
		link: {
			label: 'Student Support Services',
			url: '#',
			icon: 'caret_right'
		}
	},
	{
		image: '4',
		title: 'Curriculum',
		description:
			'Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.',
		link: {
			label: 'View Curriculum',
			url: '#',
			icon: 'caret_right'
		}
	}
];

module.exports = {
	status: 'ready',
	context: {
		title: 'Facts & Figures',
		description:
			'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auct.',
		items: items
	},
	default: 'four-up',
	variants: [
		{
			name: 'four-up',
			context: {
				items: items.slice(0, 4)
			}
		},
		{
			name: 'three-up',
			context: {
				items: items.slice(0, 3)
			}
		},
		{
			name: 'two-up',
			context: {
				items: items.slice(0, 2)
			}
		},
		{
			name: 'one-up',
			context: {
				items: items.slice(0, 1)
			}
		},
		{
			name: 'two columns',
			preview: '@preview-gutenberg-column',
			context: {
				items: items.slice(0, 4),
				gutenberg_column_count: 2
			}
		},
		{
			name: 'three columns',
			preview: '@preview-gutenberg-column',
			context: {
				items: items.slice(0, 4),
				gutenberg_column_count: 3
			}
		}
	]
};
