module.exports = {
	status: 'ready',
	context: {
		title: 'Contact Us',
		items: [
			{
				name: 'Jane Smith',
				job_title: 'Director of Department',
				url: 'page-people-detail.html',
				image: '1',
				contact: {
					email: 'name@university.edu',
					phone: '(410) 555-1234'
				},
				department: [
					{
						url: '#',
						label: 'School of Medicine'
					},
					{
						url: '#',
						label: 'Anatomy, Physiology, and Pharmacology'
					}
				]
			},
			{
				name: 'Cum sociis natoque',
				job_title: 'Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem',
				url: 'page-people-detail.html',
				image: '2',
				contact: {
					email: 'quisque-rutrum@university.edu',
					phone: ''
				},
				department: [
					{
						url: '#',
						label: 'Aenean commodo ligula eget dolor'
					}
				]
			},
			{
				name: 'Aenean massa',
				job_title:
					'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus',
				url: 'page-people-detail.html',
				image: '3',
				contact: {
					email: 'name@university.edu',
					phone: '(410) 555-1234'
				},
				department: [
					{
						url: '#',
						label: 'Mmagnis'
					}
				]
			}
		]
	}
};
