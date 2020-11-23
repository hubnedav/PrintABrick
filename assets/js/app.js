'use strict';

// any CSS you require will output into a single css file (app.css in this case)
import '../css/app.scss';
//
global.$ = global.jQuery = $;

import 'semantic-ui-css';
import 'semantic-ui-css/semantic.css'

import 'Hinclude/hinclude';

// JQUERY-UI
import 'jquery-ui';
import 'jquery-ui/themes/base/theme.css'

import 'jquery-ui-slider/jquery-ui.js';
import 'jquery-ui-slider/jquery-ui.css';
import './libs/jquery-ui-slider-pips'
import './libs/jquery-ui-slider-pips.css'
import './libs/jquery.ui.touch-punch'

// LIGHTBOX2
import 'lightbox2';
import 'lightbox2/dist/css/lightbox.css';

import './components/ajax';
import './components/slider';
import './components/style';


// THREEJS
import * as THREE from 'three';
global.THREE = THREE;
import WEBGL from 'three/examples/js/WebGL.js';
global.WEBGL = WEBGL;
import 'three/examples/js/loaders/STLLoader.js';
import 'three/examples/js/controls/OrbitControls.js';
// import 'three/examples/js/libs/stats.min.js';

import './components/ModelViewer';

// Images
import '../images/logo.svg';
import '../images/meta-logo.png';
import '../images/transparent_large.png';
import '../images/transparent_min.png';