module.exports = function (context) {
	let { isProduction, paths, isPostBuild } = context.options;

	let plugins = [
		require('postcss-pxtorem')({
			rootValue: 16,
			replace: !!isProduction,
			propList: [
				'--*',
				'*border*',
				'font-size',
				'*height*',
				'letter-spacing',
				'line-height',
				'*margin*',
				'*padding*',
				'*width*'
			]
		}),
		require('autoprefixer')
		// require('@fullhuman/postcss-purgecss')({
		// 	content: [`${paths.static.components}/**/*`, `${paths.static.js}/**/*`],
		// 	safelist: {
		// 		greedy: [/^fs(-|_)/, /icon/],
		// 		deep: [/typography|___gcse_0/]
		// 	}
		// })
	];

	return {
		plugins
	};
};
