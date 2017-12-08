<html>
<head><title>Contoh Koneksi Mesin Absensi Mengunakan SOAP Web Service</title></head>
<body bgcolor="#caffcb">

<H3>Download Log Data</H3>

<?php
$IP = $_GET["ip"];
$Key = $_GET["key"];
if($IP=="") $IP="192.168.1.20";
if($Key=="") $Key="1234";
?>

<form action="getalluserinfo.php">
IP Address: <input type="Text" name="ip" value="<?php echo $IP?>" size=15><BR>
Comm Key: <input type="Text" name="key" size="5" value="<?php echo $Key?>"><BR><BR>

<input type="Submit" value="Download">
</form>
<BR>

<?php
if($_GET["ip"]!=""){?>
	<table cellspacing="2" cellpadding="2" border="1">
	<tr align="center">
	    <td><B>UserID</B></td>
	    <td><B>Name</B></td>
	    <td width="200"><B>Password</B></td>
	    <td><B>Group</B></td>
	</tr>
	<?php

	function Parse_Data($data,$p1,$p2){
		$data=" ".$data;
		$hasil="";
		$awal=strpos($data,$p1);
		if($awal!=""){
			$akhir=strpos(strstr($data,$p1),$p2);
			if($akhir!=""){
				$hasil=substr($data,$awal+strlen($p1),$akhir-strlen($p1));
			}
		}
		return $hasil;	
	}

	function Parse_Data_2($data2,$p1,$p2){
		$data2=" ".$data2;
		$hasil2="";
		$awal2=strpos($data2,$p1);
		if($awal2!=""){
			$akhir2=strpos(strstr($data2,$p1),$p2);
			if($akhir2!=""){
				$hasil2=substr($data2,$awal2+strlen($p1),$akhir2-strlen($p1));
			}
		}
		return $hasil2;	
	}

	$Connect = fsockopen($IP, "80", $errno, $errstr, 1);
	if($Connect){
		$soap_request="<GetAllUserInfo><ArgComKey xsi:type='xsd:integer'>".$Key."</ArgComKey></GetAllUserInfo>";
		$newLine="\r\n";
		fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
	    fputs($Connect, "Content-Type: text/xml".$newLine);
	    fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
	    fputs($Connect, $soap_request.$newLine);
		$buffer="";
		while($Response=fgets($Connect, 1024)){
			$buffer=$buffer.$Response;
		}
	}else {echo "Koneksi Gagal";}

	$Connect_2 = fsockopen($IP, "80", $errno, $errstr, 1);
	if($Connect_2){
		$soap_request_2="<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
		$newLine_2="\r\n";
		fputs($Connect_2, "POST /iWsService HTTP/1.0".$newLine_2);
	    fputs($Connect_2, "Content-Type: text/xml".$newLine_2);
	    fputs($Connect_2, "Content-Length: ".strlen($soap_request_2).$newLine_2.$newLine_2);
	    fputs($Connect_2, $soap_request_2.$newLine_2);
		$buffer_2="";
		while($Response=fgets($Connect_2, 1024)){
			$buffer_2=$buffer_2.$Response;
		}
	}else {echo "Koneksi Gagal";}
	
	
	$buffer = Parse_Data($buffer,"<GetAllUserInfoResponse>","</GetAllUserInfoResponse>");
	$buffer = explode("\r\n",$buffer);
	$buffer_2 = Parse_Data_2($buffer_2,"<GetAttLogResponse>","</GetAttLogResponse>");
	$buffer_2 = explode("\r\n",$buffer_2);

	for($a=0;$a<count($buffer_2);$a++){
		$data_2 = Parse_Data($buffer_2[$a],"<Row>","</Row>");
		$PIN_2 = Parse_Data($data_2,"<PIN>","</PIN>");
		$DateTime_2 = Parse_Data($data_2,"<DateTime>","</DateTime>");
		$Verified_2 = Parse_Data($data_2,"<Verified>","</Verified>");
		$Status_2 = Parse_Data($data_2,"<Status>","</Status>");

		if($data_2 != ""){
			$test_2[] = array(
				'id' => $PIN_2,
				'date_time' => $DateTime_2,
				'verified' => $Verified_2,
				'status' => $Status_2,
			);
		}
	}
	
	for($a=0;$a<count($buffer);$a++){
		$data=Parse_Data($buffer[$a],"<Row>","</Row>");
		$PIN=Parse_Data($data,"<PIN>","</PIN>");
		$Name=Parse_Data($data,"<Name>","</Name>");
		$Password=Parse_Data($data,"<Password>","</Password>");
		$Group=Parse_Data($data,"<Group>","</Group>");

		if($data != ""){
			$test[] = array(
				'id' => $PIN,
				'name' => $Name
			);
		}
	?>
	<tr align="center">
		    <td><?php echo $PIN ?></td>
		    <td><?php echo $Name ?></td>
		    <td><?php echo $Password ?></td>
		    <td><?php echo $Group ?></td>
		</tr>
	<?php } ?>
	</table>
<?php } 

for($i = 0;$i<count($test_2);$i++){
	for($j = 0;$j<count($test);$j++){
		if($test_2[$i]['id'] == $test[$j]['id']){
			$test_3[] = array(
				'id' => $test_2[$i]['id'],
				'name' => $test[$j]['name'],
				'date_time' => $test_2[$i]['date_time'],
				'verified' => $test_2[$i]['verified'],
				'status' => $test_2[$i]['status'],
			);
		}
	}
}

echo json_encode($test_3);

?>

</body>
</html>
