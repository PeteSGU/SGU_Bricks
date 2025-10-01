module.exports = {
	status: 'ready',
	context: {
		item: {
			name: 'Jane Smith',
			phone_numbers: [
				{
					label: 'mobile',
					type: 'telephone',
					number: '(410) 555-1234'
				},
				{
					label: 'office',
					type: 'telephone',
					number: '(410) 555-1234'
				}
			],
			office_hours: [
				{
					label: 'Monday',
					hours: '9am - 5pm'
				},
				{
					label: 'Wednesday',
					hours: '11am-12pm'
				}
			],
			email: 'name@university.edu',
			location: {
				label: 'Title',
				url: '#'
			},
			social_links: [
				{
					title: 'Linkedin',
					url: '#'
				},
				{
					title: 'Twitter',
					url: '#'
				},
				{
					title: 'Facebook',
					url: '#'
				},
				{
					title: 'Instagram',
					url: '#'
				}
			]
		}
	},
	variants: [
		{
			name: 'detail',
			context: {
				item: {
					name: '',
					appointment: true,
					phone_numbers: [
						{
							label: 'mobile',
							type: 'telephone',
							number: '(410) 555-1234'
						}
					],
					office_hours: '',
					email: 'mloukas@sgu.edu',
					location: {
						label: 'Belford Centre',
						url: '#'
					},
					departments: [
						{
							name: 'University Administration',
							url: '#'
						}
					],
					social_links: '',
					faculty_type: ['Administration', 'Full Time Faculty']
				}
			}
		}
	]
};
