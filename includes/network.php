<?php

require_once PROJECT_WEB.'/connection.php';
echo "<h2>Network equipments</h2> <br><br>";

if (isset($_GET['splicebox_id']))
{
	
	$splicebox_id=mysqli_real_escape_string($mon3,$_GET['splicebox_id']);
	
	if(isset($_POST['savesplicebox']))
	{
		
        $coverage_polygon_id=$_POST['coverage_polygon_id'];
        $date_install=$_POST['date_install'];
        $name=$_POST['namen'];
        $description=$_POST['description'];
        $type=$_POST['type'];
        $network_type=$_POST['network_type'];
        $zone=$_POST['zone'];
        $install_type=$_POST['install_type'];
        $estado=$_POST['estado'];
        $armario=$_POST['armario'];
        $cv_pole_id	=$_POST['cv_pole_id'];
        $homes_covered=$_POST['homes_covered'];
        $homes_capacity	=$_POST['homes_capacity'];
        $homes_connected=$_POST['homes_connected'];
        $notes	=$_POST['notes'];
		
        //		var_dump($_POST);

            $qgis->query("update juntas SET coverage_polygon_id=\"$coverage_polygon_id\", name=\"$name\", description=\"$description\", type=\"$type\", network_type=\"$network_type\", zone=\"$zone\", install_type=\"$install_type\", estado=\"$estado\", date_install=\"$date_install\", armario=\"$armario\",cv_pole_id=\"$cv_pole_id\",homes_covered=\"$homes_covered\",homes_capacity=\"$homes_capacity\",homes_connected=\"$homes_connected\",notes=\"$notes\" where OGR_FID=$splicebox_id ;");

            echo "mysql:". $qgis->error;
		
		
		
		
		
		
		echo "<font color=green>saved</font>";
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	$splicebox=$qgis->query("select ST_AsText(SHAPE), `coverage_polygon_id`, `name`, `description`, `type`, `network_type`, `zone`, `install_type`, `estado`, `date_planned`, `date_install`, `armario`, `cv_pole_id`, `homes_covered`, `homes_capacity`, `homes_connected`, `morada`, `projeto`, `notes`

	from juntas where OGR_FID=$splicebox_id")->fetch_assoc();
	
	echo "
	<table><tr><td width=500px>
	<form name=spliceboxedit method=post>
	<table><tr><td colspan=2> <h3>Splicebox id: <b> $splicebox_id </b></h3>
	
	<tr><td>coverage polygon<td><select name=coverage_polygon_id>";
	
	if($splicebox['coverage_polygon_id']=="" || $splicebox['coverage_polygon_id']=="0")
		echo "<option selected> </option>";
	
$shpl=$qgis->query("select OGR_FID from grupos");	
 while($polid=$shpl->fetch_assoc())
 {		
	echo "<option ";
	if($polid['OGR_FID']==$splicebox['coverage_polygon_id']) echo " selected";
	echo ">".$polid['OGR_FID']."</option>";
	
 }
	echo "</select>";
	
	echo "<tr><td>date_install<td><input type=text name=date_install value=\"".$splicebox['date_install']."\">
	<tr><td>name<td><input type=text name=namen value=\"".$splicebox["name"]."\">
	<tr><td>description<td><input type=text name='name' value=\"".$splicebox['description']."\">
	<tr><td>type<td><input type=text name=type value=\"".$splicebox['type']."\">";



echo "<tr><td>network_type<td><select name=network_type >";
$options=$qgis->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA ='QGIS' AND TABLE_NAME = 'juntas'  AND COLUMN_NAME = 'network_type';")->fetch_assoc();	
$options=explode(",",substr($options['COLUMN_TYPE'],5));
foreach($options as $option){	
$option= str_replace(array("'",")"),"",$option);
echo "<option ";
if($option==$splicebox['network_type']) echo " selected";
echo ">". $option."</option>";
}	
echo 	"</select>";	
	
echo "<tr><td>zone<td><select name=zone >";
$options=$qgis->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA ='QGIS' AND TABLE_NAME = 'juntas'  AND COLUMN_NAME = 'zone';")->fetch_assoc();	
$options=explode(",",substr($options['COLUMN_TYPE'],5));
foreach($options as $option){	
$option= str_replace(array("'",")"),"",$option);
echo "<option ";
if($option==$splicebox['zone']) echo " selected";
echo ">". $option."</option>";
}	
echo 	"</select>";	


echo "<tr><td>install_type<td><select name=install_type >";
$options=$qgis->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA ='QGIS' AND TABLE_NAME = 'juntas'  AND COLUMN_NAME = 'install_type';")->fetch_assoc();	
$options=explode(",",substr($options['COLUMN_TYPE'],5));
foreach($options as $option){	
$option= str_replace(array("'",")"),"",$option);
echo "<option ";
if($option==$splicebox['install_type']) echo " selected";
echo ">". $option."</option>";
}	
echo 	"</select>";	
	

echo "<tr><td>status<td><select name=estado >";
$options=$qgis->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA ='QGIS' AND TABLE_NAME = 'juntas'  AND COLUMN_NAME = 'estado';")->fetch_assoc();	
$options=explode(",",substr($options['COLUMN_TYPE'],5));
foreach($options as $option){	
$option= str_replace(array("'",")"),"",$option);
echo "<option ";
if($option==$splicebox['estado']) echo " selected";
echo ">". $option."</option>";
}	
echo 	"</select>";	



echo	"
<tr><td>armario<td><input type=text name=armario value=\"".$splicebox['armario']."\">
<tr><td>cv_pole_id<td><input type=text name=cv_pole_id value=\"".$splicebox['cv_pole_id']."\">
<tr><td>homes_covered<td><input type=text name=homes_covered value=\"".$splicebox['homes_covered']."\">
<tr><td>homes_capacity<td><input type=text name=homes_capacity value=\"".$splicebox['homes_capacity']."\">
<tr><td>homes_connected<td><input type=text name=homes_connected value=\"".$splicebox['homes_connected']."\">

<tr><td>notes<td><textarea name=notes>".$splicebox['notes']."</textarea>
<tr><td><td> <input type=submit value=save name=savesplicebox>

	
	</table>
	</form>
	
<td>




";


$coord=explode(" ",str_replace(")","",substr($splicebox['ST_AsText(SHAPE)'],6)));
$coordlat=trim($coord[1]);
$coordlng=trim($coord[0]);

if($splicebox['coverage_polygon_id']!="" && $splicebox['coverage_polygon_id']!="0"){
$shpl=$qgis->query("select OGR_FID,ST_AsText(SHAPE) from grupos where OGR_FID=\"".$splicebox['coverage_polygon_id']."\"")->fetch_assoc();	

 $polyg=substr($shpl['ST_AsText(SHAPE)'],9);
 $polyg=str_replace(")","",$polyg);
 $coords=explode(",",$polyg);

}

echo "<div id=\"map\" >

</div>

    <script>
// Initialize and add the map
function initMap() {
	
       var imgf = 'img/red_12px.png';
		var imgdf = 'img/black_12px.png';
		var imgc = 'img/blue_12px.png';
		var imgi = 'img/orange_12px.png';
		var imgl = 'img/yellow_12px.png';
		var imgqp = 'img/qpink_12px.png';
		var imgqg = 'img/qgreen_12px.png';	
	
	
	
	
	
  // The location of Uluru
  var uluru = {lat: ".$coordlat.", lng:".$coordlng."};
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 18, center: uluru, mapTypeId: 'satellite'});
  // The marker, positioned at Uluru
  var marker = new google.maps.Marker({position: uluru,icon: imgqg, map: map});";
  
if($splicebox['coverage_polygon_id']!="" && $splicebox['coverage_polygon_id']!="0"){  
echo "var cov".$shpl['OGR_FID']." = [ ";
$i=0; 
foreach($coords as $coord)
{
$coord=explode(" ",$coord);	
if($i>0) echo ",";
echo "{lng: ".$coord[0].", lat: ".$coord[1]."}"; 

$i=1;
}	

echo "]; 
        var poly".$shpl['OGR_FID']." = new google.maps.Polygon({
          paths: cov".$shpl['OGR_FID']." ,
          strokeColor: '#00ff00',
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: '#00ff00',
		  title: \"cov_id:".$shpl['OGR_FID']."\",
		  url: \"index.php?network=1&cov_id=".$shpl['OGR_FID']."\",
		  ZIndex: 2,
          fillOpacity: 0.15
        });
        poly".$shpl['OGR_FID'].".setMap(map);
		 google.maps.event.addListener(poly".$shpl['OGR_FID'].", 'click', function (event) {
        //alert the index of the polygon
        alert(\"polygon id: ".$shpl['OGR_FID']."\");
    });
 
 ";
}
echo" 
  
  
  
  
  
  
  
}
    </script>

    <script async defer
    src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBID5Z_Iuv6A2xX7cfvnDgJyJ1PCH31TQc&callback=initMap\">
    </script>
	<a href=https://www.google.com/maps/search/?api=1&query=".$coordlat.",".$coordlng." target=_blank>open in maps</a>
	
	









</table>	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	";
	
	
	
	
	
	
	
	

	
	
	
}
else
{
	
	echo "network map here";
	
}