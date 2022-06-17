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
      $our_percentage = $functions->our_percentage;
      $transaction_status = $functions->debt_incurred;
      $transaction_type = $functions->debt;
      $not_allowed_values = $functions->not_allowed_values;
      $null = $functions->null;
      $cart_checked_out = $functions->cart_checked_out;

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $cart_unique_ids = isset($_GET['cart_unique_ids']) ? $_GET['cart_unique_ids'] : $data['cart_unique_ids'];
      $payment_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : $data['payment_method'];
      $tracker_unique_id = isset($_GET['tracker_unique_id']) ? $_GET['tracker_unique_id'] : $data['tracker_unique_id'];

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $sql5 = "SELECT firstname as address_first_name, lastname as address_last_name, address, additional_information, city, state, country FROM users_addresses WHERE user_unique_id=:user_unique_id ORDER BY added_date DESC";
        $query5 = $conn->prepare($sql5);
        $query5->bindParam(":user_unique_id", $user_unique_id);
        $query5->execute();

        if ($query5->rowCount() > 0) {

          if (!is_array($cart_unique_ids)) {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Cart IDs is not an array";
          }
          else if (in_array($cart_unique_ids,$not_allowed_values)) {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Cart IDs is required";
          }
          else {

            $countErrors = 0;

            foreach ($cart_unique_ids as $key => $cart_unique_id){
              $sql3 = "SELECT shipping_fee_unique_id FROM carts WHERE user_unique_id=:user_unique_id AND unique_id=:unique_id AND status=:status";
              $query3 = $conn->prepare($sql3);
              $query3->bindParam(":user_unique_id", $user_unique_id);
              $query3->bindParam(":unique_id", $cart_unique_id);
              $query3->bindParam(":status", $active);
              $query3->execute();

              if ($query3->rowCount() > 0) {
                $the_cart_details = $query3->fetch();
                $the_shipping_fee_unique_id = $the_cart_details[0];

                if ($the_shipping_fee_unique_id == null) {$countErrors += 1;}

              }
              else {
                $countErrors += 1;
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = $key." index, Cart Not Found";
              }

            }

            if ($countErrors != 0) {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Checkout failed, (".$countErrors.") item shipping location not available";
            }
            else {

              $the_tracker_unique_id = in_array($tracker_unique_id,$not_allowed_values) ? $functions->random_str(20) : $tracker_unique_id;

              foreach ($cart_unique_ids as $key => $cart_unique_id){
                $sql4 = "SELECT product_unique_id, quantity, shipping_fee_unique_id, vendor_unique_id FROM carts WHERE user_unique_id=:user_unique_id AND unique_id=:unique_id AND status=:status";
                $query4 = $conn->prepare($sql4);
                $query4->bindParam(":user_unique_id", $user_unique_id);
                $query4->bindParam(":unique_id", $cart_unique_id);
                $query4->bindParam(":status", $active);
                $query4->execute();

                if ($query4->rowCount() > 0) {

                  $the_cart_details = $query4->fetch();
                  $product_unique_id = $the_cart_details[0];
                  $current_quantity = $the_cart_details[1];
                  $int_quantity = (int)$current_quantity;
                  $the_shipping_fee_unique_id = $the_cart_details[2];
                  $vendor_unique_id = $the_cart_details[3];

                  $sql2 = "SELECT price, sales_price, category_unique_id FROM products WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id";
                  $query2 = $conn->prepare($sql2);
                  $query2->bindParam(":unique_id", $product_unique_id);
                  $query2->bindParam(":vendor_unique_id", $vendor_unique_id);
                  $query2->execute();

                  $the_cart_price_details = $query2->rowCount() > 0 ? $query2->fetch() : null;
                  $price = $the_cart_price_details != null ? (int)$the_cart_price_details[0] : null;
                  $sales_price = $the_cart_price_details != null ? (int)$the_cart_price_details[1] : null;
                  $the_category_unique_id = $the_cart_price_details != null ? (int)$the_cart_price_details[2] : null;

                  $the_final_price_alt = $sales_price != 0 ? $sales_price : $price;

                  $sqlOrderCoupon = "SELECT coupon_unique_id FROM order_coupons WHERE tracker_unique_id=:tracker_unique_id";
                  $queryOrderCoupon = $conn->prepare($sqlOrderCoupon);
                  $queryOrderCoupon->bindParam(":tracker_unique_id", $the_tracker_unique_id);
                  $queryOrderCoupon->execute();

                  $the_order_coupon_details = $queryOrderCoupon->rowCount() > 0 ? $queryOrderCoupon->fetch() : null;
                  $coupon_unique_id = $the_order_coupon_details != null ? $the_order_coupon_details[0] : null;

                  $sql11 = "SELECT percentage, current_count, name, unique_id, user_unique_id, product_unique_id, category_unique_id FROM coupons WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND (user_unique_id=:user_unique_id OR product_unique_id=:product_unique_id OR category_unique_id=:category_unique_id) AND expiry_date >:today AND current_count!=:current_count";
                  $query11 = $conn->prepare($sql11);
                  $query11->bindParam(":unique_id", $coupon_unique_id);
                  $query11->bindParam(":vendor_unique_id", $vendor_unique_id);
                  $query11->bindParam(":user_unique_id", $user_unique_id);
                  $query11->bindParam(":product_unique_id", $product_unique_id);
                  $query11->bindParam(":category_unique_id", $the_category_unique_id);
                  $query11->bindParam(":current_count", $not_active);
                  $query11->bindParam(":today", $date_added);
                  $query11->execute();

                  $the_coupon_price_details = $query11->rowCount() > 0 ? $query11->fetch() : null;
                  $the_coupon_percentage = $the_coupon_price_details != null ? (int)$the_coupon_price_details[0] : null;
                  $the_coupon_price = $the_coupon_price_details != null ? (($the_final_price_alt * $the_coupon_percentage) / 100) * $quantity : null;
                  // $the_coupon_count = $the_coupon_price_details != null ? (int)$the_coupon_price_details[1] : null;
                  // $the_coupon_name = $the_coupon_price_details != null ? $the_coupon_price_details[2] : null;
                  // $the_coupon_unique_id = $the_coupon_price_details != null ? $the_coupon_price_details[3] : null;
                  // $the_coupon_user_unique_id = $the_coupon_price_details != null ?  $the_coupon_price_details[4] : null;
                  // $the_coupon_product_unique_id = $the_coupon_price_details != null ? $the_coupon_price_details[5] : null;
                  // $the_coupon_category_unique_id = $the_coupon_price_details != null ? $the_coupon_price_details[6] : null;

                  $sql7 = "SELECT price FROM shipping_fees WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND product_unique_id=:product_unique_id";
                  $query7 = $conn->prepare($sql7);
                  $query7->bindParam(":unique_id", $the_shipping_fee_unique_id);
                  $query7->bindParam(":product_unique_id", $product_unique_id);
                  $query7->bindParam(":vendor_unique_id", $vendor_unique_id);
                  $query7->execute();

                  $the_shipping_price_details = $query7->rowCount() > 0 ? $query7->fetch() : null;
                  $shipping_price_alt = $the_shipping_price_details != null ? (int)$the_shipping_price_details[0] : null;
                  $shipping_price = $shipping_price_alt * $int_quantity;

                  $the_final_price = $the_order_coupon_details == null ? $the_final_price_alt * $int_quantity : ($the_final_price_alt - $the_coupon_price) * $int_quantity;

                  $final_price = $the_final_price + $shipping_price;

                  // $our_percentage_amount = ($final_price * $our_percentage) / 100; // If the percentage is for both amount and shipping fee
                  $our_percentage_amount = ($the_final_price * $our_percentage) / 100; // If the percentage is for only amount

                  $your_percentage_amount = $final_price - $our_percentage_amount;

                  $order_unique_id = $functions->random_str(20);
                  $coupon_unique_id = $functions->null;
                  $shipping_fee_unique_id = in_array($the_shipping_fee_unique_id,$not_allowed_values) ? $functions->null : $the_shipping_fee_unique_id;
                  $not_active = $functions->not_active;
                  $delivery_status = $functions->processing;
                  $completion_status = $functions->checked_out;

                  $sql = "INSERT INTO orders (unique_id, user_unique_id, vendor_unique_id, product_unique_id, tracker_unique_id, coupon_unique_id, shipping_fee_unique_id, pickup_location, rider_details, quantity, amount, shipping_fee, credit, service_charge, payment_method, checked_out, paid, shipped, disputed, delivery_status, added_date, last_modified, status)
                  VALUES (:unique_id, :user_unique_id, :vendor_unique_id, :product_unique_id, :tracker_unique_id, :coupon_unique_id, :shipping_fee_unique_id, :pickup_location, :rider_details, :quantity, :amount, :shipping_fee, :credit, :service_charge, :payment_method, :checked_out, :paid, :shipped, :disputed, :delivery_status, :added_date, :last_modified, :status)";
                  $query = $conn->prepare($sql);
                  $query->bindParam(":unique_id", $order_unique_id);
                  $query->bindParam(":user_unique_id", $user_unique_id);
                  $query->bindParam(":vendor_unique_id", $vendor_unique_id);
                  $query->bindParam(":product_unique_id", $product_unique_id);
                  $query->bindParam(":tracker_unique_id", $the_tracker_unique_id);
                  $query->bindParam(":coupon_unique_id", $coupon_unique_id);
                  $query->bindParam(":shipping_fee_unique_id", $shipping_fee_unique_id);
                  $query->bindParam(":pickup_location", $not_active);
                  $query->bindParam(":rider_details", $null);
                  $query->bindParam(":quantity", $int_quantity);
                  $query->bindParam(":amount", $final_price);
                  $query->bindParam(":shipping_fee", $shipping_price);
                  $query->bindParam(":credit", $your_percentage_amount);
                  $query->bindParam(":service_charge", $our_percentage_amount);
                  $query->bindParam(":payment_method", $payment_method);
                  $query->bindParam(":checked_out", $active);
                  $query->bindParam(":paid", $not_active);
                  $query->bindParam(":shipped", $not_active);
                  $query->bindParam(":disputed", $not_active);
                  $query->bindParam(":delivery_status", $delivery_status);
                  $query->bindParam(":added_date", $date_added);
                  $query->bindParam(":last_modified", $date_added);
                  $query->bindParam(":status", $active);
                  $query->execute();

                  if ($query->rowCount() > 0) {

                    $order_history_unique_id = $functions->random_str(20);

                    $sql10 = "INSERT INTO order_history (unique_id, user_unique_id, order_unique_id, price, completion, added_date, last_modified, status)
                    VALUES (:unique_id, :user_unique_id, :order_unique_id, :price, :completion, :added_date, :last_modified, :status)";
                    $query10 = $conn->prepare($sql10);
                    $query10->bindParam(":unique_id", $order_history_unique_id);
                    $query10->bindParam(":user_unique_id", $user_unique_id);
                    $query10->bindParam(":order_unique_id", $order_unique_id);
                    $query10->bindParam(":price", $null);
                    $query10->bindParam(":completion", $completion_status);
                    $query10->bindParam(":added_date", $date_added);
                    $query10->bindParam(":last_modified", $date_added);
                    $query10->bindParam(":status", $active);
                    $query10->execute();

                    if ($query10->rowCount() > 0) {

                      $sql6 = "UPDATE carts SET status=:status, last_modified=:last_modified WHERE user_unique_id=:user_unique_id AND unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id";
                      $query6 = $conn->prepare($sql6);
                      $query6->bindParam(":status", $cart_checked_out);
                      $query6->bindParam(":user_unique_id", $user_unique_id);
                      $query6->bindParam(":unique_id", $cart_unique_id);
                      $query6->bindParam(":vendor_unique_id", $vendor_unique_id);
                      $query6->bindParam(":last_modified", $date_added);
                      $query6->execute();

                      if ($query6->rowCount() > 0) {

                        if ($payment_method != "Card" && $payment_method != "Wallet") {

                          $transaction_unique_id = $functions->random_str(20);

                          $details = $transaction_type." : ".$our_percentage_amount." Naira ".$transaction_status." on order(".$order_unique_id.") tracking id(".$the_tracker_unique_id.")";

                          $sql8 = "INSERT INTO transactions (unique_id, vendor_unique_id, type, amount, transaction_status, details, added_date, last_modified, status)
                          VALUES (:unique_id, :vendor_unique_id, :type, :amount, :transaction_status, :details, :added_date, :last_modified, :status)";
                          $query8 = $conn->prepare($sql8);
                          $query8->bindParam(":unique_id", $transaction_unique_id);
                          $query8->bindParam(":vendor_unique_id", $vendor_unique_id);
                          $query8->bindParam(":type", $transaction_type);
                          $query8->bindParam(":amount", $our_percentage_amount);
                          $query8->bindParam(":transaction_status", $transaction_status);
                          $query8->bindParam(":details", $details);
                          $query8->bindParam(":added_date", $date_added);
                          $query8->bindParam(":last_modified", $date_added);
                          $query8->bindParam(":status", $active);
                          $query8->execute();

                          if ($query8->rowCount() > 0) {
                            $returnvalue = new genericClass();
                            $returnvalue->engineMessage = 1;
                            $returnvalue->resultData = $the_tracker_unique_id;
                          }
                          else {
                            $returnvalue = new genericClass();
                            $returnvalue->engineMessage = 1;
                            $returnvalue->resultData = $the_tracker_unique_id;
                          }

                        }
                        else {
                          $returnvalue = new genericClass();
                          $returnvalue->engineMessage = 1;
                          $returnvalue->resultData = $the_tracker_unique_id;
                        }

                      }
                      else {
                        $returnvalue = new genericClass();
                        $returnvalue->engineError = 2;
                        $returnvalue->engineErrorMessage = $key." index, Cart not checked out";
                      }

                    }
                    else {
                      $returnvalue = new genericClass();
                      $returnvalue->engineError = 2;
                      $returnvalue->engineErrorMessage = $key." index, Order history not updated";
                    }

                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = $key." index, Not inserted (user order)";
                  }

                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = $key." index, Cart Not Found";
                }
              }

            }

          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Address Not Found";
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
