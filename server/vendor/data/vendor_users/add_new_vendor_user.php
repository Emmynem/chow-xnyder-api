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

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];
      $fullname = isset($_GET['fullname']) ? $_GET['fullname'] : $data['fullname'];
      $email = isset($_GET['email']) ? $_GET['email'] : $data['email'];
      $phone_number = isset($_GET['phone_number']) ? $_GET['phone_number'] : $data['phone_number'];
      $role = isset($_GET['role']) ? $_GET['role'] : $data['role'];
      $access = isset($_GET['access']) ? $_GET['access'] : $data['access'];

      $sqlSearchUser = "SELECT unique_id FROM vendor_users WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":vendor_unique_id", $vendor_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $validation = $functions->vendor_user_validation($fullname, $email, $phone_number, $role, $access);

        if ($validation["error"] == true) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = $validation["message"];
        }
        else {

          $sql2 = "SELECT email FROM vendor_users WHERE email=:email OR phone_number=:phone_number";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":email", $email);
          $query2->bindParam(":phone_number", $phone_number);
          $query2->execute();

          if ($query2->rowCount() > 0) {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "User already exists";
          }
          else {
            $unique_id = $functions->random_str(20);

            $the_email = in_array($email, $not_allowed_values) ? null : $email;
            $the_phone_number = in_array($phone_number, $not_allowed_values) ? null : $phone_number;

            $sql = "INSERT INTO vendor_users (unique_id, user_unique_id, vendor_unique_id, fullname, email, phone_number, role, added_date, last_modified, access, status)
            VALUES (:unique_id, :user_unique_id, :vendor_unique_id, :fullname, :email, :phone_number, :role, :added_date, :last_modified, :access, :status)";
            $query = $conn->prepare($sql);
            $query->bindParam(":unique_id", $unique_id);
            $query->bindParam(":user_unique_id", $user_unique_id);
            $query->bindParam(":vendor_unique_id", $vendor_unique_id);
            $query->bindParam(":fullname", $fullname);
            $query->bindParam(":email", $the_email);
            $query->bindParam(":phone_number", $the_phone_number);
            $query->bindParam(":role", $role);
            $query->bindParam(":added_date", $date_added);
            $query->bindParam(":last_modified", $date_added);
            $query->bindParam(":access", $access);
            $query->bindParam(":status", $active);
            $query->execute();

            if ($query->rowCount() > 0) {
              $returnvalue = new genericClass();
              $returnvalue->engineMessage = 1;
            }
            else{
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Not inserted (new vendor user)";
            }
          }

        }

      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "Vendor user not found";
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
