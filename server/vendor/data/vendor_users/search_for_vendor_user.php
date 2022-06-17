<?php

  $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
  $allowed_domains = array("https://auth.reestoc.com", "https://dashboard.reestoc.com");
  foreach ($allowed_domains as $value) {if ($http_origin === $value) {header('Access-Control-Allow-Origin: ' . $http_origin);}}
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, origin, Accept-Language, Range, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");
  require '../../../config/connect_to_me.php';

  class searchUserClass {
    public $engineMessage = 0;
    public $alreadyExists = 0;
  }

  $data = json_decode(file_get_contents("php://input"), true);

  if ($connected) {

    try {
      $conn->beginTransaction();

      $sql = "SELECT unique_id FROM vendor_users WHERE email=:email OR phone_number=:phone_number AND vendor_unique_id=:vendor_unique_id";
      $query = $conn->prepare($sql);
      $query->bindParam(":email", $data['email']);
      $query->bindParam(":phone_number", $data['phone_number']);
      $query->bindParam(":vendor_unique_id", $data['vendor_unique_id']);
      $query->execute();

      if ($query->rowCount() > 0) {
        $returnvalue = new searchUserClass();
        $returnvalue->alreadyExists = 2;
      }
      else {
        $returnvalue = new searchUserClass();
        $returnvalue->engineMessage = 1;
      }

      $conn->commit();
    } catch (PDOException $e) {
      $conn->rollback();
      throw $e;
    }

    echo json_encode($returnvalue);
  }

?>
