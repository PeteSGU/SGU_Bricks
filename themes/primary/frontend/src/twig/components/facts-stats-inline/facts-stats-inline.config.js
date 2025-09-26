module.exports = {
	status: 'ready',
	context: {
		items: [
			{
				statistic: '1,090+',
				context: 'U.S. Residencies in 2021',
				description:
					'The #1 provider into first-year US residencies for the last 12 years combined.',
				link: {
					url: '#',
					label: 'Explore Residency Placements'
				}
			},
			{
				statistic: '42',
				context: 'U.S. States',
				description:
					'St. George’s University students obtained residencies in programs across 42 US states and the District of Columbia.'
			},
			{
				statistic: '95%',
				context: 'USMLE Step 1 Pass Rate',
				description:
					'Over the last three years, SGU students who took the USMLE Step 1 for the first time achieved a 95 percent pass rate.',
				link: {
					url: '#',
					label: 'Alumni Success Stories'
				}
			},
			{
				statistic: '100%',
				context: 'Et est et minim enim',
				description:
					'Minim aute est officia in enim laboris magna. Aliquip nulla irure et fugiat labore magna nisi qui tempor commodo deserunt.'
			}
		]
	},
	default: 'default',
	variants: [
		{
			name: 'default'
		},
		{
			name: 'two columns',
			preview: '@preview-gutenberg-column',
			context: {
				gutenberg_column_count: 2
			}
		}
	]
};
