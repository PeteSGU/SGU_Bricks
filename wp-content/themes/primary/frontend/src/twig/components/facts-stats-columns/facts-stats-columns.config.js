module.exports = {
	status: 'ready',
	context: {
		title: 'We are the premier choice in Caribbean Medical Schools and have evolved into a top center of international medical education.',
		image: '2',
		items: [
			{
				stat: '93%',
				context: 'US residency placement rate for graduates over the last three years',
				description:
					'Average of 2020, 2021, 2022 residency placement rate. Residency placement rate is defined as the total number of students who obtained a US residency divided by the total number of students who applied to a US residency program in a given year as of May 2022.'
			},
			{
				stat: '19,000+',
				context:
					'School of Medicine graduates who have entered the global healthcare system.',
				description: ''
			},
			{
				stat: '92%',
				context:
					'USMLE Step 1 pass rate for first-time test-takers over the last three years',
				description:
					'Average of 2019, 2020, 2021 scores First-time pass rate is defined as the number of students passing USMLE Step 1 on their first attempt divided by the total number of students taking USMLE Step 1 for the first time. In order to be certified to take USMLE Step 1, students are required to pass all basic sciences courses.'
			},
			{
				stat: '92%',
				context:
					'USMLE Step 2CK pass rate for first-time test-takers over the last three years',
				description:
					'Average of 2019, 2020, 2021 academic year scores. First-time pass rate is defined as the number of students passing USMLE Step 2CK on their first attempt divided by the total number of students taking USMLE Step 2CK for the first time. USMLE Step 2CK is typically taken upon completion of third-year core clinical rotations.'
			}
		]
	},
	variants: [
		{
			name: 'two columns',
			preview: '@preview-gutenberg-column',
			context: {
				gutenberg_column_count: 2
			}
		}
	]
};
