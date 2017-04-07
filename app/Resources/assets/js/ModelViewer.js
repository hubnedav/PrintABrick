var ModelViewer = function($container) {
    var $this = this;
    this.container = $container;

    this.camera = null;
    this.scene = null;
    this.renderer = null;
    this.controls = null;
    this.object = null;
    this.width  = parseFloat(this.container.width());
    this.height = parseFloat(this.container.height());
    this.visible = true;
    this.scale = 1;

    this.initScene();

    function renderLoop() {
        requestAnimationFrame( renderLoop );
        $this.render();
    }

    renderLoop();
};

ModelViewer.prototype.initScene = function() {

    this.scene = new THREE.Scene();

    this.camera = new THREE.PerspectiveCamera(45, this.width / this.height, 1, 1000);
    this.camera.position.z = 8;
    this.camera.position.y = -5;
    this.camera.position.x = 3;
    this.camera.up = new THREE.Vector3(0, 0, 1);

    this.reflectCamera = new THREE.CubeCamera(1, 50, 200);
    this.scene.add(this.reflectCamera);

    var material = new THREE.MeshPhongMaterial({
        color: 0xffffff,
        emissive: 0xffffff,
        shading: THREE.SmoothShading,
        fog: false,
        side: THREE.BackSide
    });

    var skybox = new THREE.Mesh(new THREE.CubeGeometry(100, 100, 100), material);
    skybox.name = 'skybox';
    this.scene.add(skybox);

    var groundPlaneMaterial = new THREE.MeshPhongMaterial({
        color: 0x888888,
        wireframe: false,
        transparent: true,
        opacity: 0.25,
        // fog: true,
        // specular: 0x999999,
        envMap: this.reflectCamera.renderTarget
    });
    var x = 80;
    var y = 80;
    var division_x = Math.floor(x / 2);
    var division_y = Math.floor(y / 2);

    this.plane = new THREE.Mesh(new THREE.PlaneGeometry(x, y, division_x, division_y), groundPlaneMaterial);
    this.plane.name = 'plane';
    this.plane.receiveShadow = true;
    this.scene.add(this.plane);

    this.grid = new THREE.GridHelper( 80, 100, 0xEEEEEE,0xEEEEEE);
    this.grid.rotation.x = Math.PI/2;

    this.scene.add(this.grid);


    // this.wirePlane = new THREE.Mesh(new THREE.PlaneGeometry(x, y, division_x, division_y), new THREE.MeshPhongMaterial({
    //     emissive: 0xEEEEEE,
    //     color: 0x000000,
    //     wireframe: true,
    //     wireframeLinewidth: 2
    // }));
    // this.wirePlane.name = 'planewire';
    // this.wirePlane.receiveShadow = true;
    // this.wirePlane.position.z = this.plane.position.z + .01;
    // this.scene.add(this.wirePlane);

    this.renderer = new THREE.WebGLRenderer();

    this.renderer.setSize(this.width, this.height);

    this.container.append(this.renderer.domElement);


    this.scene.fog = new THREE.FogExp2(0xcacaca, 0.9);

    // // Light
    this.spotLight = new THREE.SpotLight(0xffffff, 0.9, 0);
    this.spotLight.position.set(-70, 100, 100);
    this.spotLight.castShadow = false;
    this.scene.add(this.spotLight);

    this.bottomSpotLight = new THREE.SpotLight(0xffffff, 0.3, 0);
    this.bottomSpotLight.position.set(70, -100, -100);
    this.bottomSpotLight.castShadow = false;
    this.scene.add(this.bottomSpotLight);


    this.pointLight = new THREE.PointLight(0xffaaff, 0.9, 0);
    this.pointLight.position.set(32, -39, 35);
    this.pointLight.castShadow = true;
    this.scene.add(this.pointLight);

    this.renderer.shadowMap.enabled = true;
    this.renderer.shadowMap.renderReverseSided = false;

    // $container.append(this.renderer.domElement);

    this.controls = new THREE.OrbitControls( this.camera, this.renderer.domElement );
    this.controls.enableZoom = true;
};

ModelViewer.prototype.addModel = function(geometry) {
    var material = new THREE.MeshPhongMaterial({
        color: 0x136FC3,
        specular: 0x0D0D0D,
        shading: THREE.SmoothShading,
        shininess: 150,
        fog: false,
        side: THREE.DoubleSide,
        // wireframe: true,
    });

    geometry.center();
    var mesh = new THREE.Mesh(geometry, material);

    mesh.scale.set(this.scale, this.scale, this.scale);

    mesh.geometry.computeFaceNormals();
    mesh.geometry.computeVertexNormals();
    mesh.rotation.set(-Math.PI/2,0, 0);
    mesh.geometry.computeBoundingBox();

    mesh.castShadow = true;
    // mesh.receiveShadow = true;

    var dims = mesh.geometry.boundingBox.max.clone().sub(mesh.geometry.boundingBox.min);

    maxDim = Math.max(Math.max(dims.x, dims.y), dims.z);

    mesh.position.x = -(mesh.geometry.boundingBox.min.x + mesh.geometry.boundingBox.max.x) / 2 * this.scale;
    mesh.position.z = -(mesh.geometry.boundingBox.min.y + mesh.geometry.boundingBox.max.y) / 2 * this.scale;
    mesh.position.y = -mesh.geometry.boundingBox.min.z * this.scale;

    var positionY = (mesh.geometry.boundingBox.max.z + mesh.geometry.boundingBox.min.z)/2 * this.scale;
    var positionZ = (mesh.geometry.boundingBox.max.y - mesh.geometry.boundingBox.min.y)/2 * this.scale;

    mesh.position.set(0, positionY, positionZ);

    // this.scene.face_count = mesh.geometry.faces.length;
    this.scene.add(mesh);

    this.centerCamera(mesh);
};

ModelViewer.prototype.loadStl = function(model) {
    var self = this;

    var loader = new THREE.STLLoader();

    loader.load(model, function (geometry) {
        self.addModel(geometry);
    });
};

ModelViewer.prototype.centerCamera = function(mesh) {

    var boxHelper =  new THREE.BoxHelper( mesh );

    var sceneCenter = this.objectCenter(mesh);

    var geometry = mesh.geometry;

    var distanceX = (geometry.boundingBox.max.x - geometry.boundingBox.min.x) / 2 / Math.tan(this.camera.fov * this.camera.aspect * Math.PI / 360);
    var distanceY = (geometry.boundingBox.max.y - geometry.boundingBox.min.y) / 2 / Math.tan(this.camera.fov * this.camera.aspect * Math.PI / 360);
    var distanceZ = (geometry.boundingBox.max.z - geometry.boundingBox.min.z) / 2 / Math.tan(this.camera.fov * Math.PI / 360);

    var maxDistance = Math.max(Math.max(distanceX, distanceY), distanceZ);
    maxDistance *= 2.6 * this.scale;

    var cameraPosition = this.camera.position.normalize().multiplyScalar(maxDistance);

    this.controls.maxDistance = 3 * maxDistance;

    this.controls.position0 = cameraPosition;
    this.controls.target0 = sceneCenter;
    this.controls.reset();
};

ModelViewer.prototype.objectCenter = function (mesh) {
    var middle = new THREE.Vector3();
    var geometry = mesh.geometry;

    geometry.center();
    geometry.computeBoundingBox();

    middle.x = (geometry.boundingBox.max.x + geometry.boundingBox.min.x) / 2;
    middle.y = (geometry.boundingBox.max.y + geometry.boundingBox.min.y) / 2;
    middle.z = (geometry.boundingBox.max.z + geometry.boundingBox.min.z) / 2;

    mesh.localToWorld(middle);
    return middle;
};

ModelViewer.prototype.render = function() {
    this.renderer.render(this.scene, this.camera);
};