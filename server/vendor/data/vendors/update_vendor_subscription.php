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
      $not_active = $functions->not_active;
      $our_subscription_fee = $functions->our_subscription_fee;
      $transaction_status_paid = $functions->paid;
      $transaction_type = $functions->subscription;

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];
      // $amount = isset($_GET['amount']) ? $_GET['amount'] : $data['amount'];
      $amount = $our_subscription_fee;

      $sqlSearchUser = "SELECT unique_id FROM vendor_users WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":vendor_unique_id", $vendor_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $sql2 = "SELECT unique_id FROM vendors WHERE unique_id=:unique_id AND subscription=:subscription AND status=:status";
        $query2 = $conn->prepare($sql2);
        $query2->bindParam(":unique_id", $vendor_unique_id);
        $query2->bindParam(":subscription", $not_active);
        $query2->bindParam(":status", $active);
        $query2->execute();

        if ($query2->rowCount() > 0) {

          $sql3 = "UPDATE vendor_users SET access=:access, last_modified=:last_modified WHERE vendor_unique_id=:vendor_unique_id";
          $query3 = $conn->prepare($sql3);
          $query3->bindParam(":access", $active);
          $query3->bindParam(":vendor_unique_id", $vendor_unique_id);
          $query3->bindParam(":last_modified", $date_added);
          $query3->execute();

          if ($query3->rowCount() > 0) {
            $sql = "UPDATE vendors SET access=:access, subscription=:subscription, last_modified=:last_modified WHERE unique_id=:unique_id";
            $query = $conn->prepare($sql);
            $query->bindParam(":access", $active);
            $query->bindParam(":subscription", $active);
            $query->bindParam(":unique_id", $vendor_unique_id);
            $query->bindParam(":last_modified", $date_added);
            $query->execute();

            if ($query->rowCount() > 0) {

              $unique_id_2 = $functions->random_str(20);

              $sql6 = "INSERT INTO transactions (unique_id, vendor_unique_id, type, amount, transaction_status, added_date, last_modified, status)
              VALUES (:unique_id, :vendor_unique_id, :type, :amount, :transaction_status, :added_date, :last_modified, :status)";
              $query6 = $conn->prepare($sql6);
              $query6->bindParam(":unique_id", $unique_id_2);
              $query6->bindParam(":vendor_unique_id", $vendor_unique_id);
              $query6->bindParam(":type", $transaction_type);
              $query6->bindParam(":amount", $amount);
              $query6->bindParam(":transaction_status", $transaction_status_paid);
              $query6->bindParam(":added_date", $date_added);
              $query6->bindParam(":last_modified", $date_added);
              $query6->bindParam(":status", $active);
              $query6->execute();

              if ($query6->rowCount() > 0) {
                $returnvalue = new genericClass();
                $returnvalue->engineMessage = 1;
              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not inserted (new transaction paid)";
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Not edited (vendor's subscription)";
            }
          }
          else {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Not edited (vendor's users access)";
          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Vendor not found, probably subscribed already";
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
