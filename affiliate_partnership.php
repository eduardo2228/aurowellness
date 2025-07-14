<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, origin");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        handleGet();
        break;
    case 'POST':
        handlePost($input);
        break;
    case 'PUT':
        handlePut($input);
        break;
    case 'DELETE':
        handleDelete($input);
        break;
    default:
        echo json_encode(['message' => 'Invalid request method']);
        break;
}

function handleGet() {
    $result["user"] = "";
    echo json_encode($result);
}

function handlePost($input) {

    $tempate_id = "fef77b23644898ea";
    $api_key = "cc68MzM0MTE6MzA1OTM6ZERsYlFORWtIM2VnOXMzbQ=";
    $json_payload='{ "printname": "'.$input["printname"].'", "date": "'.date("m-d-Y").'" }';
    $file = generate($tempate_id,$api_key,$json_payload);

    if($file && $file != ""){
            $ch = curl_init();
            $data = array(
                "email" => $input["email"],
                "first_name" => $input["first_name"],
                "last_name" => $input["last_name"],
                "password" => $input["password"],
                "program_id" => $input["program_id"],
                "status" => 1,
                "company" => $input["company"],
                "address" => $input["address"],
                "country" => $input["country"],
                "city" => $input["city"],
                "state" => $input["state"],
                "phone" => $input["phone"],
                "twitter" => $file
            );
            
            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer pk_uliIiR7pmLvoJFg90XbV95DvqL34Sl85'
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, 'https://aff-api.uppromote.com/api/v1/affiliates');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            $response = curl_exec($ch);
            curl_close($ch);
            $decodedResponse = json_decode($response, true);

            $res["status"] = $decodedResponse["status"];
            $res["message"] = $decodedResponse["message"];
            $res["affiliate_id"] = $decodedResponse["affiliate_id"];

            echo json_encode($res);
    }
}

function handlePut($input) {
    echo json_encode(['message' => 'User updated successfully']);
}

function handleDelete($input) {
    echo json_encode(['message' => 'User deleted successfully']);
}

function generate($template_id,$api_key, $data) {
    $url = "https://rest.apitemplate.io/v2/create-pdf?template_id=" . $template_id;
    $headers = array("X-API-KEY: ".$api_key);
    $curl = curl_init();
    if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    if (!$result) {
        return null;
    }else{
        $json_result = json_decode($result, 1);
        if($json_result["status"]=="success"){
            return $json_result["download_url"];
        }else{
            return null;
        }
    }
}
