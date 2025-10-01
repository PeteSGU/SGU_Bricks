module.exports = {
	status: 'ready',
	context: {
		label: 'Directory',
		active: 'search',
		action_submit: '#',
		tools: [
			{
				label: 'Category',
				options: [
					{
						label: 'All Categories'
					},
					{
						label: 'Category One',
						selected: true
					},
					{
						label: 'Another Category'
					}
				]
			}
		],
		search_placeholder: 'Search by name or department',
		results: '',
		category: ''
	}
};
