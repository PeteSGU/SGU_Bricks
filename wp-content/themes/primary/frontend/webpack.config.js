module.exports = function (env = 'development', context) {
	let { basePath, srcPath, isDevelopment } = context;

	return {
		mode: env,
		devtool: isDevelopment ? 'source-map' : false,
		output: {
			filename: `[name].js`
		},
		stats: 'none',
		module: {
			rules: [
				{
					test: /\.m?js$/,
					exclude: /(node_modules|bower_components)/,
					use: {
						loader: 'babel-loader',
						options: {
							presets: ['@babel/preset-env']
						}
					}
				}
			]
		},
		resolve: {
			modules: ['node_modules'],
			alias: {
				'@': `${srcPath}/js`,
				'@base': basePath
			}
		},
		externals: {
			jquery: 'jQuery'
		}
	};
};
