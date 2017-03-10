ModelViewer = function() {
    var container;
    var camera, cameraTarget, scene,  renderer, controls, object;
    var width, height;

    this.initScene = function($container) {
        width  = parseFloat($container.width());
        height = parseFloat($container.height());

        camera = new THREE.PerspectiveCamera(45, width/height, 0.1, 1000);
        camera.position.set(-2, 2, 0.8);
        camera.lookAt(new THREE.Vector3(0, 3, 0));

        scene = new THREE.Scene();
        scene.fog = new THREE.FogExp2(0x000000, 0.001);

        var grid = new THREE.GridHelper( 30, 70 );
        grid.position.set(30/70,-0.5,30/70);
        scene.add( grid );

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
        renderer.setSize( width, height );
        renderer.gammaInput = true;
        renderer.gammaOutput = true;
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.renderReverseSided = false;

        $container.append(renderer.domElement);

        controls = new THREE.OrbitControls( camera, renderer.domElement );
        controls.addEventListener( 'change', this.render ); // add this only if there is no animation loop (requestAnimationFrame)
        // controls.enableDamping = true;
        // controls.dampingFactor = 0.25;
        controls.enableZoom = true;
    };

    this.loadStl = function(model) {
        var loader = new THREE.STLLoader();
        loader.load(model, function (geometry) {
            var material = new THREE.MeshPhongMaterial({color: 0xaaaaaa, shininess:200,  specular: 0x333333, shading: THREE.FlatShading});

            var mesh = new THREE.Mesh(geometry, material);
            mesh.position.set(0, 0.5, 0);
            mesh.rotation.set(Math.PI, 0, 0);
            mesh.castShadow = true;
            mesh.receiveShadow = true;
            scene.add(mesh);

            renderer.render(scene, camera);
        });
    };

    this.animate = function() {

        requestAnimationFrame( this.animate );
        this.render();
    };

    this.render = function() {

        renderer.render(scene, camera);

    };
};