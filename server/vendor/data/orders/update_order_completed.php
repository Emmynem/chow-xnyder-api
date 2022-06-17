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

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $sqlOrder = "SELECT product_unique_id, coupon_unique_id, shipping_fee_unique_id, quantity, payment_method, delivery_status, amount, shipping_fee FROM orders WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
        $queryOrder = $conn->prepare($sqlOrder);
        $queryOrder->bindParam(":unique_id", $order_unique_id);
        $queryOrder->bindParam(":user_unique_id", $user_unique_id);
        $queryOrder->bindParam(":vendor_unique_id", $vendor_unique_id);
        $queryOrder->bindParam(":status", $active);
        $queryOrder->execute();

        if ($queryOrder->rowCount() > 0) {

          $the_order_details = $queryOrder->fetch();
          $the_product_unique_id = $the_order_details[0];
          // $the_coupon_unique_id = $the_order_details[1];
          $the_shipping_fee_unique_id = $the_order_details[2];
          $the_quantity = $the_order_details[3];
          $the_payment_method = $the_order_details[4];
          $the_delivery_status = $the_order_details[5];
          $the_amount = $the_order_details[6];
          $the_shipping_fee = $the_order_details[7];

          $sqlCategory = "SELECT category_unique_id FROM products WHERE unique_id=:unique_id";
          $queryCategory = $conn->prepare($sqlCategory);
          $queryCategory->bindParam(":unique_id", $the_product_unique_id);
          $queryCategory->execute();

          $the_category_details = $queryCategory->rowCount() > 0 ? $queryCategory->fetch() : null;
          $the_category_unique_id = $the_category_details != null ? $the_category_details[0] : null;

          if (strtolower($the_delivery_status) == strtolower($completion_status)) {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Order already ".$completion_status;
          }
          else {

            $sql2 = "UPDATE orders SET delivery_status=:delivery_status, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND tracker_unique_id=:tracker_unique_id AND vendor_unique_id=:vendor_unique_id";
            $query2 = $conn->prepare($sql2);
            $query2->bindParam(":unique_id", $order_unique_id);
            $query2->bindParam(":delivery_status", $completion_status);
            $query2->bindParam(":user_unique_id", $user_unique_id);
            $query2->bindParam(":tracker_unique_id", $tracker_unique_id);
            $query2->bindParam(":vendor_unique_id", $vendor_unique_id);
            $query2->bindParam(":last_modified", $date_added);
            $query2->execute();

            if ($query2->rowCount() > 0) {

              $order_history_unique_id = $functions->random_str(20);

              $sql = "INSERT INTO order_history (unique_id, user_unique_id, order_unique_id, price, completion, added_date, last_modified, status)
              VALUES (:unique_id, :user_unique_id, :order_unique_id, :price, :completion, :added_date, :last_modified, :status)";
              $query = $conn->prepare($sql);
              $query->bindParam(":unique_id", $order_history_unique_id);
              $query->bindParam(":user_unique_id", $user_unique_id);
              $query->bindParam(":order_unique_id", $order_unique_id);
              $query->bindParam(":price", $null);
              $query->bindParam(":completion", $completion_status);
              $query->bindParam(":added_date", $date_added);
              $query->bindParam(":last_modified", $date_added);
              $query->bindParam(":status", $active);
              $query->execute();

              if ($query->rowCount() > 0) {

                $sqlProduct = "SELECT name FROM products WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
                $queryProduct = $conn->prepare($sqlProduct);
                $queryProduct->bindParam(":unique_id", $the_product_unique_id);
                $queryProduct->bindParam(":vendor_unique_id", $vendor_unique_id);
                $queryProduct->bindParam(":status", $active);
                $queryProduct->execute();

                $the_product_details = $queryProduct->rowCount() > 0 ? $queryProduct->fetch() : null;
                $product_name = $the_product_details != null ? $the_product_details[0] : null;

                $product_last_price = $the_amount;

                $sqlOrderCoupon = "SELECT coupon_unique_id FROM order_coupons WHERE tracker_unique_id=:tracker_unique_id";
                $queryOrderCoupon = $conn->prepare($sqlOrderCoupon);
                $queryOrderCoupon->bindParam(":tracker_unique_id", $tracker_unique_id);
                $queryOrderCoupon->execute();

                $the_order_coupon_details = $queryOrderCoupon->rowCount() > 0 ? $queryOrderCoupon->fetch() : null;
                $the_coupon_unique_id = $the_order_coupon_details != null ? $the_order_coupon_details[0] : null;

                $sqlCoupon = "SELECT percentage, name, code, user_unique_id, product_unique_id, category_unique_id FROM coupons WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id";
                $queryCoupon = $conn->prepare($sqlCoupon);
                $queryCoupon->bindParam(":unique_id", $the_coupon_unique_id);
                $queryCoupon->bindParam(":vendor_unique_id", $vendor_unique_id);
                $queryCoupon->execute();

                $the_coupon_details = $queryCoupon->rowCount() > 0 ? $queryCoupon->fetch() : null;
                $coupon_user_unique_id = $the_coupon_details != null ? $the_coupon_details[3] : null;
                $coupon_product_unique_id = $the_coupon_details != null ? $the_coupon_details[4] : null;
                $coupon_category_unique_id = $the_coupon_details != null ? $the_coupon_details[5] : null;
                $coupon_percentage = ($the_coupon_details != null && ($coupon_product_unique_id == $the_product_unique_id || $coupon_category_unique_id == $the_category_unique_id || $coupon_user_unique_id == $user_unique_id)) ? (int)$the_coupon_details[0] : null;
                $coupon_name = ($the_coupon_details != null && ($coupon_product_unique_id == $the_product_unique_id || $coupon_category_unique_id == $the_category_unique_id || $coupon_user_unique_id == $user_unique_id)) ? $the_coupon_details[1] : null;
                $coupon_code = ($the_coupon_details != null && ($coupon_product_unique_id == $the_product_unique_id || $coupon_category_unique_id == $the_category_unique_id || $coupon_user_unique_id == $user_unique_id)) ? $the_coupon_details[2] : null;

                $coupon_price = ($the_coupon_details != null && ($coupon_product_unique_id == $the_product_unique_id || $coupon_category_unique_id == $the_category_unique_id || $coupon_user_unique_id == $user_unique_id)) ? (($product_last_price * $coupon_percentage) / 100) : null;

                $sqlShippingFee = "SELECT price, city, state, country FROM shipping_fees WHERE unique_id=:unique_id AND product_unique_id=:product_unique_id AND status=:status";
                $queryShippingFee = $conn->prepare($sqlShippingFee);
                $queryShippingFee->bindParam(":unique_id", $the_shipping_fee_unique_id);
                $queryShippingFee->bindParam(":product_unique_id", $the_product_unique_id);
                $queryShippingFee->bindParam(":status", $active);
                $queryShippingFee->execute();

                $the_shipping_fee_details = $queryShippingFee->rowCount() > 0 ? $queryShippingFee->fetch() : null;
                // $shipping_fee_price = $the_shipping_fee_details != null ? (int)$the_shipping_fee_details[0] : null;
                $shipping_fee_price = $the_shipping_fee; // The new shipping fee
                $shipping_fee_city = $the_shipping_fee_details != null ? $the_shipping_fee_details[1] : null;
                $shipping_fee_state = $the_shipping_fee_details != null ? $the_shipping_fee_details[2] : null;
                $shipping_fee_country = $the_shipping_fee_details != null ? $the_shipping_fee_details[3] : null;

                $sqlUserAddress = "SELECT firstname as address_first_name, lastname as address_last_name, address, additional_information FROM users_addresses WHERE user_unique_id=:user_unique_id AND city=:city AND state=:state AND country=:country AND status=:status";
                $queryUserAddress = $conn->prepare($sqlUserAddress);
                $queryUserAddress->bindParam(":user_unique_id", $user_unique_id);
                $queryUserAddress->bindParam(":city", $shipping_fee_city);
                $queryUserAddress->bindParam(":state", $shipping_fee_state);
                $queryUserAddress->bindParam(":country", $shipping_fee_country);
                $queryUserAddress->bindParam(":status", $active);
                $queryUserAddress->execute();

                $the_user_address_details = $queryUserAddress->rowCount() > 0 ? $queryUserAddress->fetch() : null;
                $user_address_first_name = $the_user_address_details != null ? $the_user_address_details[0] : null;
                $user_address_last_name = $the_user_address_details != null ? $the_user_address_details[1] : null;
                $user_address_address = $the_user_address_details != null ? $the_user_address_details[2] : null;
                $user_address_addtional_information = $the_user_address_details != null ? $the_user_address_details[3] : null;

                $user_address_fullname = $user_address_first_name." ".$user_address_last_name;
                $user_address_full_address = $user_address_address." ".$user_address_addtional_information;

                $order_full_price = $coupon_price != null ? $product_last_price - $coupon_price : $product_last_price;

                $orders_completed_unique_id = $functions->random_str(20);

                $sqlOrdersCompleted = "INSERT INTO orders_completed (unique_id, user_unique_id, vendor_unique_id, order_unique_id, tracker_unique_id, quantity, payment_method, product_name,
                  coupon_name, coupon_code, coupon_percentage, coupon_price, user_address_fullname, user_full_address, city, state, country, shipping_fee_price, total_price, added_date, last_modified, status)
                VALUES (:unique_id, :user_unique_id, :vendor_unique_id, :order_unique_id, :tracker_unique_id, :quantity, :payment_method, :product_name,
                  :coupon_name, :coupon_code, :coupon_percentage, :coupon_price, :user_address_fullname, :user_full_address, :city, :state, :country, :shipping_fee_price, :total_price, :added_date, :last_modified, :status)";
                $queryOrdersCompleted = $conn->prepare($sqlOrdersCompleted);
                $queryOrdersCompleted->bindParam(":unique_id", $orders_completed_unique_id);
                $queryOrdersCompleted->bindParam(":user_unique_id", $user_unique_id);
                $queryOrdersCompleted->bindParam(":vendor_unique_id", $vendor_unique_id);
                $queryOrdersCompleted->bindParam(":order_unique_id", $order_unique_id);
                $queryOrdersCompleted->bindParam(":tracker_unique_id", $tracker_unique_id);
                $queryOrdersCompleted->bindParam(":quantity", $the_quantity);
                $queryOrdersCompleted->bindParam(":payment_method", $the_payment_method);
                $queryOrdersCompleted->bindParam(":product_name", $product_name);
                $queryOrdersCompleted->bindParam(":coupon_name", $coupon_name);
                $queryOrdersCompleted->bindParam(":coupon_code", $coupon_code);
                $queryOrdersCompleted->bindParam(":coupon_percentage", $coupon_percentage);
                $queryOrdersCompleted->bindParam(":coupon_price", $coupon_price);
                $queryOrdersCompleted->bindParam(":user_address_fullname", $user_address_fullname);
                $queryOrdersCompleted->bindParam(":user_full_address", $user_address_full_address);
                $queryOrdersCompleted->bindParam(":city", $shipping_fee_city);
                $queryOrdersCompleted->bindParam(":state", $shipping_fee_state);
                $queryOrdersCompleted->bindParam(":country", $shipping_fee_country);
                $queryOrdersCompleted->bindParam(":shipping_fee_price", $shipping_fee_price);
                $queryOrdersCompleted->bindParam(":total_price", $order_full_price);
                $queryOrdersCompleted->bindParam(":added_date", $date_added);
                $queryOrdersCompleted->bindParam(":last_modified", $date_added);
                $queryOrdersCompleted->bindParam(":status", $active);
                $queryOrdersCompleted->execute();

                if ($queryOrdersCompleted->rowCount() > 0) {
                  $returnvalue = new genericClass();
                  $returnvalue->engineMessage = 1;
                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Orders completed not inserted";
                }

              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Order history not inserted";
              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Orders not updated";
            }

          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Order not found";
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
