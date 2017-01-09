$(document).ready(function () {
    var container;
    var camera, cameraTarget, scene, renderer, controls;
    var stats;

    init();
    animate();
});

function init() {
    container = document.getElementById('model');

    modelView = $('#model');

    camera = new THREE.PerspectiveCamera(45, modelView.innerWidth() / modelView.innerHeight(), 0.1, 2000);
    camera.position.set(4, 1.5, 4);
    camera.lookAt(new THREE.Vector3(0, -0.7, 0));

    scene = new THREE.Scene();
    scene.fog = new THREE.Fog(0x72645b, 2, 15);


    var loader = new THREE.STLLoader();
    loader.load('./resources/30000.stl', function (geometry) {
        var material = new THREE.MeshPhongMaterial({color: 0xaaaaaa, shininess:200,  specular: 0x333333, shading: THREE.FlatShading});
        var mesh = new THREE.Mesh(geometry, material);
        mesh.position.set(0, 0, 0);
        mesh.rotation.set(Math.PI, 0, 0);
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        scene.add(mesh);
    });


    // Lights

    light = new THREE.DirectionalLight( 0xffffff );
    light.position.set( 1, 1, 1 );
    scene.add( light );

    light = new THREE.DirectionalLight( 0x002288 );
    light.position.set( -1, -1, -1 );
    scene.add( light );


    scene.add( new THREE.AmbientLight( 0xf0f0f0 ));
    scene.background = new THREE.Color( 0x000000 );

    // renderer
    renderer = new THREE.WebGLRenderer();
    renderer.setClearColor( scene.fog.color );
    renderer.setPixelRatio( window.devicePixelRatio );
    renderer.setSize( modelView.innerWidth(), modelView.innerHeight() );
    renderer.gammaInput = true;
    renderer.gammaOutput = true;
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.renderReverseSided = false;

    container.appendChild(renderer.domElement);

    // Stats

    stats = new Stats();
    container.appendChild( stats.dom );

    window.addEventListener('resize', onWindowResize, false);
}

function addShadowedLight(x, y, z, color, intensity) {
    var directionalLight = new THREE.DirectionalLight(color, intensity);
    directionalLight.position.set(x, y, z);
    scene.add(directionalLight);
    directionalLight.castShadow = true;
    var d = 1;
    directionalLight.shadow.camera.left = -d;
    directionalLight.shadow.camera.right = d;
    directionalLight.shadow.camera.top = d;
    directionalLight.shadow.camera.bottom = -d;
    directionalLight.shadow.camera.near = 1;
    directionalLight.shadow.camera.far = 4;
    directionalLight.shadow.mapSize.width = 1024;
    directionalLight.shadow.mapSize.height = 1024;
    directionalLight.shadow.bias = -0.005;
}

function onWindowResize() {

    // camera.aspect = window.innerWidth / window.innerHeight;
    // camera.updateProjectionMatrix();
    // renderer.setSize(window.innerWidth, window.innerHeight);

}

function animate() {

    requestAnimationFrame(animate);

    stats.update();

    render();

}

function render() {


    renderer.render(scene, camera);

}