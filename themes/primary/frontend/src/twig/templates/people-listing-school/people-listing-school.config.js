module.exports = {
	status: 'ready',
	preview: '@preview-school',
	context: {
		page: {
			theme: 'blue',
			classes: ['layout_listing'],
			title: 'People Listing',
			description: '',
			filter: {
				label: '',
				active: 'search',
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
				search_placeholder: 'Search by name or department',
				results: '',
				category: ''
			}
		}
	}
};
