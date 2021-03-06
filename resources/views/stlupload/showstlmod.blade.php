
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>WebViewer - STL</title>
		<meta charset="utf-8">
	</head>
	<body>
		<script src="/WebGLViewer/threejs/three.min.js"></script>
		<script src="/WebGLViewerthreejs/loaders/STLLoader.js"></script>
		<script src="/WebGLViewer/threejs/OrbitControls.js"></script>
		<style>body,html{margin:0;padding:0;overflow:hidden}</style>
		<script>
			var container;

			var camera, cameraTarget, scene, renderer;
			
			var cameraType = 1;
			var perspectiveAngle = 45;
			var cameraPosX = 200;
			var cameraPosY = 200;
			var cameraPosZ = 200;
			var cameraTargetX = 0;
			var cameraTargetY = 0;
			var cameraTargetZ = 0;
			var upVectorX = 0;
			var upVectorY = 1;
			var upVectorZ = 0;
			var cameralScale = 5;
			
			init();
			animate();
			
			function getQueryStringByName(name){
				 var result = location.search.match(new RegExp("[\?\&]" + name+ "=([^\&]+)","i"));
				
				 if(result == null || result.length < 1){
					 return "";
				 }

				 return result[1];
			}
			function volumeOfT(a, b, c){
				var p1 = a.x*b.y*c.z;
				var p2 = c.x*a.y*b.z;
				var p3 = b.x*c.y*a.z;
				var n1 = c.x*b.y*a.z;
				var n2 = b.x*a.y*c.z;
				var n3 = a.x*c.y*b.z;
				return (1.0/6.0)*(p1 + p2 + p3 - n1 - n2 - n3);
			}

			function init() {

				container = document.createElement( 'div' );
				document.body.appendChild( container );
				
				// set camera
				var cameraTypeStr = getQueryStringByName('cameraType');
				cameraType = cameraTypeStr == "" ? cameraType : parseInt(cameraTypeStr);
				
				var perspectiveAngleStr = getQueryStringByName('perspectiveAngle');
				perspectiveAngle = perspectiveAngleStr == "" ? perspectiveAngle : parseFloat(perspectiveAngleStr);
				
				var cameraPosXStr = getQueryStringByName('cameraPosX');
				cameraPosX = cameraPosXStr == "" ? cameraPosX : parseFloat(cameraPosXStr) * cameralScale;
				
				var cameraPosYStr = getQueryStringByName('cameraPosY');
				cameraPosY = cameraPosYStr == "" ? cameraPosY : parseFloat(cameraPosYStr) * cameralScale;
				
				var cameraPosZStr = getQueryStringByName('cameraPosZ');
				cameraPosZ = cameraPosZStr == "" ? cameraPosZ : parseFloat(cameraPosZStr) * cameralScale;
				
				var cameraTargetXStr = getQueryStringByName('cameraTargetX');
				cameraTargetX = cameraTargetXStr == "" ? cameraTargetX : parseFloat(cameraTargetXStr) * cameralScale;
				
				var cameraTargetYStr = getQueryStringByName('cameraTargetY');
				cameraTargetY = cameraTargetYStr == "" ? cameraTargetY : parseFloat(cameraTargetYStr) * cameralScale;
				
				var cameraTargetZStr = getQueryStringByName('cameraTargetZ');
				cameraTargetZ = cameraTargetZStr == "" ? cameraTargetZ : parseFloat(cameraTargetZStr) * cameralScale;
				
				var upVectorXStr = getQueryStringByName('upVectorX');
				upVectorX = upVectorXStr == "" ? upVectorX : parseFloat(upVectorXStr) * cameralScale;
				
				var upVectorYStr = getQueryStringByName('upVectorY');
				upVectorY = upVectorYStr == "" ? upVectorY : parseFloat(upVectorYStr) * cameralScale;
				
				var upVectorZStr = getQueryStringByName('upVectorZ');
				upVectorZ = upVectorZStr == "" ? upVectorZ : parseFloat(upVectorZStr) * cameralScale;
				
				if(cameraType == 0) {
					camera = new THREE.OrthographicCamera( window.innerWidth / - 2, window.innerWidth / 2, window.innerHeight / 2, window.innerHeight / - 2, 1, 10000 );
				}
				else {
					camera = new THREE.PerspectiveCamera( perspectiveAngle, window.innerWidth / window.innerHeight, 1, 10000 );
				}
			
				camera.position.set( cameraPosX, cameraPosY, cameraPosZ);
				camera.up.set(upVectorX, upVectorY, upVectorZ);

				cameraTarget = new THREE.Vector3( cameraTargetX, cameraTargetY, cameraTargetZ );
				camera.lookAt( cameraTarget );

				scene = new THREE.Scene();
				scene.fog = new THREE.Fog( 0xffffff, 1, 10000 );

				// load file

				var loader = new THREE.STLLoader();
				
				var modelName = getQueryStringByName('modelName');
				loader.load( '../model1/' + modelName, function ( geometry ) {
					geometry.computeBoundingBox();
					var material = new THREE.MeshPhongMaterial( { color: 0x808080, specular: 0x111111, shininess: 200 } );
					var mesh = new THREE.Mesh( geometry, material );
					var Area = 0.0;
					var volumes = 0.0;
					var newgeometry = new THREE.Geometry().fromBufferGeometry(mesh.geometry);
					for(var i = 0; i < newgeometry.faces.length; i++){
						var Pi = newgeometry.faces[i].a;
						var Qi = newgeometry.faces[i].b;
						var Ri = newgeometry.faces[i].c;
						var P = new THREE.Vector3(newgeometry.vertices[Pi].x, newgeometry.vertices[Pi].y, newgeometry.vertices[Pi].z);
						var Q = new THREE.Vector3(newgeometry.vertices[Qi].x, newgeometry.vertices[Qi].y, newgeometry.vertices[Qi].z);
						var R = new THREE.Vector3(newgeometry.vertices[Ri].x, newgeometry.vertices[Ri].y, newgeometry.vertices[Ri].z);
						var aot = new THREE.Triangle(R, Q, P);
						volumes += volumeOfT(R, Q, P);
						Area += aot.area();
					}
					var yx = yy = yz =0.0;
					yx = geometry.boundingBox.max.x - geometry.boundingBox.min.x;
					yy = geometry.boundingBox.max.y - geometry.boundingBox.min.y;
					yz = geometry.boundingBox.max.z - geometry.boundingBox.min.z;

					SurfaceArea = (Area).toFixed(3);
					loadedObjectVolume = (volumes).toFixed(3);
					
					console.log(yx.toFixed(3));
					console.log(yy.toFixed(3));
					console.log(yz.toFixed(3));
					console.log('?????????:'+SurfaceArea);
					console.log('??????:'+Math.abs(loadedObjectVolume));
		
					mesh.castShadow = true;
					mesh.receiveShadow = true;

					scene.add( mesh );

				} );

				// lights

				scene.add( new THREE.AmbientLight( 0x333333 ) );
				
				addDirectionalLight(-1, 1, 1, 0xFFFFFF, 1.35);
				addDirectionalLight(1, -1, -1, 0xFFFFFF, 1);

				// renderer

				renderer = new THREE.WebGLRenderer( { antialias: true } );
				renderer.setClearColor( scene.fog.color );
				renderer.setSize( window.innerWidth, window.innerHeight );

				renderer.gammaInput = true;
				renderer.gammaOutput = true;

				renderer.shadowMapEnabled = true;
				renderer.shadowMapCullFace = THREE.CullFaceBack;

				container.appendChild( renderer.domElement );
				
				// orbit control

				control = new THREE.OrbitControls( camera, renderer.domElement );

				// events

				window.addEventListener( 'resize', onWindowResize, false );
			}
			
			function addDirectionalLight( x, y, z, color, intensity ) {

				var directionalLight = new THREE.DirectionalLight( color, intensity );
				directionalLight.position.set( x, y, z )
				scene.add( directionalLight );
			}
			
			function onWindowResize() {

				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();

				renderer.setSize( window.innerWidth, window.innerHeight );

			}

			function animate() {

				requestAnimationFrame( animate );

				render();
			}

			function render() {

//				var timer = Date.now() * 0.0005;
//
//				camera.position.x = Math.cos( timer ) * 3;
//				camera.position.z = Math.sin( timer ) * 3;

				renderer.render( scene, camera );
			}

		</script>
	</body>
</html>