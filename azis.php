 <?php
 //--- Parameter: 1 = IP
 $IP='192.168.1.20';

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

 function Parse_Data2($data2,$p1,$p2){
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

function left($str, $length) {
     return substr($str, 0, $length);
}

function right($str, $length) {
     return substr($str, -$length);
}

 $fp=fopen('cardat.txt','a');
 $str="";
 fwrite($fp,$str,strlen($str));

 //------------ tarik data dari mesin
	
	$Key="1234";
	$buffer="";
	
	$Connect = fsockopen($IP, "80", $errno, $errstr, 1);
	if($Connect){
		$soap_request="<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
		$newLine="\r\n";
		fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
	    fputs($Connect, "Content-Type: text/xml".$newLine);
	    fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
	    fputs($Connect, $soap_request.$newLine);
		$buffer="";
		while($Response=fgets($Connect, 1024)){
			$buffer=$buffer.$Response;
		}		
	}else {
	  echo "Koneksi Gagal 1";
	  
	}

$ListNPK = array();

		$Connect2 = fsockopen($IP, "80", $errno, $errstr, 1);
		//if($Connect){
		$soap_request2="<GetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetUserInfo>";
		$newLine2="\r\n";
		fputs($Connect2, "POST /iWsService HTTP/1.0".$newLine2);
	    fputs($Connect2, "Content-Type: text/xml".$newLine2);
	    fputs($Connect2, "Content-Length: ".strlen($soap_request2).$newLine2.$newLine2);
	    fputs($Connect2, $soap_request2.$newLine2);
		$buffer2="";		
		while($Response2=fgets($Connect2, 1024)){
			$buffer2=$buffer2.$Response2;
		}		
	    $buffer2=Parse_Data2($buffer2,"<GetUserInfoResponse>","</GetUserInfoResponse>");
	    $buffer2=explode("\r\n",$buffer2);
	    for($a2=0;$a2<count($buffer2);$a2++){
		    $data2=Parse_Data2($buffer2[$a2],"<Row>","</Row>");
		    $PIN2=Parse_Data2($data2,"<PIN>","</PIN>");
		    $Name=Parse_Data2($data2,"<Name>","</Name>");
		    $IDCard=Parse_Data2($data2,"<Card>","</Card>");
			
		       //echo "NPK: ".$Name.PHP_EOL;
			   //$NPK=$Name;
			   $NPK="0".trim($IDCard);
		    $ListNPK[$PIN2] = $NPK;
	    }

	
	$str="";
	
	$buffer=Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
	$buffer=explode("\r\n",$buffer);
	for($a=0;$a<count($buffer);$a++){
		$data=Parse_Data($buffer[$a],"<Row>","</Row>");
		$PINx=Parse_Data($data,"<PIN>","</PIN>");
		$PIN=right("0000".Parse_Data($data,"<PIN>","</PIN>"),5);
		$Card="00000";
		$DateTime=Parse_Data($data,"<DateTime>","</DateTime>");
		$Verified=Parse_Data($data,"<Verified>","</Verified>");
		$Status=Parse_Data($data,"<Status>","</Status>");
		$NPK="";

		//----- ambil field name (npk)------------------------------------------------------
		if ($DateTime==""){}else{
			//echo "NPK: ".$Name.PHP_EOL;
			//$NPK=$Name;
			$NPK="0".$ListNPK[$PINx];

		//}else{
		    //echo "Koneksi Gagal 2";
		}		
		//-------------------------------------------------------------------------------------

		//echo "+";
		if ($DateTime=="" or $NPK==""){ echo "-";}else{
			echo "+";
		   $tgl=substr($DateTime,0,4)."-".substr($DateTime,5,2)."-".substr($DateTime,8,2);		

		   $Card=right($NPK,10);
		   if (strlen($Card)==10){
			   echo "*";
		   $str.="001".$Card.substr($DateTime,2,2).substr($DateTime,5,2).substr($DateTime,8,2).substr($DateTime,11,2).substr($DateTime,14,2).substr($DateTime,17,2).PHP_EOL;
		   }
		   
		   
		}
	}
 
 
 //-----------------------------------
 
 
 $fp=fopen('cardat.txt','a');
 fwrite($fp,$str,strlen($str));  
 
 
 
 ?>