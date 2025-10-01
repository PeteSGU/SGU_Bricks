module.exports = {
	status: 'ready',
	context: {
		page: {
			theme: 'blue',
			classes: ['layout_listing'],
			title: 'Caribbean Life',
			filter: {
				label: '',
				active: 'search',
				action_submit: '#',
				tools: [
					{
						label: 'Filter by Category',
						options: [
							{
								label: 'All Categories'
							},
							{
								label: 'Category One'
							},
							{
								label: 'Another Category'
							}
						]
					},
					{
						label: 'Filter By Type',
						options: [
							{
								label: 'All Types'
							},
							{
								label: 'Type One'
							},
							{
								label: 'Another Type'
							}
						]
					}
				],
				search_placeholder: 'Search...',
				results: '',
				category: ''
			}
		}
	},
	variants: [
		{
			name: 'no-results',
			context: {
				page: {
					title: 'News Results'
				}
			}
		}
	]
};
