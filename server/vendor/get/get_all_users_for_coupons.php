<?php

  $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
  $allowed_domains = array("https://auth.reestoc.com", "https://dashboard.reestoc.com");
  foreach ($allowed_domains as $value) {if ($http_origin === $value) {header('Access-Control-Allow-Origin: ' . $http_origin);}}
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, origin, Accept-Language, Range, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  ini_set('display_errors', 1);

  require '../../config/connect_to_me.php';
  include_once "../../config/functions.php";

  class genericClass {
    public $engineMessage = 0;
    public $engineError = 0;
    public $engineErrorMessage;
    public $resultData;
    public $filteredData;
  }

  $data = json_decode(file_get_contents("php://input"), true);

  $functions = new Functions();

  $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data["vendor_unique_id"];

  if ($connected) {

    try {
      $conn->beginTransaction();

      // Remove this aftere the phone_number - ref_table.referrals_count,
      $sql = "SELECT users.unique_id, users.fullname, users.email, users.phone_number, orders_completed_table.orders_completed_count, orders_completed_table.orders_completed_amount FROM users
      LEFT JOIN (SELECT COUNT(*) AS referrals_count, referral_user_unique_id FROM referrals GROUP BY referral_user_unique_id) AS ref_table ON users.unique_id = ref_table.referral_user_unique_id
      LEFT JOIN (SELECT COUNT(*) AS orders_completed_count, SUM(total_price) AS orders_completed_amount, user_unique_id, vendor_unique_id FROM orders_completed GROUP BY user_unique_id) AS orders_completed_table ON users.unique_id = orders_completed_table.user_unique_id
      WHERE orders_completed_table.vendor_unique_id=:vendor_unique_id GROUP BY users.unique_id ORDER BY orders_completed_table.orders_completed_count DESC, ref_table.referrals_count DESC";
      $query = $conn->prepare($sql);
      $query->bindParam(":vendor_unique_id", $vendor_unique_id);
      $query->execute();

      $result = $query->fetchAll();

      if ($query->rowCount() > 0) {
        $returnvalue = new genericClass();
        $returnvalue->engineMessage = 1;
        $returnvalue->resultData = $result;
      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "No data found";
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
