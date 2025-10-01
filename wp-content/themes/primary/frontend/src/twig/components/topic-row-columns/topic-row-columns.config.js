module.exports = {
	status: 'ready',
	context: {
		title: 'The SGU Difference',
		items: [
			{
				title: 'Practitioner Focused',
				image: '1',
				description:
					'The #1 provider into first-year US residencies for the last 12 years combined.',
				links: [
					{
						title: 'Our Faculty',
						url: '#',
						icon: 'caret_right'
					},
					{
						title: 'Student Support Services',
						url: '#',
						icon: 'caret_right'
					},
					{
						title: 'Small Cohorts & Groups',
						url: '#',
						icon: 'caret_right'
					}
				]
			},
			{
				title: 'Excellent Academic Outcomes',
				image: '2',
				description:
					'The #1 provider into first-year US residencies for the last 12 years combined.',
				links: [
					{
						title: 'Explore Outcomes',
						url: '#',
						icon: 'caret_right'
					}
				]
			},
			{
				title: 'Outstanding Return on Investment',
				image: '3',
				description: '#% of alumni pay off their student loans within x years.',
				links: [
					{
						title: 'Explore Outcomes',
						url: '#',
						icon: 'caret_right'
					}
				]
			},
			{
				title: 'Incredible Alumni Network',
				image: '4',
				description:
					'The #1 provider into first-year US residencies for the last 12 years combined.',
				links: [
					{
						title: 'Explore Outcomes',
						url: '#',
						icon: 'caret_right'
					}
				]
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
		}
	]
};
