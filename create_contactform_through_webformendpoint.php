<?php
/*
Generate a webform endpoint on https://YOUR-SUBDOMAIN.dixa.com/settings/webform-endpoints and replace the URL below with your own Webform endpoint.
Make sure PHP-Curl is installed and enabled on your web server.
*/
define('DIXA_WEBFORM_ENDPOINT_URL', 'https://forms.dixa.io/v2/forms/YOUR_CONTACTFORM/ENDPOINT_URL');

/*example usage:
createDixaContactForm("customer@example.com","Created through PHP and Webform Endpoint", "This is the message of the contact form");
*/

function createDixaContactForm($requester, $subject, $message) {
  if (!filter_var($requester, FILTER_VALIDATE_EMAIL)) {
    return false;
  }
  $handle = curl_init();
  $post = array(
    "email" => $requester,
    "message" => $message,
    "subject" => $subject
  );
  // Set the url
  curl_setopt($handle, CURLOPT_URL, DIXA_WEBFORM_ENDPOINT_URL);
  // Set the result output to be a string.
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_POSTFIELDS, $post);
  $output = curl_exec($handle);
  curl_close($handle);
  return $output;
}

?>
