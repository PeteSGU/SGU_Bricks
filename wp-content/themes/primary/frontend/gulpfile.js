const gulp = require('gulp');
const axeWebdriver = require('gulp-axe-webdriver');
const gulpif = require('gulp-if');
const rename = require('gulp-rename');
const clean = require('gulp-clean');
const imagemin = require('imagemin');
const imageminMozjpeg = require('imagemin-mozjpeg');
const imageminPngquant = require('imagemin-pngquant');
const sourcemaps = require('gulp-sourcemaps');
const realFavicon = require('gulp-real-favicon');
const sass = require('gulp-sass')(require('sass'));
const sassGlob = require('gulp-sass-glob');
const postcss = require('gulp-postcss');
const svgstore = require('gulp-svgstore');
const named = require('vinyl-named');
const webpack = require('webpack-stream');
const webpackCompiler = require('webpack');
const fractal = require('./fractal.config');
const fractalLogger = fractal.cli.console;

const webpackConfig = require('./webpack.config');
const config = require('./config.json');

const CI = !!process.env.CI;
const env = process.env.NODE_ENV || 'development';
const isProduction = env === 'production';
const isDevelopment = !isProduction;
const isPostBuild = !!process.env.POST_BUILD;
const aliases = {
	dist: 'dist',
	build: 'static-html'
};
const basePath = `${__dirname}/${aliases.dist}`;
const buildPath = `${__dirname}/${aliases.build}`;
const srcPath = `${__dirname}/src`;
const templatePath = `${srcPath}/twig`;

const paths = {
	js: {
		src: [`${srcPath}/js/site.js`],
		dest: `${basePath}/js`
	},
	css: {
		src: [
			`${srcPath}/css/site.scss`,
			`${srcPath}/css/tinymce.scss`,
			`${srcPath}/css/gutenberg.scss`,
			`${srcPath}/css/bricks.scss`
		],
		dest: `${basePath}/css`,
		destBuild: `${buildPath}/css`
	},
	icons: {
		src: `${srcPath}/icons/*.svg`,
		dest: `${basePath}/images/src`,
		destSprite: `${basePath}/images`
	},
	images: {
		src: [`${srcPath}/images/*.{jpg,png,svg}`],
		dest: `${basePath}/images`
	},
	favicons: {
		twigFilePath: `${srcPath}/twig/partials`,
		twigFileName: `_favicons.twig`
	},
	static: {
		components: `${buildPath}/components`,
		js: `${buildPath}/js`
	},
	accessibilityScans: {
		dest: `${basePath}/axe-scans`
	}
};
const context = {
	isDevelopment,
	isProduction,
	basePath,
	isPostBuild,
	srcPath,
	templatePath,
	paths
};

function scripts() {
	return gulp
		.src(paths.js.src)
		.pipe(named())
		.pipe(webpack(webpackConfig(env, context), webpackCompiler))
		.pipe(gulp.dest(paths.js.dest));
}

function styles() {
	return gulp
		.src(paths.css.src)
		.pipe(gulpif(isDevelopment, sourcemaps.init()))
		.pipe(sassGlob())
		.pipe(
			sass({
				outputStyle: isProduction ? 'compressed' : 'expanded',
				includePaths: ['node_modules', paths.css.src]
			}).on('error', sass.logError)
		)
		.pipe(postcss(context))
		.pipe(gulpif(isDevelopment, sourcemaps.write('.')))
		.pipe(gulp.dest(paths.css.dest));
}

function images() {
	return imagemin(paths.images.src, {
		destination: paths.images.dest,
		plugins: [imageminMozjpeg(), imageminPngquant({ quality: [0.6, 0.8] })]
	});
}

function icons() {
	return gulp
		.src(paths.icons.src)
		.pipe(gulp.dest(paths.icons.dest))
		.pipe(
			svgstore({
				inlineSvg: true
			})
		)
		.pipe(
			rename({
				basename: 'icons'
			})
		)
		.pipe(gulp.dest(paths.icons.destSprite));
}

function axe(cb) {
	let templateFilter = process.env.AXE_FILTER || '';
	let urlBlockList = ['404', 'search', 'preview'];

	fractal.load().then(() => {
		let urls = isDevelopment
			? fractal.components
					.filter((item) =>
						[
							item.handle.startsWith(`template-`),
							item.handle.includes(templateFilter)
						].every(Boolean)
					)
					.filter((item) => !urlBlockList.some((slug) => item.handle.includes(slug)))
					.flattenDeep()
					.toArray()
					.map((item) => `http://localhost:3000/components/preview/${item.handle}`)
			: `${aliases.build}/components/preview/template*.html`;

		axeWebdriver({
			folderOutputReport: paths.accessibilityScans.dest,
			saveOutputIn: 'static.json',
			urls: urls,
			headless: true,
			showOnlyViolations: true,
			verbose: true
		}).then(cb);
	});
}

function axe_cms(cb) {
	let urls = config.accessibility.cms_urls;

	if (!urls) return cb();

	axeWebdriver({
		folderOutputReport: paths.accessibilityScans.dest,
		saveOutputIn: 'cms.json',
		urls: urls,
		headless: true,
		showOnlyViolations: true,
		verbose: true
	}).then(cb);
}

function favicons(done) {
	const faviconFile = `${__dirname}/dist/favicons/markup.json`;
	const faviconSettings = require('./favicon-settings')({
		config,
		faviconFile
	});

	realFavicon.generateFavicon(faviconSettings, () => {
		let faviconCode = require(faviconFile).favicon.html_code;
		let regex = new RegExp(/(href|content)=\"(\/favicons\/[^\"]*)\"/, 'gm');

		faviconCode = faviconCode.replace(regex, (match, attribute, value) => {
			return `${attribute}="{{ '${value}'|path }}"`;
		});

		gulp.src(`${paths.favicons.twigFilePath}/${paths.favicons.twigFileName}`)
			.pipe(realFavicon.injectFaviconMarkups(faviconCode))
			.pipe(gulp.dest(paths.favicons.twigFilePath));

		done();
	});
}

function _clean() {
	return gulp
		.src([`${basePath}/css/*`, `${basePath}/images/*`, `${basePath}/js/*`], { read: false })
		.pipe(clean());
}

function watch() {
	gulp.watch([`${srcPath}/css/**/*.scss`, `${templatePath}/**/*.scss`], styles);
	gulp.watch([`${srcPath}/js/**/*.js`], scripts);
	gulp.watch([`${srcPath}/icons/*.svg`], icons);
	gulp.watch([`${srcPath}/images/*`], images);
	gulp.watch(['package.json'], gulp.parallel(styles, scripts));
}

function fractalSync() {
	const server = fractal.web.server({
		sync: true,
		port: 3000
	});

	server.on('error', (err) => fractalLogger.error(err.message));

	server._app.on('source:updated', () => {
		let time = new Date().toLocaleTimeString('en-US');

		fractalLogger.success(`[${time}] Source changed - Reloading Fractal`);
	});

	return server.start().then(() => {
		fractalLogger.success(`Fractal local server is now running at ${server.url}`);
		fractalLogger.success(
			`Fractal exposed server is now running at ${server.urls.sync.external}`
		);
	});
}

function fractalBuild() {
	const builder = fractal.web.builder();

	if (!CI) {
		builder.on('progress', (completed, total) =>
			fractalLogger.update(`Exported ${completed} of ${total} items`, 'info')
		);
	} else {
		builder.on('start', () => {
			fractalLogger.update(`Exporting items...`, 'info');
		});
	}

	builder.on('error', (err) => fractalLogger.error(err.message));

	return builder.start().then(() => {
		fractalLogger.success('Fractal build completed!');
	});
}

const build = gulp.series(_clean, gulp.parallel(styles, scripts, icons, images));
const buildAll = gulp.series(build, fractalBuild);
const ciBuild = gulp.series(build, fractalBuild);
const _watch = gulp.series(build, fractalSync, watch);

exports.build = build;
exports['build:all'] = buildAll;
exports.styles = styles;
exports.scripts = scripts;
exports.axe = axe;
exports.axe_cms = axe_cms;
exports.icons = icons;
exports.images = images;
exports.favicons = favicons;
exports.clean = _clean;
exports['ci:build'] = ciBuild;
exports['fractal:sync'] = fractalSync;
exports['fractal:build'] = fractalBuild;
exports.watch = _watch;
exports.default = _watch;
