let items = [
	{
		image: '1',
		categories: [
			{
				label: 'Profile',
				url: 'page-news-category.html'
			}
		],
		title: 'From The Deep South To Down Under: How This SVM Grad Found “The Place To Be”',
		date: '2022-04-28 17:00:00',
		url: 'page-news-detail.html'
	},
	{
		image: '2',
		categories: [
			{
				label: 'Announcement',
				url: 'page-news-category.html'
			}
		],
		title: 'NCFMEA: SGU Med School Accreditor On Par With US Schools',
		date: '2022-04-28 17:00:00',
		url: 'page-news-detail.html'
	},
	{
		image: '3',
		categories: [
			{
				label: 'Profile',
				url: 'page-news-category.html'
			}
		],
		title: 'New SGU Orthopaedic Surgery Club Preps Students For Competitive Specialty',
		date: '2022-04-28 17:00:00',
		url: 'page-news-detail.html'
	}
];

module.exports = {
	status: 'ready',
	context: {
		group_title: 'School of Veterinary Medicine',
		link: {
			label: 'View More',
			url: '#'
		},
		items: items.slice(0, 3)
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
