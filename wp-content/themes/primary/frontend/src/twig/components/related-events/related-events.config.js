let items = [
	{
		image: '1',
		date: {
			same_day: false,
			all_day: false,
			from: '2023-05-31 17:00:00',
			to: '2023-06-13 19:00:00'
		},
		title: 'In Enim Justo Rhoncus Ut',
		url: 'page-event-detail.html',
		description: 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus.',
		location: 'Fastspot, 2980 Long Name of Road Rd. Baltimore, MD 21218',
		categories: [
			{
				label: 'Category One',
				url: 'page-event-category.html'
			}
		]
	},
	{
		image: '4',
		date: {
			same_day: true,
			all_day: true,
			from: '2023-05-31 17:00:00',
			to: ''
		},
		title: 'Aenean commodo ligula eget dolor',
		url: 'page-event-detail.html',
		description: 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus.',
		location: 'Location',
		categories: [
			{
				label: 'Category One',
				url: 'page-event-category.html'
			}
		]
	},
	{
		image: '2',
		date: {
			same_day: false,
			all_day: false,
			from: '2023-05-31 17:00:00',
			to: '2023-06-13 19:00:00'
		},
		title: 'In Enim Justo Rhoncus Ut',
		url: 'page-event-detail.html',
		location: 'Location',
		categories: [
			{
				label: 'Category One',
				url: 'page-event-category.html'
			}
		]
	}
];

module.exports = {
	status: 'wip',
	context: {
		title: 'Related Events',
		description:
			'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auct.',
		items: items
	},
	default: 'three-up',
	variants: [
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
		}
	]
};
