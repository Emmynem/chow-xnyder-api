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

      $active = $functions->active;
      $date_added = $functions->today;

      $order_array = array();

      $sql = "SELECT orders.unique_id, orders.user_unique_id, orders.product_unique_id, orders.tracker_unique_id, orders.coupon_unique_id, orders.shipping_fee_unique_id, orders.pickup_location, orders.rider_details,
      orders.quantity, orders.amount, orders.shipping_fee, orders.credit, orders.service_charge, orders.payment_method, orders.checked_out, orders.paid, orders.shipped, orders.disputed,
      orders.delivery_status, orders.added_date, orders.last_modified, orders.status, users.fullname as user_fullname, users.email as user_email, users.phone_number as user_phone_number, products.name, products.stripped,
      products.stock, products.stock_remaining, products.price, products.sales_price, products.favorites, products.category_unique_id, vendors.business_name as vendor_name, vendors.stripped as vendor_stripped
      FROM orders INNER JOIN users ON orders.user_unique_id = users.unique_id LEFT JOIN products ON orders.product_unique_id = products.unique_id
      LEFT JOIN vendors ON orders.vendor_unique_id = vendors.unique_id WHERE orders.vendor_unique_id=:vendor_unique_id ORDER BY orders.added_date DESC";
      $query = $conn->prepare($sql);
      $query->bindParam(":vendor_unique_id", $vendor_unique_id);
      $query->execute();

      $result = $query->fetchAll();

      if ($query->rowCount() > 0) {
        foreach ($result as $key => $value) {

          $current_order = array();
          $current_order['unique_id'] = $value['unique_id'];
          $current_order['user_unique_id'] = $value['user_unique_id'];
          $current_order['product_unique_id'] = $value['product_unique_id'];
          $current_order['tracker_unique_id'] = $value['tracker_unique_id'];
          $current_order['rider_details'] = $value['rider_details'];
          $current_order['quantity'] = $value['quantity'];
          $current_order['amount'] = $value['amount'];
          $current_order['shipping_fee'] = $value['shipping_fee'];
          $current_order['credit'] = $value['credit'];
          $current_order['service_charge'] = $value['service_charge'];
          $current_order['payment_method'] = $value['payment_method'];
          $current_order['checked_out'] = $value['checked_out'];
          $current_order['paid'] = $value['paid'];
          $current_order['shipped'] = $value['shipped'];
          $current_order['disputed'] = $value['disputed'];
          $current_order['delivery_status'] = $value['delivery_status'];
          $current_order['added_date'] = $value['added_date'];
          $current_order['last_modified'] = $value['last_modified'];
          $current_order['status'] = $value['status'];
          $current_order['user_fullname'] = $value['user_fullname'];
          $current_order['user_email'] = $value['user_email'];
          $current_order['user_phone_number'] = $value['user_phone_number'];
          $current_order['name'] = $value['name'];
          $current_order['stripped'] = $value['stripped'];
          $current_order['stock'] = $value['stock'];
          $current_order['stock_remaining'] = $value['stock_remaining'];
          $current_order['price'] = $value['price'];
          $current_order['sales_price'] = $value['sales_price'];
          $current_order['favorites'] = $value['favorites'];
          $current_order['vendor_name'] = $value['vendor_name'];
          $current_order['vendor_stripped'] = $value['vendor_stripped'];

          $product_id = $value['product_unique_id'];
          $order_unique_id = $value['unique_id'];
          $user_unique_id = $value['user_unique_id'];
          $tracker_unique_id = $value['tracker_unique_id'];
          $shipping_fee_unique_id = $value['shipping_fee_unique_id'];
          $category_unique_id = $value['category_unique_id'];

          $current_order_price = (int)$value['price'];
          $current_order_sales_price = (int)$value['sales_price'];
          $current_order_quantity = (int)$value['quantity'];
          $current_order_shipping_fee = (int)$value['shipping_fee'];

          $sql2 = "SELECT image FROM product_images WHERE product_unique_id=:product_unique_id LIMIT 1";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":product_unique_id", $product_id);
          $query2->execute();

          $images_result = $query2->fetchAll();

          if ($query2->rowCount() > 0) {
            $current_order_images = array();

            foreach ($images_result as $key => $image_value) {
              $current_order_images[] = $image_value['image'];
            }

            $current_order['order_product_images'] = $current_order_images;
          }
          else{
            $current_order['order_product_images'] = null;
          }

          // $current_order['total_order_amount'] = $current_order_sales_price != 0 ? $current_order_sales_price * $current_order_quantity : $current_order_price * $current_order_quantity;
          $current_order['total_order_amount'] = (int)$value['amount'];;

          $the_total_order_amount = $current_order['total_order_amount'];

          $sqlOrderCoupon = "SELECT coupon_unique_id FROM order_coupons WHERE tracker_unique_id=:tracker_unique_id";
          $queryOrderCoupon = $conn->prepare($sqlOrderCoupon);
          $queryOrderCoupon->bindParam(":tracker_unique_id", $tracker_unique_id);
          $queryOrderCoupon->execute();

          $the_order_coupon_details = $queryOrderCoupon->rowCount() > 0 ? $queryOrderCoupon->fetch() : null;
          $the_coupon_unique_id = $the_order_coupon_details != null ? $the_order_coupon_details[0] : null;

          $sql3 = "SELECT percentage, current_count, name, unique_id, user_unique_id, product_unique_id, code, category_unique_id FROM coupons WHERE unique_id=:unique_id AND (user_unique_id=:user_unique_id OR product_unique_id=:product_unique_id OR category_unique_id=:category_unique_id) AND expiry_date >:today";
          $query3 = $conn->prepare($sql3);
          $query3->bindParam(":unique_id", $the_coupon_unique_id);
          $query3->bindParam(":user_unique_id", $user_unique_id);
          $query3->bindParam(":product_unique_id", $product_id);
          $query3->bindParam(":category_unique_id", $category_unique_id);
          $query3->bindParam(":today", $date_added);
          $query3->execute();

          if ($query3->rowCount() > 0) {
            $the_coupon_price_details = $query3->fetch();
            $the_coupon_percentage = (int)$the_coupon_price_details[0];
            $the_coupon_price = (($the_total_order_amount * $the_coupon_percentage) / 100) * $current_order_quantity;
            $the_coupon_count = (int)$the_coupon_price_details[1];
            $the_coupon_name = $the_coupon_price_details[2];
            $the_coupon_unique_id = $the_coupon_price_details[3];
            $the_coupon_user_unique_id = $the_coupon_price_details[4];
            $the_coupon_product_unique_id = $the_coupon_price_details[5];
            $the_coupon_code = $the_coupon_price_details[6];
            $the_coupon_category_unique_id = $the_coupon_price_details[7];

            if ($the_coupon_count != 0) {
              if (($key + 1) <= $the_coupon_count && ($the_coupon_user_unique_id == $user_unique_id || $the_coupon_product_unique_id == $product_id || $the_coupon_category_unique_id == $category_unique_id)) {
                $current_order['total_order_amount'] = $the_total_order_amount - $the_coupon_price;
                $current_order['coupon_name'] = $the_coupon_name;
                $current_order['coupon_code'] = $the_coupon_code;
                $current_order['coupon_price'] = $the_coupon_price;
                $current_order['coupon_status'] = "Available";
              }
              else {
                $current_order['total_order_amount'] = $the_total_order_amount;
                $current_order['coupon_name'] = $the_coupon_name;
                $current_order['coupon_code'] = $the_coupon_code;
                $current_order['coupon_price'] = $the_coupon_price;
                $current_order['coupon_status'] = "Not available";
              }
            }
            else {
              $current_order['total_order_amount'] = $the_total_order_amount;
              $current_order['coupon_name'] = $the_coupon_name;
              $current_order['coupon_code'] = $the_coupon_code;
              $current_order['coupon_price'] = $the_coupon_price;
              $current_order['coupon_status'] = "Not available";
            }
          }
          else {
            $current_order['total_order_amount'] = $the_total_order_amount;
            $current_order['coupon_name'] = null;
            $current_order['coupon_code'] = null;
            $current_order['coupon_price'] = null;
            $current_order['coupon_status'] = "Not available";
          }

          $the_total_order_amount_with_coupon = $current_order['total_order_amount'];

          $sql4 = "SELECT price, city, state, country FROM shipping_fees WHERE unique_id=:unique_id AND product_unique_id=:product_unique_id";
          $query4 = $conn->prepare($sql4);
          $query4->bindParam(":unique_id", $shipping_fee_unique_id);
          $query4->bindParam(":product_unique_id", $product_id);
          $query4->execute();

          if ($query4->rowCount() > 0) {
            $the_shipping_fee_price_details = $query4->fetch();
            $the_shipping_fee_price = (int)$the_shipping_fee_price_details[0];
            $the_shipping_fee_city = $the_shipping_fee_price_details[1];
            $the_shipping_fee_state = $the_shipping_fee_price_details[2];
            $the_shipping_fee_country = $the_shipping_fee_price_details[3];

            $current_order['total_order_amount'] = $the_total_order_amount_with_coupon + $current_order_shipping_fee;
            $current_order['shipping_to_city'] = $the_shipping_fee_city;
            $current_order['shipping_to_state'] = $the_shipping_fee_state;
            $current_order['shipping_to_country'] = $the_shipping_fee_country;
            $current_order['shipping_fee_price'] = $current_order_shipping_fee;

          }
          else {
            $current_order['total_order_amount'] = $the_total_order_amount_with_coupon;
            $current_order['shipping_to_city'] = null;
            $current_order['shipping_to_state'] = null;
            $current_order['shipping_to_country'] = null;
            $current_order['shipping_fee_price'] = null;
          }

          $order_array[] = $current_order;
        }
        $returnvalue = new genericClass();
        $returnvalue->engineMessage = 1;
        $returnvalue->resultData = $order_array;
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
