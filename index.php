<?php
require_once("Database.php");

$db = new Database("mysql:host=localhost;dbname=qgis",'root','');
$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
//            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
$db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
$db->exec("set names utf8mb4");

// $d = $db->prepare('SELECT ST_AsGeoJson(SHAPE) FROM qgis LIMIT 1 ');

// $d->execute();

// $res = $d->fetch();

// $data = [];
// foreach($res as $k){
//   $data[] = json_decode($k, true);
// }

// header('Content-type: application/json;');
// echo json_encode($data);

$d = $db->prepare('SELECT OGR_FID id, name nome, ST_AsGeoJson(SHAPE) shape FROM sedes_municipais');

$d->execute();

$res = $d->fetchAll();

// var_dump($res);
// die;

$data = [];
foreach($res as $k){
  $data[] = [
    "label" => $k->nome,
    "data" => json_decode($k->shape, true)
  ];
}
?>

<html>
  <body>
  <div id="mapdiv"></div>
  <script src="http://www.openlayers.org/api/OpenLayers.js"></script>
  <script type="text/javascript" src="http://maplib.khtml.org/khtml.maplib/khtml_all.js"> </script>
  <script>

    var coords = <?= json_encode($data); ?>;

    map = new OpenLayers.Map("mapdiv");
    map.addLayer(new OpenLayers.Layer.OSM());

    var lonLat = new OpenLayers.LonLat(-48.395249, -1.348357)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
          
    var zoom=16;

    var markers = new OpenLayers.Layer.Markers( "Markers" );
    map.addLayer(markers);
    
    markers.addMarker(new OpenLayers.Marker(lonLat));
    
    map.setCenter (lonLat, zoom);







    var ol = new OpenLayers.Layer.OSM();

    var start_point = new OpenLayers.Geometry.Point(-48.395249, -1.348357);
    var end_point = new OpenLayers.Geometry.Point(-48.39580, -1.348362);
    
    var vector = new OpenLayers.Layer.Vector();
    vector.addFeatures([
      new OpenLayers.Feature.Vector(
        new OpenLayers.Geometry.LineString([start_point, end_point]).transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913")),
        null, 
        {
          strokeColor: "#00FF00",
          strokeWidth: 5,
          strokeDashstyle: "dashdot",
        }
      )
    ]);
    map.addLayers([ol,vector]);


    var vector2 = new OpenLayers.Layer.Vector();
    a = 
      [
        {
            "lng": 106.972534,
            "lat": -6.147714
        },
        {
            "lng": 106.972519,
            "lat": -6.133398
        },
        {
            "lng": 106.972496,
            "lat": -6.105892
        }
      ];
    var ring = [
      [a[0].lng, a[0].lat], [a[1].lng, a[1].lat],
      [a[2].lng, a[2].lat], [a[0].lng, a[0].lat]
    ];
    var polygon = new OpenLayers.Geometry.Polygon([ring]);
    polygon.transform('EPSG:4326', 'EPSG:3857');

        // Create feature with polygon.
    var feature = new OpenLayers.Feature(polygon);

    vector2.addFeatures([feature])

    // Add the vector layer to the map.
    map.addLayer(vector2);
  </script>
  </body>
</html>
