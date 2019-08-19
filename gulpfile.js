var autoprefixer = require('autoprefixer'),
    calc = require('postcss-calc'),
    cssnano = require('cssnano'),
    concat = require('gulp-concat'),
    gulp = require('gulp'),
    postcss = require('gulp-postcss'),
    postcssPresetEnv = require('postcss-preset-env'),
    plumber = require('gulp-plumber'),
    jshint = require('gulp-jshint'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    stylish = require('jshint-stylish'),
    stylelint = require('stylelint'),
    uglify = require('gulp-uglify'),
    gutil = require('gulp-util');

const postcssPlugins = [
    // stylelint(),
    postcssPresetEnv({
        autoprefixer: {},
        features: {
            'custom-media-queries': true,
            'custom-properties': true,
            'nesting-rules': true
        }
    }),
    calc({
        preserve: false
    }),
    cssnano()
];

gulp.task('css', function() {
    return gulp
        .src('./assets/src/styles/**/*.scss')
        .pipe(
            plumber(function(error) {
                gutil.log(gutil.colors.red(error.message));
                this.emit('end');
            })
        )
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(postcss(postcssPlugins))
        .pipe(gulp.dest('./assets/dist/styles/'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./assets/dist/styles/'));
});

// JSHint, concat, and minify JavaScript
gulp.task('scripts', function() {
    return gulp
        .src('./assets/src/scripts/**/*.js')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(jshint())
        .pipe(jshint.reporter('jshint-stylish'))
        .pipe(concat('base.js'))
        .pipe(gulp.dest('./assets/dists/scripts'))
        .pipe(uglify())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./assets/dist/scripts'));
});

// Watch files for changes (without Browser-Sync)
gulp.task('watch', function() {
    // Watch .scss files
    gulp.watch('./assets/src/styles/**/*.scss', gulp.series('css'));
    gulp.watch('./assets/src/scripts/**/*.js', gulp.series('scripts'));
});

// Run fed-styles, site-js and foundation-js
gulp.task('default', function() {
    gulp.start('css', 'scripts');
});
