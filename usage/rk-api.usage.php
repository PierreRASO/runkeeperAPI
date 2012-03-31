<?php
define('YAMLPATH', '/path/to/yaml/install/directory/');
define('RUNKEEPERAPIPATH', '/path/to/runkeeperapi/install/directory/');
define('CONFIGPATH', '/path/to/config/file/directory/');

require(YAMLPATH.'lib/sfYamlParser.php');
require(RUNKEEPERAPIPATH.'lib/runkeeperAPI.class.php');

/* API initialization */
$rkAPI = new runkeeperAPI(
		CONFIGPATH.'rk-api.sample.yml'	/* api_conf_file */
		);
if ($rkAPI->api_created == false) {
	echo 'error '.$rkAPI->api_last_error; /* api creation problem */
	exit();
}

/* Generate link to allow user to connect to Runkeeper and to allow your app*/
$linkUrl = $rkAPI->connectRunkeeperButtonUrl();

/* After connecting to Runkeeper and allowing your app, user is redirected to redirect_uri param (as specified in YAML config file) with $_GET parameter "code" */
if ($_GET['code']) {
	$auth_code = $_GET['code'];
	if ($rkAPI->getRunkeeperToken($auth_code) == false) {
		echo $rkAPI->api_last_error; /* get access token problem */
		exit();
	}
	else {
		/* Your code to store $rkAPI->access_token (client-side, server-side or session-side) */
		/* Note: $rkAPI->access_token will have to be set et valid for following operations */

		/* Do a "Read" request on "Profile" interface => return all fields available for this Interface */
		$rkProfile = $rkAPI->doRunkeeperRequest('Profile','Read');
		print_r($rkProfile);

		/* Do a "Read" request on "Settings" interface => return all fields available for this Interface */
		$rkSettings = $rkAPI->doRunkeeperRequest('Settings','Read');
		print_r($rkSettings);

		/* Do a "Read" request on "FitnessActivities" interface => return all fields available for this Interface or false if request fails */
		$rkActivities = $rkAPI->doRunkeeperRequest('FitnessActivities','Read');
		if ($rkActivities) {
			print_r($rkActivities);
		}
		else {
			echo $rkAPI->api_last_error;
			print_r($rkAPI->request_log);
		}

		/* Do a "Read" request on "FitnessActivityFeed" interface => return all fields available for this Interface or false if request fails */
		$rkActivities = $rkAPI->doRunkeeperRequest('FitnessActivityFeed','Read');
		if ($rkUpdateActivity) {
			print_r($rkUpdateActivity);
		}
		else {
			echo $rkAPI->api_last_error;
			print_r($rkAPI->request_log);
		}

		/* Do a "Create" request on "FitnessActivity" interface with fields => return created FitnessActivity content if request success, false if not */
		$fields = json_decode('{"type": "Running", "equipment": "None", "start_time": "Sat, 1 Jan 2011 00:00:00", "notes": "My first late-night run", "path": [{"timestamp":0, "altitude":0, "longitude":-70.95182336425782, "latitude":42.312620297384676, "type":"start"}, {"timestamp":8, "altitude":0, "longitude":-70.95255292510987, "latitude":42.31230294498018, "type":"end"}], "post_to_facebook": true, "post_to_twitter": true}');
		$rkCreateActivity = $rkAPI->doRunkeeperRequest('NewFitnessActivity','Create',$fields);
		if ($rkCreateActivity) {
			print_r($rkCreateActivity);
		}
		else {
			echo $rkAPI->api_last_error;
			print_r($rkAPI->request_log);
		}
	}
}
?>
