<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/trie.php');

//----------------------------------------------------------------------------------------
function annotations_to_geojson ($annotations)
{
	if (0)
	{
		echo '<pre>';
		print_r($annotations);
		echo '</pre>';
	}

	$geojson = new stdclass;
	$geojson->type = "FeatureCollection";
	$geojson->features = array();

	foreach ($annotations as $annotation)
	{
		$feature = new stdclass;
		$feature->type = "Feature";
	
		$feature->geometry = new stdclass;
		$feature->geometry->type = "Point";
		$feature->geometry->coordinates = array();

		if (isset($annotation->thing->longitude) && isset($annotation->thing->latitude))
		{
			$feature->geometry->coordinates = array($annotation->thing->longitude, $annotation->thing->latitude);
		}

		$feature->properties = new stdclass;
		
		if (isset($annotation->thing->name))
		{
			$feature->properties->name = $annotation->thing->name;
		}		
		
		if (isset($annotation->thing->wikidata_id))
		{
			$feature->properties->wikidata_id = $annotation->thing->wikidata_id;
		}
		
		if (isset($annotation->thing->country_code))
		{
			$feature->properties->country_code = $annotation->thing->country_code;
		}					

		if (isset($annotation->thing->geonames_id))
		{
			$feature->properties->geonames_id = $annotation->thing->geonames_id;
		}
		
	
		if (isset($annotation->thing->osm_id))
		{
			$feature->properties->osm_id = $annotation->thing->osm_id;
		}
	
		$geojson->features[] = $feature;
	}

	return $geojson;
}

$post = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$post = file_get_contents('php://input');
		
	$text = $_POST['text'];

	// load serialize object
	$filename = 'trie.dat';
	$data = file_get_contents($filename);
	$trie = unserialize($data);
	
	$annotations = $trie->flash($text);

	$geo = annotations_to_geojson ($annotations);

	header("Content-type: application/json");
	echo json_encode($geo, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
else
{
?>

<html>
<head>
	<meta charset="utf-8">
	<style>
	
	body {
		padding:2em;
		font-family:sans-serif;
	}
	
	textarea {
		font-size:1em;
		padding:1em;
		line-height:1.5em;
		border:1px solid #DDD;
		color: #444;
		border-radius:0.5em;
	}
	
	button {
		font-size:1em;
		padding:1em;
		width:100%;
		margin: 1em 0 1em 0;
		border-radius:0.5em;
		border:1px solid #DDD;
	}
	
	#map {
		width:100%;
		height:50%;	
		border-radius:1em;
	}
	
	
		.mydivicon{
		width: 12px
		height: 12px;
		border-radius: 10px;
		background: #408000; 
		border: 1px solid #fff;
		opacity: 0.85
	}	  
	</style>
	
    <!-- leaflet -->
	<link rel="stylesheet" href="leaflet-0.7.3/leaflet.css" />
	<script src="leaflet-0.7.3/leaflet.js" type="text/javascript"></script>

	<script>
		var map;
		var geojson = null;

		// http://gis.stackexchange.com/a/116193
		// http://jsfiddle.net/GFarkas/qzdr2w73/4/
		var icon = new L.divIcon({className: 'mydivicon'});		
	
		//--------------------------------------------------------------------------------
		function onEachFeature(feature, layer) {
			if (feature.properties && feature.properties.name) {
				//console.log(feature.properties.popupContent);
				// content must be a string, see http://stackoverflow.com/a/22476287
				layer.bindPopup(String(feature.properties.name));
			}
		}	
			
		//--------------------------------------------------------------------------------
		function create_map() {
			map = new L.Map('map');

			// create the tile layer with correct attribution
			var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
			var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
			var osm = new L.TileLayer(osmUrl, {minZoom: 1, maxZoom: 12, attribution: osmAttrib});		

			map.setView(new L.LatLng(0, 0),4);
			map.addLayer(osm);		
		}
		
		//--------------------------------------------------------------------------------
		function clear_map() {
			if (geojson) {
				map.removeLayer(geojson);
			}
		}	
	
		//--------------------------------------------------------------------------------
		function add_data(data) {
			clear_map();
		
			geojson = L.geoJson(data, { 

			pointToLayer: function (feature, latlng) {
                return L.marker(latlng, {
                    icon: icon});
            },			
			style: function (feature) {
				return feature.properties && feature.properties.style;
			},
			onEachFeature: onEachFeature,
			}).addTo(map);
			
			// Open popups on hover
  			geojson.on('mouseover', function (e) {
    			e.layer.openPopup();
  			});
		
			if (data.type) {
				if (data.type == 'Polygon') {
					for (var i in data.coordinates) {
					  minx = 180;
					  miny = 90;
					  maxx = -180;
					  maxy = -90;
				  
					  for (var j in data.coordinates[i]) {
						minx = Math.min(minx, data.coordinates[i][j][0]);
						miny = Math.min(miny, data.coordinates[i][j][1]);
						maxx = Math.max(maxx, data.coordinates[i][j][0]);
						maxy = Math.max(maxy, data.coordinates[i][j][1]);
					  }
					}
					
					bounds = L.latLngBounds(L.latLng(miny,minx), L.latLng(maxy,maxx));
					map.fitBounds(bounds);
				}
				if (data.type == 'MultiPoint') {
					minx = 180;
					miny = 90;
					maxx = -180;
					maxy = -90;				
					for (var i in data.coordinates) {
						minx = Math.min(minx, data.coordinates[i][0]);
						miny = Math.min(miny, data.coordinates[i][1]);
						maxx = Math.max(maxx, data.coordinates[i][0]);
						maxy = Math.max(maxy, data.coordinates[i][1]);
					}
					
					bounds = L.latLngBounds(L.latLng(miny,minx), L.latLng(maxy,maxx));
					map.fitBounds(bounds);
				}
				if (data.type == 'FeatureCollection') {
					minx = 180;
					miny = 90;
					maxx = -180;
					maxy = -90;				
					for (var i in data.features) {
						//console.log(JSON.stringify(data.features[i]));
					
						minx = Math.min(minx, data.features[i].geometry.coordinates[0]);
						miny = Math.min(miny, data.features[i].geometry.coordinates[1]);
						maxx = Math.max(maxx, data.features[i].geometry.coordinates[0]);
						maxy = Math.max(maxy, data.features[i].geometry.coordinates[1]);
						
					}
					
					bounds = L.latLngBounds(L.latLng(miny,minx), L.latLng(maxy,maxx));
					map.fitBounds(bounds);
				}
			}		    					
		}
	</script>
	
</head>
<body>
	<h1>Glasgow Geoparser</h1>
	
	<p>A simple <a href="https://en.wikipedia.org/wiki/Toponym_resolution">geoparser</a>, source code <a href="https://github.com/rdmpage/glasgow-geoparser">on GitHub</a>. 
	Identifies geographic entities at the level of countries, provinces, and major islands. </p>
	
		<textarea id="text" name="text" rows="10" style="width:100%">Species of Symphurus (Pleuronectiformes: Cynoglossidae) are relatively small-sized tonguefishes occurring worldwide in tropical, subtropical, and warm-temperate seas. In the Indo-West Pacific Ocean, species of Symphurus inhabiting waters shallower than 200 m are rarely reported; only five have been described, S. microrhynchus (Weber, 1913), S. holothuriae Chabanaud, 1948, S. monostigmus Munroe, 2006, S. leucochilus Lee et al. 2014, and S. longirostris Lee et al. 2016. Examination of museum and recently collected specimens yielded over 100+ Symphurus captured in relatively shallow waters off Japan, Papua New Guinea, the Philippines, Taiwan, and Vietnam. All of these specimens, except S. monostigmus (with 14 caudal-fin rays), were originally tentatively identified as S. microrhynchus because of shared similarities (small size, low meristic values, 12 caudal-fin rays, shared pigmentation traits). Detailed comparisons revealed that, although similar, specimens from allopatric locations have small differences in meristic, morphometric and pigmentation features. In previous literature, these small differences were thought to represent intraspecific variation among populations of a widespread species, S. microrhynchus. However, further study, including molecular data, has revealed that such minor differences among specimens from allopatric locations actually represent interspecific, and not population-level, variations. Where available, molecular differences among these allopatric populations, in contrast to the morphological features, were significantly different (9.0 to 26.3%), providing additional strong support for the hypothesis that more than one species is represented among fishes examined. Combined data from morphological and molecular characters, and species delimitation analysis, reveal that five, undescribed, cryptic species should be recognized: S. brachycephalus n. sp. from Vietnam, S. hongae n. sp. from Taiwan, S. leptosomus n. sp. from the Philippines, S. polylepis n. sp. from Papua New Guinea, and S. robustus n. sp. from Japan. Also, based on new information, the previous decision to place S. holothuriae Chabanaud in the synonymy of S. microrhynchus was determined to be premature. This species should be recognized as valid until additional specimens are captured and the taxonomic status of this nominal species re-evaluated. At least 10 species of Indo-West Pacific shallow-water Symphurus are now known. Eight are members of the Symphurus microrhynchus species complex with hypothesized closer relationship to each other than to the other two species of shallow-water tonguefishes. Included in this study are redescriptions of S. microrhynchus and S. holothuriae based on their holotypes, including an expanded number of morphological characters not previously used to diagnose these species; redescriptions are also provided for comparative purposes of three other shallow-water species; five new cryptic species are described; and lastly, detailed comparisons and an identification key to all 10 species of shallow-water Symphurus occurring in the Indo-West Pacific Ocean are provided. Two additional populations are also identified that likely represent other undescribed taxa belonging to the S. microrhynchus species complex. Adequate specimens are not available at this time to formally describe these nominal species. This study contributes further understanding about species diversity within Symphurus inhabiting shallow waters of the Indo-West Pacific Ocean. Several other nominal species of small-sized cynoglossid and soleid flatfishes are currently considered to have widespread distributions in the Indo-West Pacific. Many of these species also have junior synonyms available based on nominal species described from allopatric sites within their geographic ranges. How many of these presumed populations of widespread species will be resurrected from synonymy once additional specimens and their genetic information becomes available remains an interesting question for future study.		
		</textarea>
	<button onclick="go()">Go</button>
	
	<div id="output"></div>
	
	<div id="map"></div>	
	
<script>
	create_map();


		function go() {
			var output = document.getElementById("output");
			output.style.display = "none";
			
			var text = document.getElementById("text").value;
			
			var url = "index.php";
			
			var parameters = 'text=' + encodeURIComponent(text);
			
			fetch(url, {
				method: "post",
				body: parameters,
				headers: { "Content-Type": "application/x-www-form-urlencoded"}
			}).then(
					function(response){
						if (response.status != 200) {
							console.log("Looks like there was a problem. Status Code: " + response.status);
        					return;
						}
						response.json().then(function(data) {
						
							//output.innerHTML = JSON.stringify(data, null, 2);							
							//output.style.display = "block";
							
							add_data(data);
							
					});
				}
			);
		}	
						

</script>	
	
</body>
</html>


<?php
}
?>
