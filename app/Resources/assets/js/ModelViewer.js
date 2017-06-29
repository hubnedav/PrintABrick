$.fn.ModelViewer = function ( opts ) {
    return new ModelViewer(this,opts);
};

var ModelViewer = function($dom_element, model_url) {
    var $this = this;
    this.container = document.createElement('div');
    this.container.setAttribute('class','model-view');
    this.dom_element = $dom_element;
    $dom_element.append(this.container);

    this.camera = null;
    this.scene = null;
    this.renderer = null;
    this.controls = null;
    this.width  = parseFloat($dom_element.width());
    this.height = parseFloat($dom_element.height());

    if( this.height < 30 ) {
        this.height = this.width/9*6;
        $dom_element.height(this.height);
    }

    this.wireframe = false;
    this.rendering = false;
    this.container.style.display = "none";
    this.toggleButton = null;
    this.background = 0xffffff;
    this.model_url = model_url;
    this.loaded = false;

    this.object = null;

    if ( ! Detector.webgl ) { Detector.addGetWebGLMessage(); }

    this.initHtml();
    this.initScene();

    this.render();
};

// Initialize model viewer dom element - add buttons
ModelViewer.prototype.initHtml = function () {
    $this = this;

    var buttons = document.createElement("div");
    buttons.setAttribute("class", "modelviewer-buttons");

    this.wireframeButton = $('<button/>', {
        'class':'model',
        'style':'display:none',
        'html':'<i class="eye icon"/>Wireframe',
        'click': $this.toggleMaterial.bind($this)
    }).appendTo(buttons);

    this.toggleButton = $('<button/>', {
        'class':'toggle',
        'html':'<i class="cube icon"/>3D',
        'click': $this.toggleRendering.bind($this)
    }).appendTo(buttons);

    this.dom_element.append(buttons);
};

// Initialize camera - set default position and angles
ModelViewer.prototype.initCamera = function () {
    this.camera = new THREE.PerspectiveCamera(45, this.width / this.height, .1, 3000);
    this.camera.position.z = 800;
    this.camera.position.y = -450;
    this.camera.position.x = 350;
    this.camera.up = new THREE.Vector3(0, 0, 1);
};

ModelViewer.prototype.initScene = function() {
    this.scene = new THREE.Scene();
    this.scene.background = new THREE.Color( this.background );

    this.scene.fog = new THREE.FogExp2(this.background, 0.012);

    this.initLights();
    this.initCamera();

    var groundPlaneMaterial = new THREE.MeshPhongMaterial({
        color: 0xFFFFFF,
        wireframe: false,
        transparent: true,
        opacity: 0.25,
        fog: false,
        specular: 0x999999,
        shininess: 100
    });

    this.plane = new THREE.Mesh(new THREE.PlaneGeometry(800,800), groundPlaneMaterial);
    this.plane.receiveShadow = true;
    this.scene.add(this.plane);

    this.grid = new THREE.GridHelper( 800, 100, 0x000000, 0xAAAAAA);
    this.grid.rotation.x = Math.PI/2;
    this.scene.add(this.grid);

    this.renderer = new THREE.WebGLRenderer();
    this.renderer.setSize(this.width, this.height);
    this.renderer.setPixelRatio(window.devicePixelRatio ? window.devicePixelRatio : 1);

    this.container.append(this.renderer.domElement);

    this.controls = new THREE.OrbitControls( this.camera, this.renderer.domElement );
    this.controls.enableZoom = true;
};

ModelViewer.prototype.initLights = function () {
    this.spotLight = new THREE.SpotLight(0xffffff, 0.8, 0);
    this.spotLight.position.set(-100, 100, 100);
    this.spotLight.castShadow = false;
    this.scene.add(this.spotLight);

    this.bottomSpotLight = new THREE.SpotLight(0xffffff, 0.8, 0);
    this.bottomSpotLight.position.set(0, -10, -100);
    this.bottomSpotLight.castShadow = false;
    this.scene.add(this.bottomSpotLight);

    this.ambientLight = new THREE.AmbientLight(0xffffff, 0.1);
    this.scene.add(this.ambientLight);

    this.pointLight = new THREE.PointLight(0xfdfdfd, 1, 0);
    this.pointLight.position.set(32, -39, 35);
    this.pointLight.castShadow = true;
    this.scene.add(this.pointLight);
};

ModelViewer.prototype.addModel = function(geometry) {
    var material = new THREE.MeshPhongMaterial({
        color: 0x136fc3,
        specular: 0x111111,
        shading: THREE.SmoothShading,
        shininess: 30,
        fog: false,
        side: THREE.DoubleSide,
    });

    var mesh = new THREE.Mesh(geometry, material);

    // Move model to ground
    geometry.center();
    mesh.position.z = -mesh.geometry.boundingBox.min.z;

    this.object = mesh;
    this.scene.add(mesh);

    // Center and zoom camera to model
    this.centerCamera(mesh);
};

ModelViewer.prototype.loadStl = function(model) {
    var self = this;
    var loader = new THREE.STLLoader();

    loader.load(model,
        function (geometry) {
            // Add model to view
            self.addModel(geometry);
        },
        function(progress) {},
        function(error) {
            // Show error message
            self.showError();
        }
    );

    self.loaded = true;
};

ModelViewer.prototype.centerCamera = function(mesh) {
    var boxHelper =  new THREE.BoxHelper( mesh );

    var sceneCenter = this.objectCenter(mesh);

    var geometry = mesh.geometry;

    var distanceX = ((geometry.boundingBox.max.x - geometry.boundingBox.min.x) / 2) / Math.tan(this.camera.fov * this.camera.aspect * Math.PI / 360);
    var distanceY = (geometry.boundingBox.max.y - geometry.boundingBox.min.y) / 2 / Math.tan(this.camera.fov * this.camera.aspect * Math.PI / 360);
    var distanceZ = (geometry.boundingBox.max.z - geometry.boundingBox.min.z) / 2 / Math.tan(this.camera.fov * Math.PI / 360);

    var maxDistance = Math.max(Math.max(distanceX, distanceY), distanceZ);
    maxDistance *= 1.9;

    var cameraPosition = this.camera.position.normalize().multiplyScalar(maxDistance);

    this.controls.maxDistance = 3 * maxDistance;

    this.controls.position0 = cameraPosition;
    this.controls.target0 = sceneCenter;
    this.controls.reset();
};

ModelViewer.prototype.toggleRendering = function () {
    if($this.rendering) {
        $this.container.style.display = "none";
        $this.rendering = false;
        $this.toggleButton.html('<i class="cube icon"/>3D');
        $this.wireframeButton.hide();
    } else {
        if(!$this.loaded) {
            $this.loadStl($this.model_url);
        }
        $this.container.style.display = "block";
        $this.rendering = true;
        $this.toggleButton.html('<i class="close icon"/>Close');
        $this.wireframeButton.show();
    }
};

ModelViewer.prototype.toggleMaterial = function () {
    if($this.wireframe) {
        $this.wireframe = false;
        $this.wireframeButton.html('<i class="eye icon"/>Wireframe')
    } else {
        $this.wireframe = true;
        $this.wireframeButton.html('<i class="eye icon"/>Solid')
    }

    this.scene.traverse(function(object) {
        if (object instanceof THREE.Mesh) {
            object.material.wireframe = $this.wireframe;
        }
    });
};


ModelViewer.prototype.showError = function () {
    var wrapper = $('<div/>', {'class': 'modelviewer-wrapper'});
    var errorText = $('<p/>', {
        'class': 'ui center aligned icon header',
        'html': '<i class="warning icon"/> Model could not be loaded!',
    }).appendTo(wrapper);

    $this.dom_element.append(wrapper);
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

ModelViewer.prototype.resize = function(width, height) {
    this.width = width;
    this.height = height;
    this.camera.aspect = width / height;
    this.camera.updateProjectionMatrix();
    this.renderer.setSize(width, height);
};

ModelViewer.prototype.resize = function() {
    this.resize(parseInt(this.dom_element.width()), parseInt(this.dom_element.height()));
};

ModelViewer.prototype.render = function() {
    requestAnimationFrame(this.render);
    if(this.rendering) {
        this.renderer.render(this.scene, this.camera);
    }
    this.resize();
};
