var gulp = require('gulp');
var plumber = require('gulp-plumber');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var sass       = require('gulp-sass');
var cssmin  = require('gulp-cssmin');
var autoprefixer = require('gulp-autoprefixer');
var watch      = require('gulp-watch');


gulp.task('default', function(){
    gulp.start('js');
});


gulp.task('js', function(){
    var src = 'src/js/*.js',
        dest = 'dist/';

    return gulp.src(src)
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(concat('app.js'))
        .pipe(gulp.dest(dest))
        .pipe(rename('app.min.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write('maps'))
        .pipe(gulp.dest(dest));
});


gulp.task('sass', function(){
    var src = 'src/sass/style.scss',
        dest = 'dist/';

    gulp.src(src)
        .pipe(plumber())
        .pipe(sass())
        .pipe(autoprefixer())
        .pipe(cssmin())
        .pipe(gulp.dest(dest));
});