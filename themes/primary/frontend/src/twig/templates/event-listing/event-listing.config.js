module.exports = {
	status: 'wip',
	context: {
		page: {
			theme: 'blue',
			classes: ['layout_listing'],
			filter: {
				label: '',
				active: '',
				action_category: '#',
				action_search: '#',
				tools: [
					{
						label: 'Filter By School',
						options: [
							{
								label: 'All Schools'
							},
							{
								label: 'School One'
							},
							{
								label: 'Another School'
							}
						]
					},
					{
						label: 'Category',
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
					}
				],
				search_placeholder: 'Search by keyword',
				results: '',
				category: ''
			}
		}
	},
	variants: [
		{
			name: 'category',
			context: {
				page: {
					title: 'Event Category'
				}
			}
		},
		{
			name: 'no-results',
			context: {
				page: {
					title: 'Event Results'
				}
			}
		}
	]
};
