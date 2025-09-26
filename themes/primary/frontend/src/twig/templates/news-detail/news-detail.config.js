module.exports = {
	status: 'ready',
	context: {
		page: {
			theme: 'turquoise',
			image: '',
			classes: ['layout_detail', 'layout_news_detail'],
			title: "St. George's University Awards 110 Incoming Students With Scholarships",
			description:
				"This spring, St. George's University announced it has awarded merit-based scholarships to 110 incoming medical school students.",
			duration: '7 min read'
		}
	},
	variants: [
		{
			name: 'image',
			context: {
				page: {
					cta: {
						image: '1'
					}
				}
			}
		},
		{
			name: 'video',
			context: {
				page: {
					cta: {
						image: '1',
						video: ''
					}
				}
			}
		}
	]
};
