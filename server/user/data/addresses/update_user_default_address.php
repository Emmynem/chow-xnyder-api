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

      $address_unique_id = isset($_GET['address_unique_id']) ? $_GET['address_unique_id'] : $data['address_unique_id'];
      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $default_status = isset($_GET['default_status']) ? $_GET['default_status'] : $data['default_status'];

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $not_allowed_values = $functions->not_allowed_values;

        $the_default_status = in_array($default_status,$not_allowed_values) ? "No" : $default_status;

        $old_default_status = "No";

        $sql4 = "SELECT address FROM users_addresses WHERE user_unique_id=:user_unique_id AND status=:status";
        $query4 = $conn->prepare($sql4);
        $query4->bindParam(":user_unique_id", $user_unique_id);
        $query4->bindParam(":status", $active);
        $query4->execute();

        if ($query4->rowCount() > 0 && $query4->rowCount() == 1) {
          $returnvalue = new genericClass();
          $returnvalue->engineMessage = 1;
        }
        else {

          if ($the_default_status == "Yes") {
            $sql2 = "SELECT default_status FROM users_addresses WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND status=:status";
            $query2 = $conn->prepare($sql2);
            $query2->bindParam(":unique_id", $address_unique_id);
            $query2->bindParam(":user_unique_id", $user_unique_id);
            $query2->bindParam(":status", $active);
            $query2->execute();

            if ($query2->rowCount() > 0) {

              $sql = "UPDATE users_addresses SET default_status=:default_status, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id";
              $query = $conn->prepare($sql);
              $query->bindParam(":unique_id", $address_unique_id);
              $query->bindParam(":user_unique_id", $user_unique_id);
              $query->bindParam(":default_status", $the_default_status);
              $query->bindParam(":last_modified", $date_added);
              $query->execute();

              $sql3 = "UPDATE users_addresses SET default_status=:default_status, last_modified=:last_modified WHERE unique_id!=:unique_id AND user_unique_id=:user_unique_id";
              $query3 = $conn->prepare($sql3);
              $query3->bindParam(":unique_id", $address_unique_id);
              $query3->bindParam(":user_unique_id", $user_unique_id);
              $query3->bindParam(":default_status", $old_default_status);
              $query3->bindParam(":last_modified", $date_added);
              $query3->execute();

              if ($query->rowCount() > 0) {
                $returnvalue = new genericClass();
                $returnvalue->engineMessage = 1;
              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not edited (user address)";
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "User address not found";
            }
          }
          else {
            $sql2 = "SELECT default_status FROM users_addresses WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND status=:status";
            $query2 = $conn->prepare($sql2);
            $query2->bindParam(":unique_id", $address_unique_id);
            $query2->bindParam(":user_unique_id", $user_unique_id);
            $query2->bindParam(":status", $active);
            $query2->execute();

            if ($query2->rowCount() > 0) {

              $the_default_details = $query2->fetch();
              $recent_status = $the_default_details[0];

              if ($recent_status == 'Yes') {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Address already default";
              }
              else {
                $sql = "UPDATE users_addresses SET default_status=:default_status, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id";
                $query = $conn->prepare($sql);
                $query->bindParam(":unique_id", $address_unique_id);
                $query->bindParam(":user_unique_id", $user_unique_id);
                $query->bindParam(":default_status", $the_default_status);
                $query->bindParam(":last_modified", $date_added);
                $query->execute();

                if ($query->rowCount() > 0) {
                  $returnvalue = new genericClass();
                  $returnvalue->engineMessage = 1;
                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Not edited (user address)";
                }
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "User address not found";
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
    $returnvalue->engineError = 2;
    $returnvalue->engineErrorMessage = "No connection";
  }

  echo json_encode($returnvalue);

?>
