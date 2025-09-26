module.exports = {
	status: 'wip',
	context: {
		image: '1'
	},
	default: 'youtube',
	variants: [
		{
			name: 'youtube',
			context: {
				video: {
					type: 'youtube',
					id: 'XPjQhvjQbSM',
					title: 'Fastspot - Our Methodology'
				}
			}
		},
		{
			name: 'vimeo',
			context: {
				video: {
					type: 'vimeo',
					id: '258133523',
					title: 'Fastspot Moments'
				}
			}
		}
	]
};
