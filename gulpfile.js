var gulp = require('gulp'),
    plugins = require('gulp-load-plugins')(),
    build_sematnic = require('./app/Resources/libs/semantic/tasks/build'),
    watch_sematnic = require('./app/Resources/libs/semantic/tasks/watch');


gulp.task('semantic:build', build_sematnic);

gulp.task('semantic:watch', watch_sematnic);

gulp.task('css', function() {
    return gulp.src([
        'app/Resources/libs/semantic/dist/semantic.css',
    ])
        .pipe(plugins.sass().on('error', plugins.sass.logError))
        .pipe(plugins.concat('main.css', {newLine: ' '}))
        .pipe(gulp.dest('web/resources/css'));
});

gulp.task('three', function() {
    gulp.src([
        'bower_components/three/build/three.js',
        'bower_components/three/examples/js/libs/stats.min.js',
        'bower_components/three/examples/js/loaders/STLLoader.js',
    ])
        .pipe(plugins.concat('three.js'))
        .pipe(gulp.dest('web/resources/js'));

    gulp.src([
        'bower_components/three/examples/js/controls/OrbitControls.js',
    ])
        .pipe(plugins.concat('OrbitControls.js'))
        .pipe(gulp.dest('web/resources/js'));
});

gulp.task('js', function() {
    return gulp.src([
        'bower_components/jquery/dist/jquery.js',
        'app/Resources/libs/semantic/dist/semantic.js',
        'app/Resources/js/**.js',
    ])
        .pipe(plugins.concat('main.js'))
        .pipe(gulp.dest('web/resources/js'));
});

gulp.task('watch', ['js', 'css', 'three'], function () {
    gulp.watch('app/Resources/js/**.js' , ['js']);
    gulp.watch('app/Resources/css/**/*.sass' , ['css']);
});

gulp.task('default', ['semantic:build'], function () {
    return gulp.start(['js', 'css']);
});