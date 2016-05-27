<?php

// Reference payload reader, call with ?payload=xxx otherwise payload_example will be read.

//
$payload_example = "2408200315143C1E00";
$device = "Solidus Tech - IndoorUNI DTH";
$firmware = "fw 0.2.1";

//
if ($_GET['payload'] == "") {
	$filename = basename(__FILE__, '.php');
	echo '<meta http-equiv="refresh" content="0;url=' . $filename . '?payload=' . $payload_example . '">';
	exit;
} else if ($_GET['payload'] == $payload_example) {
	$payload = $_GET['payload'];
	$note = "reference payload reader / found example payload in URL";
} else {
	$payload = $_GET['payload'];
	$note = "reference payload reader";
};

//
$payload_array = str_split($payload, 2);

// 1 byte, info byte
$byte1 = hexdec($payload_array['0']);
$byte1bits = base_convert($byte1, 16, 2);
while (strlen($byte1bits) < 8) {$byte1bits = "0" . $byte1bits;};
$byte1array = str_split($byte1bits, 1);
if ($byte1array[0] == 0) {$adr = "OFF";} else {$adr = "ON";};
if ($byte1array[4] == 0) {$snr_sign = "+";} else {$snr_sign = "-";};
$dr = $byte1array[1] . $byte1array[2] . $byte1array[3];
$dr = base_convert($dr, 2, 10);

// 2 byte, SNR dB
$byte2 = hexdec($payload_array['1']);

// 3 byte, batery voltage nn-- mV (dec)
$byte3 = hexdec($payload_array['2']);

// 4 byte, batery voltage --nn mV (dec)
$byte4 = hexdec($payload_array['3']);
if (strlen($byte4) == 1) {$byte4 = $byte4 . "0";}; // keep 0n

// 5 byte, temperature left from coma (dec)
$byte5 = hexdec($payload_array['4']);

// 6 byte, temperature right from coma (dec)
$byte6 = hexdec($payload_array['5']);
if (strlen($byte7) == 1) {$byte7 = $byte7 . "0";}; // keep 0n

// 7 byte, humidity left from coma (dec)
$byte7 = hexdec($payload_array['6']);

// 8 byte, humidity right from coma (dec)
$byte8 = hexdec($payload_array['7']);
if (strlen($byte8) == 1) {$byte8 = $byte8 . "0";}; // keep 0n

// 9 byte, info byte 2
$byte9 = hexdec($payload_array['8']);
$byte9bits = base_convert($byte9, 16, 2);
while (strlen($byte9bits) < 8) {$byte9bits = "0" . $byte9bits;};
$byte9array = str_split($byte9bits, 1);
if ($byte9array[5] == 0) {$power = "battery";} else {$power = "external";};
if ($byte9array[6] == 0) {$short_circuit = "OFF";} else {$short_circuit = "ON";};
if ($byte9array[7] == 0) {$temperature_sign = "+";} else {$temperature_sign = "-";};

//
$snr = $snr_sign . $byte2 . " dB";
$temperature = $temperature_sign . $byte5 . "," . $byte6 . " C";
$humidity =  $byte7 . "," . $byte8 . " %";
if ($power == "external") {
	$battery_voltage = "-- mV";	
} else {
	$battery_voltage = $byte3 . $byte4 . " mV";
};

//
$data = array('device' => $device, 'firmware' => $firmware, 'note' => $note, 'payload' => $payload, 'infobyte' => $byte1bits, 'infobyte2' => $byte9bits, 'adr' => $adr, 'snr' => $snr, 'dr' => $dr, 'power' => $power, 'battery_voltage' => $battery_voltage, 'temperature' => $temperature, 'humidity' => $humidity, 'short_circuit' => $short_circuit);
header('Content-type:application/json;charset=utf-8');
echo json_encode($data);
