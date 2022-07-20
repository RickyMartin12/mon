<?php



// http://mon.lazertelecom.com/webservice.php?dump_serv=1&
// http://mon.lazertelecom.com/webservice.php?dump_onts=1&
// http://mon.lazertelecom.com/webservice.php?dump_prop=1&
// http://mon.lazertelecom.com/webservice.php?dump_leads=1&

require_once 'connection.php';


session_start();
if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL mon3: " . mysqli_connect_error();
}



//
if($_GET['dump_serv']!="")
{
    $cons= $mon3->query("select * from connections where (date_end=\"0000-00-00\" or date_end=\"\") order by date_start");




    echo $mon3->error;
    $csv="";
    // echo "total: ".$cons->num_rows."<br>";

    while($con=$cons->fetch_assoc())
    {

        $ignore=$mon3->query("select conid from billing_ignored where conid=".$con['id']);
        if($ignore->num_rows==0)
        {

            //echo $con['id']."<br>";
            $prop= $mon3->query("select * from properties where id=".$con['property_id'])->fetch_assoc();
            echo $mon3->error;

            $servs=$mon3->query("select * from services where connection_id=".$con['id']." and (date_end=\"0000-00-00\" or date_end=\"\") ");
            if($servs->num_rows>0)
            {

                while($serv=$servs->fetch_assoc())
                {

                    if($serv['type']=="INT")
                    {
                        $speed=$mon3->query("select int_services.name from service_attributes left join int_services on service_attributes.value=int_services.id where service_attributes.service_id=".$serv['id']." and service_attributes.name=\"speed\"")->fetch_assoc();
                        echo $mon3->error;


                        $csv[]=$con['id'].";".$prop['ref'].";\"".$prop['address']."\";".$con['type'].";".$con['date_start'].";".$serv['type'].";".$speed['name'];
                    }
                    elseif($serv['type']=="PHN")
                    {
                        $nr=$mon3->query("select voip_accounts.caller_id from service_attributes left join voip_accounts on service_attributes.value=voip_accounts.username where service_attributes.service_id=".$serv['id']." and service_attributes.name=\"account\"")->fetch_assoc();
                        echo $mon3->error;

                        $csv[]= $con['id'].";".$prop['ref'].";\"".$prop['address']."\";".$con['type'].";".$con['date_start'].";".$serv['type'].";".$nr['caller_id'];
                    }
                    else
                    {
                        $csv[]= $con['id'].";".$prop['ref'].";\"".$prop['address']."\";".$con['type'].";".$con['date_start'].";".$serv['type'].";";
                    }



                }


            }





        }
    }


    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=services.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    foreach($csv as $line)
        echo $line."\n";

}





elseif($_GET['dump_onts']!="")
{





    $cons= $mon3->query("SELECT `fsan`,`serial`,ref,properties.id,connections.date_start FROM `ftth_ont` left join connections on ftth_ont.fsan=connections.equip_id left join properties on properties.id=connections.property_id WHERE 1 ORDER BY `connections`.`date_start` DESC");

    $csv[]="fsan;serial;ref;connection_id;date_start";
    while($con=$cons->fetch_assoc())
    {
        $csv[]=$con['fsan'].";".$con['serial'].";".$con['ref'].";http://mon.lazertelecom.com/index.php?props=1&propid=".$con['id'].";".$con['date_start'];
    }






    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=ONTs.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    foreach($csv as $line)
        echo $line."\n";



}










elseif($_GET['dump_prop']!="")
{

    $props= $mon3->query("select * from properties ");
    echo $mon3->error;
    if($props->num_rows>0)
    {

        while($prop=$props->fetch_assoc())
        {
            $csv[]="\"".$prop['ref']."\",\"".$prop['address']."\",\"".$prop['ref']."@lazerspeed.com\"";


        }
    }



    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=props.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    foreach($csv as $line)
        echo $line."\n";

}









elseif($_GET['dump_leads']!="")
{
    $csv[]="\"id\",  \"address\", \"freguesia\", \"coords\", \"name\", \"email\", \"phone\", \"agent_id\", \"status\", \"prop_id\", \"date_lead\", \"date_viability\", \"date_accept\", \"date_quoted\", \"date_papwk\", \"date_book\", \"date_install\", \"date_installed\", \"date_closed\", \"date_modified\", \"drop_length\", \"FAT_coords\", \"ORAC_pits\", \"ORAP_poles\", \"connection_cost\", \"is_network_ready\", \"network_cost\", \"estimated_quote\", \"timeframe\", \"quoted\", \"contract_id\", \"con_type\", \"fwa_id\", \"olt_id\", \"internet_prof\", \"fixed_ip\", \"tv\", \"phone1\", \"phone2\", \"aps\", \"install_price\", \"monthly_price\", \"model\", \"is_changeover\", \"prev_rev_month\", \"notes\", \"ORAP_id\", \"ORAC_id\", \"networking_job_id\", \"installation_job_id\", \"technician\", \"final_netw_cost\", \"final_inst_cost\", \"NPS_score\", \"manager_score\", \"has_pictures\", \"speedtest\", \"created_by\", \"is_active\"";

    $props= $mon3->query("SELECT `id`, `address`, `freguesia`, `coords`, `name`, `email`, `phone`, `agent_id`, `status`, `prop_id`, `date_lead`, `date_viability`, `date_accept`, `date_quoted`, `date_papwk`, `date_book`, `date_install`, `date_installed`, `date_closed`, `date_modified`, `drop_length`, `FAT_coords`, `ORAC_pits`, `ORAP_poles`, `connection_cost`, `is_network_ready`, `network_cost`, `estimated_quote`, `timeframe`, `quoted`, `contract_id`, `con_type`, `fwa_id`, `olt_id`, `internet_prof`, `fixed_ip`, `tv`, `phone1`, `phone2`, `aps`, `install_price`, `monthly_price`, `model`, `is_changeover`, `prev_rev_month`, `notes`, `ORAP_id`, `ORAC_id`, `networking_job_id`, `installation_job_id`, `technician`, `final_netw_cost`, `final_inst_cost`, `NPS_score`, `manager_score`, `has_pictures`, `speedtest`, `created_by`, `is_active` FROM `property_leads`");
    echo $mon3->error;
    if($props->num_rows>0)
    {

        while($prop=$props->fetch_assoc())
        {


            $csv[]="\"".$prop['id']."\",\"".$prop[ 'address']."\",\"".$prop[ 'freguesia']."\",\"".$prop[ 'coords']."\",\"".$prop[ 'name']."\",\"".$prop[ 'email']."\",\"".$prop[ 'phone']."\",\"".$prop[ 'agent_id']."\",\"".$prop[ 'status']."\",\"".$prop[ 'prop_id']."\",\"".$prop[ 'date_lead']."\",\"".$prop[ 'date_viability']."\",\"".$prop[ 'date_accept']."\",\"".$prop[ 'date_quoted']."\",\"".$prop[ 'date_papwk']."\",\"".$prop[ 'date_book']."\",\"".$prop[ 'date_install']."\",\"".$prop[ 'date_installed']."\",\"".$prop[ 'date_closed']."\",\"".$prop[ 'date_modified']."\",\"".$prop[ 'drop_length']."\",\"".$prop[ 'FAT_coords']."\",\"".$prop[ 'ORAC_pits']."\",\"".$prop[ 'ORAP_poles']."\",\"".$prop[ 'connection_cost']."\",\"".$prop[ 'is_network_ready']."\",\"".$prop[ 'network_cost']."\",\"".$prop[ 'estimated_quote']."\",\"".$prop[ 'timeframe']."\",\"".$prop[ 'quoted']."\",\"".$prop[ 'contract_id']."\",\"".$prop[ 'con_type']."\",\"".$prop[ 'fwa_id']."\",\"".$prop[ 'olt_id']."\",\"".$prop[ 'internet_prof']."\",\"".$prop[ 'fixed_ip']."\",\"".$prop[ 'tv']."\",\"".$prop[ 'phone1']."\",\"".$prop[ 'phone2']."\",\"".$prop[ 'aps']."\",\"".$prop[ 'install_price']."\",\"".$prop[ 'monthly_price']."\",\"".$prop[ 'model']."\",\"".$prop[ 'is_changeover']."\",\"".$prop[ 'prev_rev_month']."\",\"".$prop[ 'notes']."\",\"".$prop[ 'ORAP_id']."\",\"".$prop[ 'ORAC_id']."\",\"".$prop[ 'networking_job_id']."\",\"".$prop[ 'installation_job_id']."\",\"".$prop[ 'technician']."\",\"".$prop[ 'final_netw_cost']."\",\"".$prop[ 'final_inst_cost']."\",\"".$prop[ 'NPS_score']."\",\"".$prop[ 'manager_score']."\",\"".$prop[ 'has_pictures']."\",\"".$prop[ 'speedtest']."\",\"".$prop[ 'created_by']."\",\"".$prop[ 'is_active']."\" ";





        }
    }



    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename=props.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    foreach($csv as $line)
        echo $line."\n";

}



elseif($_GET['downloadgponlivestatus']!="")
{
//webservice.php?downloadgponlivestatus=1&olt=$olt&pon=$pon
    $pon=mysqli_real_escape_string($mon3,$_GET['pon']);
    $olt=mysqli_real_escape_string($mon3,$_GET['olt']);

    if($olt>0 && $pon!="")
    {



        $onts= $mon3->query("SELECT olt_id,ont_id,mng_ip,address FROM `ftth_ont` left join connections on ftth_ont.fsan=connections.equip_id left join properties on connections.property_id=properties.id
 WHERE olt_id=$olt AND ont_id like \"1-".$pon."%\"  ORDER BY olt_id,ont_id ");


        while($ont=$onts->fetch_assoc())
        {

            $rf="";
            $rx="";

            $ip=$ont['mng_ip'];

            $model = snmp2_get($ip, "ZhonePrivate", ".1.3.6.1.2.1.1.1.0","500000",2);
            $model=explode('"',$model);
            $model=explode(' ',$model[1]);
            $model=$model[0];
            if($model=="")
                $model="offline";
            else{
                $rf = snmp2_get($ip, "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.43.1.2.1.12.1","400000",2);
                $rf=explode('"',$rf);
                $rf=explode(' ',$rf[1]);
                $rf=$rf[0];

                $rx= snmp2_get($ip, "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.43.1.2.1.7.1","400000",2);
                $rx=explode('"',$rx);
                $rx=explode(' ',$rx[1]);
                $rx=$rx[0];
            }



            $csv[]= $ont['olt_id'].";".$ont['ont_id'].";$model;".$ont['address'].";".$ont['mng_ip'].";$rx;$rf";
        }








        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename=liveonts.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        foreach($csv as $line)
            echo $line."\n";


    }
}



































else
{







// JSON / webservice from here below



    header('Content-type: text/html; charset=UTF-8');
    if($_GET['ponsbyolt']!="")
    {
        $olt=mysqli_real_escape_string($mon3, $_GET['ponsbyolt']);

        $pons=$mon3->query("select card,pon,name from ftth_pons where olt_id=\"$olt\" order by name ");
        echo $mon3->error;
        // echo $olt;

        while($pon=$pons->fetch_assoc())
        {
            $ponb[]=array($pon['card']."-".$pon['pon'],$pon['name']);
        }

        //var_dump($ponb);
        echo json_encode($ponb, JSON_UNESCAPED_UNICODE);
    }


    elseif($_GET['cmtsbyolt']!="")
    {
        $cmts=mysqli_real_escape_string($mon3, $_GET['cmtsbyolt']);

        $cm=$mon3->query("select * from coax_cmts order by name ");
        echo $mon3->error;
        // echo $olt;

        while($c=$cm->fetch_assoc())
        {
            $cb[]=array($c['name'],$c['id']);
        }

        //var_dump($ponb);
        echo json_encode($cb, JSON_UNESCAPED_UNICODE);
    }

    elseif($_GET['ethbyolt']!="")
    {
        $eth=mysqli_real_escape_string($mon3, $_GET['ethbyolt']);

        $e=$mon3->query("select properties.ref,connections.id,properties.address,connections.type from properties left join connections on properties.id=connections.property_id where connections.type='ETH' order by properties.ref");
        echo $mon3->error;
        // echo $olt;

        while($e_s=$e->fetch_assoc())
        {
            $eb[]=array($e_s['ref']." ".$e_s['type'],$e_s['id']);
        }

        //var_dump($ponb);
        echo json_encode($eb, JSON_UNESCAPED_UNICODE);
    }



    elseif($_GET['vlansbyolt']!="")
    {
        $olt=mysqli_real_escape_string($mon3, $_GET['vlansbyolt']);

        $vlans=$mon3->query("select vlan,description,total_dynamic_ips from int_vlans where olt_id=\"$olt\" order by vlan ");

        while($vlan=$vlans->fetch_assoc())
        {
            $inuse=$mon3->query("select count(name) from service_attributes left join services on service_attributes.service_id=services.id where service_attributes.name=\"vlan\" and service_attributes.value=\"".$vlan['vlan']."\" and services.date_end=\"0000-00-00\" ")->fetch_assoc();

            $vlanb[]=array($vlan['vlan'],$vlan['description'],$vlan['total_dynamic_ips'],$inuse['count(name)']);
        }


        echo json_encode($vlanb, JSON_UNESCAPED_UNICODE);
    }






    elseif($_GET['customer']!="")
    {
        $cust=mysqli_real_escape_string($mon3, $_GET['customer']);
        $mng=mysqli_real_escape_string($mon3, $_GET['mng']);
        $query="select id,name,email,fiscal_nr from customers where ( name LIKE \"%$cust%\" OR email LIKE \"%$cust%\" OR fiscal_nr  LIKE \"%$cust%\" ) ";
        if ($mng==1)
            $query .= "AND is_management=1 ";

        $fregs=$mon3->query($query." order by name limit 0,10");
        //echo $query . mysqli_error($mon3);


    }





    elseif($_GET['getfreg']!="")
    {
        $conc=mysqli_real_escape_string($mon3, $_GET['conc']);
        // echo "list fregs $conc";
        $fregs=$mon3->query("select id,freguesia from freguesias where concelho=$conc order by freguesia");
        while($freg=$fregs->fetch_assoc())
        {
            //echo $freg['id'].$freg['freguesia'];
            $frg[]=array_map('utf8_encode',$freg);
        }

//	var_dump($frg);
        echo json_encode($frg, JSON_UNESCAPED_UNICODE);
    }

    elseif($_GET['getconcelho']!="")
    {
        $merge = array();
        $i=0;
        $concelho=mysqli_real_escape_string($mon3, $_GET['country']);
        // echo "list fregs $conc";
        $concs=$mon3->query('select * from concelhos where pais="'.$concelho.'" order by concelho');
        while($conc_e=$concs->fetch_assoc())
        {
            //echo $freg['id'].$freg['freguesia'];
            $co[]=array_map('utf8_encode',$conc_e);
            $merge = array('concelhos' => $co);
            $i++;




        }



        for($j=0; $j<$i; $j++)
        {
            //echo $co[$j]['id'];

            $q = 'select * from freguesias where concelho = '.$co[$j]['id'];
            $fregs=$mon3->query($q);
            while($freg=$fregs->fetch_assoc())
            {
                //echo $freg['id'].$freg['freguesia'];
                $fg[]=array_map('utf8_encode',$freg);
                $merge = array_merge($merge, ['freguesia' => $fg]);




            }
        }

        //var_dump($merge);

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }

    elseif($_GET['properties_id_leads']!="")
    {
        $merge = array();

        $coaxs=$mon3->query("select properties.id,properties.ref,properties.address from properties left join connections on connections.property_id=properties.id 
        order by properties.ref ");
        echo $mon3->error;
        // echo $olt;

        while($coax=$coaxs->fetch_assoc())
        {
            $coa[]=array($coax['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$coax['address'])),$coax['id']);
        }

        //var_dump($ponb);
        echo json_encode($coa, JSON_UNESCAPED_UNICODE);
    }

    elseif($_GET['prop_id_conn_id_type'] != '')
    {
        $i = 0;
        $merge = array();
        $wq='';
        $conn_id = $_GET['conn_id'];
        if($conn_id != 0)
        {
            $wq .= "AND connections.id = ".$conn_id;
        }

        $conn_di=$mon3->query("SELECT connections.id as 'connection_id', connections.type as 'type', 
connections.equip_id as 'eq', properties.ref as 'referencia' FROM `connections` 
    INNER JOIN properties ON connections.property_id = properties.id where 1 ".$wq);
        while($conn_di_each=$conn_di->fetch_assoc())
        {
            //echo $freg['id'].$freg['name'];
            $conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
            $merge = array('conexoes_diff' => $conn_di_each_b);
            $i++;
        }

        for($j=0; $j<$i; $j++)
        {
            if($conn_di_each_b[$j]['type'] != '')
            {
                $q .= ' AND type = "'.$conn_di_each_b[$j]['type'].'"';
            }

            $qquery = 'SELECT DISTINCT type from connections where type NOT IN(select DISTINCT type from connections where 1 '.$q.')';
            //echo $qquery."<br>";
            $fregs=$mon3->query($qquery);

            while($freg=$fregs->fetch_assoc())
            {

                //echo $freg['id'].$freg['freguesia'];
                $fg[]=array_map('utf8_encode',$freg);





            }


            $qd = 'SELECT DISTINCT type from connections where 1 '.$q;
            $qds=$mon3->query($qd);

            while($qd=$qds->fetch_assoc())
            {
                //echo $freg['id'].$freg['freguesia'];
                $qgd[]=array_map('utf8_encode',$qd);
            }

            $fg = array_map("unserialize", array_unique(array_map("serialize", $fg)));




            $type_conn_ant = $qgd[0]['type'];

            if($type_conn_ant == "GPON")
            {
                $array_conn_type_diff[]['type'] = 'FWA';
            }
            else if($type_conn_ant == "COAX")
            {
                $array_conn_type_diff[]['type'] = 'FWA';
                $array_conn_type_diff[]['type'] = 'GPON';
            }
            else if($type_conn_ant == "ETH")
            {
                $array_conn_type_diff[]['type'] = 'GPON';
            }
            else if($type_conn_ant == "FWA")
            {
                $array_conn_type_diff[]['type'] = 'GPON';
            }

            //var_dump($fg);
            $merge = array_merge($merge, ['tipo_conn' => $fg, 't_conn' => $qgd, 'conn_type_diff' => $array_conn_type_diff]);

        }


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }


    elseif($_GET['prop_id_owner'] != '')
    {
        $i = 0;
        $merge = array();
        $wq='';
        $prop_id = $_GET['prop_id'];
        if($prop_id != 0)
        {
            $wq .= "AND properties.id = ".$prop_id;
        }

        $conn_di=$mon3->query("SELECT customers.id,name, customers.fiscal_nr FROM `customers` 
    INNER JOIN properties ON customers.id = properties.owner_id where 1 ".$wq);
        while($conn_di_each=$conn_di->fetch_assoc())
        {
            $conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
            $merge = array('cust_prop' => $conn_di_each_b);
            $i++;
        }



        //$merge = array_merge($merge, ['tipo_conn' => $fg, 't_conn' => $qgd, 'conn_type_diff' => $array_conn_type_diff]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    elseif($_GET['owner_id_prop'] != '')
    {
        $i = 0;
        $merge = array();
        $wq='';
        $owner_id = $_GET['owner_id'];
        if($owner_id != 0)
        {
            $wq .= "AND owner_id = ".$owner_id;
        }

        $conn_di=$mon3->query("select properties.id,properties.ref,properties.address, connections.type
        from properties left join connections on connections.property_id=properties.id WHERE 1 ".$wq." order by properties.ref");
        while($conn_di_each=$conn_di->fetch_assoc())
        {
            $conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
            $merge = array('prop_cust' => $conn_di_each_b);
        }



        //$merge = array_merge($merge, ['tipo_conn' => $fg, 't_conn' => $qgd, 'conn_type_diff' => $array_conn_type_diff]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    elseif ($_GET['prop_conn_type_rec_7'] != '')
    {
        $type = $_GET['type'];
        $prop_id = $_GET['prop_id'];
        $merge = array();
        $n=0;
        $m=0;
        $wq='';
        $wq_prop='';
        $wq_subs_conn = '';
        //$owner_id = $_GET['con_type'];
        if($type != "")
        {
            $wq .= "AND property_leads.con_type = '".$type."'";
            $wq_subs_conn .= "AND con_type = '".$type."'";
            $wq_prop .= "AND connections.type = '".$type."'";
        }
        if($prop_id == "")
        {
            $prop_id = 0;
        }


        $query_prop_con = "SELECT property_leads.prop_id, property_leads.con_type, properties.owner_id  FROM property_leads inner join properties ON properties.id = property_leads.prop_id WHERE 1 ".$wq_subs_conn." AND prop_id=".$prop_id;

        $prop_con_owner = $mon3->query($query_prop_con);

        if($prop_con_owner->num_rows > 0)
        {
            $prop_owner = $prop_con_owner->fetch_assoc();
            $merge = array_merge($merge, ['prop_id' => $prop_owner['prop_id'], 'owner_id' => $prop_owner['owner_id']]);

        }
        else
        {
            // SE A CONEXAO DA PROPRIEDADE CORRESPONDE A LEAD DA CONEXAO (connections.type = lead.con_type)

            $query_con_prop_own = "select properties.id,properties.ref,properties.address, properties.owner_id from properties INNER JOIN connections ON connections.property_id = properties.id WHERE 1 ".$wq_prop;
            $prop_con = $mon3->query($query_con_prop_own);
            if($prop_con->num_rows > 0)
            {
                while($prop_con_o = $prop_con->fetch_assoc())
                {
                    //$prop_con_o_b[]=array_map('utf8_encode',$prop_con_o);

                    /*
                     * echo "<option value=".$coax['id'];

			if($prop['prop_id']==$coax['id']) echo " selected";
			echo "> ".$coax['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$coax['address']))."</option>";
                     */

                    $prop_con_o_b[]=array($prop_con_o['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$prop_con_o['address'])),$prop_con_o['id'], $prop_con_o['owner_id']);

                    $merge = array_merge($merge, ['prop_con_o_b' => $prop_con_o_b]);


                }

                $custs=$mon3->query("select id,name,fiscal_nr from customers order by name");
                while($cust=$custs->fetch_assoc())
                {
                    $owner_cust_b[]=array($cust['id']."-".addslashes($cust['name'])."#".$cust['fiscal_nr'],$cust['id']);

                    $merge = array_merge($merge, ['owner_cust_prop' => $owner_cust_b]);
                }


                //$custs=$mon3->query("select id,name,fiscal_nr from customers order by name");
                //	while($cust=$custs->fetch_assoc())
                //	{
                //        echo "<option value=".$cust['id'];
                //        if($owner['owner_id']==$cust['id']) echo " selected";
                //        echo ">". $cust['id']."-".addslashes($cust['name'])."#".$cust['fiscal_nr']."</option>";
                //    }


            }
            else
            {


                /*$query_prop = "select properties.id,properties.ref,properties.address
            from properties 
            INNER JOIN property_leads ON property_leads.prop_id = properties.id WHERE 1 ".$wq." order by properties.ref";
                $conn_prop_owner_rec = $mon3->query($query_prop);
                while($conn_prop_owner_rec_each=$conn_prop_owner_rec->fetch_assoc())
                {
                    //echo $conn_prop_owner_rec_each["id"];
                    $conn_prop_owner_rec_each_b[]=$conn_prop_owner_rec_each['id'];

                    //var_dump($conn_prop_owner_rec_each_b);
                    $n++;

                    $merge = array_merge($merge,['prop_id' => $conn_prop_owner_rec_each_b]);

                }


                for($i=0; $i<$n; $i++)
                {
                    //echo $conn_prop_owner_rec_each_b[$i];
                    $query_prop_owner = "SELECT id, owner_id FROM properties WHERE id =".$conn_prop_owner_rec_each_b[$i];
                    $query_prop_owner_rec = $mon3->query($query_prop_owner);
                    while($conn_prop_owner_rec_each=$conn_prop_owner_rec->fetch_assoc())
                    {

                    }
                }*/

                $conn_prop_owner_rec_each_b = array("","0");

                $merge = array_merge($merge,['prop_con_o_b_not_type' => $conn_prop_owner_rec_each_b]);
            }
        }

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);


    }


    // RECONNECTION

    // Propriedades do tipo de conxao coreespondente

    elseif($_GET['prop_conn_type'] != '')
    {
        $wq='';
        //$owner_id = $_GET['con_type'];
        if($_GET['con_type'] != 0)
        {
            $wq .= "AND connections.type = '".$_GET['con_type']."'";
        }

        $conn_di=$mon3->query("select properties.id,properties.ref,properties.address 
        from properties left join connections on connections.property_id=properties.id WHERE 1 ".$wq." order by properties.ref");


        $num_con = $conn_di->num_rows;

        if($num_con > 0)
        {
            while($conn_di_each=$conn_di->fetch_assoc())
            {

                $coa[]=array($conn_di_each['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$conn_di_each['address'])),$conn_di_each['id']);
                //$conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
                $merge = array('prop_conn_type' => $coa);
            }
        }
        else
        {
            $conn_non_conn=$mon3->query("select properties.id,properties.ref,properties.address 
        from properties left join connections on connections.property_id=properties.id WHERE 1 AND connections.type is null order by properties.ref");
            while($conn_non_conn_each=$conn_non_conn->fetch_assoc())
            {

                $coa[]=array($conn_non_conn_each['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$conn_non_conn_each['address'])),$conn_non_conn_each['id']);
                //$conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
                $merge = array('prop_conn_type' => $coa);
            }
        }




        //$merge = array_merge($merge, ['tipo_conn' => $fg, 't_conn' => $qgd, 'conn_type_diff' => $array_conn_type_diff]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }


    // Propriedades do tipo de conxao do serviço da conexao que estao desativados


    elseif($_GET['prop_conn_type_serv_des'] != '')
    {
        $wq = '';
        $wq_not_des_serv = '';
        //$owner_id = $_GET['con_type'];
        if ($_GET['con_type'] != "") {
            $wq .= " AND connections.type = '" . $_GET['con_type'] . "'";
        }

        $conn_serv_des=$mon3->query("select DISTINCT properties.id as 'prop_id',properties.ref as 'ref_prop',
        properties.address as 'prop_addr', connections.id as 'conn_id', 
        connections.type as 'conn_type', connections.equip_id as 'conn_equip_id'
        from properties left join connections on connections.property_id=properties.id 
        left join services on services.connection_id=connections.id where 1".$wq." 
        AND services.date_end != '0000-00-00'
        order by properties.ref ASC, services.id DESC");


        $num_con_serv_des = $conn_serv_des->num_rows;

        if($num_con_serv_des > 0)
        {
            $merge = array();
            while($conn_di_each=$conn_serv_des->fetch_assoc())
            {

                $coa[]=array($conn_di_each['ref_prop']."-".addslashes(str_replace(array("\n", "\r"), '',$conn_di_each['prop_addr']))." - Connection:".$conn_di_each['conn_id'],$conn_di_each['prop_id'], $conn_di_each['conn_id'],$conn_di_each['serv_id'] );
                //$conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
                $merge = array('prop_conn_type' => $coa);
            }

            $merge = array('prop_conn_type' => $coa);
        }
        else
        {
                $merge = array();
                $num_con_prop_des = 0;
                $num_con_prop_ser_des = 0;
                $i=0;
                $j=0;
                $t=0;
                if ($_GET['con_type'] != "") {
                    $wq_not_des_serv .= " AND con_type = '" . $_GET['con_type'] . "'";
                }

            // PROPERTIES

                $prop_ser_not_des_serv=$mon3->query("SELECT * FROM property_leads WHERE 1".$wq_not_des_serv);
                while($prop_ser_not_des_serv_each=$prop_ser_not_des_serv->fetch_assoc())
                {

                    $prop_ser_not_des_serv_each_b[]=array_map('utf8_encode',$prop_ser_not_des_serv_each);
                    $merge = array('lead_prop' => $prop_ser_not_des_serv_each_b);
                    $i++;
                    $prop = $prop_ser_not_des_serv_each['prop_id'];
                    $text_not_exists = "Not Exists Disabled Services on properties number ".$prop;
                    $coa[]=array($text_not_exists,$prop);
                }

            // CONNECTIONS

            for($l=0; $l<$i; $l++)
            {
                //echo $prop_ser_not_des_serv_each_b[$l]['prop_id']."<br>";
                $con_ser_not_des_serv=$mon3->query("SELECT * FROM connections WHERE property_id = ".$prop_ser_not_des_serv_each_b[$l]['prop_id']);
                $num_con_prop_des += $con_ser_not_des_serv->num_rows;

                    while($con_ser_not_des_serv_each=$con_ser_not_des_serv->fetch_assoc())
                    {

                        //echo $con_ser_not_des_serv_each['id'];
                        $con_ser_not_des_serv_each_b[]=array_map('utf8_encode',$con_ser_not_des_serv_each);
                        $merge = array_merge($merge, ['con_serv_not_des' => $con_ser_not_des_serv_each_b]);
                        $j++;

                        $prop = $con_ser_not_des_serv_each['property_id'];
                        $conn = $con_ser_not_des_serv_each['id'];
                        //echo "Not Exists Disabled Services on properties number ".$prop." ".$conn;
                        $text_not_exists = "Not Exists Disabled Services on properties number ".$prop." , connection number ".$conn;
                        $coas[]=array($text_not_exists,$prop);
                    }

            }

            // SERVICES

            for($k=0; $k<$j; $k++)
            {
                //echo $con_ser_not_des_serv_each_b[$k]['id'];
                $ser_not_des_serv=$mon3->query("SELECT connections.property_id as 'prop_id', connections.id as 'conn_id', services.id as 'serv_id' FROM services INNER JOIN connections ON connections.id = services.connection_id WHERE connection_id = ".$con_ser_not_des_serv_each_b[$k]['id']);
                $num_con_prop_ser_des += $ser_not_des_serv->num_rows;
                while($ser_not_des_serv_each=$ser_not_des_serv->fetch_assoc())
                {
                    $ser_not_des_serv_each_b[]=array_map('utf8_encode',$ser_not_des_serv_each);
                    $merge = array_merge($merge, ['serv_not_des' => $ser_not_des_serv_each_b]);
                    $t++;

                    $prop = $ser_not_des_serv_each['prop_id'];
                    $conn = $ser_not_des_serv_each['conn_id'];
                    $serv = $ser_not_des_serv_each['serv_id'];

                    $text_not_exists = "Not Exists Disabled Services on properties number ".$prop." , connection number ".$conn." , services number ".$serv;
                    $coas_services[]=array($text_not_exists,$prop);

                    //echo $ser_not_des_serv_each['connection_id']." ".$ser_not_des_serv_each['id']."<br>";
                }


            }


                if($prop_ser_not_des_serv->num_rows <= 0)
                {
                    $coas_zero[]=array("Not Existe Properties on this type connection in this lead","0");
                    $merge = array('prop_conn_type_not_bel' => $coas_zero);
                }
                else
                {

                    //echo $num_con_prop_des;
                    if($num_con_prop_des <= 0)
                    {
                        $merge = array('prop_conn_type_not_bel' => $coa);
                    }
                    else
                    {
                        if($num_con_prop_ser_des <= 0)
                        {
                            $merge = array('prop_conn_type_not_bel' => $coas);
                        }
                        else
                        {
                            $merge = array('prop_conn_type_not_bel' => $coas_services);
                        }
                    }

                }


                //$conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);

               //$merge = array('prop_conn_type_not_bel' => $coas_services);




        }


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);



    }

    elseif($_GET['con_prop_rec'] != "")
    {
        $merge = array();
        $wq='';
        if($_GET['con_type'] != '')
        {
            $wq .= " AND type = '".$_GET['con_type']."'";
        }
        $qs = "SELECT * FROM connections where 1".$wq." AND property_id = ".$_GET['prop_id'];
        $conns_prop=$mon3->query($qs);



        $merge = array_merge($merge, ['con_assoc' => $conns_prop->num_rows]);


        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    elseif($_GET['con_prop_serv_des'] != "")
    {
        $merge = array();
        $i=0;
        $wq='';
        if($_GET['con_type'] != '')
        {
            $wq .= " AND type = '".$_GET['con_type']."'";
        }

        //echo $_GET['con_type'];

        // Connections
        $qs = "SELECT * FROM connections where 1".$wq." AND property_id = ".$_GET['prop_id'];
        $conns_prop=$mon3->query($qs)->fetch_assoc();

        $conns_p = $conns_prop['id'];

        if($conns_p != "")
        {
            $merge = array_merge($merge, ['conn_id' => $conns_p]);
        }

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);




    }

    elseif($_GET['conn_prop_id_services_equal'] != "")
    {
        $type = $_GET['type'];


    }


    elseif($_GET['prop_id_type_connection'] != "")
    {
        $merge = array();
        $i=0;
        $wq='';
        if($_GET['prop_id'] != 0)
        {
            $wq .= "AND property_id = ".$_GET['prop_id'];
        }
        $coaxs=$mon3->query("SELECT connections.id as 'connection_id', connections.type as 'type', 
connections.equip_id as 'eq', properties.ref as 'referencia' FROM `connections` 
    INNER JOIN properties ON connections.property_id = properties.id where 1 ".$wq);
        while($coax=$coaxs->fetch_assoc())
        {
            //echo $freg['id'].$freg['name'];
            $custb[]=array_map('utf8_encode',$coax);
            $merge = array('conexoes' => $custb);
            $i++;
        }

        //var_dump($custb);

        if($custb != NULL)
        {

            $q = '';
            //var_dump($custb);
            for($j=0; $j<$i; $j++)
            {
                if($custb[$j]['connection_id'] != '1')
                {
                    $q .= ' AND id = '.$custb[$j]['connection_id'];
                }

                if($custb[$j]['type'] != '')
                {
                    $q .= ' AND type = "'.$custb[$j]['type'].'"';
                }

                // GPON

                // COAX

                //

                $qquery = 'SELECT DISTINCT type from connections where type NOT IN(select DISTINCT type from connections where 1 '.$q.')';
                //echo $qquery."<br>";
                $fregs=$mon3->query($qquery);

                while($freg=$fregs->fetch_assoc())
                {

                    //echo $freg['id'].$freg['freguesia'];
                    $fg[]=array_map('utf8_encode',$freg);





                }


                $qd = 'SELECT DISTINCT type from connections where 1 '.$q;
                $qds=$mon3->query($qd);

                while($qd=$qds->fetch_assoc())
                {
                    //var_dump($qd);
                    //echo $freg['id'].$freg['freguesia'];
                    $qgd[]=array_map('utf8_encode',$qd);

                }

                // Susbcriber

                $wq_subs='';
                $prop_id = $_GET['prop_id'];
                if($prop_id != 0)
                {
                    $wq_subs .= "AND properties.id = ".$prop_id;
                }

                $conn_di=$mon3->query("SELECT customers.id,name, customers.fiscal_nr FROM `customers` 
    INNER JOIN properties ON customers.id = properties.owner_id where 1 ".$wq_subs);
                while($conn_di_each=$conn_di->fetch_assoc())
                {
                    $conn_di_each_b[]=array_map('utf8_encode',$conn_di_each);
                }


            }
            $fg = array_map("unserialize", array_unique(array_map("serialize", $fg)));





            //var_dump($fg);
            $merge = array_merge($merge, ['tipo_conn' => $fg, 't_conn' => $qgd, 'subs' => $conn_di_each_b]);

        }
        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    elseif($_GET['initial_equip_con_prop'] != "")
    {
        //'type': type, 'prop_id': prop_id, 'con_id': con_id
        $type = $_GET['type'];
        $prop_id = $_GET['prop_id'];
        $conexao=$mon3->query("SELECT * FROM connections where property_id = ".$prop_id." AND type='".$type."'")->fetch_assoc();
        $merge = array();

        // Equipamento
        $equip_id = $conexao['equip_id'];

        // Conexao
        $conn_id = $conexao['id'];

        if ($type == "GPON")
        {
            $modelo = $mon3->query("SELECT * FROM ftth_ont where fsan = '".$equip_id."'")->fetch_assoc();
            // CPE Model
            $cpe_model = $modelo['model'];


            // OLT
            $olt = $modelo['olt_id'];


            // PON
            $pons = $mon3->query("SELECT * FROM ftth_pons where olt_id = ".$olt." order by name ");


            while($pon=$pons->fetch_assoc())
            {
                $pon_select[]=array_map('utf8_encode',$pon);
            }


            $merge = array_merge($merge, ['equip_id' => $equip_id, 'cpe_model' => $cpe_model, 'olt' => $olt, 'pon' => $pon_select]);



        }

        else if($type == "FWA")
        {
            // Equipamento
            $equip_id = $conexao['equip_id'];

            // Conexao
            $conn_id = $conexao['id'];




            // CPE Model

            // FWA CPE
            $modelo = $mon3->query("SELECT * FROM fwa_cpe WHERE mac = '".$equip_id."'")->fetch_assoc();


            // FWA antenna
            $antennas = $mon3->query("SELECT * FROM fwa_antennas where id = ".$modelo['antenna']." order by name ")->fetch_assoc();






            $merge = array_merge($merge, ['equip_id' => $equip_id, 'model_fwa' => $modelo['model'], 'antenna' => $antennas['id']]);





        }

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);




    }


    elseif($_GET['equip_connection_assoc'] != "")
    {
        $merge = array();
        $equip_id = $_GET['equip_id'];

        $type = $_GET['type'];

        $msg = "";

        $eq_check = 0;

        // VER OS EQUIPAMENTOS DAS CONEXOES QUE PERTENCEM NA BD

        if($type == "GPON")
        {
            // GPON
            $onts_num = $mon3->query("SELECT * FROM ftth_ont WHERE fsan = '".$equip_id."'")->num_rows;
            if($onts_num > 0)
            {
                $conn_num_valid = $mon3->query("SELECT * FROM connections WHERE equip_id = '".$equip_id."' AND type='GPON'");
                $conn_fsan = $conn_num_valid->fetch_assoc();
                if($conn_num_valid->num_rows >= 1)
                {
                    $msg .= "<font color=red>This Equipment ".$equip_id." is already associated the connection number ".$conn_fsan['id']." please choose a equipment which has not associated to this connection on equipment</font>";
                    $eq_check = 0;
                }
                else
                {
                    $msg .= "<font color=cyan>This ONT doesn't associate on connections list</font>";
                    $eq_check = 1;
                }
            }
            else
            {
                $msg .= "<font color=blue>THe ONT needs to be created in this connection</font>";
                $eq_check = 2;
            }
        }

        else if($type == "FWA")
        {
            // FWA_CPE
            $fwa_mac = $mon3->query("SELECT * FROM fwa_cpe WHERE mac = '".$equip_id."'")->num_rows;
            $fwa_antenna = $mon3->query("SELECT * FROM fwa_antennas WHERE mac = '".$equip_id."'")->num_rows;
            if($fwa_mac > 0)
            {
                $conn_num_valid = $mon3->query("SELECT * FROM connections WHERE equip_id = '".$equip_id."' AND type='FWA'");
                $conn_fsan = $conn_num_valid->fetch_assoc();
                if($conn_num_valid->num_rows >= 1)
                {
                    $msg .= "<font color=red>This Equipment ".$equip_id." is already associated the connection number ".$conn_fsan['id']." please choose a equipment which has not associated to this connection on equipment</font>";
                    $eq_check = 0;
                }
                else
                {
                    $msg .= "<font color=cyan>This FWA CPE/Antenna doesn't associate on connections list</font>";
                    $eq_check = 1;
                }
            }
            else
            {
                $msg .= "<font color=blue>The FWA CPE/Antenna needs to be created in this connection</font>";
                $eq_check = 2;
            }

        }

        $merge = array_merge($merge, ['msg' => $msg, 'eq_check' => $eq_check]);

        echo json_encode($merge, JSON_UNESCAPED_UNICODE);

    }

    elseif($_GET['conn_prop_id_diff'] != "")
    {
        $type = $_GET['type'];

        $props=$mon3->query("select properties.id,properties.ref,properties.address 
        from properties left join connections on connections.property_id=properties.id where connections.type!='".$type."' order by properties.ref");

        while($prop=$props->fetch_assoc())
        {
            //var_dump($qd);
            //echo $freg['id'].$freg['freguesia'];
            $ref = $prop['ref']."-".addslashes(str_replace(array("\n", "\r"), '',$prop['address']));
            $ponb[]=array($ref,$prop['id']);

        }

        echo json_encode($ponb, JSON_UNESCAPED_UNICODE);

    }

    elseif($_GET['prop_id_type_connection_nova_conexao'] != "")
    {
        $merge = array();
        $i=0;
        $wq='';

        $con_type = $_GET['con_type'];

        if($con_type == "GPON")
        {
            // OLT

            $olts=$mon3->query("select * from ftth_olt");

            while($olt=$olts->fetch_assoc())
            {
                $oltb[]=array_map('utf8_encode',$olt);
                $merge = array('olt' => $oltb);
                $i++;
            }
            // PON
            if($oltb != NULL)
            {
                for($j=0; $j<$i; $j++)
                {
                    if($oltb[$j]['id'] != '')
                    {
                        $q = 'AND olt_id = '.$oltb[$j]['id'];
                    }
                    $pons=$mon3->query("select card,pon,name from ftth_pons where 1 ".$q." order by name ");
                    while($pon=$pons->fetch_assoc())
                    {
                        $pg[]=array_map('utf8_encode',$pon);
                    }
                }

                $merge = array_merge($merge, ['pon' => $pg]);
            }
        }

        else if($con_type == "COAX")
        {
            // CMTS

            $cmtss=$mon3->query("select * from coax_cmts");
            while($cmts=$cmtss->fetch_assoc()){
                $cmtsb[]=array_map('utf8_encode',$cmts);
                //$merge = array('cmts' => $cmtsb);
                $merge = array_merge($merge, ['cmts' => $cmtsb]);
                $i++;
            }
        }

        else if($con_type == "ETH")
        {
            // CMTS

            $propsv=$mon3->query("select properties.ref,connections.id,properties.address,connections.type from properties left join connections on properties.id=connections.property_id where connections.date_end=\"0000-00-00\" order by properties.ref");
            while($propv=$propsv->fetch_assoc())
            {
                $propvb[]=array_map('utf8_encode',$propv);
                //$merge = array('propv' => $propvb);
                $merge = array_merge($merge, ['propv' => $propvb]);
                $i++;
            }
        }



        echo json_encode($merge, JSON_UNESCAPED_UNICODE);
    }

    elseif($_GET['conexao_id_prop_equ'] != "")
    {
        $m = array();
        $con = $_GET['conn_id'];
        $tipo = $_GET['tipo'];



        $wq='';
        if($con != '')
        {
            $wq .= " AND id = ".$con;
        }

        //echo "SELECT * from connections where 1 ".$wq;
        $conn=$mon3->query("SELECT * from connections where 1".$wq);



        // TIPO de conexao
        if ($tipo != '') {
            $wq .= " AND type = '" . $tipo . "'";

        }


        while($con=$conn->fetch_assoc())
        {
            //echo $freg['id'].$freg['freguesia'];
            $co[]=array_map('utf8_encode',$con);
            $equip = $con['equip_id'];
        }
        $m = array_merge($m, ['equip' => $equip]);

        $conn_diff=$mon3->query("SELECT DISTINCT type from connections where type NOT IN( SELECT DISTINCT type from connections where 1".$wq.")");
        while($conn_diff_te=$conn_diff->fetch_assoc()) {
            //echo $freg['id'].$freg['freguesia'];
            $conn_diff_te_array[] = array_map('utf8_encode', $conn_diff_te);
            $m = array_merge($m, ['conn_diff' => $conn_diff_te_array]);


        }

        foreach($m['conn_diff'] as $con_dof)
        {
            //echo $con_dof["type"]."<br>";

            if ($con_dof["type"] == "GPON")
            {
                //$eq = $mon3->query("SELECT * FROM ftth_ont WHERE fsan ='".$equip."'");

                $ont=$mon3->query("select * from ftth_ont")->fetch_assoc();

                $ont_x=explode("-",$ont['ont_id']);
                $olt=$mon3->query("select * from ftth_olt where id=\"".$ont['olt_id']."\" ;")->fetch_assoc();


                $m = array_merge($m, ['equip' => $equip, 'olt' => $olt['name'], 'ont_id' => $ont['ont_id'], 'olt_id' => $ont['olt_id']]);


            }

            if ($con_dof["type"] == "COAX")
            {
                // Modem

                $modems=$mon3->query("select * from coax_modem;");

                while($modem=$modems->fetch_assoc()) {
                    $modem_equip[] = array_map('utf8_encode', $modem);
                    $m = array_merge($m, ['equip_mac_array' => $modem_equip]);
                }



                //$m = array_merge($m, ['equip_mac' => $modem['mac'], 'cmts_id' => $modem['cmts']]);
            }

            if ($con_dof["type"] == "ETH")
            {
                // ETH
                $eth=$mon3->query("select * from ftth_eth ;")->fetch_assoc();


                $m = array_merge($m, ['equip_mac_eth' => $eth['mac'], 'cmts_id' => $eth['model']]);

            }
        }












        echo json_encode($m, JSON_UNESCAPED_UNICODE);













    }



    elseif($_GET['connection_prop'] != "")
    {
        $merge = array();
        $i=0;
        $wq='';
        if($_GET['prop_id'] != 0)
        {
            $wq .= "AND property_id = ".$_GET['prop_id']." ";
        }



        $coaxs=$mon3->query("SELECT connections.id as 'connection_id', connections.type as 'type', connections.equip_id as 'eq', 
        properties.ref as 'referencia' FROM `connections` INNER JOIN properties 
            ON connections.property_id = properties.id where 1 ".$wq." AND type='".$_GET['type']."'");
        while($coax=$coaxs->fetch_assoc())
        {
            //echo $freg['id'].$freg['name'];
            $custb[]=array_map('utf8_encode',$coax);
        }

        //var_dump($frg);
        echo json_encode($custb, JSON_UNESCAPED_UNICODE);
    }





    elseif($_GET['searchprop']!="")
    {
        $str=mysqli_real_escape_string($mon3, $_GET['searchprop']);
        if(isset($_GET['offset']))
            $offset=mysqli_real_escape_string($mon3, $_GET['offset']);
        else
            $offset=0;

        if(strlen($str)<2)
        {
            $props=$mon3->query("select id,ref,address from properties order by ref limit ".$offset.",30 ");
            $count=$mon3->query("select count(*) from properties")->fetch_row();
        }else
        {
            $props=$mon3->query("select id,ref,address from properties where address LIKE '%".$str."%' or ref LIKE '%".$str."%' order by ref limit 0,30 ");
            $count=$mon3->query("select count(*) from properties where address LIKE '%".$str."%' or ref LIKE '%".$str."%'")->fetch_row();
        }
//	echo "error: ".mysqli_error($mon3);
        $props_array[0]= array("id"=>"total", "ref"=> $count[0], "address"=>"");
        while($prop=$props->fetch_assoc())
        {
            array_push($props_array, $prop);
        }
//	print_r($props_array);
        echo json_encode($props_array);
    }





    elseif($_GET['searchlead']!="")
    {
        $str=mysqli_real_escape_string($mon3, $_GET['searchlead']);
//	echo "searching for:".$str;
        if(isset($_GET['offset']))
            $offset=mysqli_real_escape_string($mon3, $_GET['offset']);
        else
            $offset=0;

        if(strlen($str)<2)
        {
            $props=$mon3->query("select * from property_leads order by date limit ".$offset.",30 ");
            $count=$mon3->query("select count(*) from property_leads")->fetch_row();
        }else
        {
            $props=$mon3->query("select * from property_leads where address LIKE '%".$str."%' or id LIKE '%".$str."%' or name LIKE '%".$str."%' order by date limit 0,30 ");
            $count=$mon3->query("select count(*) property_leads where address LIKE '%".$str."%' or id LIKE '%".$str."%' or name LIKE '%".$str."%' order by date ")->fetch_row();
        }
//	echo "error: ".mysqli_error($mon3);
        $props_array[0]= array("id"=>"total", "address"=> $count[0]);
        while($prop=$props->fetch_assoc())
        {
            array_push($props_array, $prop);
        }
//	print_r($props_array);
        echo json_encode($props_array);
    }








    elseif($_GET['status_ont']!="")
    {
        $status['date']=date("Y-m-d H:i:s");
        $olt=mysqli_real_escape_string($mon3, $_GET['olt']);
        $ont=mysqli_real_escape_string($mon3, $_GET['ont']);
        $ont_a=explode("-",$ont);
        $ont_= $mon3->query("select * from ftth_ont where olt_id=$olt AND ont_id=\"$ont\" ")->fetch_assoc();
        $olt_ip = $mon3->query("select ip from ftth_olt where id=$olt")->fetch_assoc();
        $msg="";



        /*$rf = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.43.1.2.1.12.1","100000",2);
        $rf=explode('"',$rf);
        $rf=explode(' ',$rf[1]);
        $status['rf']=$rf[0];

        $rx = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.43.1.2.1.7.1","100000",2);
        $rx=explode('"',$rx);
        $rx=explode(' ',$rx[1]);
        $status['rx']=$rx[0];

        $rx = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.41.1.6.2","100000",2);
        $rx=explode(':',$rx);
        $status['ip_voip']=$rx[1];

        $rx = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.41.1.6.2","100000",2);
        $rx=explode(':',$rx);
        $status['ip_wan']=$rx[1];


        $status['ip_mng']=$ont_['mng_ip'];


        $rx = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.2.1.1.1","100000",2);
        $rx=explode('"',$rx);
        $status['model']=$rx[1];


        $sw = snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.2.5.2.1.1.21","100000",2);
        $sw=explode('"',$sw);
        $status['sw']=trim($sw[1]);

        $uptime=snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.2.1.1.3.0","100000",2);
        $uptime=explode(')',$uptime);
        $status['uptime']=trim($uptime[1]);

        $uptime=snmp2_get($ont_['mng_ip'], "ZhonePrivate", ".1.3.6.1.4.1.5504.3.1.21.1.3.1.1.3","100000",2);
        $uptime=explode('"',$uptime);
        $status['fsan']=trim($uptime[1]);*/






        $status['msg']=$msg;
        echo json_encode($status);

    }







    elseif($_GET['status_modem']!="")
    {
        $status['date']=date("Y-m-d H:i:s");
        $olt=mysqli_real_escape_string($mon3, $_GET['cmts']);
        $ont=mysqli_real_escape_string($mon3, $_GET['modem']);
        $ont_= $mon3->query("select * from coax_modem where mac=\"$ont\" ")->fetch_assoc();
        $olt_ip = $mon3->query("select ip from coax_cmts where id=$olt")->fetch_assoc();
        $status['uptime']=snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.1.3.0",500000);

        if($status['uptime']!="")
        {
            $msg="";

            $status['model']=htmlspecialchars(snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.1.1.0",1000000));
            $status['uptime']=snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.1.3.0",1000000);
            $status['phone']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.4.1.4491.2.1.14.1.1.2.1.2.16",1000000))[1];
            $status['bootfile']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.69.1.4.5.0",1000000))[1];
            $status['ssid']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.4.1.8595.80211.5.1.14.1.3.1",1000000))[1];
            $status['pass']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.4.1.8595.80211.5.2.4.1.2.1",1000000))[1];

            $status['pubip']=explode(":",snmpgetnext($ont_['mng_ip'],"public",".1.3.6.1.2.1.4.22.1.3.1",1000000))[1];
            $status['ds_freq']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.10.127.1.1.1.1.2.3",1000000))[1]/1000000 ."MHz";
            $status['us_freq']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.10.127.1.1.2.1.2.4",1000000))[1]/1000000 ."MHz";

            $status['us_lev']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.10.127.1.2.2.1.3.2",1000000))[1]/10 ."dBm";


            $status['ds_lev']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.10.127.1.1.1.1.6.3",1000000))[1]/10 ."dBm";
            $status['ds_snr']=explode(":",snmpget($ont_['mng_ip'],"public",".1.3.6.1.2.1.10.127.1.1.4.1.5.3",1000000))[1]/10 ."dB";



//	.1.3.6.1.2.1.10.127.1.1.1.1.2.3

        }
        $status['msg']=$msg;
//	var_dump($status);
        echo json_encode($status);

    }


    elseif($_GET['reboot_modem']!="")
    {
        $status['date']=date("Y-m-d H:i:s");
        $olt=mysqli_real_escape_string($mon3, $_GET['cmts']);
        $ont=mysqli_real_escape_string($mon3, $_GET['modem']);
        $ont_= $mon3->query("select * from coax_modem where mac=\"$ont\" ")->fetch_assoc();
        $olt_ip = $mon3->query("select ip from coax_cmts where id=$olt")->fetch_assoc();
        $msg="";

        $msg .= snmpset($ont_['mng_ip'], "private", ".1.3.6.1.2.1.69.1.1.3.0","i",1);


        $status['msg']=$msg;
//	var_dump($status);
        echo json_encode($status);
    }






















    else{}

}






$mon3->close();
