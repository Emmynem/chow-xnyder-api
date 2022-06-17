<?php

  $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
  $allowed_domains = array("https://auth.reestoc.com", "https://dashboard.reestoc.com");
  foreach ($allowed_domains as $value) {if ($http_origin === $value) {header('Access-Control-Allow-Origin: ' . $http_origin);}}
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, origin, Accept-Language, Range, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");
  require '../../../config/connect_to_me.php';
  include_once "../../../config/functions.php";

  class genericClass {
    public $engineMessage = 0;
    public $engineError = 0;
    public $engineErrorMessage;
    public $resultData;
    public $filteredData;
  }

  $data = json_decode(file_get_contents("php://input"), true);

  $functions = new Functions();

  if ($connected) {

    try {
      $conn->beginTransaction();

      $date_added = $functions->today;
      $active = $functions->active;
      $not_allowed_values = $functions->not_allowed_values;

      $email = isset($_GET['email']) ? $_GET['email'] : $data['email'];
      $phone_number = isset($_GET['phone_number']) ? $_GET['phone_number'] : $data['phone_number'];
      $fullname = isset($_GET['fullname']) ? $_GET['fullname'] : $data['fullname'];
      $image = isset($_GET['image']) ? $_GET['image'] : $data['image'];

      $sql2 = "SELECT access, unique_id FROM users WHERE email=:email OR phone_number=:phone_number AND status=:status";
      $query2 = $conn->prepare($sql2);
      $query2->bindParam(":email", $email);
      $query2->bindParam(":phone_number", $phone_number);
      $query2->bindParam(":status", $active);
      $query2->execute();

      if ($query2->rowCount() > 0) {

        $the_access_details = $query2->fetch();
        $access = (int)$the_access_details[0];
        $unique_id = $the_access_details[1];

        switch ($access) {
          case $functions->granted:
            $sql = "UPDATE users SET fullname=:fullname, email=:email, phone_number=:phone_number, image=:image, last_modified=:last_modified WHERE unique_id=:unique_id";
            $query = $conn->prepare($sql);
            $query->bindParam(":fullname", $fullname);
            $query->bindParam(":email", $email);
            $query->bindParam(":phone_number", $phone_number);
            $query->bindParam(":image", $image);
            $query->bindParam(":unique_id", $unique_id);
            $query->bindParam(":last_modified", $date_added);
            $query->execute();

            if ($query->rowCount() > 0) {
              $returnvalue = new genericClass();
              $returnvalue->engineMessage = 1;
              $returnvalue->resultData = $unique_id;
            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineMessage = 1;
              $returnvalue->resultData = $unique_id;
            }
            break;
          case $functions->suspended:
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Access suspended";
            break;
          case $functions->revoked:
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Access revoked";
            break;
          default:
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Access restricted";
            break;
        }

      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "Account not found";
      }

      $conn->commit();
    } catch (PDOException $e) {
      $conn->rollback();
      throw $e;
    }

  }
  else {
    $returnvalue = new genericClass();
    $returnvalue->engineError = 3;
    $returnvalue->engineErrorMessage = "No connection";
  }

  echo json_encode($returnvalue);

?>
