<?php
// Please See Dixa for APIKEY Value
define("BASEURL" , "https://integrations.dixa.io/v1/");
define("APIKEY" , "");
define("EMAILINTID", "example-endpoint@email.dixa.io");

/* ##################################################

**Example usage:**
DixaCreateContactForm(name, email, subject, message);

DixaCreateContactForm("John Doe","john.doe@example.com","Hello World!","Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas risus magna, congue eget rutrum quis, accumsan ac purus. Duis magna dolor, ultrices ac placerat ullamcorper, rutrum sit amet massa. Aliquam.");


 ################################################# */

function DixaCreateContactform($name, $email, $subject, $message) {
  $jsonData = array(
    "name" => $name,
    "email" => $email,
    "subject" => $subject,
    "message" => $message
  );
  $userID = checkIfUserExists($jsonData);
  if($userID)
      return createConversation($jsonData,$userID);
  else
      return createConversation($jsonData,createUser($jsonData["name"],$jsonData["email"]));
}

//necessary functions - don't change those.
function checkIfUserExists($jsonData)
{
    $userURL = "users?email=".$jsonData["email"];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASEURL. $userURL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization:" . APIKEY,
            "Content-Type: application/json"
        )
    ));
    $resp = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($resp,true);
    if(isset($result["data"]["0"]["id"]))
      return $result['data']['0']['id'];
    else
      return false;
}

function createUser($name,$email)
{
    $data = [
        'name' => "$name",
        'email' => "$email"
    ];
    $payload = json_encode($data);
    $uri_path = BASEURL . "users";
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $uri_path,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
            'Authorization:' . APIKEY
        ),
        CURLOPT_POSTFIELDS => $payload
    ]);
    $resp = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($resp,true);
    if(!isset($result['data']['id'])){
      return false;
    }
    return $result['data']['id'];
}

function createConversation($data, $id) {
    $data = [
      "requester_id" => "$id",
      "email_integration_id" => EMAILINTID,
      "channel" => "contact_form",
      "subject" => $data["subject"],
      "message" => [
        "text" => $data["message"]
      ]
    ];
    $payload = json_encode($data);
    $uri_path = BASEURL . "conversations";
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $uri_path,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
            'Authorization:' . APIKEY
        ),
        CURLOPT_POSTFIELDS => $payload
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    print_r($response);
}
