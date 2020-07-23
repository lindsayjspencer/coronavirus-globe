<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Coronavirus globe</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<script src="/assets/vendor/jquery/jquery.min.js"></script>
		<link type="text/css" rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" integrity="sha256-46qynGAkLSFpVbEBog43gvNhfrOj+BmwXdxFgVK/Kvc=" crossorigin="anonymous">
		<script type="module" src="/assets/vendor/popperjs/popper.js"></script>
		<script src="/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="/assets/vendor/tween.js/tween.js"></script>
		<script src="/assets/vendor/three/build/three.min.js"></script>
		<script src="//unpkg.com/three-globe"></script>

		<style>
            @media only screen and (min-width: 578px) {
				.open-menu {
					position: fixed;
					bottom: 0;
					left: 0;
					min-width: 300px;
					transition: transform 400ms ease-in-out;
					transform: translateY(0vh);
				}
				.open-menu.hide {
					transition: transform 400ms ease-in-out;
					transform: translateY(100vh);
				}
            }
            @media only screen and (max-width: 577px) {
	            div.card.left-menu {
					width: 100vw;
					max-width: 577px;
	            }
	            div.card.country-menu {
					width: 100vw;
					max-width: 577px;
	                bottom: unset;
	                right: 0;
					top: 0;
	                box-shadow: 0px 0px 0px 0px #00000030;
	                position: fixed;
	                /* overflow-x: hidden; */
	                /* height: 100vh; */
	                transition: transform 400ms ease-in-out, box-shadow 400ms ease-in-out;
	                transform: translateY(-100vh);
	            }
				.open-menu {
					position: fixed;
					bottom: 0;
					left: 0;
					min-width: 100vw;
					transition: transform 400ms ease-in-out;
					transform: translateY(0vh);
				}
				.open-menu.hide {
					transition: transform 400ms ease-in-out;
					transform: translateY(100vh);
				}
            }
			body {
				background-color: #cce0ff;
				color: #000;
				margin: 0;
			}
			a {
				color: #080;
                box-shadow: none;
			}
            .left-panel-internal a, .left-panel-internal a:focus {
                box-shadow: none;
            }
			canvas { display: block; }
            .btn-menu {
                color: var(--primary);
                transition: all 150ms;
                box-shadow: none;
            }
            .btn-menu:hover {
                /* color: #ffffff; */
                background: #eaeaea;
            }
			.loading-msg {
				height: 100vh;
				width: 100vw;
				position: fixed;
				z-index: 1100;
				opacity: 1;
				background: white;
				transition: opacity 200ms ease-in-out;
			}
			.loading-msg.hide {
				opacity: 0;
			}
			.loading-text {
				color: #636363;
				display: inline-block;
				text-align: center;
				padding: 0rem 0rem 0.5rem 0rem;
			}
			.loading-msg.initialising .progress {
				max-height: 0px;
				padding: 0rem 0rem 0rem 0rem;
				transition: all 200ms ease-in-out;
			}
			.loading-msg.initialising .loading-text {
				max-height: 0px;
				transition: all 200ms 100ms ease-in-out;
				padding: 0rem 0rem 0rem 0rem;
			}
			.loading-cog {
				color: #09abe8;
				max-height: 0px;
				overflow: hidden;
			}
			.loading-msg.initialising .loading-cog {
				max-height: 100px;
				transition: all 200ms 300ms ease-in-out;
			}
			.loading-msg .progress {
				width: 20rem;
				padding: 0.5rem 0rem 0.5rem 0rem;
			}
			a.btn i {
				background: #00000010;
				min-width: 3rem;
			}
			a i::before {
				vertical-align: sub;
			}
            .card.country-menu {
				width: 20rem;
				max-width: 20rem;
                bottom: 0;
                right: 0;
                box-shadow: 0px 0px 0px 0px #00000030;
                position: fixed;
                /* overflow-x: hidden; */
                /* height: 100vh; */
                transition: transform 400ms ease-in-out, box-shadow 400ms ease-in-out;
                transform: translateY(100vh);
            }
            .card.country-menu.show {
                transform: translateY(0vh);
                box-shadow: 0px 0px 24px 0px #00000030;
            }
            .card.left-menu {
                min-width: 20rem;
                bottom: 0;
                left: 0;
                box-shadow: 0px 0px 0px 0px #00000030;
                position: fixed;
				z-index: 1;
                /* overflow-x: hidden; */
                /* height: 100vh; */
                transition: transform 400ms ease-in-out, box-shadow 400ms ease-in-out;
                transform: translateY(100vh);
            }
            .card.left-menu.show {
                transform: translateY(0vh);
                box-shadow: 0px 0px 24px 0px #00000030;
            }
            .darker {
                background: #00000030;
                min-width: 3rem;
            }
			.cases-t-progress {
				background-color: #ff8aff;
			}
			.cases-pm-progress {
				background-color: #c300c3;
			}
			.deaths-t-progress {
				background-color: #ff7c5c;
			}
			.deaths-pm-progress {
				background-color: #ff3200;
			}
			.tests-t-progress {
				background-color: #6ab2ff;
			}
			.small-title {
				font-size: 12px;
			}
			.highlight-cases.active, .open-menu.cases {
				background: #c300c3;
				color: #fff;
			}
			.highlight-tests.active, .open-menu.tests {
				background: #007bff;
				color: #fff;
			}
			.highlight-deaths.active, .open-menu.deaths {
				background: #ff3200;
				color: #fff;
			}
		</style>
		<script>
		function clip(text) {
	  		var copyElement = document.createElement('input');
			copyElement.setAttribute('type', 'text');
	  		copyElement.setAttribute('value', text);
	  		copyElement = document.body.appendChild(copyElement);
	  		copyElement.select();
	  		document.execCommand('copy');
	  		copyElement.remove();
	  	}
		function consoleLog(text) {
			console.log(text);
			$(".console-ticker").html(text);
		}
		function _fcur(num) {
			return formatMoney(parseFloat(num), 0);
		}

		function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
		  try {
			decimalCount = Math.abs(decimalCount);
			decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

			const negativeSign = amount < 0 ? "-" : "";

			let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
			let j = (i.length > 3) ? i.length % 3 : 0;

			return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
		  } catch (e) {
			// console.log(e)
		  }
		};
		</script>
	</head>

	<body>

		<!-- <div class="loading-msg d-flex flex-column align-items-center">
			<div class="loading-cog mb-2 mt-auto">
				<i class="fas fa-cog fa-spin fa-3x"></i>
			</div>
			<h5 class="loading-text">
				0%
			</h5>
			<div class="progress">
				<div class="progress-bar progress-bar-striped progress-bar-animated loading-progress" role="progressbar" style="width: 0%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
			<h5 class="loading mb-auto mt-2">
				Loading models
			</h5>
		</div> -->
        <div class="card country-menu d-flex flex-column rounded-0 align-items-center border-0">
            <div class="d-flex flex-column left-panel-internal w-100 show flex-fill">
                <h4 class="d-flex flex-row align-items-center my-3 px-3">
                    <span class="country-name"></span>
                </h4>
                <div class="d-flex w-100 product-options-container mb-2">
                    <div class="d-flex flex-column flex-fill">
						<div class="progress my-1 mx-3">
							<div class="progress-bar progress-bar-striped deaths-t-progress" role="progressbar" style="width: 50%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
							<div class="mb-2 d-flex flex-row mx-3 align-items-center">
								<span class="mr-auto small-title">DEATHS</span>
								<div class="text-right text-black-50 deaths-t-num"></div>
							</div>
						<div class="progress my-1 mx-3">
							<div class="progress-bar progress-bar-striped deaths-pm-progress" role="progressbar" style="width: 50%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="mb-2 d-flex flex-row mx-3 align-items-center">
							<span class="mr-auto small-title">DEATHS PER MILLION</span>
							<div class="text-right text-black-50 deaths-pm-num"></div>
						</div>
						<div class="progress my-1 mx-3">
							<div class="progress-bar progress-bar-striped cases-t-progress" role="progressbar" style="width: 50%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="mb-2 d-flex flex-row mx-3 align-items-center">
							<span class="mr-auto small-title">CASOS</span>
							<div class="text-right text-black-50 cases-t-num"></div>
						</div>
						<div class="progress my-1 mx-3">
							<div class="progress-bar progress-bar-striped cases-pm-progress" role="progressbar" style="width: 50%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="mb-2 d-flex flex-row mx-3 align-items-center">
							<span class="mr-auto small-title">CASES PER MILLION</span>
							<div class="text-right text-black-50 cases-pm-num"></div>
						</div>
						<div class="progress my-1 mx-3">
							<div class="progress-bar progress-bar-striped tests-t-progress" role="progressbar" style="width: 50%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="mb-2 d-flex flex-row mx-3 align-items-center">
							<span class="mr-auto small-title">TESTS</span>
							<div class="text-right text-black-50 tests-t-num"></div>
						</div>
						<div class="progress my-1 mx-3">
							<div class="progress-bar progress-bar-striped tests-pt-progress" role="progressbar" style="width: 50%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
						<div class="mb-2 d-flex flex-row mx-3 align-items-center">
							<span class="mr-auto small-title">TESTS PER THOUSAND</span>
							<div class="text-right text-black-50 tests-pt-num"></div>
						</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card left-menu d-flex flex-column rounded-0 align-items-center border-0">
            <div class="d-flex flex-column left-panel-internal w-100 show flex-fill">
                <div class="d-flex flex-column align-items-start mb-3">
					<a href="#" class="highlight-btn btn btn-menu rounded-0 border-0 d-flex align-content-stretch p-0 w-100 highlight-cases active">
						<span class="py-3 px-3">Cases</span>
						<i class="py-3 fas fa-notes-medical fa-fw ml-auto"></i>
					</a>
                    <a href="#" class="highlight-btn btn btn-menu rounded-0 border-0 d-flex align-content-stretch p-0 w-100 highlight-deaths">
                        <span class="py-3 px-3">Deaths</span>
                        <i class="py-3 fas fa-skull fa-fw ml-auto"></i>
                    </a>
                    <a href="#" class="highlight-btn btn btn-menu rounded-0 border-0 d-flex align-content-stretch p-0 w-100 highlight-tests">
                        <span class="py-3 px-3">Tests</span>
                        <i class="py-3 fas fa-vial fa-fw ml-auto"></i>
                    </a>
                </div>
                <!-- <h5 class="d-flex flex-row align-items-center my-2 px-3">
                    <small class="text-muted">Camera</small>
                    <div class="d-flex flex-row justify-content-end ml-auto">
                        <a href="#" class="btn btn-menu rounded-0 border-0 lock-camera-btn d-flex align-content-stretch p-0 w-100">
                            <i class="p-2 fas fa-lock fa-fw ml-auto"></i>
                        </a>
                        <a href="#" class="btn btn-warning text-white rounded-0 border-0 auto-rotate-btn d-flex align-content-stretch p-0 w-100">
                            <i class="p-2 fas fa-times fa-fw ml-auto"></i>
                        </a>
                    </div>
                </h5> -->
            </div>
        </div>
		<a href="#" class="btn rounded-0 border-0 open-menu p-0 d-flex align-content-stretch cases">
			<span class="p-3 global-type">Cases</span>
			<span class="p-3 ml-auto global-num">0</span>
			<i class="ml-3 py-3 fas fa-notes-medical fa-fw"></i>
		</a>

		<div id="container"></div>

		<script type="module">

			var lines = [];
			var hovering = [];
			var selected = [];
			var updateLines = [];
			var updateMode = false;

			var global_tests, global_cases, global_deaths;

			var rayCaster = new THREE.Raycaster();
			var mousePosition = new THREE.Vector2();

			var highlightColors = {
				selected: {
					r: 0,
					g: 0.9,
					b: 0.2
				},
				hover: {
					r: 0.1,
					g: 0.7,
					b: 0
				},
				neutral: {
					r: 1,
					g: 1,
					b: 1
				}
			}

            import Stats from '/assets/vendor/three/examples/jsm/libs/stats.module.js';
            import { GUI } from '/assets/vendor/three/examples/jsm/libs/dat.gui.module.js';

            import { OrbitControls } from '/assets/vendor/three/examples/jsm/controls/OrbitControls.js';

			fetch('/assets/vendor/three-globe-master/example/country-polygons/ne_110m_admin_0_countries.geojson').then(res => res.json()).then(countries => {

				$(document).ready(function() {
				    $.ajax({
				        type: "GET",
				        url: "/data/coronavirus-data.csv?_=" + new Date().getTime(),
				        dataType: "text",
				        success: function(data) {processData(data);}
				     });
				});

				function processData(allText) {
				    var allTextLines = allText.split(/\r\n|\n/);
				    var headers = allTextLines[0].split(',');
				    lines = [];

				    for (var i=1; i<allTextLines.length; i++) {
				        var data = allTextLines[i].split(',');
				        if (data.length == headers.length) {

				            var tarr = [];
				            for (var j=0; j<headers.length; j++) {
				                tarr.push(data[j]);
				            }
				            lines.push(tarr);
				        }
				    }
					init();
				}

				function init() {

					// countries.features.forEach((feat) => {
					// 	if(country[0] == feat.properties.SOVEREIGNT) {
					// 		if(updateMode=="deaths") {
					// 			feat.altitude = parseFloat(country[3])+0.1;
					// 			feat.alphaColor = 'rgba('+(255-(60*country[4]))+', '+(255-(255*country[4]))+', 0, '+(Math.random()/3+0.5)+')'
					// 			feat.originalColor = {
					// 				r: (255-(60*country[4]))/255,
					// 				g: (255-(255*country[4]))/255,
					// 				b: 0
					// 			};
					// 		} else if(updateMode=="cases") {
					// 			feat.altitude = parseFloat(country[1])+0.1;
					// 			feat.alphaColor = 'rgba(255, '+(255-(255*country[2]))+', 0, '+(Math.random()/3+0.5)+')'
					// 			feat.originalColor = {
					// 				r: 1,
					// 				g: (255-(255*country[2]))/255,
					// 				b: 0
					// 			};
					// 		}
					// 	}
					// });

					var h_death_t = 0;
					var h_death_pm = 0;
					var h_cases_t = 0;
					var h_cases_pm = 0;
					var h_tests_t = 0;
					var h_tests_pt = 0;
					countries.features.forEach((feat) => {
						lines.forEach((country) => {
							if(country[0] == feat.properties.NAME_LONG || country[0] == feat.properties.NAME || country[0] == feat.properties.SOVEREIGNT) {
								console.log(country[0]+" matched");
								if(country[2]>h_death_t) { h_death_t = parseFloat(country[2]); }
								if(country[1]>h_death_pm) { h_death_pm = parseFloat(country[1]); }
								if(country[4]>h_cases_t) { h_cases_t = parseFloat(country[4]); }
								if(country[3]>h_cases_pm) { h_cases_pm = parseFloat(country[3]); }
								if(country[6]>h_tests_t) { h_tests_t = parseFloat(country[6]); }
								if(country[5]>h_tests_pt) { h_tests_pt = parseFloat(country[5]); }
							}
						});
					});
					countries.features.forEach((feat) => {
						feat.stats = {
							death_t: 0,
							death_pm: 0,
							cases_t: 0,
							cases_pm: 0,
							tests_t: 0,
							tests_pt: 0,
							r_death_t: 0,
							r_death_pm: 0,
							r_cases_t: 0,
							r_cases_pm: 0,
							r_tests_t: 0,
							r_tests_pt: 0
						};
						lines.forEach((country) => {
							if(country[0] == feat.properties.NAME_LONG || country[0] == feat.properties.NAME || country[0] == feat.properties.SOVEREIGNT) {
								feat.stats = {
									death_t: country[2],
									death_pm: country[1],
									cases_t: country[4],
									cases_pm: country[3],
									tests_t: country[6],
									tests_pt: country[5],
									r_death_t: Math.round((country[2]/h_death_t)*100)/100,
									r_death_pm: Math.round((country[1]/h_death_pm)*100)/100,
									r_cases_t: Math.round((country[4]/h_cases_t)*100)/100,
									r_cases_pm: Math.round((country[3]/h_cases_pm)*100)/100,
									r_tests_t: Math.round((country[6]/h_tests_t)*100)/100,
									r_tests_pt: Math.round((country[5]/h_tests_pt)*100)/100
								};
							}
						});
						// console.log(feat);
						feat.alphaColor = 'rgba(255, 255, 255, 0.8)'
						feat.originalColor = highlightColors.neutral;
						feat.altitude = 0.1;
					});

				    const Globe = new ThreeGlobe()
				      .globeImageUrl('//unpkg.com/three-globe/example/img/earth-day.jpg')
				      .bumpImageUrl('//unpkg.com/three-globe/example/img/earth-topology.png')
					  .showAtmosphere(true)
				      .polygonsData(countries.features)
				      .polygonCapColor('alphaColor')
					  .polygonSideColor(() => 'rgba(204, 224, 255, 0.2)')
				      .polygonAltitude('altitude')
					  .polygonsTransitionDuration(50)
				      .polygonStrokeColor(() => '#111');
				      // .pointsData(gData)
				      // .pointAltitude('size')
				      // .pointColor('color');

					  // custom globe material
					const globeMaterial = Globe.globeMaterial();
					globeMaterial.bumpScale = 10;
					new THREE.TextureLoader().load('//unpkg.com/three-globe/example/img/earth-water.png', texture => {
					  globeMaterial.specularMap = texture;
					  globeMaterial.specular = new THREE.Color('grey');
					  globeMaterial.shininess = 15;
					});

				    // setTimeout(() => {
				    //   gData.forEach(d => d.size = Math.random());
				    //   Globe.pointsData(gData);
				    // }, 4000);

					var id = false;

					function updateAltitudes() {
						console.log("updating 10");
						var x = 0;
						for(var x=0; x<10; x++) {
							var country = updateLines.shift();
							var altitude = 0;
							var color = 0;
							if(country!=undefined) {
								countries.features.forEach((feat) => {
									if(country[0] == feat.properties.NAME_LONG || country[0] == feat.properties.NAME || country[0] == feat.properties.SOVEREIGNT) {
										if(updateMode=="deaths") {
											global_deaths = (parseInt(global_deaths)+parseInt(feat.stats.death_t));
											$(".global-num").html(_fcur(global_deaths));
											altitude = feat.stats.r_death_pm;
											color = feat.stats.r_death_t;
											feat.altitude = parseFloat(altitude)+0.1;
											feat.alphaColor = 'rgba(255, 50, '+(200-(255*color))+', '+(Math.random()/3+0.5)+')'
											feat.originalColor = {
												r: 1,
												g: (50/255),
												b: (200-(255*color))/255
											};
										} else if(updateMode=="cases") {
											altitude = feat.stats.r_cases_pm;
											color = feat.stats.r_cases_t;
											global_cases = (parseInt(global_cases)+parseInt(feat.stats.cases_t));
											$(".global-num").html(_fcur(global_cases));
											feat.altitude = parseFloat(altitude)+0.1;
											feat.alphaColor = 'rgba(255, '+(200-(255*color))+', 255, '+(Math.random()/3+0.5)+')'
											feat.originalColor = {
												r: 1,
												g: (200-(255*color))/255,
												b: 1
											};
										} else if(updateMode=="tests") {
											altitude = feat.stats.r_tests_pt;
											color = feat.stats.r_tests_t;
											global_tests = (parseInt(global_tests)+parseInt(feat.stats.tests_t));
											$(".global-num").html(_fcur(global_tests));
											feat.altitude = parseFloat(altitude)+0.1;
											feat.alphaColor = 'rgba('+Math.round((255*color))+', 165, 255, '+(Math.random()/3+0.5)+')'
											feat.originalColor = {
												r: ((255*color))/255,
												g: (165/255),
												b: 1
											};
										}
									}
								});
							}
						}
						Globe.polygonsData(countries.features)
						  .polygonCapColor('alphaColor')
						  .polygonAltitude('altitude')
					}

				    // Setup renderer
				    const renderer = new THREE.WebGLRenderer({ antialias: true });
				    renderer.setSize(window.innerWidth, window.innerHeight);
				    document.getElementById('container').appendChild(renderer.domElement);

				    // Setup scene
				    const scene = new THREE.Scene();

					Globe.name = 'earth';
				    scene.add(Globe);
				    scene.add(new THREE.AmbientLight(0xbbbbbb));
				    scene.add(new THREE.DirectionalLight(0xffffff, 0.6));

				    // Setup camera
				    const camera = new THREE.PerspectiveCamera();
				    camera.aspect = window.innerWidth/window.innerHeight;
				    camera.updateProjectionMatrix();
				    camera.position.z = 500;

				    // Add camera controls
					var controls = new OrbitControls( camera, renderer.domElement );
					controls.maxPolarAngle = Math.PI * 0.85;
					controls.minPolarAngle = Math.PI * 0.05;
					controls.minDistance = 300;
					controls.maxDistance = 700;
					controls.autoRotate = true;
					controls.enableDamping = true;
					controls.dampingFactor = 0.15;
					controls.enablePan = false;
					controls.autoRotateSpeed = 1.5;

					console.log(scene.getObjectByName('earth').children[0].children[4]);

					// performance monitor

					// var stats = new Stats();
					// container.appendChild( stats.dom );
					//
					// // hijack stats.dom and move it into the left menu
					// $("#container div").addClass("framerate-ticker d-flex w-100");
					// //add framerate-ticker class to find it
					// $(".left-menu").append($(".framerate-ticker"));
					// //append to the left menu
					// $(".framerate-ticker").css("position", "relative");
					// //change position from fixed
					// $(".framerate-ticker canvas").addClass("d-block");
					// //prevent canvases from disappearing on click
					// $(".framerate-ticker").prepend('<a href="#" class="btn btn-primary rounded-0 btn-block menu-down d-block"><i class="fas fa-angle-down" style="background: transparent;"></i></a>');
					//add close menu button

					window.addEventListener( 'resize', onWindowResize, false );

					function onWindowResize() {

						camera.aspect = window.innerWidth / window.innerHeight;
						camera.updateProjectionMatrix();

						renderer.setSize( window.innerWidth, window.innerHeight );

					}

					var canvas = renderer.domElement;
		            var canvasPosition = $(canvas).position();

		            var moveMarker = false;


					var clickCreate = false;
					var cancelClick = false;

					var touchMoveCount = 0;
					var clickCount = 0;

					$(renderer.domElement).on("mousedown", function(){
						cancelClick = false;
					});
					$(renderer.domElement).on("mouseup", function(){
						clickCount = 0;
						cancelClick = false;
					});
					$(renderer.domElement).on("mousemove", function(evt) {
						clickCount++;
						if(clickCount>3) {
							cancelClick = true;
							clickCount = 0;
						}
						mousePosition.x = ((evt.clientX - canvasPosition.left) / canvas.width) * 2 - 1;
						mousePosition.y = -((evt.clientY - canvasPosition.top) / canvas.height) * 2 + 1;

						rayCaster.setFromCamera(mousePosition, camera);
						var intersects = rayCaster.intersectObjects(scene.getObjectByName('earth').children, true);
						var earth_hover = (intersects.length) > 0 ? intersects : null;

						if(earth_hover) {
							var s = false;
							intersects.forEach((sect) => {
								if(sect.object.type == "Mesh" && !s) {
									if(Array.isArray(sect.object.material)) {
										s = sect;
									}
								}
							});
							if(s) {
								var cid = s.object.parent.__data.data.__id;
								var found = false;
								selected.forEach((country) => {
									if(country[0].__data.data.__id===cid) { found = true; }
								});
								if(!found) {
									var found = false;
									while(hovering.length>0) {
										var country = hovering.shift();
										if(country[0].__data.data.__id !== cid) {
											country.forEach((piece) => {
												new TWEEN.Tween( piece.children[0].material[1].color ).to( piece.__data.data.originalColor, 100).start();
												new TWEEN.Tween( piece.children[0].material[1] ).to( { opacity:0.7 }, 100).start();
											});
										}
									}
									hovering.forEach((country) => {
										if(country[0].__data.data.__id===cid) { found = true; }
									});
									if(!found) {
										var country_group = [];
										scene.getObjectByName("earth").children[0].children[4].children.forEach((ccountry) => {
											if(ccountry.__data!=undefined) {
												if(ccountry.__data.data.__id === cid) {
													// console.log(cid+' '+ccountry.__data.data.__id);
													new TWEEN.Tween( ccountry.children[0].material[1].color ).to( highlightColors.hover, 50).start();
													new TWEEN.Tween( ccountry.children[0].material[1] ).to( { opacity:1 }, 100).start();
													country_group.push( ccountry );
												}
											}
										});
										hovering.push( country_group );
										// console.log(country_group[0].__data.data.properties.SOVEREIGNT);
									}
								}
							} else {
								while(hovering.length>0) {
									var country = hovering.shift();
									country.forEach((piece) => {
										new TWEEN.Tween( piece.children[0].material[1].color ).to( piece.__data.data.originalColor, 100).start();
										new TWEEN.Tween( piece.children[0].material[1] ).to( { opacity:0.7 }, 100).start();
									});
								}
							}
						} else {
							//unhover
							while(hovering.length>0) {
								var country = hovering.shift();
								country.forEach((piece) => {
									new TWEEN.Tween( piece.children[0].material[1].color ).to( piece.__data.data.originalColor, 100).start();
									new TWEEN.Tween( piece.children[0].material[1] ).to( { opacity:0.7 }, 100).start();
								});
							}
						}
					});
					$(renderer.domElement).on("touchstart", function(){
						cancelClick = false;
					});
					$(renderer.domElement).on("touchmove", function() {
						touchMoveCount++;
						if(touchMoveCount>10) {
							cancelClick = true;
							touchMoveCount = 0;
						}
					});
					$(renderer.domElement).on("touchend", function() {
						touchMoveCount = 0;
					});
					$(renderer.domElement).on("click", getClicked3DPoint);
					$(renderer.domElement).on("touchend", getClicked3DPoint);
					$(".open-menu").on("click", function() {
						var b = $(".open-menu");
						$(".left-menu").toggleClass("show");
						b.toggleClass("hide");
						if($(".menu-container").hasClass("hide")) {
							stop2dShapeCapture();
						}
					});
					$(".menu-down").on("click", function(e) {
						if (!e) var e = window.event;
						e.cancelBubble = true;
						if (e.stopPropagation) e.stopPropagation();
						var b = $(".open-menu");
						$(".left-menu").toggleClass("show");
						b.toggleClass("hide");
						if($(".menu-container").hasClass("hide")) {
							stop2dShapeCapture();
						}
					});
					$(".lock-camera-btn").on("click", function() {
						var b = $(this);
						b.find("i").toggleClass("fa-lock fa-unlock");
						b.toggleClass("btn-menu btn-warning text-white");
						controls.enabled = (!controls.enabled);
						if(controls.enabled) {
							controls.enableDamping = true;
						} else {
							controls.enableDamping = false;
						}
					});
					$(".auto-rotate-btn").on("click", function() {
						var b = $(this);
						b.find("i").toggleClass("fa-sync-alt fa-times");
						b.toggleClass("btn-menu btn-warning text-white");
						controls.autoRotate = (!controls.autoRotate);
					});
					$(".highlight-deaths").on("click", function() {
						updateMode = "deaths";
						updateLines = [...lines];
						global_deaths = 0;
						var b = $(this);
						b.addClass("active");
						$(".highlight-btn").not(b).removeClass("active");
						$(".open-menu span").html('Deaths');
						$(".left-menu").removeClass("show");
						$(".open-menu").removeClass("hide").removeClass("tests cases deaths").addClass("deaths").find("i").removeClass("fa-notes-medical fa-skull fa-vial").addClass("fa-skull");
					});
					$(".highlight-cases").on("click", function() {
						updateMode = "cases";
						updateLines = [...lines];
						global_cases = 0;
						var b = $(this);
						b.addClass("active");
						$(".highlight-btn").not(b).removeClass("active");
						$(".open-menu span").html('Cases');
						$(".left-menu").removeClass("show");
						$(".open-menu").removeClass("hide").removeClass("tests cases deaths").addClass("cases").find("i").removeClass("fa-notes-medical fa-skull fa-vial").addClass("fa-notes-medical");
					});
					$(".highlight-tests").on("click", function() {
						updateMode = "tests";
						updateLines = [...lines];
						global_tests = 0;
						var b = $(this);
						b.addClass("active");
						$(".highlight-btn").not(b).removeClass("active");
						$(".open-menu span").html('Tests');
						$(".left-menu").removeClass("show");
						$(".open-menu").removeClass("hide").removeClass("tests cases deaths").addClass("tests").find("i").removeClass("fa-notes-medical fa-skull fa-vial").addClass("fa-vial");
					});


					var save2dClick = false;
					var capturedShape = [];

	                function getClicked3DPoint(evt) {
	                    if(!cancelClick) {
	                        evt.preventDefault();

	                        mousePosition.x = ((evt.clientX - canvasPosition.left) / canvas.width) * 2 - 1;
	                        mousePosition.y = -((evt.clientY - canvasPosition.top) / canvas.height) * 2 + 1;

	                        if(evt.type=="touchend") {
	                            mousePosition.x = (event.changedTouches[0].clientX / window.innerWidth) * 2 - 1;
	                            mousePosition.y = -(event.changedTouches[0].clientY / window.innerHeight) * 2 + 1;
	                        }


							rayCaster.setFromCamera(mousePosition, camera);
							var intersects = rayCaster.intersectObjects(scene.getObjectByName('earth').children, true);
							var earth_click = (intersects.length) > 0 ? intersects : null;

							if(earth_click) {
								var s = false;
								intersects.forEach((sect) => {
									if(sect.object.type == "Mesh" && !s) {
										if(Array.isArray(sect.object.material)) {
											s = sect;
										}
									}
								});
								if(s) {
									// var found = false;
									// var i = 0;
									// hovering.forEach((select) => {
									// 	if(select==s.object) {
									// 		hovering.splice(i, 1);
									// 	}
									// 	i++;
									// });
									var cid = s.object.parent.__data.data.__id;
									while(hovering.length>0) {
										var country = hovering.shift();
										if(country[0].__data.data.__id !== cid) {
											country.forEach((piece) => {
												new TWEEN.Tween( piece.children[0].material[1].color ).to( piece.__data.data.originalColor, 100).start();
												new TWEEN.Tween( piece.children[0].material[1] ).to( { opacity:0.7 }, 100).start();
											});
										}
									}
									while(selected.length>0) {
										var country = selected.shift();
										country.forEach((piece) => {
											new TWEEN.Tween( piece.children[0].material[1].color ).to( piece.__data.data.originalColor, 100).start();
											new TWEEN.Tween( piece.children[0].material[1] ).to( { opacity:0.7 }, 100).start();
										});
									}
									var country_group = [];
									// console.log(scene.getObjectByName("earth").children[0].children[4]);
									scene.getObjectByName("earth").children[0].children[4].children.forEach((ccountry) => {
										if(ccountry.__data!=undefined) {
											if(ccountry.__data.data.__id === cid) {
												// console.log(cid+' '+ccountry.__data.data.__id);
												new TWEEN.Tween( ccountry.children[0].material[1].color ).to( highlightColors.selected, 50).start();
												new TWEEN.Tween( ccountry.children[0].material[1] ).to( { opacity:1 }, 100).start();
												country_group.push( ccountry );
											}
										}
									});
									selected.push( country_group );
									console.log(country_group);
									console.log(country_group[0].__data.data.properties.NAME_LONG);
									$(".country-name").html(country_group[0].__data.data.properties.NAME_LONG);
									$(".country-menu").addClass("show");
									$(".deaths-t-progress").css("width", parseFloat(country_group[0].__data.data.stats.r_death_t)*100+"%");
									$(".deaths-pm-progress").css("width", parseFloat(country_group[0].__data.data.stats.r_death_pm)*100+"%");
									$(".cases-t-progress").css("width", parseFloat(country_group[0].__data.data.stats.r_cases_t)*100+"%");
									$(".cases-pm-progress").css("width", parseFloat(country_group[0].__data.data.stats.r_cases_pm)*100+"%");
									$(".tests-t-progress").css("width", parseFloat(country_group[0].__data.data.stats.r_tests_t)*100+"%");
									$(".tests-pt-progress").css("width", parseFloat(country_group[0].__data.data.stats.r_tests_pt)*100+"%");
									$(".deaths-t-num").html(_fcur(parseInt(country_group[0].__data.data.stats.death_t)));
									$(".deaths-pm-num").html(_fcur(parseInt(country_group[0].__data.data.stats.death_pm)));
									$(".cases-t-num").html(_fcur(parseInt(country_group[0].__data.data.stats.cases_t)));
									$(".cases-pm-num").html(_fcur(parseInt(country_group[0].__data.data.stats.cases_pm)));
									$(".tests-t-num").html(_fcur(parseInt(country_group[0].__data.data.stats.tests_t)));
									$(".tests-pt-num").html(_fcur(parseInt(country_group[0].__data.data.stats.tests_pt)));
								} else {
									while(selected.length>0) {
										var country = selected.shift();
										country.forEach((piece) => {
											new TWEEN.Tween( piece.children[0].material[1].color ).to( piece.__data.data.originalColor, 100).start();
											new TWEEN.Tween( piece.children[0].material[1] ).to( { opacity:0.7 }, 100).start();
										});
									}
									$(".country-menu").removeClass("show");
								}
								// while(stalls_group.getObjectByProperty("hovering", true)!==undefined) {
								// 	stalls_group.getObjectByProperty("hovering", true).getObjectByName("stallSelectFrame").visible = false;
								// 	stalls_group.getObjectByProperty("hovering", true).hovering = false;
								// }
								// if(s.name=='stall') {
								// 	if(s.selected===undefined) { s.selected = false; }
								// 	if(!s.selected) {
								// 		s.getObjectByName("stallSelectFrame").visible = true;
								// 		s.hovering = true;
								// 	} else {
								// 		s.getObjectByName("stallSelectFrame").visible = false;
								// 	}
								// }
							} else {
								//unhover
								while(selected.length>0) {
									var country = selected.shift();
									country.forEach((piece) => {
										new TWEEN.Tween( piece.children[0].material[1].color ).to( piece.__data.data.originalColor, 100).start();
										new TWEEN.Tween( piece.children[0].material[1] ).to( { opacity:0.7 }, 100).start();
									});
								}
								$(".country-menu").removeClass("show");
							}
	                    }
	                };

					const loader = new THREE.CubeTextureLoader();
					  const texture = loader.load([
					    '/assets/vendor/three-globe-master/example/img/skybox/back.png',
					    '/assets/vendor/three-globe-master/example/img/skybox/front.png',
					    '/assets/vendor/three-globe-master/example/img/skybox/top.png',
					    '/assets/vendor/three-globe-master/example/img/skybox/bottom.png',
					    '/assets/vendor/three-globe-master/example/img/skybox/left.png',
					    '/assets/vendor/three-globe-master/example/img/skybox/left.png',
					  ]);
					  scene.background = texture;

				    // Kick-off renderer
					var frameCount = 0;
				    (function animate() { // IIFE
					  frameCount++;
				      // Frame cycle
					  // stats.update();
					  TWEEN.update();
				      controls.update();
				      renderer.render(scene, camera);
				      requestAnimationFrame(animate);
					  if(frameCount==30) {
						updateMode = "cases";
						updateLines = [...lines];
						global_cases = 0;
					  }
					  if(frameCount>30) {
						  if(frameCount % 3 == 0) {
							  if(updateLines.length>0) {
								  updateAltitudes();
							  }
						  }
					  }
				    })();

				}

			});
  		</script>
	</body>
</html>
