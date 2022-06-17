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
      $not_active = $functions->not_active;
      $Yes = $functions->Yes;
      $not_allowed_values = $functions->not_allowed_values;

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $product_unique_id = isset($_GET['product_unique_id']) ? $_GET['product_unique_id'] : $data['product_unique_id'];
      $quantity = isset($_GET['quantity']) ? $_GET['quantity'] : $data['quantity'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];

      $sqlSearchUser = "SELECT unique_id FROM users WHERE unique_id=:unique_id AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $null = $functions->null;

        $sql5 = "SELECT firstname as address_first_name, lastname as address_last_name, address, additional_information, city, state, country FROM users_addresses WHERE user_unique_id=:user_unique_id AND default_status=:default_status AND status=:status ORDER BY added_date DESC";
        $query5 = $conn->prepare($sql5);
        $query5->bindParam(":user_unique_id", $user_unique_id);
        $query5->bindParam(":default_status", $Yes);
        $query5->bindParam(":status", $active);
        $query5->execute();

        if ($query5->rowCount() > 0) {

          $sql6 = "SELECT unique_id, stock_remaining FROM products WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
          $query6 = $conn->prepare($sql6);
          $query6->bindParam(":unique_id", $product_unique_id);
          $query6->bindParam(":vendor_unique_id", $vendor_unique_id);
          $query6->bindParam(":status", $active);
          $query6->execute();

          if ($query6->rowCount() > 0) {

            $the_vendor_stock_details = $query6->fetch();
            $stock_remaining = (int)$the_vendor_stock_details[1];

            if ($stock_remaining >= $quantity) {

              $the_address_details = $query5->fetch();
              $the_address_city = $the_address_details[4];
              $the_address_state = $the_address_details[5];
              $the_address_country = $the_address_details[6];

              $sql7 = "SELECT unique_id, price FROM shipping_fees WHERE product_unique_id=:product_unique_id AND vendor_unique_id=:vendor_unique_id AND city=:city AND state=:state AND country=:country AND status=:status";
              $query7 = $conn->prepare($sql7);
              $query7->bindParam(":product_unique_id", $product_unique_id);
              $query7->bindParam(":vendor_unique_id", $vendor_unique_id);
              $query7->bindParam(":city", $the_address_city);
              $query7->bindParam(":state", $the_address_state);
              $query7->bindParam(":country", $the_address_country);
              $query7->bindParam(":status", $active);
              $query7->execute();

              $the_shipping_details = $query7->fetch();

              if ($query7->rowCount() > 0) {

                $the_shipping_fee_unique_id = $the_shipping_details[0];

                $sql3 = "SELECT quantity, unique_id FROM carts WHERE user_unique_id=:user_unique_id AND product_unique_id=:product_unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
                $query3 = $conn->prepare($sql3);
                $query3->bindParam(":user_unique_id", $user_unique_id);
                $query3->bindParam(":product_unique_id", $product_unique_id);
                $query3->bindParam(":vendor_unique_id", $vendor_unique_id);
                $query3->bindParam(":status", $active);
                $query3->execute();

                if ($query3->rowCount() > 0) {
                  $the_cart_details = $query3->fetch();
                  $the_quantity = (int)$the_cart_details[0];
                  $cart_unique_id = $the_cart_details[1];
                  $new_quantity = $the_quantity + 1;

                  $sql = "UPDATE carts SET quantity=:quantity, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND product_unique_id=:product_unique_id AND vendor_unique_id=:vendor_unique_id";
                  $query = $conn->prepare($sql);
                  $query->bindParam(":quantity", $new_quantity);
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

                  $cart_unique_id = $functions->random_str(20);

                  $sql = "INSERT INTO carts (unique_id, user_unique_id, vendor_unique_id, product_unique_id, quantity, shipping_fee_unique_id, pickup_location, added_date, last_modified, status)
                  VALUES (:unique_id, :user_unique_id, :vendor_unique_id, :product_unique_id, :quantity, :shipping_fee_unique_id, :pickup_location, :added_date, :last_modified, :status)";
                  $query = $conn->prepare($sql);
                  $query->bindParam(":unique_id", $cart_unique_id);
                  $query->bindParam(":user_unique_id", $user_unique_id);
                  $query->bindParam(":product_unique_id", $product_unique_id);
                  $query->bindParam(":vendor_unique_id", $vendor_unique_id);
                  $query->bindParam(":quantity", $quantity);
                  $query->bindParam(":shipping_fee_unique_id", $the_shipping_fee_unique_id);
                  $query->bindParam(":pickup_location", $not_active);
                  $query->bindParam(":added_date", $date_added);
                  $query->bindParam(":last_modified", $date_added);
                  $query->bindParam(":status", $active);
                  $query->execute();

                  if ($query->rowCount() > 0) {
                    $returnvalue = new genericClass();
                    $returnvalue->engineMessage = 1;
                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = "Not inserted (user cart)";
                  }

                }

              }
              else {

                $shipping_fee_unique_id = $null;

                $sql3 = "SELECT quantity, unique_id FROM carts WHERE user_unique_id=:user_unique_id AND product_unique_id=:product_unique_id AND vendor_unique_id=:vendor_unique_id AND status=:status";
                $query3 = $conn->prepare($sql3);
                $query3->bindParam(":user_unique_id", $user_unique_id);
                $query3->bindParam(":product_unique_id", $product_unique_id);
                $query3->bindParam(":vendor_unique_id", $vendor_unique_id);
                $query3->bindParam(":status", $active);
                $query3->execute();

                if ($query3->rowCount() > 0) {
                  $the_cart_details = $query3->fetch();
                  $the_quantity = (int)$the_cart_details[0];
                  $cart_unique_id = $the_cart_details[1];
                  $new_quantity = $the_quantity + 1;

                  $sql = "UPDATE carts SET quantity=:quantity, last_modified=:last_modified WHERE unique_id=:unique_id AND user_unique_id=:user_unique_id AND product_unique_id=:product_unique_id AND vendor_unique_id=:vendor_unique_id";
                  $query = $conn->prepare($sql);
                  $query->bindParam(":quantity", $new_quantity);
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

                  $cart_unique_id = $functions->random_str(20);

                  $sql = "INSERT INTO carts (unique_id, user_unique_id, vendor_unique_id, product_unique_id, quantity, shipping_fee_unique_id, pickup_location, added_date, last_modified, status)
                  VALUES (:unique_id, :user_unique_id, :vendor_unique_id, :product_unique_id, :quantity, :shipping_fee_unique_id, :pickup_location, :added_date, :last_modified, :status)";
                  $query = $conn->prepare($sql);
                  $query->bindParam(":unique_id", $cart_unique_id);
                  $query->bindParam(":user_unique_id", $user_unique_id);
                  $query->bindParam(":product_unique_id", $product_unique_id);
                  $query->bindParam(":vendor_unique_id", $vendor_unique_id);
                  $query->bindParam(":quantity", $quantity);
                  $query->bindParam(":shipping_fee_unique_id", $the_shipping_fee_unique_id);
                  $query->bindParam(":pickup_location", $not_active);
                  $query->bindParam(":added_date", $date_added);
                  $query->bindParam(":last_modified", $date_added);
                  $query->bindParam(":status", $active);
                  $query->execute();

                  if ($query->rowCount() > 0) {
                    $returnvalue = new genericClass();
                    $returnvalue->engineMessage = 1;
                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = "Not inserted (user cart)";
                  }

                }

              }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Vendor's product is out of stock";
            }

          }
          else {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Vendor product not found";
          }

        }
        else {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Address not found";
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
