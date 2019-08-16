var autoprefixer = require('autoprefixer'),
    calc = require('postcss-calc'),
    cssnano = require('cssnano'),
    gulp = require('gulp'),
    postcss = require('gulp-postcss'),
    postcssPresetEnv = require('postcss-preset-env'),
    plumber = require('gulp-plumber'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    stylelint = require('stylelint'),
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

// Watch files for changes (without Browser-Sync)
gulp.task('watch', function() {
    // Watch .scss files
    gulp.watch('./assets/src/styles/**/*.scss', gulp.series('css'));
});

// Run fed-styles, site-js and foundation-js
gulp.task('default', function() {
    gulp.start('css');
});
