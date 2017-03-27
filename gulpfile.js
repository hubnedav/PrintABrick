var gulp = require('gulp'),
    plugins = require('gulp-load-plugins')();

gulp.task('css', function() {
    return gulp.src([
        'node_modules/semantic-ui/dist/semantic.css',
        'app/Resources/assets/style/style.scss',
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
    ])
        .pipe(plugins.concat('three.js'))
        .pipe(gulp.dest('web/resources/js'));

    gulp.src([
        'node_modules/three/examples/js/controls/OrbitControls.js',
    ])
        .pipe(plugins.concat('OrbitControls.js'))
        .pipe(gulp.dest('web/resources/js'));
});

gulp.task('js', function() {
    return gulp.src([
        'node_modules/jquery/dist/jquery.js',
        'node_modules/semantic-ui/dist/semantic.js',
        'app/Resources/assets/js/**.js',
    ])
        .pipe(plugins.concat('main.js'))
        .pipe(gulp.dest('web/resources/js'));
});

gulp.task('files', function () {
    return gulp.src('node_modules/semantic-ui/dist/themes/**')
        .pipe(plugins.newer('web/resources/css/themes'))
        .pipe(gulp.dest('web/resources/css/themes'));
});

gulp.task('watch', ['js', 'css', 'three'], function () {
    gulp.watch('app/Resources/assets/js/**.js' , ['js']);
    gulp.watch('app/Resources/assets/style/**/*.sass' , ['css']);
});

gulp.task('default', function () {
    return gulp.start(['files', 'js', 'css', 'three']);
});