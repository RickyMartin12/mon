<?php

echo "  
	<a href=?headend=1&channels=1><img src=img/tv.png></a>
	<a href=?headend=1&cards=1><img src=img/skycard.png></a>
	<a href=?headend=1&lists=1><img src=img/lists.png></a> 
	<img src=img/sp.png>
	<a href=?headend=1&servers=1><img src=img/servers.png></a>


";


if (0)
{
echo " GPON ONT id <b> $equip </b> <br>";
$ont=$mon3->query("select * from ftth_ont where fsan=\"$equip\"")->fetch_assoc();
$con=$mon3->query("select * from connections where equip_id=\"$equip\"")->fetch_assoc();
$prop=$mon3->query("select * from properties where id=\"".$con['property_id']."\"")->fetch_assoc();
$history=$mon3->query("select * from history_ont where fsan=\"$equip\" order by timestamp desc");


echo "status (at ".date("Y-m-d H:i:s",$ont['status_timestamp']).") :".$ont['status'] ."<br>
Address: <a href=?propid=".$prop['id'].">". $prop['address']
."</a><br>

<br>";




 }
 
 
 //############### cards #########
 elseif($_GET['cards']==1)
 {
	 
	 
 
	 
	 
	 
	echo "<h3>Headend TV Cards</h3>";
  
  
  if($_GET['cancelled_cards']==1)
  {	
	$cards=$mon3->query("select * from TV_cards where date_cancelled!=\"0000-00-00\" order by card_nr");
  $cost=0;
  $cards_active=$cards->num_rows;
	  
  }
	elseif(isset($_GET['supplier']))
	{
		$cards=$mon3->query("select * from TV_cards where supplier=\"".$_GET['supplier']."\" order by card_nr");
  $cost=$mon3->query("select sum(cost) from TV_cards where supplier=\"".$_GET['supplier']."\" AND date_cancelled=\"0000-00-00\"")->fetch_assoc();
  $cards_act=$mon3->query("select * from TV_cards where supplier=\"".$_GET['supplier']."\" AND  date_cancelled!=\"0000-00-00\" order by card_nr");
  $cards_active=$cards_act->num_rows;
	  
	
	}
  
  else
  {
  $cards=$mon3->query("select * from TV_cards where date_cancelled=\"0000-00-00\" order by card_nr");
  $cost=$mon3->query("select sum(cost) from TV_cards where date_cancelled=\"0000-00-00\"")->fetch_assoc();
  $cards_active=$cards->num_rows;
  }
  
  
  echo "<a href=?headend=1&cards=1&cancelled_cards=1> show cancelled cards</a>
  <a href=?headend=1&cards=1> show active cards</a>
  
  <br>"
  
  
  
  ." total active cards: $cards_active | total cost:".ceil($cost['sum(cost)'])."€" 
  .""
  
  
  ;
  
  
  
  
   echo "<table cellpadding=5>
 <tr> 

 <th>card_nr </th>
 <th>thumb</th> 
 <th>supplier </th> 
 <th>channel id</th>
 <th>package</th> 
 <th>receiver type</th>
 <th>location</th>  
  <th>cost</th>  
  <th>send email </th> 
 ";
 
 while($card=$cards->fetch_assoc())
 {
  
	echo "<tr><td align=center><a href=?headend=1&card=".$card['id'].">".$card['card_nr']."</a></b><br>".$card['broadcaster']."
	<td><a href=includes/resources/satcards/".$card['id'].".jpg target=_blank> <img  width=200px height=113px src=includes/resources/satcards/".$card['id'].".jpg></a>
	<td><a href=?headend=1&cards=1&supplier=".$card['supplier'].">".$card['supplier']."</a>
	<td>";
	
	$channels=$mon3->query("select * from TV_channels where card_id=\"".$card['id']."\"");
	
	while($channel=$channels->fetch_assoc())
	{
		echo "<a href=?headend=1&channel=".$channel['sid'].">".$channel['name']."</a><br>";
	}
	//.$card['channel_id'].



	echo "<td ";
	if($card['date_cancelled']!="0000-00-00")
		echo "bgcolor=red";
	echo ">".trim($card['package_active']).
	"<td>".$card['receiver_type']."
	<td>".$card['location']."	
	<td>".$card['cost']."€	
	";

	
	
	
	

	
 }
 
 
 
 
 }
 
 
 
 
 //###individual  card
 elseif($_GET['card']>0)
 {
	 	 $id=$_GET['card'];
	 
	 
	 if($_POST['update'])
	 {
		 
		 $reason=mysqli_real_escape_string($mon3, $_POST['reason']);
		 $notes=mysqli_real_escape_string($mon3, $_POST['notes']);
		$mon3->query("INSERT INTO `TV_cards_notes` (`card_id`, `reason`, `notes`, `date`, `user`) VALUES ($id, \"$reason\", \"$notes\", \"".date("Y-m-d")."\", \"".$_SERVER['PHP_AUTH_USER']."\");");
		 
		 echo "updating notes";
		 
		 var_dump($_POST);
		 
	 }

	echo "<h3>Headend TV Cards</h3>";
	$card=$mon3->query("select * from TV_cards where id=$id")->fetch_assoc();
	
	
	
	echo" <b>Card id $id - ".$card['card_nr'] ."</b> for ".$card['broadcaster'] ."<br>".
	"<table>".  
	"<tr><td>supplier: <td>".$card['supplier'] .
	"<tr><td>activation date: <td>".$card['date_activation'] .
	"<tr><td>box id: <td>".$card['receiver_type'] . " - ".$card['paired_box'] . " - <a href=?headend=1&receiver_id=".$card['receiver_id'].">".$card['receiver_id']."</a>".
	"<tr><td>packages active: <td>".$card['package_active'] .
	"<tr><td>location: <td>".$card['location'].
	"<tr><td>channels: <td>";
	$channels=$mon3->query("select sid,name from TV_channels where card_id=$id");
	if($channels->num_rows>0)
		while($channel=$channels->fetch_assoc())
		{echo "<a href=?headend=1&channel=".$channel['sid']." >".$channel['name']." </a>";}
	
	
	
	echo	"<tr><td>status:<td>";
	if($card['date_cancelled']!="0000-00-00")
	{
		echo "<font color=red><b>Cancelled on the ".$card['date_cancelled'];
	}
	else
	{
		echo "active";
		
	
	
	}
	echo "<tr><td>cost:<td>".$card['cost']."€";
	
	echo "<tr><td colspan=2><br><a href=includes/resources/satcards/".$id.".jpg target=_blank> <img  width=400px  src=includes/resources/satcards/".$id.".jpg></a>";

 	echo "
	<tr><td><br><form action=?headend=1&card=$id method=post>
	<tr><td><br>new log: 
	<tr><td> note:<td><input type=text name=notes size=60>
	<tr><td>reason: <td>
	<select name=reason>
	<option>box down</option>
	<option>cam/box stuck-reset</option>
	<option>card subs down</option>
	<option>lost pairing</option>
	<option>no payment</option>
	<option>cancelled for streaming</option>
	<option>cancelled lost card</option>
	<option>cancelled faulty card</option>
	<option>faulty box</option>
	<option><font color=green>back on</font></option>
	</select>
	
	<input type=submit name=update value=update>
	</form>
	
	";
	
	echo "<tr><td><tr><td>log: <td>";
	
	$logs=$mon3->query("select * from TV_cards_notes where card_id=$id order by date");
	while($log=$logs->fetch_assoc())
	{
		echo $log['date']."-".$log['user'].": <b>".$log['reason']."</b> -> ". $log['notes']."<br>";
	}
	
	echo"</table>";
	
	
	
	
	
	
	
	
	
	 
	 
	 
	 
	 
	 
	 
	 
	 
 }
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
  //############### channel list ########
 
 elseif($_GET['channels']==1)
 {
 	echo "<h3>Headend TV Channels</h3><br>";

/*
 ?>
 
 <script type="text/javascript">
setTimeout(function() {
  location.reload();
}, 30000);
 </script>
 <?php
*/ 
 $channels=$mon3->query("select * from TV_channels order by channel_type,name");

 
 
  echo "<table cellpadding=5>
 <tr> <th>channel </th><th>thumb</th> <th>card </th> <th>lists </th>   
 ";
 
 while($channel=$channels->fetch_assoc())
 {
  
	echo "<tr><td align=center><a href=?headend=1&channel="
	.$channel['sid']."> <img width=64px src=../channels/".$channel['sid'].".png><br>"
	.$channel['sid']."<br>".$channel['name']."</a>
	
	<td>";	if($channel['channel_type']=="TV")
	{echo "<img  width=200px height=113px src=../img/get_thumb.php?sid=".$channel['sid']."&for=gif>";}
	else{echo "<img  height=113px src=../img/radio.png>";}
	echo 
	"<td>	";
	
	$card=$mon3->query("select * from TV_cards where id=\"".$channel['card_id']."\" ")->fetch_assoc();
	
	
	echo "<a href=?headend=1&card=".$channel['card_id'].">".$card['broadcaster']." <br> ".$card['supplier']." <br> ".$card['card_nr']."</a>";
 	



	
 }
 
 
 
 }
 
 
 
// #### individual channel ##############################
 
 
  elseif($_GET['channel']>0)
  {
	  
	  
	  
	$id=$_GET['channel'];
	$channel=$mon3->query("select * from TV_channels where sid=$id")->fetch_assoc();
	
	echo "<h3>Headend TV channel id $id <br> <font color=red>".$channel['name']."</font>
	<br><img width=64px src=../channels/".$channel['sid'].".png> </h3><br>";
	  
	 
 
	 
	 if($_POST['update_channel'])
	 {
		 
		 $card=mysqli_real_escape_string($mon3, $_POST['card_id']);
		 $notes=mysqli_real_escape_string($mon3, $_POST['notes']);
		 
		$mon3->query("update TV_channels set card_id=\"$card\", notes=\"$notes\" where sid=\"$id\" ;");
		 echo mysqli_error($mon3); 
		 
		 if($notes==$channel['notes']) $notes="";
		 else $notes=" new notes: ".$notes;
		
		 if($card!=$channel['card_id']) $notes=" new card assigned:$card ".$notes;
		
		
		chanlog($id,$card,"updated channel.  $notes");
		 echo "<font color=green><b> Saved</b></font>";
		 
		$channel=$mon3->query("select * from TV_channels where sid=$id")->fetch_assoc();
		 
	 }



	//	chanlog($id,$channel['card_id'],$text);
	
	
	
	echo"<form name=chinfo method=post action=?headend=1&channel=$id>
	<table>	 ".
	"<tr><td colspan=2> ";
	if($channel['channel_type']=="TV")
	{echo "<img  width=200px height=113px src=../img/get_thumb.php?sid=".$channel['sid']."&for=gif>";}
	else{echo "<img  height=113px src=../img/radio.png>";}
	
	
	
	echo
 	"<tr><td>broadcaster: <td>".$channel['broadcaster'] .
	
	
	"<tr><td>card_id: <td> <select name=card_id><option value=0"; 
	if($channel['card_id']==0) echo " selected"; 
	echo "> no card</option>";
	
	
	$cards=$mon3->query("select * from TV_cards where date_cancelled=\"0000-00-00\" order by broadcaster,card_nr");
	while($card=$cards->fetch_assoc())
 {
	 echo"<option value=".$card['id'];
	 if($card['id']==$channel['card_id']) echo " selected";
	 echo ">".$card['broadcaster']." - ".$card['supplier']." - ".$card['card_nr']."</option>";
					
 }
	echo "</select> <a href=?headend=1&card=".$channel['card_id'].">".$channel['card_id']."</a>".
	"<tr><td>channel_type: <td> ".$channel['channel_type'].
	"<tr><td>genre: <td> ".$channel['genre'].
	"<tr><td>language: <td> ".$channel['lang'].
	"<tr><td>country: <td> ".$channel['country'].	



	"<tr><td>Input IPTV: <td>".$channel['input_iptv_stream'].
	
	
	"<tr><td>Satellite: <td> <a target=_blank href=https://en.kingofsat.net/".$channel['sat'].".php>".$channel['sat']."</a> Antenna_id:".$channel['antenna_id'].

	

	"<tr><td>receiver: <td> ".$channel['receiver_type']." ip:<a target=_blank href=http://"	.$channel['receiver_ip'].">"	.$channel['receiver_ip']."</a> card/port: "	.$channel['receiver_card_port']." cam: "	.$channel['cam_card_id']." at "	.$channel['receiver_location'].

	"<tr><td>encoder: <td> <a target=_blank href=http://"	.$channel['encoder_ip'].">"	.$channel['encoder_ip']."</a> port: "	.$channel['encoder_port'].

	"<tr><td>processor: <td> <a target=_blank href=http://"	.$channel['processor_ip'].">"	.$channel['processor_ip']."</a> card: "	.$channel['processor_card'].

	
	"<tr><td>SPTS_ip: <td> rtp://".$channel['SPTS_ip'].":1234".
	"<tr><td>vlan: <td> ".$channel['vlan'].
	
	
	"<tr><td>Notes: <td><textarea name=notes>".$channel['notes']."</textarea>".
	
	"<tr><td> <td> <input type=submit name=update_channel>
	</table></form>".
	
	
	
	
	
	
/*	
	"<tr><td> SPTS_ip:<td><input type=text name=SPTS_ip value=".$channel['SPTS_ip']." size=20>".
	"<tr><td> vlan:<td><select name=vlan> <option value=501"; if($channel['vlan']==501) echo " selected"; echo "> 501 </option> <option value=502"; if($channel['vlan']==502) echo " selected"; echo "> 502  </option> ".
*/	
	
	
	
	
	
	"";
	
	
	
	
	
	
	
	
	
	
	
	
	

 	echo "
	<table><tr><td><br>
	<form name=chlog action=?headend=1&channel=$id method=post>
	<tr><td><br>new log: <td><input type=text name=notes size=60>
	<tr><td>reason: <td>
	
	<select name=reason>
	<option>channel changed frequency</option>
	<option>box down</option>
	<option>receiver stuck</option>
	<option>card subs down</option>
	<option>lost pairing</option>
	<option>no payment</option>
	<option>cancelled for streaming</option>
	<option>cancelled lost card</option>
	<option>cancelled faulty card</option>
	<option>faulty box</option>
	</select>
	
	<input type=submit name=insert_log value=update>
	</form>
	
	";
	
	echo "
	<tr><td>
	<tr><td>logs: <td>";
	$lines = `tail -20 /var/www/html/mon/channels/$id.txt`;
echo $lines;

	echo"</table>";
	
	
























	 
  }
 
 
 
 
 
 
 
 
 
 
// ######################## channel lists
 
 
  elseif($_GET['lists']==1)
 {
 	echo "<h3>Headend TV lists</h3><br>";
 


}


// ######################## servers
 

elseif($_GET['servers']==1)
 {
 	echo "<h3>Headend servers/equipment</h3><br>";

$popsel=$_GET['pop'];

echo"<form action=index.php method=get> 
<input type=hidden name=headend value=1>
<input type=hidden name=servers value=1>
<select name=pop onchange=\"submit();\"><option>";
$pops=$mon3->query("select OGR_FID,name from headend_pop order by OGR_FID");
while($pop=$pops->fetch_assoc())
{
	echo "<option value=".$pop['OGR_FID'];
	if($pop['OGR_FID']==$popsel) echo " selected ";
	echo " > ".$pop['name']."</option>";
}	

echo "</select></form>";

	
$q="select headend_equip.id,headend_equip.ip_addr,headend_equip.title,headend_equip.pop_id,headend_equip.location, headend_pop.name from headend_equip left join headend_pop on headend_equip.pop_id=headend_pop.OGR_FID ";
if($popsel>0)
{
$q.=" where headend_pop.OGR_FID=$popsel ";
}
$q.=" order by pop_id,ip_addr ";


	
$equips=$mon3->query($q);




echo "<table cellpadding=5>
 <tr> <th>id </th><th>title</th> <th>ip </th> <th>pop </th> <th>location</th>  
 ";
 
 while($equip=$equips->fetch_assoc())
 {
  
	echo "<tr><td align=center><a href=?headend=1&equips=".$equip['id'].">".$equip['id']."</a>
	<td>".$equip['title']."
	<td><a target=_blank href=http://".$equip['ip_addr'].">".$equip['ip_addr']."</a>
	<td>".$equip['name']."
	<td>".$equip['location']."
	";

	
	
	
	

	
 }
 


}


 //########### default view alarms/probe/graphs
 else
 {
  	echo "<h3>Headend</h3><br>";
 
 }
 
 