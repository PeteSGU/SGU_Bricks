module.exports = {
	status: 'ready',
	context: {
		page: {
			theme: 'blue',
			classes: ['layout_listing', 'layout_news_listing'],
			title: 'News Listing'
		}
	},
	default: 'landing',
	variants: [
		{
			name: 'landing',
			context: {
				page: {
					theme: 'blue',
					classes: ['layout_listing'],
					title: 'Newsroom',
					description: 'Explore the latest news from SGU.',
					no_decoration: true,
					news_carousel: true
				}
			}
		},
		{
			name: 'listing',
			context: {
				page: {
					theme: 'blue',
					classes: ['layout_listing'],
					title: 'School of Medicine News',
					description: 'Explore the latest news from SGU.',
					no_decoration: true,
					news_carousel: true,
					filter: {
						label: '',
						active: '',
						action_category: '#',
						action_search: '#',
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
						search_placeholder: 'Search by keyword',
						results: '',
						category: ''
					}
				}
			}
		},
		{
			name: 'category',
			context: {
				page: {
					title: 'News Category',
					filter: {
						label: '',
						active: '',
						action_category: '#',
						action_search: '#',
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
						search_placeholder: 'Search by keyword',
						results: '10',
						category: 'Category One'
					}
				}
			}
		},
		{
			name: 'no-results',
			context: {
				page: {
					title: 'News Results',
					filter: {
						label: '',
						active: '',
						action_category: '#',
						action_search: '#',
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
						search_placeholder: 'Search by keyword',
						results: '10',
						category: 'Category One'
					}
				}
			}
		}
	]
};
