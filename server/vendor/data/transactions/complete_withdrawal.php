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
      $transaction_status = $functions->processing;
      $transaction_type = $functions->withdrawal;
      $completed = $functions->completed;

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $transaction_unique_id = isset($_GET['transaction_unique_id']) ? $_GET['transaction_unique_id'] : $data['transaction_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];

      // $sqlSearchUser = "SELECT unique_id FROM management WHERE unique_id=:unique_id AND status=:status";
      // $querySearchUser = $conn->prepare($sqlSearchUser);
      // $querySearchUser->bindParam(":unique_id", $user_unique_id);
      // $querySearchUser->bindParam(":status", $active);
      // $querySearchUser->execute();
      //
      // if ($querySearchUser->rowCount() > 0) {

        if (in_array($user_unique_id,$authorized_users)) {

          $sql4 = "SELECT balance, service_charge FROM vendors WHERE unique_id=:unique_id AND status=:status";
          $query4 = $conn->prepare($sql4);
          $query4->bindParam(":unique_id", $vendor_unique_id);
          $query4->bindParam(":status", $active);
          $query4->execute();

          if ($query4->rowCount() > 0) {

            $the_balance_details = $query4->fetch();
            $balance = (int)$the_balance_details[0];
            $service_charge = (int)$the_balance_details[1];

            $sql2 = "SELECT unique_id, amount FROM transactions WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND type=:type AND transaction_status=:transaction_status AND status=:status";
            $query2 = $conn->prepare($sql2);
            $query2->bindParam(":unique_id", $transaction_unique_id);
            $query2->bindParam(":vendor_unique_id", $vendor_unique_id);
            $query2->bindParam(":type", $transaction_type);
            $query2->bindParam(":transaction_status", $transaction_status);
            $query2->bindParam(":status", $active);
            $query2->execute();

            if ($query2->rowCount() > 0) {

              $the_transaction_details = $query2->fetch();
              $amount = (int)$the_transaction_details[1];

              $new_balance = $balance - $amount;

              $sql3 = "UPDATE vendors SET balance=:balance, last_modified=:last_modified WHERE unique_id=:unique_id";
              $query3 = $conn->prepare($sql3);
              $query3->bindParam(":unique_id", $vendor_unique_id);
              $query3->bindParam(":balance", $new_balance);
              $query3->bindParam(":last_modified", $date_added);
              $query3->execute();

              if ($query3->rowCount() > 0) {

                $sql = "UPDATE transactions SET transaction_status=:transaction_status, last_modified=:last_modified WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND type=:type";
                $query = $conn->prepare($sql);
                $query->bindParam(":unique_id", $transaction_unique_id);
                $query->bindParam(":vendor_unique_id", $vendor_unique_id);
                $query->bindParam(":type", $transaction_type);
                $query->bindParam(":transaction_status", $completed);
                $query->bindParam(":last_modified", $date_added);
                $query->execute();

                if ($query->rowCount() > 0) {
                  $returnvalue = new genericClass();
                  $returnvalue->engineMessage = 1;
                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Not completed (withdrawal)";
                }

              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not updated (vendor's balance)";
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Transaction not found";
            }

          }
          else {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Vendor not found";
          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Unauthorized access key";
        }

      // }
      // else {
      //   $returnvalue = new genericClass();
      //   $returnvalue->engineError = 2;
      //   $returnvalue->engineErrorMessage = "Management user not found";
      // }

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
