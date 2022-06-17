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
    public $return_amount;
    public $return_transaction_unique_id;
  }

  $data = json_decode(file_get_contents("php://input"), true);

  $functions = new Functions();

  if ($connected) {

    try {
      $conn->beginTransaction();

      $date_added = $functions->today;
      $active = $functions->active;
      $not_allowed_values = $functions->not_allowed_values;
      $default_status = $functions->Yes;
      $transaction_status = $functions->processing;
      $transaction_type = $functions->withdrawal;

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];
      $amount = isset($_GET['amount']) ? $_GET['amount'] : $data['amount'];
      $role = "Owner";

      $sqlSearchUser = "SELECT unique_id FROM vendor_users WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND role=:role AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":vendor_unique_id", $vendor_unique_id);
      $querySearchUser->bindParam(":role", $role);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $validation = $functions->vendor_withdrawal_validation($amount);

        if ($validation["error"] == true) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = $validation["message"];
        }
        else {

          $sql4 = "SELECT transaction_status FROM transactions WHERE vendor_unique_id=:vendor_unique_id AND type=:type AND transaction_status=:transaction_status AND status=:status ORDER BY added_date LIMIT 1";
          $query4 = $conn->prepare($sql4);
          $query4->bindParam(":vendor_unique_id", $vendor_unique_id);
          $query4->bindParam(":type", $transaction_type);
          $query4->bindParam(":transaction_status", $transaction_status);
          $query4->bindParam(":status", $active);
          $query4->execute();

          if ($query4->rowCount() > 0) {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "You have a pending withdrawal";
          }
          else {

            $sql3 = "SELECT account_name, account_number, bank FROM vendor_bank_accounts WHERE vendor_unique_id=:vendor_unique_id AND default_status=:default_status AND status=:status";
            $query3 = $conn->prepare($sql3);
            $query3->bindParam(":vendor_unique_id", $vendor_unique_id);
            $query3->bindParam(":default_status", $default_status);
            $query3->bindParam(":status", $active);
            $query3->execute();

            if ($query3->rowCount() > 0) {

              $the_bank_details = $query3->fetch();
              $account_name = $the_bank_details[0];
              $account_number = $the_bank_details[1];
              $bank = $the_bank_details[2];

              $sql2 = "SELECT balance, service_charge FROM vendors WHERE unique_id=:unique_id AND status=:status";
              $query2 = $conn->prepare($sql2);
              $query2->bindParam(":unique_id", $vendor_unique_id);
              $query2->bindParam(":status", $active);
              $query2->execute();

              if ($query2->rowCount() > 0) {

                $the_balance_details = $query2->fetch();
                $balance = (int)$the_balance_details[0];
                $service_charge = (int)$the_balance_details[1];

                // If service charge is greater than 20k user can't withdraw till they pay up

                if ($amount > $balance) {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Insufficient balance";
                }
                else if ($service_charge >= 20000) {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Pay outstanding service charge";
                }
                else {

                  $transaction_unique_id = $functions->random_str(20);

                  $details = $transaction_type." : ".$amount." Naira ".$transaction_type." ".$transaction_status.". Bank details : Name - ".$account_name.", Acc No - ".$account_number.", Bank - ".$bank;

                  $sql8 = "INSERT INTO transactions (unique_id, vendor_unique_id, type, amount, transaction_status, details, added_date, last_modified, status)
                  VALUES (:unique_id, :vendor_unique_id, :type, :amount, :transaction_status, :details, :added_date, :last_modified, :status)";
                  $query8 = $conn->prepare($sql8);
                  $query8->bindParam(":unique_id", $transaction_unique_id);
                  $query8->bindParam(":vendor_unique_id", $vendor_unique_id);
                  $query8->bindParam(":type", $transaction_type);
                  $query8->bindParam(":amount", $amount);
                  $query8->bindParam(":transaction_status", $transaction_status);
                  $query8->bindParam(":details", $details);
                  $query8->bindParam(":added_date", $date_added);
                  $query8->bindParam(":last_modified", $date_added);
                  $query8->bindParam(":status", $active);
                  $query8->execute();

                  if ($query8->rowCount() > 0) {
                    $returnvalue = new genericClass();
                    $returnvalue->engineMessage = 1;
                    $returnvalue->return_amount = $amount;
                    $returnvalue->return_transaction_unique_id = $transaction_unique_id;
                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = "Not inserted (transaction)";
                  }

                }

              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Vendor's balance not found";
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Bank account not found";
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
