<?php

require_once "lib/EasyRdf.php";

// deklarasi prefix DBpedia yang diperlukan
EasyRdf_Namespace::set('dbp', 'http://dbpedia.org/property/');
EasyRdf_Namespace::set('dbr', 'http://dbpedia.org/resource/');
EasyRdf_Namespace::set('dbo', 'http://dbpedia.org/ontology/');
EasyRdf_Namespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
EasyRdf_Namespace::set('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');

// set DBpedia sparql endpoint
$sparql = new EasyRdf_Sparql_Client('http://dbpedia.org/sparql');

?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Country Description</title>
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
	<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
	<link rel="stylesheet" href="assets/css/tubes.css">

</head>

<body>
<nav class="navbar navbar-light bg-light">
  <a class="navbar-brand" href="welcome.php">MAP SPARQL</a>
  <form action="search.php" method="post" class="form-inline">
        <div class="form-group mx-sm-3 mb-2 test">
                <input class="form-control mr-sm-2" type="text" name="lokasi" placeholder="Masukkan Nama" class="form-control">
                <input class="btn btn-outline-success my-2 my-sm-0" type="submit" name="submit" value="Cari" class="btn btn-info mb-2">
        </div>
        
</form>
</nav>

	<div class="container">
<br><br>

<div>

<?php

if(isset($_POST['lokasi']))
{
        $result = $sparql->query(
                'SELECT distinct  ?lat ?long ?situs ?namalokasi ?abstrak WHERE {   '.
                ' ?a dbo:architecturalStyle dbr:Neoclassical_architecture.'.
				' ?a dbo:location ?lokasi.   '.
                ' ?a foaf:name ?situs.  '.
                ' ?a dbo:abstract ?abstrak.'.
                // ' ?lokasi geo:lat ?lat.   '.
                // ' ?lokasi geo:long ?long.  '.
             	' ?lokasi foaf:name ?namalokasi. '.
				' FILTER regex(?namalokasi,"'.str_replace(' ', '_', ucwords($_POST['lokasi'])).'")'.
				'FILTER (lang(?namalokasi) = "en"). '.
				'FILTER (lang(?situs) = "en"). '.
				'FILTER (lang(?abstrak) = "en")'.
                '}LIMIT 10' 
            );
foreach ($result as $row) {
			
        echo "<hr><a href='detail.php?situs=$row->situs' style='color:white'><table>";
				// echo "<hr><a href style='color:white' data-toggle='modal' data-target='#myModal' data-id='".$row->situs."'><table>";
				echo "<tr>";
                echo "<td><h2><b>". $row->situs. "</b></h2></td>" ;
				echo "<td><small>". $row->namalokasi . "</color></small></td></tr>";
                echo "<tr>";
				echo "<td colspan=2><small>". $row->abstrak . "</small></td>";
				echo "</tr>";
				echo "</table></a>";
				echo "</p><hr>";
				
        }
}
       
?>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <?php
		$batu = $sparql->query(
                'SELECT distinct  ?lat ?long ?situs ?namalokasi ?abstrak WHERE {   '.
                ' ?a dbo:architecturalStyle dbr:Neoclassical_architecture.'.
				' ?a dbo:location ?lokasi.   '.
                ' ?a foaf:name ?situs.  '.
                ' ?a dbo:abstract ?abstrak.'.
                ' ?lokasi geo:lat ?lat.   '.
                ' ?lokasi geo:long ?long.  '.
             	' ?lokasi foaf:name ?namalokasi. '.
				' FILTER regex(?situs,"'.str_replace(' ', '_', ucwords($_POST['situs'])).'")'.
				'FILTER (lang(?namalokasi) = "en"). '.
				'FILTER (lang(?situs) = "en"). '.
				'FILTER (lang(?abstrak) = "en")'.
                '}LIMIT 1' 
            );

      		foreach ($batu as $hai) {?>
        <h4 style="color:black" class="modal-title"><?php echo $hai->situs ?></h4>
      </div>
      <div class="modal-body">
      	<p style="color:black">

      		<?php
      		 echo $hai->abstrak;
      		
      		}
      		 ?>
      	</p>
        <p><div id="map" class="map map-home" style="height: 200px; margin-top: 50px"></div></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script>
var lat = <?php foreach ($result as $row) { echo $row->lat; }?>;
var long = <?php foreach ($result as $row) { echo $row->long; }?>;
var map = L.map('map').setView([lat, long], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

L.marker([lat, long]).addTo(map)
    .bindPopup("Lat : "+lat+" , Long : "+long)
    .openPopup();
</script>
</body>
</html>
<!-- $data['buku'] = $this->BukuModel->single($id); -->