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

      $cart_unique_id = isset($_GET['cart_unique_id']) ? $_GET['cart_unique_id'] : $data['cart_unique_id'];
      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $product_unique_id = isset($_GET['product_unique_id']) ? $_GET['product_unique_id'] : $data['product_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];
      $quantity = isset($_GET['quantity']) ? $_GET['quantity'] : $data['quantity'];

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $validation = $functions->cart_validation($product_unique_id, $quantity);

        if ($validation["error"] == true) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = $validation["message"];
        }
        else {

          $sql2 = "SELECT status FROM carts WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND product_unique_id=:product_unique_id AND vendor_unique_id=:vendor_unique_id";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":unique_id", $cart_unique_id);
          $query2->bindParam(":user_unique_id", $user_unique_id);
          $query2->bindParam(":product_unique_id", $product_unique_id);
          $query2->bindParam(":vendor_unique_id", $vendor_unique_id);
          $query2->execute();

          if ($query2->rowCount() > 0) {

            $sql = "UPDATE carts SET quantity=:quantity, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND product_unique_id=:product_unique_id AND vendor_unique_id=:vendor_unique_id";
            $query = $conn->prepare($sql);
            $query->bindParam(":quantity", $quantity);
            $query->bindParam(":unique_id", $cart_unique_id);
            $query->bindParam(":user_unique_id", $user_unique_id);
            $query->bindParam(":product_unique_id", $product_unique_id);
            $query->bindParam(":vendor_unique_id", $vendor_unique_id);
            $query->bindParam(":last_modified", $date_added);
            $query->execute();

            if ($query->rowCount() > 0) {
              $returnvalue = new genericClass();
              $returnvalue->engineMessage = 1;
            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Not edited (user cart quantity)";
            }
          }
          else {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Cart not found";
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
