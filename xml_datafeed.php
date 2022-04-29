//Script by David Hollander, www.foxy-shop.com
//version 1.0, 7/9/2012

<?php
 
//Set Globals and Get Settings
$apikey = "spfx042ef8b9c0ad08c5450144dcb0a6b916fddcc8eb9b9266926e83b1a2ea5cc90e";

$filename = "xml_data_dummy " . $_SERVER['REQUEST_TIME'] . ".xml";

ob_start();
var_dump($_POST);
$content = ob_get_contents();
ob_end_clean();

$myfile = fopen($filename, 'a') or die("Unable to open file!");
$txt = $content;
fwrite($myfile, $txt);
fclose($myfile);

if (isset($_POST["FoxySubscriptionData"])) {

	$FoxyData_decrypted = foxycart_decrypt($_POST["FoxySubscriptionData"]);
	$xml = simplexml_load_string($FoxyData_decrypted, NULL, LIBXML_NOCDATA);
	
	ob_start();
    var_dump($xml);
    $content2 = ob_get_contents();
    ob_end_clean();
	
	$myfile = fopen("xml_data.xml", 'a') or die("Unable to open file!");
    $txt = $content2;
    fwrite($myfile, $txt);
    fclose($myfile);
	
	die("foxy");
	
} else {
	die('No Content Received From Datafeed');
}

//Decrypt Data From Source
function foxycart_decrypt($src) {
    	global $apikey;
	return rc4crypt::decrypt($apikey,urldecode($src));
}
 
 
class rc4crypt {

	public static function encrypt ($pwd, $data, $ispwdHex = 0) {
		if ($ispwdHex) $pwd = @pack('H*', $pwd); // valid input, please!
 		$key[] = '';
		$box[] = '';
		$cipher = '';
		$pwd_length = strlen($pwd);
		$data_length = strlen($data);
		for ($i = 0; $i < 256; $i++) {
			$key[$i] = ord($pwd[$i % $pwd_length]);
			$box[$i] = $i;
		}
		for ($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $key[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for ($a = $j = $i = 0; $i < $data_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$k = $box[(($box[$a] + $box[$j]) % 256)];
			$cipher .= chr(ord($data[$i]) ^ $k);
		}
		return $cipher;
	}
	/**
	 * Decryption, recall encryption
	 *
	 * @param string $pwd Key to decrypt with (can be binary of hex)
	 * @param string $data Content to be decrypted
	 * @param bool $ispwdHex Key passed is in hexadecimal or not
	 * @access public
	 * @return string
	 */
	public static function decrypt ($pwd, $data, $ispwdHex = 0) {
		return rc4crypt::encrypt($pwd, $data, $ispwdHex);
	}
}