<?php
include 'config.php';
ignore_user_abort(true);
set_time_limit(0);

function curl_get($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result = curl_exec($ch);
    
    /* Check for 404 (file not found). */
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($httpCode != 200) {
        return false;
    }
    if (!$result) { die("Connection Failure"); }
	curl_close($ch);
    return $result;
}

function curl_post($url, $data) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	$result = curl_exec($ch);
    if (!$result) { die("Connection Failure"); }
	curl_close($ch);
	return $result;
}

echo 'Start';

$url = 'https://www.parsehub.com/api/v2/projects/' . $PARSEHUB_PROJECT_TOKEN . '/run';
$post = array("api_key" => $PARSEHUB_API_KEY);
$startScraper = curl_post($url, $post);


$startJson =  json_decode($startScraper, true);
$runToken = $startJson['run_token'];

sleep(3);

$wait = true;
while ($wait) {
    $url = 'https://www.parsehub.com/api/v2/runs/' . $runToken . '/data?api_key=' . $PARSEHUB_API_KEY . '&format=json';
    $response = curl_get($url);
    // check is JSON data incorrect
    if ($response === false) {
        echo '.';
        sleep(5);
    } else {
        $response = gzdecode($response);
        $prices = json_decode($response, true);
        $wait = false;
    }
}

print_r($prices);

$sendMail = false;
$mailtext = '';
foreach ($ALERTS as $alert) {
    $product = $alert[0];
    $alertPrice = $alert[1];
    if (isset($prices[$product])) {
        $currentPrice = preg_replace('/[^0-9]/', '', $prices[$product]);
        if ($currentPrice <= $alertPrice) {
            $sendMail = true;
            $mailtext .= $product . ' price is: ' . $currentPrice . '<br>';
        }
    } else {
        $sendMail = true;
        $mailtext .= $product . ' not found in the api list<br>';
    }
}

if ($sendMail === true) {
    require 'sendgrid-php/sendgrid-php.php';

    $email = new \SendGrid\Mail\Mail();
    $email->setFrom("test@example.com", "Price alert");
    $email->setSubject("Price drop alert!");
    $email->addTo($EMAIL);
    $email->addContent(
        "text/html", $mailtext
    );
    $sendgrid = new \SendGrid($SENDGRID_API_KEY);
    try {
        $response = $sendgrid->send($email);
        if ($response->statusCode() == 202) {
            echo "<br>Email sent.";
        }
    } catch (Exception $e) {
        echo 'Caught exception: '. $e->getMessage() ."\n";
    }
}
