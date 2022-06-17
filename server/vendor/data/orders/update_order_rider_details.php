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
      $null = $functions->null;
      $completion_status = $functions->completed;

      $order_unique_id = isset($_GET['order_unique_id']) ? $_GET['order_unique_id'] : $data['order_unique_id'];
      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];
      $tracker_unique_id = isset($_GET['tracker_unique_id']) ? $_GET['tracker_unique_id'] : $data['tracker_unique_id'];
      $rider_details = isset($_GET['rider_details']) ? $_GET['rider_details'] : $data['rider_details'];

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $sql3 = "SELECT unique_id FROM orders WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND tracker_unique_id=:tracker_unique_id AND vendor_unique_id=:vendor_unique_id AND delivery_status=:delivery_status";
        $query3 = $conn->prepare($sql3);
        $query3->bindParam(":delivery_status", $completion_status);
        $query3->bindParam(":unique_id", $order_unique_id);
        $query3->bindParam(":user_unique_id", $user_unique_id);
        $query3->bindParam(":tracker_unique_id", $tracker_unique_id);
        $query3->bindParam(":vendor_unique_id", $vendor_unique_id);
        $query3->execute();

        $sql4 = "SELECT unique_id FROM orders WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND tracker_unique_id=:tracker_unique_id AND vendor_unique_id=:vendor_unique_id AND disputed=:disputed AND delivery_status!=:delivery_status";
        $query4 = $conn->prepare($sql4);
        $query4->bindParam(":disputed", $active);
        $query4->bindParam(":delivery_status", $completion_status);
        $query4->bindParam(":unique_id", $order_unique_id);
        $query4->bindParam(":user_unique_id", $user_unique_id);
        $query4->bindParam(":tracker_unique_id", $tracker_unique_id);
        $query4->bindParam(":vendor_unique_id", $vendor_unique_id);
        $query4->execute();

        if ($query3->rowCount() > 0) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Item already completed";
        }
        else if ($query4->rowCount() > 0) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Can't edit rider details at this time";
        }
        else {

          $sql2 = "UPDATE orders SET rider_details=:rider_details, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND tracker_unique_id=:tracker_unique_id AND vendor_unique_id=:vendor_unique_id";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":rider_details", $rider_details);
          $query2->bindParam(":unique_id", $order_unique_id);
          $query2->bindParam(":user_unique_id", $user_unique_id);
          $query2->bindParam(":tracker_unique_id", $tracker_unique_id);
          $query2->bindParam(":vendor_unique_id", $vendor_unique_id);
          $query2->bindParam(":last_modified", $date_added);
          $query2->execute();

          if ($query2->rowCount() > 0) {
            $returnvalue = new genericClass();
            $returnvalue->engineMessage = 1;
          }
          else {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Orders Not updated";
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
