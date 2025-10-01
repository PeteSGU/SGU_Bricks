module.exports = {
	status: 'ready',
	context: {
		title: 'Get to Know Our University',
		description:
			'In addition to our <a href="#">School of Medicine</a> we also have a class leading <a href="#">School of Veterinary Medicine</a>, <a href="#">School of Arts and Sciences</a>, and <a href="#">School of Graduate Studies</a>.',
		rows: [
			{
				layout: 'cards',
				items: [
					{
						type: 'image',
						image: '2',
						alt: ''
					},
					{
						type: 'stat',
						figure: '990+',
						label: 'US residencies in 2022',
						description:
							'<p>Data as of May 2022. The largest provider of doctors into first-year US residencies for the last eight years.</p><p>As the medical school graduating the largest number of students annually, SGU places the largest number of graduates into residency programs each year, based on internal SGU graduate and residency placement data as of May 2022.</p>',
						link: {
							url: '#',
							label: 'Explore Residency Placements'
						}
					},
					{
						type: 'text',
						title: 'School of Medicine',
						description:
							"St. George's University, an accredited Caribbean Medical School draws med school and MD program students, alumni and faculty from over 150 countries.",
						links: [
							{
								url: '#',
								label: 'About the School of Medicine'
							},
							{
								url: '#',
								label: 'Residency Placements'
							},
							{
								url: '#',
								label: 'Scholarship Opportunities'
							}
						]
					}
				]
			},
			{
				layout: 'single',
				item: {
					image: '3',
					alt: '',
					title: 'School of Veterinary Medicine',
					description:
						'Accredited by both the Veterinary Medical Association (U.S.) and Royal College of Veterinary Surgeons in the UK. ',
					links: [
						{
							url: '#',
							label: 'School of Veterinary Medicine'
						},
						{
							url: '#',
							label: 'Residency Placements'
						},
						{
							url: '#',
							label: 'Scholarship Opportunities'
						}
					]
				}
			},
			{
				layout: 'double',
				items: [
					{
						title: 'School of Arts and Sciences',
						image: '4',
						alt: '',
						description:
							'Our School of Arts and Sciences helps you gain professional skills through undergraduate degree, dual-degree, and postbaccalaureate preclinical programs. You can apply your knowledge in internships and service-learning opportunities.',
						link: {
							url: '#',
							label: 'School of Arts & Sciences'
						}
					},
					{
						title: 'School of Graduate Studies',
						image: '5',
						alt: '',
						description:
							"St. George's University designed its graduate degree programs to keep pace with the evolving needs of business, public health, research, information science, and veterinary medical care.",
						link: {
							url: '#',
							label: 'School of Graduate Studies'
						}
					}
				]
			}
		]
	}
};
