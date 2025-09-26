module.exports = {
	status: 'ready',
	context: {
		title: 'Ipsum Fermentum Tristique',
		description:
			'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auct.',
		rows: [
			{
				layout: 'video',
				items: [
					{
						direction: 'left',
						image: '1',
						alt: '',
						video: {
							type: 'vimeo',
							id: '258133523',
							title: 'Fastspot Moments',
							autoplay: false
						},
						title: 'A Student Oriented Campus',
						description:
							'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem <a href="#">malesuada</a> magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.'
					}
				]
			},
			{
				layout: '50_50',
				items: [
					{
						image: '2',
						alt: '',
						crop: 'classic',
						title: '',
						description:
							'Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.'
					},
					{
						image: '3',
						alt: '',
						crop: 'portraitFull',
						title: '',
						description:
							'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.'
					}
				]
			},
			{
				layout: '100',
				items: [
					{
						image: '4',
						alt: '',
						crop: 'wide',
						title: '',
						description: ''
					}
				]
			},
			{
				layout: '70_30',
				items: [
					{
						image: '1',
						alt: '',
						crop: 'classic',
						title: '',
						description:
							'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.'
					},
					{
						image: '2',
						alt: '',
						crop: 'wide',
						title: '',
						description:
							'Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.'
					}
				]
			},
			{
				layout: '30_70',
				items: [
					{
						image: '4',
						alt: '',
						crop: 'portraitFull',
						title: 'A Student Oriented Campus',
						description:
							'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.'
					},
					{
						image: '5',
						alt: '',
						crop: 'wide',
						title: '',
						description:
							'Etiam porta sem malesuada magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.'
					}
				]
			},
			{
				layout: 'video',
				items: [
					{
						direction: 'right',
						image: '1',
						alt: '',
						video: {
							type: 'vimeo',
							id: '258133523',
							title: 'Fastspot Moments',
							autoplay: false
						},
						title: 'A Student Oriented Campus',
						description:
							'Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Curabitur blandit tempus porttitor. Etiam porta sem <a href="#">malesuada</a> magna mollis euismod. Donec ullamcorper nulla non metus auctor fringilla.'
					}
				]
			}
		]
	},
	variants: [
		{
			name: 'default',
			label: 'Light',
			context: {
				theme: 'light'
			}
		},
		{
			name: 'dark',
			context: {
				theme: 'dark'
			}
		}
	]
};
