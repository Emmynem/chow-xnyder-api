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

      $account_unique_id = isset($_GET['account_unique_id']) ? $_GET['account_unique_id'] : $data['account_unique_id'];
      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];
      $default_status = isset($_GET['default_status']) ? $_GET['default_status'] : $data['default_status'];
      $role = "Owner";

      $sqlSearchUser = "SELECT unique_id FROM vendor_users WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND role=:role AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":vendor_unique_id", $vendor_unique_id);
      $querySearchUser->bindParam(":role", $role);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $not_allowed_values = $functions->not_allowed_values;

        $the_default_status = in_array($default_status,$not_allowed_values) ? "No" : $default_status;

        $old_default_status = "No";

        $sql4 = "SELECT bank FROM vendor_bank_accounts WHERE vendor_unique_id=:vendor_unique_id AND status=:status";
        $query4 = $conn->prepare($sql4);
        $query4->bindParam(":vendor_unique_id", $vendor_unique_id);
        $query4->bindParam(":status", $active);
        $query4->execute();

        if ($query4->rowCount() > 0 && $query4->rowCount() == 1) {
          $returnvalue = new genericClass();
          $returnvalue->engineMessage = 1;
        }
        else {

          if ($the_default_status == "Yes") {
            $sql2 = "SELECT default_status FROM vendor_bank_accounts WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
            $query2 = $conn->prepare($sql2);
            $query2->bindParam(":unique_id", $account_unique_id);
            $query2->bindParam(":vendor_unique_id", $vendor_unique_id);
            $query2->bindParam(":status", $active);
            $query2->execute();

            if ($query2->rowCount() > 0) {

              $sql = "UPDATE vendor_bank_accounts SET default_status=:default_status, last_modified=:last_modified WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id";
              $query = $conn->prepare($sql);
              $query->bindParam(":unique_id", $account_unique_id);
              $query->bindParam(":vendor_unique_id", $vendor_unique_id);
              $query->bindParam(":default_status", $the_default_status);
              $query->bindParam(":last_modified", $date_added);
              $query->execute();

              $sql3 = "UPDATE vendor_bank_accounts SET default_status=:default_status, last_modified=:last_modified WHERE unique_id!=:unique_id AND vendor_unique_id=:vendor_unique_id";
              $query3 = $conn->prepare($sql3);
              $query3->bindParam(":unique_id", $account_unique_id);
              $query3->bindParam(":vendor_unique_id", $vendor_unique_id);
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
                $returnvalue->engineErrorMessage = "Not edited (vendor bank account)";
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Vendor bank account not found";
            }
          }
          else {
            $sql2 = "SELECT default_status FROM vendor_bank_accounts WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
            $query2 = $conn->prepare($sql2);
            $query2->bindParam(":unique_id", $account_unique_id);
            $query2->bindParam(":vendor_unique_id", $vendor_unique_id);
            $query2->bindParam(":status", $active);
            $query2->execute();

            if ($query2->rowCount() > 0) {

              $the_default_details = $query2->fetch();
              $recent_status = $the_default_details[0];

              if ($recent_status == 'Yes') {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Bank account already default";
              }
              else {
                $sql = "UPDATE vendor_bank_accounts SET default_status=:default_status, last_modified=:last_modified WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id";
                $query = $conn->prepare($sql);
                $query->bindParam(":unique_id", $account_unique_id);
                $query->bindParam(":vendor_unique_id", $vendor_unique_id);
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
                  $returnvalue->engineErrorMessage = "Not edited (vendor bank account)";
                }
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Vendor bank account not found";
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
    $returnvalue->engineError = 2;
    $returnvalue->engineErrorMessage = "No connection";
  }

  echo json_encode($returnvalue);

?>
