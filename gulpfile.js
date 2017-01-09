var gulp = require('gulp'),
    plugins = require('gulp-load-plugins')(),
    build_sematnic = require('./app/Resources/assets/semantic/tasks/build'),
    watch_sematnic = require('./app/Resources/assets/semantic/tasks/watch');


gulp.task('semantic:build', build_sematnic);

gulp.task('semantic:watch', watch_sematnic);

gulp.task('css', function() {
    return gulp.src([
        'app/Resources/assets/semantic/dist/semantic.css',
    ])
        .pipe(plugins.sass().on('error', plugins.sass.logError))
        .pipe(plugins.concat('main.css', {newLine: ' '}))
        .pipe(gulp.dest('web/resources/css'));
});

gulp.task('three', function() {
    gulp.src([
        'node_modules/three/build/three.js',
        'node_modules/three/examples/js/libs/stats.min.js',
        'node_modules/three/examples/js/loaders/STLLoader.js',
        'node_modules/three/examples/js/controls/TrackballControls.js',
    ])
        .pipe(plugins.concat('three.js'))
        .pipe(gulp.dest('web/resources/js'));

    gulp.src([
        'node_modules/three/examples/js/libs/stats.min.js',
    ])
        .pipe(plugins.concat('stats.js'))
        .pipe(gulp.dest('web/resources/js'));

    gulp.src([
        'app/Resources/assets/javascripts/ModelViewer.js',
    ])
        .pipe(plugins.concat('ModelViewer.js'))
        .pipe(gulp.dest('web/resources/js'));
});

gulp.task('js', function() {
    return gulp.src([
        'node_modules/jquery/dist/jquery.js',
        'app/Resources/assets/semantic/dist/semantic.js',
    ])
        .pipe(plugins.concat('main.js'))
        .pipe(gulp.dest('web/resources/js'));
});

gulp.task('default', ['semantic:build'], function () {
    return gulp.start(['js', 'css']);
});