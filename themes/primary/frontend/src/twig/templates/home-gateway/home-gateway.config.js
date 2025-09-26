module.exports = {
	status: 'ready',
	context: {
		page: {
			theme: 'blue',
			classes: ['layout_home'],
			alert: false,
			title: "We're helping meet the world's demand for new doctors.",
			description:
				'Join our 22,000 graduates including physicians, veterinarians, scientists, and public health and business professionals across the world.',
			hero_image: '1',
			hero_alt: '',
			hero_video: {
				type: 'youtube',
				id: 'RmS2O2kpfVM',
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
		}
	]
};
