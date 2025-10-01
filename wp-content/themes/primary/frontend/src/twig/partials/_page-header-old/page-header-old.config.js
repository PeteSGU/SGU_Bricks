module.exports = {
	status: 'wip',
	context: {
		page: {
			title: 'Nullam dictum felis eu',
			description:
				'Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem.'
		}
	},
	variants: [
		{
			name: 'default',
			label: 'No Media'
		},
		{
			name: 'inline-photo',
			label: 'Inline Photo',
			context: {
				page: {
					image: '1'
				}
			}
		},
		{
			name: 'inline-vimeo',
			label: 'Inline Vimeo',
			context: {
				page: {
					video: {
						type: 'vimeo',
						id: '258133523',
						title: 'Fastspot Moments'
					},
					image: '1'
				}
			}
		},
		{
			name: 'inline-youtube',
			label: 'Inline Youtube',
			context: {
				page: {
					video: {
						type: 'youtube',
						id: 'XPjQhvjQbSM',
						title: 'Fastspot - Our Methodology'
					},
					image: '1'
				}
			}
		},
		{
			name: 'bg',
			label: 'Background Photo',
			view: 'page-header--bg.twig',
			context: {
				page: {
					image: '1'
				}
			}
		},
		{
			name: 'bg-vimeo',
			label: 'Background Vimeo',
			view: 'page-header--bg.twig',
			context: {
				page: {
					video: {
						type: 'vimeo',
						id: '76979871',
						title: 'Fastspot Moments'
					},
					image: '1'
				}
			}
		},
		{
			name: 'bg-youtube',
			label: 'Background Youtube',
			view: 'page-header--bg.twig',
			context: {
				page: {
					video: {
						type: 'youtube',
						id: 'XPjQhvjQbSM',
						title: 'Fastspot - Our Methodology'
					},
					image: '1'
				}
			}
		}
	]
};
