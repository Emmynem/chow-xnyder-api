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

  $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data["user_unique_id"];

  if ($connected) {

    try {
      $conn->beginTransaction();

      $active = $functions->active;

      $sql_cart = "SELECT COUNT(*) FROM carts WHERE user_unique_id=:user_unique_id AND status=:status";
      $query_cart = $conn->prepare($sql_cart);
      $query_cart->bindParam(":user_unique_id", $user_unique_id);
      $query_cart->bindParam(":status", $active);
      $query_cart->execute();

      if ($query_cart->rowCount() > 0) {
        $the_user_cart_count = $query_cart->fetch();
        $user_cart_count = (int)$the_user_cart_count[0];
      }
      else {
        $user_cart_count = 0;
      }

      $sql_order = "SELECT COUNT(*) FROM orders WHERE user_unique_id=:user_unique_id AND status=:status";
      $query_order = $conn->prepare($sql_order);
      $query_order->bindParam(":user_unique_id", $user_unique_id);
      $query_order->bindParam(":status", $active);
      $query_order->execute();

      if ($query_order->rowCount() > 0) {
        $the_user_order_count = $query_order->fetch();
        $user_order_count = (int)$the_user_order_count[0];
      }
      else {
        $user_order_count = 0;
      }

      $sql_referral = "SELECT COUNT(*) FROM referrals WHERE referral_user_unique_id=:referral_user_unique_id AND status=:status";
      $query_referral = $conn->prepare($sql_referral);
      $query_referral->bindParam(":referral_user_unique_id", $user_unique_id);
      $query_referral->bindParam(":status", $active);
      $query_referral->execute();

      if ($query_referral->rowCount() > 0) {
        $the_user_referral_count = $query_referral->fetch();
        $user_referral_count = (int)$the_user_referral_count[0];
      }
      else {
        $user_referral_count = 0;
      }

      $sql_favorite = "SELECT COUNT(*) FROM favorites WHERE user_unique_id=:user_unique_id AND status=:status";
      $query_favorite = $conn->prepare($sql_favorite);
      $query_favorite->bindParam(":user_unique_id", $user_unique_id);
      $query_favorite->bindParam(":status", $active);
      $query_favorite->execute();

      if ($query_favorite->rowCount() > 0) {
        $the_user_favorite_count = $query_favorite->fetch();
        $user_favorite_count = (int)$the_user_favorite_count[0];
      }
      else {
        $user_favorite_count = 0;
      }

      $sql_coupon = "SELECT COUNT(*) FROM coupons WHERE user_unique_id=:user_unique_id OR product_unique_id IS NOT NULL OR category_unique_id IS NOT NULL AND status=:status";
      $query_coupon = $conn->prepare($sql_coupon);
      $query_coupon->bindParam(":user_unique_id", $user_unique_id);
      $query_coupon->bindParam(":status", $active);
      $query_coupon->execute();

      if ($query_coupon->rowCount() > 0) {
        $the_user_coupon_count = $query_coupon->fetch();
        $user_coupon_count = (int)$the_user_coupon_count[0];
      }
      else {
        $user_coupon_count = 0;
      }

      $stats_object = array(
        "user_cart_count"=>$user_cart_count,
        "user_order_count"=>$user_order_count,
        "user_referral_count"=>$user_referral_count,
        "user_favorite_count"=>$user_favorite_count,
        "user_coupon_count"=>$user_coupon_count
      );

      $returnvalue = new genericClass();
      $returnvalue->engineMessage = 1;
      $returnvalue->resultData = $stats_object;

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
