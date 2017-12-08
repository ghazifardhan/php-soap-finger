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
	}else echo "Koneksi Gagal";
	
	include("parse.php");
	$buffer=Parse_Data($buffer,"<GetAllUserInfoResponse>","</GetAllUserInfoResponse>");
	$buffer=explode("\r\n",$buffer);
	
	for($a=0;$a<count($buffer);$a++){
		$data=Parse_Data($buffer[$a],"<Row>","</Row>");
		$test[$a] = $data;
		$PIN=Parse_Data($data,"<PIN>","</PIN>");
		$Name=Parse_Data($data,"<Name>","</Name>");
		$Password=Parse_Data($data,"<Password>","</Password>");
		$Group=Parse_Data($data,"<Group>","</Group>");
	?>
	<tr align="center">
		    <td><?php echo $PIN ?></td>
		    <td><?php echo $Name ?></td>
		    <td><?php echo $Password ?></td>
		    <td><?php echo $Group ?></td>
		</tr>
	<?php } ?>
	</table>
<?php } ?>

</body>
</html>
