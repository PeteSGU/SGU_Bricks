module.exports = {
	status: 'ready',
	preview: '@preview-school',
	context: {
		page: {
			theme: 'blue',
			classes: ['layout_home', 'layout_school_home'],
			alert: false,
			title: 'Fulfill your dream of becoming a doctor.',
			description: 'Our education leads to residency and long-term success.',
			hero_school_label: '#1 provider of doctors into first-year US residencies',
			hero_school_links: [
				{
					url: '#',
					label: 'Scholarship Opportunities'
				},
				{
					url: '#',
					label: 'Request Information'
				}
			],
			hero_school_button: {
				label: 'First Hand Accounts',
				name: 'Karen Baker'
			},
			hero_school_portrait: true,
			hero_school_video: {
				type: 'youtube',
				id: 'zkYtMyiHN0g',
				title: 'School of Medicine'
			}
		}
	},
	variants: [
		{
			name: 'alert',
			context: {
				page: {
					alert: true
				}
			}
		},
		{
			name: 'landscape',
			context: {
				page: {
					hero_school_portrait: false,
					hero_school_video: {
						type: 'youtube',
						id: 'RmS2O2kpfVM',
						title: 'School of Medicine'
					}
				}
			}
		}
	]
};
