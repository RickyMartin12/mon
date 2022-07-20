<?php
/*
todo
search by fsan and id on the list fibre







needs: 
-lib-phpmailer

*/

session_start();


require 'connection.php';

include 'includes/functions.php';


?>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="css.css">
<title>Lazer technical system</title>
   <style>
      #map {
        height: 400px;  /* The height is 400 pixels */
        width: 400px;  /* The width is the width of the web page */
       }
	   #mapl {
        height: 400px;  /* The height is 400 pixels */
        width: 1000px;  /* The width is the width of the web page */
       }
	   
	   
	   #mapcov {
        height: 1300px;  /* The height is 400 pixels */
        width: 900px;  /* The width is the width of the web page */
       }
    </style>
	


<script type=text/javascript src='https://code.jquery.com/jquery-3.1.1.min.js'></script>
<script src='js/select2.min.js' type='text/javascript'></script>
<link href='js/select2.min.css' rel='stylesheet' type='text/css'>
<script src="js/moment.js"></script>
<script src="js/Chart.js"></script>
<script src="js/functions.js"></script>


<script type=text/javascript> 

function searchprop(str)
{ 
      $.ajax({ method: "GET", url: "webservice.php", data: {'searchprop': str }})

        .done(function( data ) 
		{ 
          var result = $.parseJSON(data); 
          var string = "<table><tr> <th>ref</th><th>address</th><tr>";
		  var total= result[0]['ref'];
		  console.log(total);
		  var i=0;
        $.each( result, function( key, value )
		{
			if(i!=0)
			{
             string += "<tr><td><a href=?props=1&propid="+ value['id'] +">  " + value['ref'] + "</a> </td><td>"
			 + value['address'] + "</td></tr>";
			}
			i=1;
		});
        string += '</table>'; 
        document.getElementById("tablec").innerHTML = string; 
		document.getElementById("paging").innerHTML = "";



       }); 
}

function searchlead(str)
{ 
      $.ajax({ method: "GET", url: "webservice.php", data: {'searchlead': str }})
    
        .done(function( data ) 
		{ 
          var result = $.parseJSON(data); 
          var string = "<table><tr> <th>id</th><th>address</th><th>name</th>";
		  var total= result[0]['address'];
		  console.log(total);
		  var i=0;
        $.each( result, function( key, value ) 
		{ 
			if(i!=0)
			{
             string += "<tr><td><a href=?lead_id="+ value['id'] +">" +value['id'] +" </a></td><td> " + value['address'] + "</a> </td><td>"
			 + value['name'] + "</td></tr>";
			}
			i=1;
		}); 
        string += '</table>'; 
        document.getElementById("tablec").innerHTML = string; 
		document.getElementById("paging").innerHTML = "";
		
		
		
       }); 
}

function updatefregep(conc)
{
    //console.log(conc);
      $.ajax({ method: "GET", url: "webservice.php", data: {'getfreg': 1 , 'conc': conc}})
    
        .done(function( data ) 
		{
            //console.log(data);
          var result = $.parseJSON(data); 
		  
		  // console.log(result[0]);
		  
		  	var frega=document.getElementById("freg");
			frega.innerHTML = "";
/*			
		  frega.options[frega.selectedIndex].text;
		  
          var string = "<table><tr> <th>ref</th><th>address</th><tr>";
		  var total= result[0]['ref'];
		  console.log(total); */
        var i=0;
		var fregb;
        $.each( result, function( key, value ) 
		{ 
//			console.log(value['freguesia']);
			fregb = new Option(value['freguesia'], value['id']);
            frega.options.add(fregb);
		}); 

		
		
		
       }); 
}

function updateconcelhosep(country)
{
    $.ajax({ method: "GET", url: "webservice.php", data: {'getconcelho': 1 , 'country': country}})

        .done(function( data )
        {
            //console.log(data);
            var result = $.parseJSON(data);
            console.log(result);


            //CONCELHOS
            var concelho=document.getElementById("concelho");
            concelho.innerHTML = "";
            var concelho_b;
            //FREGUESIAS
            var frega=document.getElementById("freg");
            frega.innerHTML = "";
            var frega_b;

            $.each( result['concelhos'], function( key, value ) {
                var vl = value['distrito'] + ' - ' + value['concelho'];
                concelho_b = new Option(vl, value['id']);
                concelho.options.add(concelho_b);

            });

            $.each( result['freguesia'], function( key, value ) {
                var vl = value['freguesia'];
                freg_b = new Option(vl, value['concelho']);
                frega.options.add(freg_b);

            });
            /*var result = $.parseJSON(data);

            var concelho=document.getElementById("concelho");
            concelho.innerHTML = "";
            var concelho_b;
            $.each( result, function( key, value )
            {
                var vl = value['distrito'] + ' - ' + value['concelho'];
                concelho_b = new Option(vl, value['id']);
                concelho.options.add(concelho_b);

                var frega=document.getElementById("freg");
                frega.innerHTML = "";

                var fregb;
                fregb = new Option(value['freguesia'], value['id']);
                frega.options.add(fregb);
            });*/







        });
}

function status1() 
{
  document.getElementById('popup1').style.display = 'block';
}
function status1o()
{
  document.getElementById('popup1').style.display = 'none';
}


function gpslink(opt) {
	
	var url;
	var coord;
	var address;
	var frega;
	frega=document.getElementById("freg");
	url="gps.php?coord=";
	coord=document.getElementById("coord").value.replace(/ /g,'');
	address=document.getElementById("address").value.replace(/ /g, "+");
	address += "," + frega.options[frega.selectedIndex].text;
//	alert("updated gps" + url + coord + "&address=" + address);
	window.open(url + coord + "&address=" + address + "&mode=" + opt, 'Pagina', 'STATUS=NO, TOOLBAR=NO, LOCATION=NO, DIRECTORIES=NO, RESISABLE=NO, SCROLLBARS=YES, TOP=10, LEFT=10, WIDTH=800px, HEIGHT=600px');
}	

</script> 


</head>
<body class="bground">

<div class="canvas">
<?php
    echo date("Y-m-d H:m:s")." - login as <b>". $localuser['username']."</b>";
?>
<div class="divlogo">
    <img width="200px" src="img/lazer_logo.png">
</div>
<br><br>
<a class=redlink href=index.php>Home</a> | 
<a class=redlink href=?props=1>Properties</a> 
<a class=redlink href=?servs=1>Services</a> | 
<a class=redlink href=?gpon=1>GPON</a> 
<a class=redlink href=?fwa=1>FWA</a> 
<a class=redlink href=?coax=1>COAX</a> |
<a class=redlink href=?headend=1>Headend</a>  
 <a class=redlink href=?tickets=1>Tickets</a>  
<!--<a class=redlink href=?jobs=1>Jobs</a>  
<a class=redlink href=?stock=1>Stock</a>  -->
<a class=redlink href=?stats=1>Stats</a> | 
<a class=redlink href=?procedures=1>Procedures</a>
<br><br>








<?php


// function isset anything from get


if($_GET['propleads']==1)
{
	include 'includes/leads.php';
}

elseif($_GET['props']==1 )
{
	include 'includes/props.php';
}

elseif($_GET['custs']==1)
{
	include 'includes/custs.php';
}

elseif($_GET['servs']==1)
{
	include 'includes/services.php';
}


elseif($_GET['gpon']==1)
{
	include 'includes/gpon.php';
}

elseif($_GET['fwa']==1)
{
	include 'includes/fwa.php';
}

elseif($_GET['coax']==1)
{
	include 'includes/coax.php';
}


// Equipments monitoring
elseif($_GET['equip']!="" && $_GET['equip_type']!="" )
{

	include 'includes/equips.php';
}


elseif($_GET['headend']==1)
{
	include 'includes/headend.php';
}

elseif($_GET['network']==1)
{
	include 'includes/network.php';
}





elseif($_GET['tickets']==1)
{
	include 'includes/tickets.php';
}
elseif($_GET['jobs']==1)
{
	include 'includes/jobs.php';
}
elseif($_GET['stock']==1)
{
	include 'includes/stock.php';
}
elseif($_GET['stats']==1)
{
	include 'includes/stats.php';

}
elseif($_GET['procedures'])
{
	include 'includes/procedures.php';

}





else
{
if($_POST['upd_tech'])
{
	$oncall=$mon3->query("update settings set valor=\"".$_POST['techcall']."\" where nome=\"tech_oncall\" ");

}
if($_POST['upd_noc'])
{
	$oncall=$mon3->query("update settings set valor=\"".$_POST['noc_agent']."\" where nome=\"noc_agent\" ");

}



?>
<form action=index.php method=post>
forward notifications to the tech on call: <select name=techcall>
<?php 
$techs=$mon3->query("select username,telf from users where is_tech=1");
$oncall=$mon3->query("select valor from settings where nome=\"tech_oncall\" ")->fetch_assoc();
while($tech=$techs->fetch_assoc())
{echo "<option ";
if($oncall['valor']==$tech['username']) echo " selected ";
echo " value=\"".$tech['username']."\">".$tech['username']."-".$tech['telf']."</option>";
}
?>
</select>
<input type=submit name=upd_tech value=save>
</form>



<form action=index.php method=post>
forward notifications to the NOC agent: <select name=noc_agent>
<?php 
$techs=$mon3->query("select username,telf from users where is_helpdesk=1");
$oncall=$mon3->query("select valor from settings where nome=\"noc_agent\" ")->fetch_assoc();
while($tech=$techs->fetch_assoc())
{echo "<option ";
if($oncall['valor']==$tech['username']) echo " selected ";
echo " value=\"".$tech['username']."\">".$tech['username']."-".$tech['telf']."</option>";
}
?>
</select>
<input type=submit name=upd_noc value=save>
</form>






<?php
/*
 * Ficheiro logs do mon de accesso não está no ficheiro - Comentei OK :)
 *
 *
exec('tail -100 /var/www/html/mon/log.txt', $output);
echo "<h2>last MON logs:</h2> 
    <table>
    <tr>
        <td id=logt height=150px width=900px style=\"display:block; overflow:auto;\">
        <div>".implode("<br>",$output)."</div>
    </table>";
*/
echo" <script>
var objDiv = document.getElementById(\"logt\");
objDiv.scrollTop = objDiv.scrollHeight;
</script>";




?>
<h2>Temperature</h2>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1492&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1492&rra_id=0&view_type=tree></a>


<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1038&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1038&rra_id=0&view_type=tree></a>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1039&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1039&rra_id=0&view_type=tree></a>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1494&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1494&rra_id=0&view_type=tree></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1519&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1519&rra_id=0&view_type=tree></a>

<h2>Internet feeds</h2>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=29&rra_id=all ><img width=450px src= https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=29&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1496&rra_id=all ><img width=450px src= https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1496&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=636&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=636&rra_id=0&view_type=tree></a>


<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=214&rra_id=all ><img width=450px src= https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=214&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=953&rra_id=all ><img width=450px src= https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=953&rra_id=0&view_type=tree ></a>










<br><br>
<h2>OLTs</h2>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=5&rra_id=all ><img width=450px src= https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=5&rra_id=0&view_type=tree></a>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=640&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=640&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=623&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=623&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1209&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1209&rra_id=0&view_type=tree ></a>



<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=637&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=637&rra_id=0&view_type=tree ></a>



<h2>CMTSs</h2>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=40&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=40&rra_id=0&view_type=tree ></a>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=41&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=41&rra_id=0&view_type=tree ></a>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=621&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=621&rra_id=0&view_type=tree ></a>


<h2>VPN UK</h2>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1513&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1513&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=733&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=733&rra_id=0&view_type=tree ></a>

<h2>FWA</h2>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1571&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1571&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1570&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1570&rra_id=0&view_type=tree ></a>


<h2>Links</h2>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=46&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=46&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=108&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=108&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=597&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=597&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=889&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=889&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=117&rra_id=all><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=117&rra_id=0&view_type=tree ></a>





<a href= ><img src= ></a>

<h2>TV equip</h2>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=781&rra_id=all ><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=781&rra_id=0&view_type=tree ></a>





<h2>DIAs</h2>
<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=599&rra_id=all><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=599&rra_id=0&view_type=tree ></a>


<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=249&rra_id=all><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=249&rra_id=0&view_type=tree ></a>


<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=250&rra_id=all><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=250&rra_id=0&view_type=tree ></a>


<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=251&rra_id=all><img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=251&rra_id=0&view_type=tree ></a>


<br><br>


<h2>DNS Servers</h2>



<br>


<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1707&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1707&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1709&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1709&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1485&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1485&rra_id=0&view_type=tree ></a>


<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1698&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1698&rra_id=0&view_type=tree ></a>










<br><br>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1035&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1035&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1036&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1036&rra_id=0&view_type=tree ></a>


<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1486&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1486&rra_id=0&view_type=tree ></a>


<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1713&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1713&rra_id=0&view_type=tree ></a>











<h2>Internet experience</h2>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1493&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1493&rra_id=0&view_type=tree ></a>


<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1475&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1475&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1491&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1491&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1490&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1490&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1479&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1479&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1489&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1489&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1478&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1478&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1476&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1476&rra_id=0&view_type=tree ></a>

<a href=https://mon.lazertelecom.com/cacti/graph.php?action=view&local_graph_id=1477&rra_id=all>
<img width=450px src=https://mon.lazertelecom.com/cacti/graph_image.php?local_graph_id=1477&rra_id=0&view_type=tree ></a>















<?php
}


mysqli_close($mon3);
?>


</div>
</body>
</html>
