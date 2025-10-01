'use strict';

const pkgJson = require('./package.json');
const fractal = (module.exports = require('@frctl/fractal').create());
const mandelbrot = require('@frctl/mandelbrot');
const config = require('./config.json');
const imageSizes = require(`./image-sizes.json`);

const twigAdapter = require('@frctl/twig')({
	functions: require('./twig-functions.js')
});
const env = process.env.NODE_ENV || 'development';
const isProduction = env === 'production';
const customizedTheme = mandelbrot({
	skin: 'black',
	styles: ['default'],
	highlightStyles: '/vendor/highlight.js/gruvbox-dark.10.5.0.min.css',
	favicon: '/favicons/favicon.ico',
	nav: ['search', 'components', 'docs'],
	panels: isProduction
		? ['html', 'view', 'info', 'notes']
		: ['html', 'view', 'resources', 'info', 'notes']
});

customizedTheme.addLoadPath(__dirname + '/src/fractal/mandelbrot');

/* Set the title of the project */
fractal.set('project.title', `${config.twig_variables.name} Component Library`);
fractal.set('project.version', pkgJson.version);
fractal.set('project.author', 'Fastspot');

fractal.docs.engine(twigAdapter);
fractal.docs.set('path', __dirname + '/src/docs');
fractal.docs.set('default.status', 'draft');
fractal.docs.set('indexLabel', 'Project Overview');

fractal.components.set('statuses', {
	wip: {
		label: 'WIP',
		description: 'Work in progress. Implement with caution.',
		color: '#DD6B20'
	},
	ready: {
		label: 'Ready',
		description: 'Ready to implement.',
		color: '#38A169'
	}
});

fractal.components.set('default.context', {
	grid: true,
	navigation: config.navigation,
	img: imageSizes,
	config: config.twig_variables
});

fractal.components.engine(twigAdapter);
fractal.components.set('ext', '.twig');
fractal.components.set('path', __dirname + '/src/twig');
fractal.components.set('default.preview', '@preview');
fractal.components.set('default.status', 'wip');
fractal.components.set('default.collator', function (markup, item) {
	const headingStyle = item.preview === '@preview-dark' ? 'color: #fff;' : 'color: #000;';
	const bgStyle =
		item.preview === '@preview-dark'
			? 'background-color: #000; padding: 2rem;'
			: 'background-color: #fff;  padding: 2rem 0';

	return `
        <div style="${bgStyle}">
            <h2 style="${headingStyle}">
                ${item.title}
            </h2>

			<br>

            <div>
                ${markup}
            </div>
		</div>
    `;
});

fractal.web.theme(customizedTheme);
fractal.web.set('static.path', __dirname + '/dist');
fractal.web.set('builder.dest', __dirname + '/static-html');
fractal.components.set('label', 'Library');
fractal.components.set('exclude', ['**/node_modules/**']);
fractal.web.set('server.syncOptions', {
	watchOptions: {
		ignored: ['**/components/**/*.{scss,js}', '!**/components/**/*.config.js']
	}
});
