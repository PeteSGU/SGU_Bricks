module.exports = {
	status: 'ready',
	context: {
		group_title: 'SGU Stories',
		view_all: {
			label: 'View More',
			url: '#'
		},
		item: {
			image: '1',
			categories: [
				{
					label: 'Category 1',
					url: 'page-news-category.html'
				}
			],
			title: 'In Enim Justo Rhoncus Ut',
			url: '#',
			date: '2019-01-01 17:00:00',
			author: 'Johnny Appleseed',
			description:
				'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus.'
		}
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
