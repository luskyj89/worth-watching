var gulp 			= require('gulp'),
    sass 			= require('gulp-sass'),
	autoprefixer	= require('gulp-autoprefixer'),
    uglify 			= require('gulp-uglify'),
    rename 			= require('gulp-rename'),
    concat 			= require('gulp-concat'),
	dest 			= require('gulp-dest'),
    notify 			= require('gulp-notify');

gulp.task('styles', function () {
    return gulp.src('../_uncompressed/scss/**/*.scss')
		.pipe(sass({
			includePaths: [
			],
			outputStyle: 'nested',
			errLogToConsole: true
		}))
		.on('error', notify.onError(function(error) {
			'SASS Error <%= error.message %>'
		}))
        .pipe(autoprefixer({
            browsers: ['last 2 versions']
        }))
        .pipe(gulp.dest('../css/'))
		.pipe(notify({ message: 'Sass successfully compiled ;-)' }));
});

gulp.task('scripts', function() {
  return gulp.src('../_uncompressed/js/**/*.js')
    .pipe(concat('app.js'))
    .pipe(gulp.dest('../_uncompressed/js/'))
    .pipe(rename({suffix: '.min'}))
    .pipe(uglify())
    .pipe(gulp.dest('../js/'))
    .pipe(notify({ message: 'Scripts Compiled' }));
});

gulp.task('watch', function() {

  // Watch .scss files
  gulp.watch('../_uncompressed/scss/**/*.scss', ['styles']);

  // Watch .js files
  gulp.watch('../_uncompressed/js/**/*.js', ['scripts']);

});

gulp.task('default', function() {
    gulp.start('styles', 'scripts');
});
