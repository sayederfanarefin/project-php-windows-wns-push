<?php
include_once 'wpn.php';
$channel = '';
if($_POST['check_uri'] == "true")
{
	check_uri();
}
function check_uri()
{
	global $dbh;
	global $ChannelUri;
	$stmt1 = $dbh->prepare("select ChannelUri from wns where ChannelUri= :ChannelUri");
	$stmt1->bindParam(':ChannelUri',$ChannelUri,PDO::PARAM_STR);
	$stmt1->execute();
	$result = $stmt1->fetchAll();
	foreach($result as $row)
	{
		$channel = $row['ChannelUri'];
	}
	$uricheck = $stmt1->rowCount();
	if($uricheck == 1)
	{
		$data = array("uri_exists"=>"true");
		echo json_encode($data);
	}
	else
	{
		$data = array("uri_exists"=>"false");
		echo json_encode($data);
		register_wns();
	}
}
function register_wns()
{
		global $dbh;
		global $ChannelUri;
		global $channel;

        // Set POST request variable
        $url = 'https://login.live.com/accesstoken.srf';

        $fields = array(
            'grant_type' => urlencode('client_credentials'),
            'client_id' => urlencode(SID),
            'client_secret' => urlencode(CLIENT_SECRET),
            'scope' => urlencode('notify.windows.com')
        );
		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded'
        );
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // disable SSL certificate support
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);


        // execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
		$obj = json_decode($result);
		$access_token = $obj->{'access_token'};
		$token_type = $obj->{'token_type'};
        // Close connection
        curl_close($ch);
        //echo $result;
	    if($channel == '')
		{
			$stmt1 = $dbh->prepare("insert into wns(ChannelUri) values(:ChannelUri)");
			$stmt1->bindParam(':ChannelUri',$ChannelUri,PDO::PARAM_STR);
			$stmt1->execute();
		}
		else
		{
			$stmt1 = $dbh->prepare("update wns set ChannelUri = :ChannelUri where ChannelUri= :channel ");
			$stmt1->bindParam(':ChannelUri',$ChannelUri,PDO::PARAM_STR);
			$stmt1->bindParam(':channel',$channel,PDO::PARAM_STR);
			$stmt1->execute();
		}
}
function notify_wns_users($message,$img,$subject)
{
	global $dbh;
	if($img != "")
	{
		$xml_data = '<toast>
			<visual>
				<binding template="ToastImageAndText04">
					<text id="1">'.$subject.'</text>
					<text id="2">'.$message.'</text>
				</binding>
			</visual>
		</toast>';
	}
	else
	{
			$xml_data = '<toast launch="'.$link_suffix.'">
		<visual>
			<binding template="ToastImageAndText04">
				<image id="1" placement="appLogoOverride" src="'.$img.'" alt="image1"/>
				<text id="1">'.$subject.'</text>
				<text id="2">'.$message.'</text>
			</binding>
		</visual>
	</toast>';
	}
	/*$xml_data = '<toast>
		<visual>
			<binding template="ToastGeneric">
				<text id="1">'.$subject.'</text>
				<text id="2">'.$message.'</text>
				<image placement="inline" id="3" src="'.$img.'"/>
			</binding>
		</visual>
	</toast>';*/
        $xml_data1 = '<tile>'.
          '<visual lang="en-US">'.
            '<binding template="TileMedium" branding="nameAndLogo" hint-overlay="40">'.
			  '<image placement="peek" id="2" src="'.$img.'"/>'.
              '<text id="1">'.$subject.'</text>'.
              '<text hint-style="captionsubtle" hint-wrap="true" id="3">'.$message.'</text>'.
            '</binding>'.
          '</visual>'.
        '</tile>';

		        $xml_data2 = '<tile>'.
          '<visual lang="en-US">'.
            '<binding template="TileWide" branding="nameAndLogo" hint-overlay="40">'.
			  '<image placement="peek" id="2" src="'.$img.'"/>'.
              '<text id="1">'.$subject.'</text>'.
              '<text hint-style="captionsubtle" hint-wrap="true" id="3">'.$message.'</text>'.
            '</binding>'.
          '</visual>'.
        '</tile>';
	$stmt1 = $dbh->prepare("select * from wns");
	$stmt1->execute();
	$res = $stmt1->fetchAll();
	foreach($res as $row)
	{
		$uri = $row['ChannelUri'];
		//$ChannelUri = $row['ChannelUri'];
		//$array = explode("token=", $ChannelUri);
		//$ChannelUri2 = urlencode($array[1]);
		//$uri = $array[0]."token=".$ChannelUri2;
		$obj = new WPN(SID,CLIENT_SECRET);
		$obj->post_tile($uri, $xml_data, $type = WPNTypesEnum::Toast, $tileTag = '');
		$obj->post_tile($uri, $xml_data1, $type = WPNTypesEnum::Tile, $tileTag = '');
		$obj->post_tile($uri, $xml_data2, $type = WPNTypesEnum::Tile, $tileTag = '');
	}
}
?>
