var gulp = require('gulp'),
    plugins = require('gulp-load-plugins')();

gulp.task('css', function() {
    return gulp.src([
        'node_modules/semantic-ui/dist/semantic.css',
        'node_modules/lightbox2/dist/css/lightbox.css',
        'bower_components/jquery-ui/themes/base/jquery-ui.css',
        'bower_components/jQuery-ui-Slider-Pips/dist/jquery-ui-slider-pips.css',
        'bower_components/select2/dist/css/select2.css',
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
        'node_modules/three/examples/js/Detector.js',
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
        'bower_components/jquery/dist/jquery.js',
        'bower_components/jquery-ui/jquery-ui.js',
        'bower_components/jQuery-ui-Slider-Pips/dist/jquery-ui-slider-pips.js',
        'bower_components/select2/dist/js/select2.full.js',
        'node_modules/semantic-ui/dist/semantic.js',
        'node_modules/lightbox2/dist/js/lightbox.js',
        'app/Resources/assets/js/**.js',
        'node_modules/three/examples/js/libs/stats.min.js'
    ])
        .pipe(plugins.concat('main.js'))
        .pipe(gulp.dest('web/resources/js'));
});

gulp.task('files:semantic', function () {
    return gulp.src(
        'node_modules/semantic-ui/dist/themes/**'
    )
        .pipe(gulp.dest('web/resources/css/themes'));
});

gulp.task('files:images', function () {
    return gulp.src([
        'node_modules/lightbox2/dist/images/**',
        'app/Resources/assets/images/**'
    ])
        .pipe(gulp.dest('web/resources/images'));
});


gulp.task('watch', ['js', 'css', 'three'], function () {
    gulp.watch('app/Resources/assets/js/**.js' , ['js']);
    gulp.watch('app/Resources/assets/style/**/*.scss' , ['css']);
});

gulp.task('default', function () {
    return gulp.start(['files:semantic', 'files:images', 'js', 'css', 'three']);
});