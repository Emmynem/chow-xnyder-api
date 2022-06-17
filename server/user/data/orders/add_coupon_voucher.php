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
    public $coupon_price;
    public $filteredData;
  }

  $data = json_decode(file_get_contents("php://input"), true);

  $functions = new Functions();

  if ($connected) {

    try {
      $conn->beginTransaction();

      $date_added = $functions->today;
      $start_today = $functions->start_today;
      $end_today = $functions->end_today;
      $tomorrow = $functions->tomorrow;
      $active = $functions->active;
      $null = $functions->null;
      $completion_status = $functions->add_coupon;
      $completion = $functions->completed;

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];
      $code = isset($_GET['code']) ? $_GET['code'] : $data['code'];
      $cart_unique_ids_alternate = isset($_GET['cart_unique_ids_alternate']) ? $_GET['cart_unique_ids_alternate'] : $data['cart_unique_ids_alternate'];
      $cart_total_price = isset($_GET['cart_total_price']) ? $_GET['cart_total_price'] : $data['cart_total_price'];

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $the_code = strtoupper($code);

        foreach ($cart_unique_ids_alternate as $key => $cart_unique_ids_alternate_details){

          $cart_unique_id = $cart_unique_ids_alternate_details['cart_unique_id'];
          $quantity = $cart_unique_ids_alternate_details['quantity'];

          $sql6 = "SELECT carts.product_unique_id, products.category_unique_id, products.price, products.sales_price FROM carts LEFT JOIN products ON carts.product_unique_id = products.unique_id WHERE carts.unique_id=:unique_id AND carts.vendor_unique_id=:vendor_unique_id";
          $query6 = $conn->prepare($sql6);
          $query6->bindParam(":unique_id", $cart_unique_id);
          $query6->bindParam(":vendor_unique_id", $vendor_unique_id);
          $query6->execute();

          $the_cart_details = $query6->rowCount() > 0 ? $query6->fetch() : null;
          $product_unique_id = $the_cart_details != null ? $the_cart_details[0] : null;
          $category_unique_id = $the_cart_details != null ? $the_cart_details[1] : null;
          $product_price = $the_cart_details != null ? $the_cart_details[2] : null;
          $product_sales_price = $the_cart_details != null ? $the_cart_details[3] : null;

          $the_final_product_price = $product_sales_price == 0 ? $product_price : $product_sales_price;
          $the_final_price = $the_final_product_price * $quantity;

          $sql3 = "SELECT current_count, unique_id, percentage, user_unique_id, product_unique_id, category_unique_id FROM coupons WHERE code=:code AND vendor_unique_id=:vendor_unique_id AND (user_unique_id=:user_unique_id OR product_unique_id=:product_unique_id OR category_unique_id=:category_unique_id) AND expiry_date >:today";
          $query3 = $conn->prepare($sql3);
          $query3->bindParam(":code", $the_code);
          $query3->bindParam(":vendor_unique_id", $vendor_unique_id);
          $query3->bindParam(":user_unique_id", $user_unique_id);
          $query3->bindParam(":product_unique_id", $product_unique_id);
          $query3->bindParam(":category_unique_id", $category_unique_id);
          $query3->bindParam(":today", $date_added);
          $query3->execute();

          if ($query3->rowCount() > 0) {
            $the_coupon_price_details = $query3->fetch();
            $the_coupon_count = (int)$the_coupon_price_details[0];
            $the_coupon_unique_id = $the_coupon_price_details[1];
            $the_coupon_percentage = $the_coupon_price_details[2];
            $the_coupon_user_unique_id = $the_coupon_price_details[3];
            $the_coupon_product_unique_id = $the_coupon_price_details[4];
            $the_coupon_category_unique_id = $the_coupon_price_details[5];

            if ($the_coupon_count != 0) {

              $sql4 = "SELECT added_date FROM order_coupons WHERE user_unique_id=:user_unique_id AND coupon_unique_id=:coupon_unique_id AND added_date >:start_today AND (added_date <:end_today OR added_date=:end_today)";
              $query4 = $conn->prepare($sql4);
              $query4->bindParam(":user_unique_id", $user_unique_id);
              $query4->bindParam(":coupon_unique_id", $the_coupon_unique_id);
              $query4->bindParam(":start_today", $start_today);
              $query4->bindParam(":end_today", $end_today);
              $query4->execute();

              if ($query4->rowCount() > 0) {

                $order_coupons_details = $query4->fetch();
                $the_added_date = $order_coupons_details[0];

                $converted_date = strtotime($the_added_date);
                $new_date = date('Y-m-d H:i:s', $converted_date);

                $compare_date = new DateTime($new_date);
                $todays_date = new DateTime($date_added);
                $difference = $compare_date->diff($todays_date);

                $string_return_error = $difference->h." hours, ".$difference->i." mins ago";

                // Check if it's up to 1 hour since the last use of coupon
                if ($difference->h >= 1) {
                  $order_coupons_unique_id = $functions->random_str(20);
                  $tracker_unique_id = $functions->random_str(20);

                  $sql2 = "INSERT INTO order_coupons (unique_id, user_unique_id, tracker_unique_id, coupon_unique_id, completion, added_date, last_modified, status)
                  VALUES (:unique_id, :user_unique_id, :tracker_unique_id, :coupon_unique_id, :completion, :added_date, :last_modified, :status)";
                  $query2 = $conn->prepare($sql2);
                  $query2->bindParam(":unique_id", $order_coupons_unique_id);
                  $query2->bindParam(":user_unique_id", $user_unique_id);
                  $query2->bindParam(":tracker_unique_id", $tracker_unique_id);
                  $query2->bindParam(":coupon_unique_id", $the_coupon_unique_id);
                  $query2->bindParam(":completion", $completion);
                  $query2->bindParam(":added_date", $date_added);
                  $query2->bindParam(":last_modified", $date_added);
                  $query2->bindParam(":status", $active);
                  $query2->execute();

                  if ($query2->rowCount() > 0) {

                    $the_user_coupon_price = $the_coupon_user_unique_id != null ? ($cart_total_price * $the_coupon_percentage) / 100 : 'Nothing';
                    $the_product_coupon_price = $the_coupon_product_unique_id == $product_unique_id ? ($the_final_price * $the_coupon_percentage) / 100 : 'Nothing';
                    $the_category_coupon_price = $the_coupon_category_unique_id == $category_unique_id ? ($the_final_price * $the_coupon_percentage) / 100 : 'Nothing';

                    if ($the_user_coupon_price != 'Nothing') {
                      $total_coupon_price = $the_user_coupon_price;
                      $returnvalue = new genericClass();
                      $returnvalue->engineMessage = 1;
                      $returnvalue->resultData = $tracker_unique_id;
                      $returnvalue->coupon_price = $total_coupon_price;
                      break;
                    }
                    else if ($the_product_coupon_price != 'Nothing') {
                      $total_coupon_price = $the_product_coupon_price;
                      $returnvalue = new genericClass();
                      $returnvalue->engineMessage = 1;
                      $returnvalue->resultData = $tracker_unique_id;
                      $returnvalue->coupon_price = $total_coupon_price;
                      break;
                    }
                    else {
                      $total_coupon_price = $the_category_coupon_price;
                      $returnvalue = new genericClass();
                      $returnvalue->engineMessage = 1;
                      $returnvalue->resultData = $tracker_unique_id;
                      $returnvalue->coupon_price = $total_coupon_price;
                      break;
                    }

                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = "Orders Not updated";
                    break;
                  }
                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Coupon used ".$string_return_error."";
                }

              }
              else {

                $order_coupons_unique_id = $functions->random_str(20);
                $tracker_unique_id = $functions->random_str(20);

                $sql2 = "INSERT INTO order_coupons (unique_id, user_unique_id, tracker_unique_id, coupon_unique_id, completion, added_date, last_modified, status)
                VALUES (:unique_id, :user_unique_id, :tracker_unique_id, :coupon_unique_id, :completion, :added_date, :last_modified, :status)";
                $query2 = $conn->prepare($sql2);
                $query2->bindParam(":unique_id", $order_coupons_unique_id);
                $query2->bindParam(":user_unique_id", $user_unique_id);
                $query2->bindParam(":tracker_unique_id", $tracker_unique_id);
                $query2->bindParam(":coupon_unique_id", $the_coupon_unique_id);
                $query2->bindParam(":completion", $completion);
                $query2->bindParam(":added_date", $date_added);
                $query2->bindParam(":last_modified", $date_added);
                $query2->bindParam(":status", $active);
                $query2->execute();

                if ($query2->rowCount() > 0) {
                  $the_user_coupon_price = $the_coupon_user_unique_id != null ? ($cart_total_price * $the_coupon_percentage) / 100 : 'Nothing';
                  $the_product_coupon_price = $the_coupon_product_unique_id == $product_unique_id ? ($the_final_price * $the_coupon_percentage) / 100 : 'Nothing';
                  $the_category_coupon_price = $the_coupon_category_unique_id == $category_unique_id ? ($the_final_price * $the_coupon_percentage) / 100 : 'Nothing';

                  if ($the_user_coupon_price != 'Nothing') {
                    $total_coupon_price = $the_user_coupon_price;
                    $returnvalue = new genericClass();
                    $returnvalue->engineMessage = 1;
                    $returnvalue->resultData = $tracker_unique_id;
                    $returnvalue->coupon_price = $total_coupon_price;
                    break;
                  }
                  else if ($the_product_coupon_price != 'Nothing') {
                    $total_coupon_price = $the_product_coupon_price;
                    $returnvalue = new genericClass();
                    $returnvalue->engineMessage = 1;
                    $returnvalue->resultData = $tracker_unique_id;
                    $returnvalue->coupon_price = $total_coupon_price;
                    break;
                  }
                  else {
                    $total_coupon_price = $the_category_coupon_price;
                    $returnvalue = new genericClass();
                    $returnvalue->engineMessage = 1;
                    $returnvalue->resultData = $tracker_unique_id;
                    $returnvalue->coupon_price = $total_coupon_price;
                    break;
                  }
                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Orders Not updated";
                  break;
                }

              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Coupon not available";
            }
          }
          else {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Coupon expired or not found";
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
