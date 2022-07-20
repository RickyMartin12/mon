<?php

//header 
echo " 
	<a href=?servs=1&type=INT><img src=img/internet.png title=\"internet access\"></a>
	<a href=?servs=1&type=INTonly><img src=img/internetonly.png title=\"internet only access\"></a>	
	<a href=?servs=1&type=DIA><img src=img/DIA.png title=\"Dedicated internet access\"></a>
	<a href=?servs=1&type=IPs><img src=img/IPF.png title=\"fixed IP address\"></a>
	<a href=?servs=1&type=WIF><img src=img/wifi.png title=\"wifi\"></a>
	<a href=?servs=1&type=VLN><img src=img/VLAN.png title=\"VLAN\"></a>
	<a href=?servs=1&type=PHN><img src=img/telephone.png title=\"Phone\"></a>
	<a href=?servs=1&type=TV><img src=img/tv.png title=\"TV\"></a>
	<a href=?servs=1&type=TVonly><img src=img/tvonly.png title=\"TVonly\"></a>	
	<a href=?servs=1&type=NOS><img src=img/cancel.png title=\"No services\"></a>
	

<h3>Services</h3><br>
";

	$sid=mysqli_real_escape_string($mon3, $_GET['sid']);

 






if($sid>0) // service details
{

	$servc=$mon3->query("select * from services where id=\"$sid\"")->fetch_assoc();
	$con=$mon3->query("select * from connections where id=\"".$servc['connection_id']."\"")->fetch_assoc();
	$propid=$con['property_id'];


if($_POST['deletesid']==1)
{
	
	

	if($servc['type']=="PHN")
	{
		$acc=$mon3->query("select value from service_attributes where service_id=\"$sid\" and name=\"account\"")->fetch_assoc();echo $mon3->error; echo "account: ".$acc['value'];
		$voip=$mon3->query("select caller_id from voip_accounts where username=\"".$acc['account']."\" ")->fetch_assoc();echo $mon3->error;
		$nrr=$mon3->query("update voip_numbers set in_use=0 where phone_number=\"".substr($voip['caller_id'],3)."\"");echo $mon3->error;
		$q=$mon3->query("delete from voip_accounts where username=\"".$acc['value']."\" ");
		$q=$mon3->query("update settings set valor=\"1\" where nome=\"voip_accounts_changed\" ");
	}
	
	
	
	$a=$mon3->query("delete from service_attributes where service_id=\"$sid\"");
	echo $mon3->error;
	$a=$mon3->query("delete from services where id=\"$sid\"");
	echo $mon3->error;
	proplog($propid,"deleted service $sid ".$servc['type']." ");
	echo "<font color=red>DELETED Service $sid</font>";
	
}
elseif($_POST['cancelsid']==1)
{
	echo "suspending..";
	$servc=$mon3->query("select * from services where id=\"$sid\"")->fetch_assoc();
	if($servc['type']=="PHN")
	{
		$acc=$mon3->query("select value from service_attributes where service_id=\"$sid\" and name=\"account\"")->fetch_assoc();echo $mon3->error; echo "account: ".$acc['value'];
		$q=$mon3->query("update voip_accounts set passw=\"L4zerc4nCell#d\" where username=\"".$acc['value']."\" ");
		$q=$mon3->query("update settings set valor=\"1\" where nome=\"voip_accounts_changed\" ");
	}
	$q=$mon3->query("update services set date_end=\"".date("Y-m-d")."\" where id=$sid ");
	echo $mon3->error;
	echo "<font color=red>Cancelled Service $sid </font>";
	
}
elseif($_POST['enablesid']==1)
{
	echo "reactivating..";
	if($servc['type']=="PHN")
	{
		$acc=$mon3->query("select value from service_attributes where service_id=\"$sid\" and name=\"account\"")->fetch_assoc();echo $mon3->error; echo "account: ".$acc['value'];
		$q=$mon3->query("update voip_accounts set passw=\"L4zerc4nCell#d\" where username=\"".$acc['value']."\" ");
		$q=$mon3->query("update settings set valor=\"1\" where nome=\"voip_accounts_changed\" ");
	}
	$q=$mon3->query("update services set date_end=\"0000-00-00\" where id=$sid ");
	echo $mon3->error;
	echo "<font color=green>Enabled Service $sid </font>";
	
}
elseif(isset($_POST['editserv']))
{


	$atts=$mon3->query("select * from service_attributes where service_id=$sid");
	while($att=$atts->fetch_assoc())
	{
		if($att['value']!=$_POST[$att['name']])
		{
			echo "prev:".$att['value']." ->".$_POST[$att['name']]."\n";
			$mon3->query("update service_attributes set value=\"".$_POST[$att['name']]."\" where service_id=$sid and name=\"".$att['name']."\" ");
			//update coax files
			if($con['type']=="COAX" && $att['name']=="speed")
			{
				$mon3->query("update settings set valor=\"1\" where nome=\"modems_changed\" ");
				$mon3->query("insert into reboot_schedule (type,serial,request_ts,reason) 
					values(\"COAX\",\"".$con['equip_id']."\",\"".time()."\",\"speed update\" ) ");
			}	
			proplog($propid,"update service_attributes set value=\"".$_POST[$att['name']]."\" where service_id=$sid and name=\"".$att['name']."\" ");
		}
	
	
	}
	if($_POST['newatt']!="")
	{
		$mon3->query("insert into service_attributes (service_id,name,value,date) values ($sid,\"".$_POST['newatt']."\",\"".$_POST['newattvalue']."\",\"".date("Y-m-d")."\") ");
		proplog($propid,"insert into service_attributes (service_id,name,value,date) values ($sid,\"".$_POST['newatt']."\",\"".$_POST['newattvalue']."\",\"".date("Y-m-d")."\") ");
		
	}

	echo "<font color=green> Saved </font>";
}
















$atts=$mon3->query("select * from service_attributes where service_id=$sid");
$serv=$mon3->query("select * from services where id=$sid")->fetch_assoc();
$conn=$mon3->query("select * from connections where id=\"".$serv['connection_id']."\"")->fetch_assoc();
$equip=0;
$prop=$mon3->query("select * from properties where id=\"".$conn['property_id']."\"")->fetch_assoc();
echo "<table><tr><td>Service id: <b>$sid </b> <td>

<form id=deleteserv method=post action=?servs=1&sid=$sid>
<input type=hidden name=sid value=$sid>
<input type=hidden name=deletesid value=1>

<a href=# onClick=\"if(confirm(`If the service is terminated, use the end date.\n Use delete only if service was inserted by mistake.\n Do you want to proceed?`))	{
		document.getElementById('deleteserv').submit();	}\">
		<img src=img/del.png width=40px></a>
		
</form>
<td>
";

if($serv['date_end']=="0000-00-00")
echo "
<form id=cancelserv method=post action=?servs=1&sid=$sid>
<input type=hidden name=sid value=$sid>
<input type=hidden name=cancelsid value=1>

<a href=# onClick=\"if(confirm(`this will cancel the service.\n Do you want to proceed?`))	{
		document.getElementById('cancelserv').submit();	}\">
		<img src=img/disabled.png width=40px></a>
		
</form>
";
else
echo "
<form id=enableserv method=post action=?servs=1&sid=$sid>
<input type=hidden name=sid value=$sid>
<input type=hidden name=enablesid value=1>

<a href=# onClick=\"if(confirm(`this will enable the service and remove the date end.\n Do you want to proceed?`))	{
		document.getElementById('enableserv').submit();	}\">
		<img src=img/check.png width=40px></a>
		
</form>
";


echo"
</table>

type: <b>".$serv['type']." </b> "; 
if($serv['date_end']!="0000-00-00") echo "<font color=red><b>Disabled </b></font> ";
echo "<br>";
echo "<br>Property id: <a href=?props=1&propid=".$prop['id'].">".$prop['ref']."</a><br> ".$prop['address']."  <br>";

echo "<br>Connection id: <a href=?props=1&conedit=".$conn['id']."> ".$conn['id']." - ".$conn['type']."</a><br>  <br>";
echo "<br>

<form name=servedit action=?servs=1&sid=$sid method=post><input type=hidden name=sid value=$sid>
<table width=800px>
<tr><td width=150px>Date start: <td> ".$serv['date_start']."
<tr><td>Date end: <td> ".$serv['date_end']."


";
	while($att=$atts->fetch_assoc())
	{
	
	
	
		//speed selection
		if($att['name']=="speed")
		{
		
			if($conn['type']=="COAX")
			{	
				$speeds=$mon3->query("select * from int_services where con_type=\"COAX\" order by id");
				echo " <tr><td>".$att['name'].": <td><select name=speed>";
			
			while($speedv=$speeds->fetch_assoc())
			{
			
				echo "<option value=".$speedv['id'];
					if($speedv['id']==$att['value']) 
						echo " selected ";
				echo"> ".$speedv['filename']." </option>";
			}
				
				
				
				
				
			}
			
			elseif($conn['type']=="GPON")
			{	
				$speeds=$mon3->query("select * from int_services where con_type=\"GPON\" order by prof_down");
				
			echo " <tr><td>".$att['name'].": <td><select name=speed>";
			
			while($speedv=$speeds->fetch_assoc())
			{
			
				echo "<option value=".$speedv['id'];
					if($speedv['id']==$att['value']) 
						echo " selected ";
				echo"> ".$speedv['name']." </option>";
			}
				
				
				
			}
			
			
			
			
		




			
			echo " </select>";
		}
		
		

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		else
		{
	
	
			echo " <tr><td>".$att['name'].": <td><input type=text name=\"".$att['name']."\" value=\"".$att['value']."\">";
		
		}
		
		
		
	}

	
	
	
	
	
	
	
	
	if($serv['type']=="PHN")
	{
		
		
		$account=$mon3->query("select value from service_attributes where service_id=$sid AND name=\"account\"")->fetch_assoc()['value'];
		$nr=$mon3->query("select * from voip_accounts where username=\"$account\" ;")->fetch_assoc();
		echo " <tr><td> phone number: <td>".$nr['caller_id'].
		"<tr><td> voicemail: <td>".$nr['voicemail']. 
		"<tr><td> vm timeout: <td>".$nr['voicemail_time'].
		"<tr><td> forward: <td>".$nr['forward_to'].
		"<tr><td> 2xxx_only: <td>".$nr['2x_only'].
		"<tr><td> IP: <td>".$nr['ip'];
		echo "
		<tr><td>Latency <td><b>".$nr['latency']. "ms</b> ".$nr['status']. "(".date("Y-m-d H:i:s",$nr['status_timestamp']) ;
		
		echo "<tr><td colspan=2>";
		
		
		
		
		
		
		
		
	//latency graph
			$i=0;
			$month_1=time()-2678400; //31 dias para tras
			$history=$mon3->query("select timestamp,latency from history_voip where account=\"$account\" AND timestamp>$month_1 order by timestamp desc ");
			echo $mon3->error;
			while($event=$history->fetch_assoc())
			{		
				if($event['latency']=="") $event['latency']=0;
				if($event['latency']>200) $event['latency']=200;

				$eventb[]=$event;
			}
	//var_dump($eventb);
	
	
	
?>

<canvas id="myChart" width="400px" height="200px"></canvas>
<script>
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        
        datasets: [{
            label: ' Latency for ',
            data: [ <?php 

				$i=0;
				foreach($eventb as $event)
				{
						echo "{ x:".$event['timestamp']*1000 .", y:".$event['latency']."}," ;
						$i++;	
				}

				?> ],
				borderColor: 'rgba(255, 0, 0, 1)',
				backgroundColor: 'rgba(0, 0, 0, 0)',
				pointRadius: '0',
				borderWidth: '1'
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }],
			xAxes: [{
                ticks: {
                    beginAtZero:false
                },
				type: 'time',
				ticks: {
								source: 'data{x}'
							},
				time: {
                            unit: 'day',
                            tooltipFormat: 'lll',
                        }
            }]
			
        }
    }
});
</script>


<?php	
	
		
	}
	elseif($serv['type']=="INT")
	{
		
		
	}






echo "

<tr><td><br>
<tr><td>new attribute:<td> <select name=newatt>
<option selected></option> 
<option>wifi</option>
<option>wifi_ssid</option>
<option>wifi_key</option>
<option>wifi_profile</option>
<option>vlan</option>
<option>is_router</option>
<option>bridge_port</option>
<option>unifi_site</option>
<option>unms_site</option>
<option>fixed_ip</option>
<option>account</option>
<option>portfw_id</option>
<option>ip_addr</option>
<option>dhcp_id</option>
<option>ip_com</option>
</select>
value:<input type=text name=newattvalue>

<tr><td><input type=submit name=editserv value=save> 
</table> </form>";

}























///// adicionar serviços





elseif($_GET['addserv']>0)
{
	$conn=mysqli_real_escape_string($mon3, $_GET['addserv']);
	$con=$mon3->query("select * from connections where id=\"$conn\"")->fetch_assoc();
	$prop=$mon3->query("select * from properties where id=\"".$con['property_id']."\" ")->fetch_assoc();
	
	if($con['type']=="GPON")
	{
		$ont=$mon3->query("select * from ftth_ont where fsan=\"".$con['equip_id']."\" ")->fetch_assoc();
	}
	elseif($con['type']=="COAX")
	{
		$modem=$mon3->query("select * from coax_modem where mac=\"".$con['equip_id']."\" ");
	}




	if($_POST['addservsubm'])
	{
		$date_start=mysqli_real_escape_string($mon3, $_POST['date_start']);
		$stype=mysqli_real_escape_string($mon3, $_POST['stype']);
		if($stype=="INT")
		{
			
			$speed=mysqli_real_escape_string($mon3, $_POST['speed']);
			$is_router=mysqli_real_escape_string($mon3, $_POST['is_router']);
			$vlan=mysqli_real_escape_string($mon3, $_POST['vlan']);
			$fixed_ip=mysqli_real_escape_string($mon3, $_POST['fixed_ip']);
			$wifi=mysqli_real_escape_string($mon3, $_POST['wifi']);
			$wifi_ssid=mysqli_real_escape_string($mon3, $_POST['wifi_ssid']);
			$wifi_key=mysqli_real_escape_string($mon3, $_POST['wifi_key']);

			
			$q=$mon3->query("INSERT INTO services(connection_id,type, date_start) VALUES (
			\"".$con['id']."\",
			\"$stype\",
			\"$date_start\"
			) ");
			$sid=$mon3->insert_id;
			
			
			if($con['type']=="GPON")
			{

			$speedb=$mon3->query("select * from int_services where id=\"$speed\" ")->fetch_assoc();
			
			echo "speedd: ".$speedb['speed'];
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"speed\",
			\"".$speedb['id']."\",
			\"$date_start\"
			)");
/*			
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"prof_up\",
			\"".$speedb['prof_up']."\",
			\"$date_start\"
			)"); echo $mon3->error;
			
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,`value`,date) VALUES (
			$sid,
			\"prof_down\",
			\"".$speedb['prof_down']."\",
			\"$date_start\"
			)"); echo $mon3->error;
*/			
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"vlan\",
			\"$vlan\",
			\"$date_start\"
			)");
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"is_router\",
			\"$is_router\",
			\"$date_start\"
			)");
			if($is_router==0){
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"bridge_port\",
			\"1\",
			\"$date_start\"	
			)");
			}
			
			
			
			
			
			if($wifi==1){
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi\",
			\"1\",
			\"$date_start\"	
			)");
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi_ssid\",
			\"$wifi_ssid\",
			\"$date_start\"	
			)");
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi_key\",
			\"$wifi_key\",
			\"$date_start\"	
			)");
			}else $q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi\",
			\"0\",
			\"$date_start\"	
			)");
			
			
			
			
			
			}
			elseif($con['type']=="COAX")
			{
				$speedb=$mon3->query("select * from int_services where id=\"$speed\" ")->fetch_assoc();
			
			echo "speedd: ".$speedb['speed'];
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"speed\",
			\"".$speedb['id']."\",
			\"$date_start\"
			)");
			
			
			if($fixed_ip!="")
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"is_router\",
			\"$is_router\",
			\"$date_start\"
			)");	
			
			if($wifi==1){
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi\",
			\"1\",
			\"$date_start\"	
			)");
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi_ssid\",
			\"$wifi_ssid\",
			\"$date_start\"	
			)");
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi_key\",
			\"$wifi_key\",
			\"$date_start\"	
			)");
			}else $q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi\",
			\"0\",
			\"$date_start\"	
			)");
			
			
	

			}
			elseif($con['type']=="ETH")
			{
				$speedb=$mon3->query("select * from int_services where id=\"$speed\" ")->fetch_assoc();
			
			echo "speedd: ".$speedb['speed'];
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"speed\",
			\"".$speedb['id']."\",
			\"$date_start\"
			)");
			
			
			if($fixed_ip!="")
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"is_router\",
			\"$is_router\",
			\"$date_start\"
			)");	
			
			if($wifi==1){
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi\",
			\"1\",
			\"$date_start\"	
			)");
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi_ssid\",
			\"$wifi_ssid\",
			\"$date_start\"	
			)");
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi_key\",
			\"$wifi_key\",
			\"$date_start\"	
			)");
			}else $q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"wifi\",
			\"0\",
			\"$date_start\"	
			)");
			
			
				
				
			}


















			
		}
		elseif($stype=="TV")
		{
			$tv=mysqli_real_escape_string($mon3, $_POST['tv']);

			$q=$mon3->query("INSERT INTO services(connection_id,type, date_start) VALUES (
			\"".$con['id']."\",
			\"$stype\",
			\"$date_start\"
			) ");
			$sid=$mon3->insert_id;	
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"supplier\",
			\"$tv\",
			\"$date_start\"	
			)");
		}
		
		
		elseif($stype=="PHN")
		{
			$phone=mysqli_real_escape_string($mon3, $_POST['phone']);
			$phn_port=mysqli_real_escape_string($mon3, $_POST['phn_port']);
			$voicemail=mysqli_real_escape_string($mon3, $_POST['voicemail']);
			$divert=mysqli_real_escape_string($mon3, $_POST['divert']);
			$_2x_only=mysqli_real_escape_string($mon3, $_POST['_2x_only']);
			$phn_equip=mysqli_real_escape_string($mon3, $_POST['phn_equip']);
			
			$q=$mon3->query("INSERT INTO services(connection_id,type, date_start) VALUES (
			\"".$con['id']."\",
			\"$stype\",
			\"$date_start\"
			) ");
			$sid=$mon3->insert_id;	
			
			if($_2x_only=="on") $_2x_only="1"; else $_2x_only="0"; 
			if($voicemail>"0") $voicemailt="1"; else $voicemailt="0"; 
			
			$w=$mon3->query("INSERT INTO voip_accounts( password,caller_id,voicemail,voicemail_time, call_limit,forward_to,2x_only) VALUES (
			\"".substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 8)."\",
			\"351".$phone."\",
			\"$voicemailt\",
			\"$voicemail\",
			\"1\",
			\"$divert\",
			\"$_2x_only\"
			)");
			echo $mon3->error;
			$account=$mon3->insert_id;
			$q=$mon3->query("update settings set valor=1 where nome=\"voip_accounts_changed\"");
			
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"account\",
			\"$account\",
			\"$date_start\"	
			)");
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"phn_port\",
			\"$phn_port\",
			\"$date_start\"	
			)");
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"phn_equip\",
			\"$phn_equip\",
			\"$date_start\"	
			)");





			
		}
		
		
		elseif($stype=="VLN")
		{
			$svlan=mysqli_real_escape_string($mon3, $_POST['svlan']);
			$bridge_port=mysqli_real_escape_string($mon3, $_POST['bridge_port']);

			$q=$mon3->query("INSERT INTO services(connection_id,type, date_start) VALUES (
			\"".$con['id']."\",
			\"$stype\",
			\"$date_start\"
			) ");
			$sid=$mon3->insert_id;	
			$q=$mon3->query("INSERT INTO service_attributes(service_id,name,value,date) VALUES (
			$sid,
			\"vlan\",
			\"$svlan\",
			\"$date_start\"	
			)");
		}
		
		
		
		
		
		
		
		
		
		
		
		
		echo "saved";
		
	}
	
	
	
	
	echo "
	<form action=?servs=1&addserv=$conn method=post id=aa name=aa>	
	<table border=1 width=900px>
	<tr><td colspan=2><h3>Add service</h3>
	<b>Property:</b> <a href=?props=1&propid=".$prop['id'].">".$prop['ref']."</a><br> 
	<b>Address:</b> ".$prop['address']."<br>  
	<b>Connection id:</b> <a href=?props=1&conedit=".$con['id'].">".$con['type']." </a><br>
	
	

<script>
	function updateservs(serv)
	{
	var textdiv='<table>';
		
	
	if(serv=='INT') 
	{
		textdiv += ' <tr><td colspan=2>internet service <img width=40px src=img/internet.png>' +
		'<tr><td>Speed: <td> <select name=speed >'+ ";
		$speeds=$mon3->query("select id, name from int_services where con_type=\"".$con['type']."\"");
		while($speed=$speeds->fetch_assoc())
		{
			echo "'<option value=".$speed['id'].">".$speed['name']."</option>'+";
			
		}
		
		
		
		
		if($con['type']=="GPON")
		{
		echo "'<tr><td>router mode<td><select name=is_router ><option value=1>Router</option><option value=0>Bridge on eth1</option></select><br> '+	
				'<tr><td>vlan<td><select id=vlans name=vlan >";
				
		
		$vlans=$mon3->query("select vlan,description,total_dynamic_ips from int_vlans where olt_id=".$ont['olt_id']." ");
		while($vlan=$vlans->fetch_assoc())
		{
			$inuse=$mon3->query("select count(name) from service_attributes left join services on service_attributes.service_id=services.id  where name=\"vlan\" and value=\"".$vlan['vlan']."\" and services.date_end=\"0000-00-00\" ")->fetch_assoc();
			echo "<option value=".$vlan['vlan'].">".$vlan['description']." - ".$inuse['count(name)']." of ".$vlan['total_dynamic_ips'];
		}
		echo "</select>  '+";
		
		
		echo"
		'<tr><td>Fixed IP<td><select name=fixed_ip> <option value=>no fixed ip</option>"; 
		$refs=$mon3->query("select ip,vlan from int_fixed_ips where in_use!=1 and connection_type=\"".$con['type']."\" order by ip"); 
		while($ref=$refs->fetch_assoc())
		{ echo "<option value=".$ref['ip'].">".$ref['ip']." - vlan".$ref['vlan']."</option>";
		}
		echo " </select>'+";
		}
		
		
		
		
		
		elseif($con['type']=="COAX")
		{
		echo "'<tr><td>router mode<td><select name=is_router ><option value=1>Router</option><option value=0>Bridge</option></select><br> '+
		
		'<tr><td>vlan<td><select id=vlans name=vlan >";
				
		
		$vlans=$mon3->query("select vlan,description,total_dynamic_ips from int_vlans where olt_id=0 ");
		while($vlan=$vlans->fetch_assoc())
		{
			$inuse=$mon3->query("select count(name) from service_attributes left join services on service_attributes.service_id=services.id  where name=\"vlan\" and value=\"".$vlan['vlan']."\" and services.date_end=\"0000-00-00\"  ")->fetch_assoc();
			echo "<option value=".$vlan['vlan'].">".$vlan['description']." - ".$inuse['count(name)']." of ".$vlan['total_dynamic_ips'];
		}
		echo "</select>  '+";
		
		
		echo"
		'<tr><td>Fixed IP<td><select name=fixed_ip> <option value=>no fixed ip</option>"; 
		$refs=$mon3->query("select ip,vlan from int_fixed_ips where in_use!=1 and connection_type=\"".$con['type']."\" order by ip"); 
		while($ref=$refs->fetch_assoc())
		{ echo "<option value=".$ref['ip'].">".$ref['ip']." - vlan".$ref['vlan']."</option>";
		}
		echo " </select>'+";
		}
		
		
		
		
		
		
		
		
		elseif($con['type']=="ETH")
		{
		
		echo "'<tr><td>router mode<td><select name=is_router ><option value=1>Router</option><option value=0>Bridge</option></select><br> '+	
				'<tr><td>vlan<td><select id=vlans name=vlan >";
				
		
		$vlans=$mon3->query("select vlan,description,total_dynamic_ips from int_vlans where olt_id=0 ");
		while($vlan=$vlans->fetch_assoc())
		{
			$inuse=$mon3->query("select count(name) from service_attributes left join services on service_attributes.service_id=services.id  where name=\"vlan\" and value=\"".$vlan['vlan']."\" and services.date_end=\"0000-00-00\"  ")->fetch_assoc();
			echo "<option value=".$vlan['vlan'].">".$vlan['description']." - ".$inuse['count(name)']." of ".$vlan['total_dynamic_ips'];
		}
		echo "</select>  '+";
		
		
		echo"
		'<tr><td>Fixed IP<td><select name=fixed_ip> <option value=>no fixed ip</option>"; 
		$refs=$mon3->query("select ip,vlan from int_fixed_ips where in_use!=1 and connection_type=\"".$con['type']."\" order by ip"); 
		while($ref=$refs->fetch_assoc())
		{ echo "<option value=".$ref['ip'].">".$ref['ip']." - vlan".$ref['vlan']."</option>";
		}
		echo " </select>'+";
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		echo "
		'<tr><td>Wifi<td><select name=wifi><option value=1 selected>enabled</option><option value=0>disabled</option></select> '+	
		'<tr><td>Wifi SSID<td><input type=text name=wifi_ssid size=10 value=Lazer_".explode(",",$prop['address'])[0]."><br> '+	
		'<tr><td>Wifi passwd<td><input type=text name=wifi_key size=10 value=lzr".substr($prop['ref'],3,3).strtolower(substr($prop['ref'],0,3))."> <tr><td><br> ';
	
	}
	if(serv=='TV') 
	{
		textdiv += ' <tr><td colspan=2>TV service <img width=40px src=img/tv.png>' +
		'<tr><td>Provider: <td> <select name=tv><option value=0 selected>no TV</option><option value=AMLA>AMLA</option><option value=NOWO >NOWO</option></select> ';
	}
	if(serv=='VLN') 
	{
		textdiv += '<tr><td>VLAN: <td> <select name=svlan><option value=0 selected>no Vlan</option><option value=1001>Vigiquinta</option><option value=1005>QDL</option></select> ';
	}
	if(serv=='DRKF') 
	{
		textdiv += ' <tr><td colspan=2>Dark Fibre service <img width=40px src=img/drkf.png>' +
		'<tr><td>distance: <td> <input size=6 type=text name=lenght value=1000>m ';
	}
	if(serv=='PHN') 
	{
		textdiv +=	'<tr><td colspan=2>Phone service <img width=40px src=img/telephone.png>' +
		'<tr><td>phone line<td><select name=phone> <option selected value= >no line</option>"; 
		$refs=$mon3->query("select phone_number from voip_numbers where in_use!=1 order by phone_number"); 
		while($ref=$refs->fetch_assoc()){ 
		echo "<option value=".$ref['phone_number'];
		if($prop['phone1']==$ref['phone_number']) echo " selected ";
		echo ">".$ref['phone_number']."</option>";
		} 
		echo " </select>'+
		'<tr><td>port<td><select name=phn_port><option value=1>port 1</option> <option value=2>port 2</option></select><tr><td>Voicemail timeout<td> <select name=voicemail><option value=0> no voicemail</option> <option value=20> 20s </option><option selected value=50> 50s </option></select>'+
		'<tr><td>Divert all calls to<td><input type=text name=divert value=>'+
		'<tr><td>landline only:<td><input type=checkbox name=_2x_only>'+
		'<tr><td>provision to:<td><select name=phn_equip>";
		if($con['type']=="GPON") echo "<option value=ont selected>ONT</option>";
		elseif($con['type']=="COAX") echo "<option value=emta>EMTA</option>";
		echo "<option value=pbx>Customer PBX</option> </select> ';
	
	
	}
	
	textdiv += ' </table>';
	document.getElementById('additionalset').innerHTML = textdiv;
}
</script>

	
	
	
	
	
	
	
	
	

	
	<tr><td>Service type: <td> <select name=stype onchange=\"updateservs(this.options[this.selectedIndex].value)\"> 
	<option value=>no service</option>
	<option value=INT>INT</option>";
	if($con['type']=="GPON")
	{
	echo "
	<option value=VLN>VLAN</option>
	<option value=DRKF>Dark Fibre</option>	
	<option value=PHN>PHN</option>
	";
	}
	echo" <option value=TV>TV</option></select><br><tr><td>Date start: <td> <input type=text name=date_start value=".date("Y-m-d").">
	
	<tr><td colspan=2>
	<div id='additionalset'>
	
	</div>
	
	
	
	";
	
	
	
	
	
	
	
	
	
	echo "<tr><td colspan=2><input type=submit value=add name=addservsubm>
	
	</table></form> ";
	
}






//######################################################default#######################################



else // list services by query/search
{
	$type=mysqli_real_escape_string($mon3, $_GET['type']);

$offset=0;
if(isset($_GET['offset']))
	$offset=mysqli_real_escape_string($mon3, $_GET['offset']);	
	

	
echo "<table>";	
	
	
	
	
	


if($type=="IPs")
{



echo "<tr><td colspan=5> <img src=img/IPF.png title=\"IPs\">IPs   <a href=?servs=1&type=IPs&fixed=1> show fixed IPs </a>  &nbsp; <a href=?servs=1&type=IPs> show all </a>        <br> ";
echo "

<tr><td colspan=6><form name=searchip method=get action=?servs=1&type=IPs><input type=hidden name=servs value=1><input type=hidden name=type value=IPs> Search IP:<input type=text name=sip> &nbsp; Search mac:<input type=text name=smac> <input type=submit name=ipsearch value=search></form>
<tr><td><br>";





	if(isset($_GET['ipsearch']))
	{
$sip=mysqli_real_escape_string($mon3, $_GET['sip']);
$smac=mysqli_real_escape_string($mon3, $_GET['smac']);

if($sip!="")
	$q=" and ip LIKE \"%$sip%\" ";
if($smac!="")
	$q=" and mac LIKE \"%$smac%\" ";
	
$q=substr($q,4);
	
echo "<tr><td colspan=2> $q <br><br>
<tr><td>datetime<td>IP <td>mac <td> type <td> address<td>connection<td>details";		

$ips=$ips=$mon3->query("select history_ip.datetime ,history_ip.ip,history_ip.mac,connections.type,properties.ref,properties.address,connections.property_id from history_ip left join connections on history_ip.connection_id=connections.id left join properties on connections.property_id=properties.id  where $q order by datetime desc limit 0,500");

	while($ip=$ips->fetch_assoc())
	{
		echo "<tr><td>".$ip['datetime']."<td><a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a><td>".$ip['mac']."<td>".$ip['type']."<td><a href=?props=1&propid=".$ip['property_id'].">".$ip['ref']."</a><td>".$ip['address'];
	
	}





	}	
	
	

	elseif(isset($_GET['ip']) || isset($_GET['mac']))
	{
$ip=mysqli_real_escape_string($mon3, $_GET['ip']);
$mac=mysqli_real_escape_string($mon3, $_GET['mac']);

if($ip!="")
	$q=" and ip=\"$ip\" "; 
if($mac!="")
	$q=" and mac=\"$mac\" ";
$q=substr($q,4);	

echo "<tr><td colspan=2> $q <br><br>
<tr><td>datetime<td>IP <td>mac <td> type <td> address<td>connection<td>details";	
$ips=$ips=$mon3->query("select history_ip.datetime,history_ip.ip,history_ip.mac,connections.type,properties.ref,properties.address,connections.property_id from history_ip left join connections on history_ip.connection_id=connections.id left join properties on connections.property_id=properties.id  where $q order by ip limit 0,50");

	while($ip=$ips->fetch_assoc())
	{
		echo "<tr><td>".$ip['datetime']."<td><a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a><td>".$ip['mac']."<td>".$ip['type']."<td><a href=?props=1&propid=".$ip['property_id'].">".$ip['ref']."</a><td>".$ip['address'];
	
	}
	
	
	
	
	
	
	
	}





	elseif($_GET['fixed']==1)
	{

	


	}
	
	
	
	
	
	else
	{
	
	
	echo "<tr><td>IP <td>mac <td> type <td> address<td>connection<td>details";
	
	
	$ips=$mon3->query("select distinct(history_ip.ip),history_ip.mac,connections.type,properties.ref,properties.address,connections.property_id from history_ip left join connections on history_ip.connection_id=connections.id left join properties on connections.property_id=properties.id order by ip limit $offset,50");

	$num=$mon3->query("select count(distinct(history_ip.ip)) from history_ip ")->fetch_assoc();
	$num=$num['count(distinct(history_ip.ip))'];
	
	while($ip=$ips->fetch_assoc())
	{
		echo "<tr><td><a href=?servs=1&type=IPs&ip=".$ip['ip'].">".$ip['ip']."</a><td>".$ip['mac']."<td>".$ip['type']."<td><a href=?props=1&propid=".$ip['property_id'].">".$ip['ref']."</a><td>".$ip['address'];
	
	}
	}

}















if($type=="NOS")
{

	echo "<img src=img/cancel.png title=\"No services\">Connections with no services  <br> ";

	echo "<tr><td>prop ref<td>address<td>connection<td>details";
	
	
	$props=$mon3->query("select id,property_id from connections");
	
	while($prop=$props->fetch_assoc())
	{
		$servs=$mon3->query("select id,type from services where connection_id=\"".$prop['id']."\" ");
		if($servs->num_rows==0){
			$addr=$mon3->query("select address,ref from properties where id=\"".$prop['property_id']."\" ")->fetch_assoc();
			echo "<a href=?props=1&propid=".$prop['id'].">".$addr['ref']."</a> ".$addr['address']."<br>";
		}
	
	}

	

}









elseif($type=="INT")
{

$num=$mon3->query("select id from services where services.type=\"".$type."\" ")->num_rows;

	
	echo "<tr><td colspan=6> <img src=img/internet.png title=\"Internet services\">Connections with $type services total:$num <br> ";

	
		echo "<tr><td>prop ref<td>address<td>connection<td>details";

		$props=$mon3->query("select connections.property_id,connections.type,connections.equip_id,services.id from services left join connections 
		on services.connection_id=connections.id where services.type=\"".$type."\" limit 0,50");
		echo $mon3->error;
		
		while($prop=$props->fetch_assoc()){
			
			//echo "<tr><td>".$prop['property_id'];
			
			$addr=$mon3->query("select address,ref from properties where id=\"".$prop['property_id']."\" ")->fetch_assoc();
/*			if($prop['type']=="GPON")
				$equip=$mon3->query("select * from ftth_ont where fsan=\"".$prop['equip_id']."\"")->fetch_assoc();
			if($prop['type']=="COAX"){
				$equip=$mon3->query("select * from coax_modem where modem=\"".$prop['equip_id']."\"");
				if($equip->num_rows>0)$equip=$equip->fetch_assoc();
			}
*/			$speed=$mon3->query("select value from service_attributes where service_id=\"".$prop['id']."\" and name=\"speed\" ")->fetch_assoc();
			echo $mon3->error;
			echo "<tr><td><a href=?props=1&propid=".$prop['property_id'].">".$addr['ref']."</a><td> ".$addr['address']."<td>".$prop['type']."<td> ". $speed['value']."";
		}
	


	

}








elseif($type=="INTonly")
{

	$num=$mon3->query("SELECT * FROM `services` WHERE `connection_id` not in (select connection_id from services where type=\"TV\" and date_end=\"0000-00-00\" ) and type=\"INT\" and date_start>\"2018-00-00\" and date_end=\"0000-00-00\"")->num_rows;
	$res=$mon3->query("SELECT * FROM `services` WHERE `connection_id` not in (select connection_id from services where type=\"TV\" and date_end=\"0000-00-00\" ) and type=\"INT\" and date_start>\"2018-00-00\" and date_end=\"0000-00-00\" order by date_start desc limit $offset,50");
	
	echo " <tr><td colspan=6><img src=img/internetonly.png title=\"INTonly services\">Connections with $type services only total: $num <br> ";


echo "<tr><td>prop ref<td width=350px>address<td>type<td>service date<td>con datedetails";

while($serv=$res->fetch_assoc())
{

		$props=$mon3->query("select connections.property_id,connections.type,connections.equip_id,services.id,connections.date_start from services left join connections 
		on services.connection_id=connections.id where services.id=\"".$serv['id']."\" ");
		
		while($prop=$props->fetch_assoc()){
			$addr=$mon3->query("select address,ref from properties where id=\"".$prop['property_id']."\" ")->fetch_assoc();
			
	
			
			echo "<tr><td><a href=?props=1&propid=".$prop['property_id'].">".$addr['ref']."</a> <td width=350px>".$addr['address']."<td>".$prop['type']."<td>".$serv['date_start']."<td>".$prop['date_start'];
		}
	


}	

}


















elseif($type=="TV")
{

	$num=$mon3->query("select id from services where services.type=\"".$type."\" ")->num_rows;

	
	echo " <tr><td colspan=6><img src=img/tv.png title=\"TV services\">Connections with $type services  total: $num <br> ";


echo "<tr><td>prop ref<td>address<td>connection<td>details";

		$props=$mon3->query("select connections.property_id,connections.type,connections.equip_id,services.id from services left join connections 
		on services.connection_id=connections.id where services.type=\"".$type."\" limit 0,50");
		
		while($prop=$props->fetch_assoc()){
			$addr=$mon3->query("select address,ref from properties where id=\"".$prop['property_id']."\" ")->fetch_assoc();
			
	
			
			echo "<tr><td><a href=?props=1&propid=".$prop['property_id'].">".$addr['ref']."</a> <td>".$addr['address']."<td>".$prop['type'];
		}
	


	

}







elseif($type=="TVonly")
{

	$num=$mon3->query("SELECT * FROM `services` WHERE `connection_id` not in (select connection_id from services where type=\"INT\" and date_end=\"0000-00-00\" ) and type=\"TV\" and date_end=\"0000-00-00\"")->num_rows;
	$res=$mon3->query("SELECT * FROM `services` WHERE `connection_id` not in (select connection_id from services where type=\"INT\" and date_end=\"0000-00-00\" ) and type=\"TV\" and date_end=\"0000-00-00\" limit $offset,50");
	
	echo " <tr><td colspan=6><img src=img/tvonly.png title=\"TVonly services\">Connections with $type services only total: $num <br> ";


echo "<tr><td>prop ref<td>address<td>connection<td>TV service<";

while($serv=$res->fetch_assoc())
{

		$props=$mon3->query("select connections.property_id,connections.type,connections.equip_id,services.id,services.connection_id from services left join connections 
		on services.connection_id=connections.id where services.id=\"".$serv['id']."\" ");
		
		while($prop=$props->fetch_assoc()){
			$addr=$mon3->query("select address,ref from properties where id=\"".$prop['property_id']."\" ")->fetch_assoc();
			
	
			
			echo "<tr><td><a href=?props=1&propid=".$prop['property_id'].">".$addr['ref']."</a> <td>".$addr['address']."<td><a href=?props=1&conedit=".$prop['connection_id'].">".$prop['type']."</a><td><a href=?servs=1&sid=".$prop['id'].">sid:".$prop['id']."</a>";
		}
	


}	

}















elseif($type=="PHN")
{


if(isset($_GET['isearch']))
{
	$isearch=mysqli_real_escape_string($mon3, $_GET['isearch']);	
	$qwhere=" where username LIKE \"%$isearch%\" OR caller_id LIKE \"%$isearch%\" ";
}
elseif(isset($_GET['offline']))
{
	$qwhere=" where status=\"UNKNOWN\" ";
}





	
	
	
$props=$mon3->query("select username,caller_id,latency,status from voip_accounts $qwhere order by caller_id limit $offset,50 ");
$num=$mon3->query("select username from voip_accounts $qwhere ")->num_rows;	
	
if($qwhere=="") $qwhereb=" where status=\"UNKNOWN\" ";
else $qwhereb=$qwhere." AND status=\"UNKNOWN\" ";
	
$offline=$mon3->query("select username from voip_accounts $qwhereb ")->num_rows;	
	
	echo " <form action=?> 
	<tr><td colspan=5><img src=img/telephone.png title=\"phone services\">Connections with $type services <b><a href=?servs=1&type=PHN>total:</a></b> $num <b> &nbsp; <a href=?servs=1&type=PHN&offline=1>offline:</a></b> $offline(".ceil($offline/$num*100)."%) 
	<td>search <input type=text name=isearch><input type=hidden name=servs value=1><input type=hidden name=type value=PHN>
	</form> ";
	
	
	echo "<tr><td>prop ref<td>address<td>connection<td>details";

//		$props=$mon3->query("select connections.property_id,connections.type,connections.equip_id,services.id from services left join connections on services.connection_id=connections.id where services.type=\"".$type."\" $qwhere order by connections.property_id limit 0,50 ");



		echo $mon3->error;
		while($prop=$props->fetch_assoc()){
			
			$srvs=$mon3->query("select service_id from service_attributes where name=\"account\" AND value=\"".$prop['username']."\"  ")->fetch_assoc();
			echo $mon3->error;
			$srvt=$mon3->query("select connection_id from services where id=\"".$srvs['service_id']."\"  ")->fetch_assoc();
			$srvf=$mon3->query("select property_id,type from connections where id=\"".$srvt['connection_id']."\"  ")->fetch_assoc();
			echo $mon3->error;
			$addr=$mon3->query("select address,ref from properties where id=\"".$srvf['property_id']."\" ")->fetch_assoc();
			echo $mon3->error;
			
			echo "<tr><td><a href=?props=1&propid=".$srvf['property_id'].">".$addr['ref']."</a> <td>".$addr['address']."<td>".$srvf['type'].
			"<td><a href=?servs=1&sid=".$srvs['service_id'].">".$prop['username']."</a><td>".$prop['caller_id'].
			"<td align=center> ".$prop['status']." <td> ".$prop['latency']."ms";
		}
	












}






	echo "<tr><td colspan=6><br>";	
	if ($num>50)
{
	$lastp=ceil($num/50);
	$curpage=($offset/50)+1;
	


	
//print initial page
	if($curpage>1)
	{
		echo "<a href=?servs=1&type=$type&search=".urlencode($isearch)."&offset=0>|<</a> ";
	}
//print page -2
	if($curpage>2)
	{
		echo "<a href=?servs=1&type=$type&search=".urlencode($isearch)."&offset=".($curpage-3)*50 .">".($curpage-2) ."</a> ";
	}
//print page -1
	if($curpage>1)
	{
		echo "<a href=?servs=1&type=$type&search=".urlencode($isearch)."&offset=".($curpage-2)*50 .">".($curpage-1) ."</a> ";
	}
//print curpage	
	
		echo " <b> $curpage </b> ";
//print page -1
	if($curpage<$lastp)
	{
		echo "<a href=?servs=1&type=$type&search=".urlencode($isearch)."&offset=".($curpage)*50 .">".($curpage+1) ."</a> ";
	}
	if($curpage<$lastp-1)
	{
		echo "<a href=?servs=1&type=$type&search=".urlencode($isearch)."&offset=".($curpage+1)*50 .">".($curpage+2) ."</a> ";
	}	
		
		if($curpage<$lastp)
	{
		echo "<a href=?servs=1&type=$type&search=".urlencode($isearch)."&offset=".($lastp-1)*50 .">>|</a> ";
	}	
	
}




echo" showing ". ($curpage-1)*50 ." to ".$curpage*50 . " of $num results";
echo "</table>";	
	

}



















