module.exports = {
	status: 'ready',
	context: {
		page: {
			theme: 'blue',
			image: '',
			classes: ['layout_detail', 'layout_blog_detail'],
			title: '4 Perks of Becoming a Family Physician',
			description:
				'Aspiring physicians don’t have to declare a specialty when they start medical school.',
			breadcrumbNav: ['Medical School Blog'],
			meta: 'blog',
			no_decoration: true
		}
	},
	variants: [
		{
			name: 'image',
			context: {
				page: {
					image: '1'
				}
			}
		}
	]
};
