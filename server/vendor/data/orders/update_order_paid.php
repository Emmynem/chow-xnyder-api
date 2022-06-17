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
      $our_percentage_for_service_charge = $functions->our_percentage_for_service_charge;
      $null = $functions->null;
      $completion = $functions->completed;
      $completion_status = $functions->paid;

      $order_unique_id = isset($_GET['order_unique_id']) ? $_GET['order_unique_id'] : $data['order_unique_id'];
      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $vendor_user_unique_id = isset($_GET['vendor_user_unique_id']) ? $_GET['vendor_user_unique_id'] : $data['vendor_user_unique_id'];
      $tracker_unique_id = isset($_GET['tracker_unique_id']) ? $_GET['tracker_unique_id'] : $data['tracker_unique_id'];
      $product_unique_id = isset($_GET['product_unique_id']) ? $_GET['product_unique_id'] : $data['product_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];

      $sqlSearchUser = "SELECT unique_id FROM vendor_users WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $vendor_user_unique_id);
      $querySearchUser->bindParam(":vendor_unique_id", $vendor_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $sqlQuantity = "SELECT quantity, coupon_unique_id, shipping_fee_unique_id, amount, shipping_fee, credit, service_charge, payment_method FROM orders WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND tracker_unique_id=:tracker_unique_id AND vendor_unique_id=:vendor_unique_id AND paid!=:paid";
        $queryQuantity = $conn->prepare($sqlQuantity);
        $queryQuantity->bindParam(":unique_id", $order_unique_id);
        $queryQuantity->bindParam(":user_unique_id", $user_unique_id);
        $queryQuantity->bindParam(":tracker_unique_id", $tracker_unique_id);
        $queryQuantity->bindParam(":vendor_unique_id", $vendor_unique_id);
        $queryQuantity->bindParam(":paid", $active);
        $queryQuantity->execute();

        if ($queryQuantity->rowCount() > 0) {

          $the_quantity_details = $queryQuantity->fetch();
          $quantity = (int)$the_quantity_details[0];
          $amount = (int)$the_quantity_details[3];
          $shipping_fee = (int)$the_quantity_details[4];
          $credit_amount = (int)$the_quantity_details[5];
          $service_charge_amount = (int)$the_quantity_details[6];
          $payment_method = $the_quantity_details[7];

          $sqlOrderCoupon = "SELECT coupon_unique_id FROM order_coupons WHERE tracker_unique_id=:tracker_unique_id";
          $queryOrderCoupon = $conn->prepare($sqlOrderCoupon);
          $queryOrderCoupon->bindParam(":tracker_unique_id", $tracker_unique_id);
          $queryOrderCoupon->execute();

          $the_order_coupon_details = $queryOrderCoupon->rowCount() > 0 ? $queryOrderCoupon->fetch() : null;
          $coupon_unique_id = $the_order_coupon_details != null ? $the_order_coupon_details[0] : null;
          // Old one I used
          // $coupon_unique_id = $the_quantity_details[1];
          $shipping_fee_unique_id = $the_quantity_details[2];

          $sqlCategory = "SELECT category_unique_id, price, sales_price, stock_remaining FROM products WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id";
          $queryCategory = $conn->prepare($sqlCategory);
          $queryCategory->bindParam(":unique_id", $product_unique_id);
          $queryCategory->bindParam(":vendor_unique_id", $vendor_unique_id);
          $queryCategory->execute();

          if ($queryCategory->rowCount() > 0) {
            $the_price_details = $queryCategory->fetch();
            $the_category_unique_id = $the_price_details[0];
            $product_price = (int)$the_price_details[1];
            $product_sales_price = (int)$the_price_details[2];
            $stock_remaining = (int)$the_price_details[3];
            $new_stock = $stock_remaining - $quantity;

            $product_full_price = $amount;

            $sql3 = "SELECT percentage, current_count, name, unique_id, user_unique_id, product_unique_id, category_unique_id FROM coupons WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND (user_unique_id=:user_unique_id OR product_unique_id=:product_unique_id OR category_unique_id=:category_unique_id) AND expiry_date >:today AND current_count!=0";
            $query3 = $conn->prepare($sql3);
            $query3->bindParam(":unique_id", $coupon_unique_id);
            $query3->bindParam(":vendor_unique_id", $vendor_unique_id);
            $query3->bindParam(":user_unique_id", $user_unique_id);
            $query3->bindParam(":product_unique_id", $product_unique_id);
            $query3->bindParam(":category_unique_id", $the_category_unique_id);
            $query3->bindParam(":today", $date_added);
            $query3->execute();

            if ($query3->rowCount() > 0) {
              $the_coupon_price_details = $query3->fetch();
              $the_coupon_percentage = (int)$the_coupon_price_details[0];
              $the_coupon_price = (($product_full_price * $the_coupon_percentage) / 100) * $quantity;
              $the_coupon_count = (int)$the_coupon_price_details[1];
              $the_coupon_name = $the_coupon_price_details[2];
              $the_coupon_unique_id = $the_coupon_price_details[3];
              $the_coupon_user_unique_id = $the_coupon_price_details[4] != null ? $the_coupon_price_details[4] : $user_unique_id;
              $the_coupon_product_unique_id = $the_coupon_price_details[5];
              $the_coupon_category_unique_id = $the_coupon_price_details[6];

              if ($the_coupon_count != 0) {

                $the_shipping_fee_price = $shipping_fee;

                // $final_price = (($product_full_price + $the_shipping_fee_price) * $quantity ) - $the_coupon_price;
                $final_price = $product_full_price - $the_coupon_price;
                // $final_price = $product_full_price;
                $coupon_history_unique_id = $functions->random_str(20);

                $sql5 = "INSERT INTO coupon_history (unique_id, user_unique_id, product_unique_id, category_unique_id, name, price, completion, added_date, last_modified, status)
                VALUES (:unique_id, :user_unique_id, :product_unique_id, :category_unique_id, :name, :price, :completion, :added_date, :last_modified, :status)";
                $query5 = $conn->prepare($sql5);
                $query5->bindParam(":unique_id", $coupon_history_unique_id);
                $query5->bindParam(":user_unique_id", $the_coupon_user_unique_id);
                $query5->bindParam(":product_unique_id", $the_coupon_product_unique_id);
                $query5->bindParam(":category_unique_id", $the_coupon_category_unique_id);
                $query5->bindParam(":name", $the_coupon_name);
                $query5->bindParam(":price", $the_coupon_price);
                $query5->bindParam(":completion", $completion);
                $query5->bindParam(":added_date", $date_added);
                $query5->bindParam(":last_modified", $date_added);
                $query5->bindParam(":status", $active);
                $query5->execute();

                if ($query5->rowCount() > 0) {

                  $current_count_update = $the_coupon_count - 1;

                  $sql6 = "UPDATE coupons SET current_count=:current_count, last_modified=:last_modified WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND (user_unique_id=:user_unique_id OR product_unique_id=:product_unique_id OR category_unique_id=:category_unique_id)";
                  $query6 = $conn->prepare($sql6);
                  $query6->bindParam(":current_count", $current_count_update);
                  $query6->bindParam(":unique_id", $the_coupon_unique_id);
                  $query6->bindParam(":vendor_unique_id", $vendor_unique_id);
                  $query6->bindParam(":user_unique_id", $the_coupon_user_unique_id);
                  $query6->bindParam(":product_unique_id", $the_coupon_product_unique_id);
                  $query6->bindParam(":category_unique_id", $the_coupon_category_unique_id);
                  $query6->bindParam(":last_modified", $date_added);
                  $query6->execute();

                  if ($query6->rowCount() > 0) {

                    $sql9 = "UPDATE orders SET paid=:paid, delivery_status=:delivery_status, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND vendor_unique_id=:vendor_unique_id";
                    $query9 = $conn->prepare($sql9);
                    $query9->bindParam(":paid", $active);
                    $query9->bindParam(":delivery_status", $completion_status);
                    $query9->bindParam(":unique_id", $order_unique_id);
                    $query9->bindParam(":user_unique_id", $user_unique_id);
                    $query9->bindParam(":vendor_unique_id", $vendor_unique_id);
                    $query9->bindParam(":last_modified", $date_added);
                    $query9->execute();

                    if ($query9->rowCount() > 0) {

                      $order_history_unique_id = $functions->random_str(20);

                      $sql10 = "INSERT INTO order_history (unique_id, user_unique_id, order_unique_id, price, completion, added_date, last_modified, status)
                      VALUES (:unique_id, :user_unique_id, :order_unique_id, :price, :completion, :added_date, :last_modified, :status)";
                      $query10 = $conn->prepare($sql10);
                      $query10->bindParam(":unique_id", $order_history_unique_id);
                      $query10->bindParam(":user_unique_id", $user_unique_id);
                      $query10->bindParam(":order_unique_id", $order_unique_id);
                      $query10->bindParam(":price", $final_price);
                      $query10->bindParam(":completion", $completion_status);
                      $query10->bindParam(":added_date", $date_added);
                      $query10->bindParam(":last_modified", $date_added);
                      $query10->bindParam(":status", $active);
                      $query10->execute();

                      if ($query10->rowCount() > 0) {

                        $sql11 = "UPDATE products SET stock_remaining=:stock_remaining WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id";
                        $query11 = $conn->prepare($sql11);
                        $query11->bindParam(":unique_id", $product_unique_id);
                        $query11->bindParam(":vendor_unique_id", $vendor_unique_id);
                        $query11->bindParam(":stock_remaining", $new_stock);
                        $query11->execute();

                        if ($query11->rowCount() > 0) {

                          if ($current_count_update == 0) {
                            $sql6 = "UPDATE coupons SET completion=:completion, last_modified=:last_modified WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND (user_unique_id=:user_unique_id OR product_unique_id=:product_unique_id OR category_unique_id=:category_unique_id)";
                            $query6 = $conn->prepare($sql6);
                            $query6->bindParam(":completion", $completion);
                            $query6->bindParam(":unique_id", $the_coupon_unique_id);
                            $query6->bindParam(":vendor_unique_id", $vendor_unique_id);
                            $query6->bindParam(":user_unique_id", $the_coupon_user_unique_id);
                            $query6->bindParam(":product_unique_id", $the_coupon_product_unique_id);
                            $query6->bindParam(":category_unique_id", $the_coupon_category_unique_id);
                            $query6->bindParam(":last_modified", $date_added);
                            $query6->execute();

                            if ($query6->rowCount() > 0) {

                              $sql8 = "SELECT balance, service_charge FROM vendors WHERE unique_id=:unique_id";
                              $query8 = $conn->prepare($sql8);
                              $query8->bindParam(":unique_id", $vendor_unique_id);
                              $query8->execute();

                              $the_service_charge_details = $query8->rowCount() > 0 ? $query8->fetch() : null;
                              $vendor_balance = $the_service_charge_details != null ? (int)$the_service_charge_details[0] : null;
                              $vendor_service_charge = $the_service_charge_details != null ? (int)$the_service_charge_details[1] : null;

                              if ($payment_method == "Card" || $payment_method == "Wallet") {

                                $total_service_charge = $vendor_service_charge + $service_charge_amount;

                              	$calculate_service_charge_if_not_greater = $final_price > $total_service_charge ? $final_price - $total_service_charge : 0;

                              	$calculate_service_charge_if_greater = $total_service_charge > $final_price ? $total_service_charge - $final_price : $total_service_charge;

                              	$new_card_balance = $vendor_balance + $calculate_service_charge_if_not_greater;

                              	$new_service_charge_for_card = $new_card_balance > $calculate_service_charge_if_greater ? 0 : $total_service_charge - $final_price;

                                $sql12 = "UPDATE vendors SET balance=:balance, service_charge=:service_charge, last_modified=:last_modified WHERE unique_id=:unique_id";
                                $query12 = $conn->prepare($sql12);
                                $query12->bindParam(":unique_id", $vendor_unique_id);
                                $query12->bindParam(":balance", $new_card_balance);
                                $query12->bindParam(":service_charge", $new_service_charge_for_card);
                                $query12->bindParam(":last_modified", $date_added);
                                $query12->execute();

                                if ($query12->rowCount() > 0) {
                                  $returnvalue = new genericClass();
                                  $returnvalue->engineMessage = 1;
                                }
                                else {
                                  $returnvalue = new genericClass();
                                  $returnvalue->engineError = 2;
                                  $returnvalue->engineErrorMessage = "Vendor balance not updated";
                                }

                              }
                              else {

                                $total_service_charge = $vendor_service_charge + $service_charge_amount;

                              	$calculate_service_charge_if_not_greater = $final_price > $total_service_charge ? $final_price - $service_charge_amount : 0;

                              	$supposed_cash_balance = $vendor_balance + $calculate_service_charge_if_not_greater;

                              	$preferred_cash_balance = $vendor_balance + ($final_price - $service_charge_amount);

                              	$new_cash_balance = $total_service_charge > $final_price ? $preferred_cash_balance : $supposed_cash_balance;

                              	$new_service_charge_for_cash = $total_service_charge;

                                $sql12 = "UPDATE vendors SET balance=:balance, service_charge=:service_charge, last_modified=:last_modified WHERE unique_id=:unique_id";
                                $query12 = $conn->prepare($sql12);
                                $query12->bindParam(":unique_id", $vendor_unique_id);
                                $query12->bindParam(":balance", $new_cash_balance);
                                $query12->bindParam(":service_charge", $new_service_charge_for_cash);
                                $query12->bindParam(":last_modified", $date_added);
                                $query12->execute();

                                if ($query12->rowCount() > 0) {
                                  $returnvalue = new genericClass();
                                  $returnvalue->engineMessage = 1;
                                }
                                else {
                                  $returnvalue = new genericClass();
                                  $returnvalue->engineError = 2;
                                  $returnvalue->engineErrorMessage = "Vendor balance not updated";
                                }

                              }

                            }
                            else {
                              $returnvalue = new genericClass();
                              $returnvalue->engineError = 2;
                              $returnvalue->engineErrorMessage = "Coupons Not updated";
                            }
                          }
                          else {

                            $sql8 = "SELECT balance, service_charge FROM vendors WHERE unique_id=:unique_id";
                            $query8 = $conn->prepare($sql8);
                            $query8->bindParam(":unique_id", $vendor_unique_id);
                            $query8->execute();

                            $the_service_charge_details = $query8->rowCount() > 0 ? $query8->fetch() : null;
                            $vendor_balance = $the_service_charge_details != null ? (int)$the_service_charge_details[0] : null;
                            $vendor_service_charge = $the_service_charge_details != null ? (int)$the_service_charge_details[1] : null;

                            if ($payment_method == "Card" || $payment_method == "Wallet") {

                              $total_service_charge = $vendor_service_charge + $service_charge_amount;

                            	$calculate_service_charge_if_not_greater = $final_price > $total_service_charge ? $final_price - $total_service_charge : 0;

                            	$calculate_service_charge_if_greater = $total_service_charge > $final_price ? $total_service_charge - $final_price : $total_service_charge;

                            	$new_card_balance = $vendor_balance + $calculate_service_charge_if_not_greater;

                            	$new_service_charge_for_card = $new_card_balance > $calculate_service_charge_if_greater ? 0 : $total_service_charge - $final_price;

                              $sql12 = "UPDATE vendors SET balance=:balance, service_charge=:service_charge, last_modified=:last_modified WHERE unique_id=:unique_id";
                              $query12 = $conn->prepare($sql12);
                              $query12->bindParam(":unique_id", $vendor_unique_id);
                              $query12->bindParam(":balance", $new_card_balance);
                              $query12->bindParam(":service_charge", $new_service_charge_for_card);
                              $query12->bindParam(":last_modified", $date_added);
                              $query12->execute();

                              if ($query12->rowCount() > 0) {
                                $returnvalue = new genericClass();
                                $returnvalue->engineMessage = 1;
                              }
                              else {
                                $returnvalue = new genericClass();
                                $returnvalue->engineError = 2;
                                $returnvalue->engineErrorMessage = "Vendor balance not updated";
                              }

                            }
                            else {

                              $total_service_charge = $vendor_service_charge + $service_charge_amount;

                            	$calculate_service_charge_if_not_greater = $final_price > $total_service_charge ? $final_price - $service_charge_amount : 0;

                            	$supposed_cash_balance = $vendor_balance + $calculate_service_charge_if_not_greater;

                            	$preferred_cash_balance = $vendor_balance + ($final_price - $service_charge_amount);

                            	$new_cash_balance = $total_service_charge > $final_price ? $preferred_cash_balance : $supposed_cash_balance;

                            	$new_service_charge_for_cash = $total_service_charge;

                              $sql12 = "UPDATE vendors SET balance=:balance, service_charge=:service_charge, last_modified=:last_modified WHERE unique_id=:unique_id";
                              $query12 = $conn->prepare($sql12);
                              $query12->bindParam(":unique_id", $vendor_unique_id);
                              $query12->bindParam(":balance", $new_cash_balance);
                              $query12->bindParam(":service_charge", $new_service_charge_for_cash);
                              $query12->bindParam(":last_modified", $date_added);
                              $query12->execute();

                              if ($query12->rowCount() > 0) {
                                $returnvalue = new genericClass();
                                $returnvalue->engineMessage = 1;
                              }
                              else {
                                $returnvalue = new genericClass();
                                $returnvalue->engineError = 2;
                                $returnvalue->engineErrorMessage = "Vendor balance not updated";
                              }

                            }

                          }

                        }
                        else {
                          $returnvalue = new genericClass();
                          $returnvalue->engineError = 2;
                          $returnvalue->engineErrorMessage = "Vendor's stock not updated";
                        }

                      }
                      else {
                        $returnvalue = new genericClass();
                        $returnvalue->engineError = 2;
                        $returnvalue->engineErrorMessage = "Order history not updated";
                      }

                    }
                    else {
                      $returnvalue = new genericClass();
                      $returnvalue->engineError = 2;
                      $returnvalue->engineErrorMessage = "Orders not updated";
                    }

                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = "Coupons Not updated";
                  }

                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Coupon history not inserted";
                }

              }
              else{

                $the_shipping_fee_price = $shipping_fee;

                // $final_price = ($product_full_price + $the_shipping_fee_price) * $quantity;
                $final_price = $product_full_price;

                $sql9 = "UPDATE orders SET paid=:paid, delivery_status=:delivery_status, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND vendor_unique_id=:vendor_unique_id";
                $query9 = $conn->prepare($sql9);
                $query9->bindParam(":paid", $active);
                $query9->bindParam(":delivery_status", $completion_status);
                $query9->bindParam(":unique_id", $order_unique_id);
                $query9->bindParam(":user_unique_id", $user_unique_id);
                $query9->bindParam(":vendor_unique_id", $vendor_unique_id);
                $query9->bindParam(":last_modified", $date_added);
                $query9->execute();

                if ($query9->rowCount() > 0) {

                  $order_history_unique_id = $functions->random_str(20);

                  $sql10 = "INSERT INTO order_history (unique_id, user_unique_id, order_unique_id, price, completion, added_date, last_modified, status)
                  VALUES (:unique_id, :user_unique_id, :order_unique_id, :price, :completion, :added_date, :last_modified, :status)";
                  $query10 = $conn->prepare($sql10);
                  $query10->bindParam(":unique_id", $order_history_unique_id);
                  $query10->bindParam(":user_unique_id", $user_unique_id);
                  $query10->bindParam(":order_unique_id", $order_unique_id);
                  $query10->bindParam(":price", $final_price);
                  $query10->bindParam(":completion", $completion_status);
                  $query10->bindParam(":added_date", $date_added);
                  $query10->bindParam(":last_modified", $date_added);
                  $query10->bindParam(":status", $active);
                  $query10->execute();

                  if ($query10->rowCount() > 0) {

                    $sql11 = "UPDATE products SET stock_remaining=:stock_remaining WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id";
                    $query11 = $conn->prepare($sql11);
                    $query11->bindParam(":unique_id", $product_unique_id);
                    $query11->bindParam(":vendor_unique_id", $vendor_unique_id);
                    $query11->bindParam(":stock_remaining", $new_stock);
                    $query11->execute();

                    if ($query11->rowCount() > 0) {

                      $sql8 = "SELECT balance, service_charge FROM vendors WHERE unique_id=:unique_id";
                      $query8 = $conn->prepare($sql8);
                      $query8->bindParam(":unique_id", $vendor_unique_id);
                      $query8->execute();

                      $the_service_charge_details = $query8->rowCount() > 0 ? $query8->fetch() : null;
                      $vendor_balance = $the_service_charge_details != null ? (int)$the_service_charge_details[0] : null;
                      $vendor_service_charge = $the_service_charge_details != null ? (int)$the_service_charge_details[1] : null;

                      if ($payment_method == "Card" || $payment_method == "Wallet") {

                        $total_service_charge = $vendor_service_charge + $service_charge_amount;

                      	$calculate_service_charge_if_not_greater = $final_price > $total_service_charge ? $final_price - $total_service_charge : 0;

                      	$calculate_service_charge_if_greater = $total_service_charge > $final_price ? $total_service_charge - $final_price : $total_service_charge;

                      	$new_card_balance = $vendor_balance + $calculate_service_charge_if_not_greater;

                      	$new_service_charge_for_card = $new_card_balance > $calculate_service_charge_if_greater ? 0 : $total_service_charge - $final_price;

                        $sql12 = "UPDATE vendors SET balance=:balance, service_charge=:service_charge, last_modified=:last_modified WHERE unique_id=:unique_id";
                        $query12 = $conn->prepare($sql12);
                        $query12->bindParam(":unique_id", $vendor_unique_id);
                        $query12->bindParam(":balance", $new_card_balance);
                        $query12->bindParam(":service_charge", $new_service_charge_for_card);
                        $query12->bindParam(":last_modified", $date_added);
                        $query12->execute();

                        if ($query12->rowCount() > 0) {
                          $returnvalue = new genericClass();
                          $returnvalue->engineMessage = 1;
                        }
                        else {
                          $returnvalue = new genericClass();
                          $returnvalue->engineError = 2;
                          $returnvalue->engineErrorMessage = "Vendor balance not updated";
                        }

                      }
                      else {

                        $total_service_charge = $vendor_service_charge + $service_charge_amount;

                      	$calculate_service_charge_if_not_greater = $final_price > $total_service_charge ? $final_price - $service_charge_amount : 0;

                      	$supposed_cash_balance = $vendor_balance + $calculate_service_charge_if_not_greater;

                      	$preferred_cash_balance = $vendor_balance + ($final_price - $service_charge_amount);

                      	$new_cash_balance = $total_service_charge > $final_price ? $preferred_cash_balance : $supposed_cash_balance;

                      	$new_service_charge_for_cash = $total_service_charge;

                        $sql12 = "UPDATE vendors SET balance=:balance, service_charge=:service_charge, last_modified=:last_modified WHERE unique_id=:unique_id";
                        $query12 = $conn->prepare($sql12);
                        $query12->bindParam(":unique_id", $vendor_unique_id);
                        $query12->bindParam(":balance", $new_cash_balance);
                        $query12->bindParam(":service_charge", $new_service_charge_for_cash);
                        $query12->bindParam(":last_modified", $date_added);
                        $query12->execute();

                        if ($query12->rowCount() > 0) {
                          $returnvalue = new genericClass();
                          $returnvalue->engineMessage = 1;
                        }
                        else {
                          $returnvalue = new genericClass();
                          $returnvalue->engineError = 2;
                          $returnvalue->engineErrorMessage = "Vendor balance not updated";
                        }

                      }

                    }
                    else {
                      $returnvalue = new genericClass();
                      $returnvalue->engineError = 2;
                      $returnvalue->engineErrorMessage = "Vendor's stock not updated";
                    }

                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = "Order history not updated";
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

              $the_shipping_fee_price = $shipping_fee;

              // $final_price = ($product_full_price + $the_shipping_fee_price) * $quantity;
              $final_price = $product_full_price;

              $sql9 = "UPDATE orders SET paid=:paid, delivery_status=:delivery_status, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND vendor_unique_id=:vendor_unique_id";
              $query9 = $conn->prepare($sql9);
              $query9->bindParam(":paid", $active);
              $query9->bindParam(":delivery_status", $completion_status);
              $query9->bindParam(":unique_id", $order_unique_id);
              $query9->bindParam(":user_unique_id", $user_unique_id);
              $query9->bindParam(":vendor_unique_id", $vendor_unique_id);
              $query9->bindParam(":last_modified", $date_added);
              $query9->execute();

              if ($query9->rowCount() > 0) {

                $order_history_unique_id = $functions->random_str(20);

                $sql10 = "INSERT INTO order_history (unique_id, user_unique_id, order_unique_id, price, completion, added_date, last_modified, status)
                VALUES (:unique_id, :user_unique_id, :order_unique_id, :price, :completion, :added_date, :last_modified, :status)";
                $query10 = $conn->prepare($sql10);
                $query10->bindParam(":unique_id", $order_history_unique_id);
                $query10->bindParam(":user_unique_id", $user_unique_id);
                $query10->bindParam(":order_unique_id", $order_unique_id);
                $query10->bindParam(":price", $final_price);
                $query10->bindParam(":completion", $completion_status);
                $query10->bindParam(":added_date", $date_added);
                $query10->bindParam(":last_modified", $date_added);
                $query10->bindParam(":status", $active);
                $query10->execute();

                if ($query10->rowCount() > 0) {

                  $sql11 = "UPDATE products SET stock_remaining=:stock_remaining WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id";
                  $query11 = $conn->prepare($sql11);
                  $query11->bindParam(":unique_id", $product_unique_id);
                  $query11->bindParam(":vendor_unique_id", $vendor_unique_id);
                  $query11->bindParam(":stock_remaining", $new_stock);
                  $query11->execute();

                  if ($query11->rowCount() > 0) {

                    $sql8 = "SELECT balance, service_charge FROM vendors WHERE unique_id=:unique_id";
                    $query8 = $conn->prepare($sql8);
                    $query8->bindParam(":unique_id", $vendor_unique_id);
                    $query8->execute();

                    $the_service_charge_details = $query8->rowCount() > 0 ? $query8->fetch() : null;
                    $vendor_balance = $the_service_charge_details != null ? (int)$the_service_charge_details[0] : null;
                    $vendor_service_charge = $the_service_charge_details != null ? (int)$the_service_charge_details[1] : null;

                    if ($payment_method == "Card" || $payment_method == "Wallet") {

                      $total_service_charge = $vendor_service_charge + $service_charge_amount;

                      $calculate_service_charge_if_not_greater = $final_price > $total_service_charge ? $final_price - $total_service_charge : 0;

                      $calculate_service_charge_if_greater = $total_service_charge > $final_price ? $total_service_charge - $final_price : $total_service_charge;

                      $new_card_balance = $vendor_balance + $calculate_service_charge_if_not_greater;

                      $new_service_charge_for_card = $new_card_balance > $calculate_service_charge_if_greater ? 0 : $total_service_charge - $final_price;

                      $sql12 = "UPDATE vendors SET balance=:balance, service_charge=:service_charge, last_modified=:last_modified WHERE unique_id=:unique_id";
                      $query12 = $conn->prepare($sql12);
                      $query12->bindParam(":unique_id", $vendor_unique_id);
                      $query12->bindParam(":balance", $new_card_balance);
                      $query12->bindParam(":service_charge", $new_service_charge_for_card);
                      $query12->bindParam(":last_modified", $date_added);
                      $query12->execute();

                      if ($query12->rowCount() > 0) {
                        $returnvalue = new genericClass();
                        $returnvalue->engineMessage = 1;
                      }
                      else {
                        $returnvalue = new genericClass();
                        $returnvalue->engineError = 2;
                        $returnvalue->engineErrorMessage = "Vendor balance not updated";
                      }

                    }
                    else {

                      $total_service_charge = $vendor_service_charge + $service_charge_amount;

                      $calculate_service_charge_if_not_greater = $final_price > $total_service_charge ? $final_price - $service_charge_amount : 0;

                      $supposed_cash_balance = $vendor_balance + $calculate_service_charge_if_not_greater;

                      $preferred_cash_balance = $vendor_balance + ($final_price - $service_charge_amount);

                      $new_cash_balance = $total_service_charge > $final_price ? $preferred_cash_balance : $supposed_cash_balance;

                      $new_service_charge_for_cash = $total_service_charge;

                      $sql12 = "UPDATE vendors SET balance=:balance, service_charge=:service_charge, last_modified=:last_modified WHERE unique_id=:unique_id";
                      $query12 = $conn->prepare($sql12);
                      $query12->bindParam(":unique_id", $vendor_unique_id);
                      $query12->bindParam(":balance", $new_cash_balance);
                      $query12->bindParam(":service_charge", $new_service_charge_for_cash);
                      $query12->bindParam(":last_modified", $date_added);
                      $query12->execute();

                      if ($query12->rowCount() > 0) {
                        $returnvalue = new genericClass();
                        $returnvalue->engineMessage = 1;
                      }
                      else {
                        $returnvalue = new genericClass();
                        $returnvalue->engineError = 2;
                        $returnvalue->engineErrorMessage = "Vendor balance not updated";
                      }

                    }

                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = "Vendor's stock not updated";
                  }

                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Order history not updated";
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
            $returnvalue->engineErrorMessage = "Item not found";
          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Order not found (probably paid for already)";
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
