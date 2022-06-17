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

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $firstname = isset($_GET['firstname']) ? $_GET['firstname'] : $data['firstname'];
      $lastname = isset($_GET['lastname']) ? $_GET['lastname'] : $data['lastname'];
      $address = isset($_GET['address']) ? $_GET['address'] : $data['address'];
      $additional_information = isset($_GET['additional_information']) ? $_GET['additional_information'] : $data['additional_information'];
      $city = isset($_GET['city']) ? $_GET['city'] : $data['city'];
      $state = isset($_GET['state']) ? $_GET['state'] : $data['state'];
      $country = isset($_GET['country']) ? $_GET['country'] : $data['country'];

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $validation = $functions->address_validation($firstname, $lastname, $address, $additional_information, $city, $state, $country);

        if ($validation["error"] == true) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = $validation["message"];
        }
        else {

          $address_count = $functions->check_length_3;
          $not_allowed_values = $functions->not_allowed_values;
          $null = $functions->null;
          $the_additional_information = in_array($additional_information,$not_allowed_values) ? $null : $additional_information;

          $sql2 = "SELECT address FROM users_addresses WHERE user_unique_id=:user_unique_id AND status=:status";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":user_unique_id", $user_unique_id);
          $query2->bindParam(":status", $active);
          $query2->execute();

          if ($query2->rowCount() >= $address_count) {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "User address limit reached";
          }
          else {
            $unique_id = $functions->random_str(20);

            $default_status = $query2->rowCount() == 0 ? "Yes" : "No";

            $sql = "INSERT INTO users_addresses (unique_id, user_unique_id, firstname, lastname, address, additional_information, city, state, country, default_status, added_date, last_modified, status)
            VALUES (:unique_id, :user_unique_id, :firstname, :lastname, :address, :additional_information, :city, :state, :country, :default_status, :added_date, :last_modified, :status)";
            $query = $conn->prepare($sql);
            $query->bindParam(":unique_id", $unique_id);
            $query->bindParam(":user_unique_id", $user_unique_id);
            $query->bindParam(":firstname", $firstname);
            $query->bindParam(":lastname", $lastname);
            $query->bindParam(":address", $address);
            $query->bindParam(":additional_information", $the_additional_information);
            $query->bindParam(":city", $city);
            $query->bindParam(":state", $state);
            $query->bindParam(":country", $country);
            $query->bindParam(":default_status", $default_status);
            $query->bindParam(":added_date", $date_added);
            $query->bindParam(":last_modified", $date_added);
            $query->bindParam(":status", $active);
            $query->execute();

            if ($query->rowCount() > 0) {
              $returnvalue = new genericClass();
              $returnvalue->engineMessage = 1;
            }
            else{
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Not inserted (new user address)";
            }
          }

        }

      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "User not found";
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
