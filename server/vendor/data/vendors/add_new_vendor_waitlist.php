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
      $zero = $functions->zero;
      $our_subscription_fee = $functions->our_subscription_fee;
      $transaction_status = $functions->processing;
      $transaction_status_paid = $functions->paid;
      $transaction_type = $functions->subscription;
      $not_allowed_values = $functions->not_allowed_values;

      $business_name = isset($_GET['business_name']) ? $_GET['business_name'] : $data['business_name'];
      $details = isset($_GET['details']) ? $_GET['details'] : $data['details'];
      $fullname = isset($_GET['fullname']) ? $_GET['fullname'] : $data['fullname'];
      $email = isset($_GET['email']) ? $_GET['email'] : $data['email'];
      $phone_number = isset($_GET['phone_number']) ? $_GET['phone_number'] : $data['phone_number'];
      $profile_image = isset($_GET['profile_image']) ? $_GET['profile_image'] : $data['profile_image'];
      $cover_image = isset($_GET['cover_image']) ? $_GET['cover_image'] : $data['cover_image'];
      $cover_image_file = isset($_GET['cover_image_file']) ? $_GET['cover_image_file'] : $data['cover_image_file'];
      $opening_hours = isset($_GET['opening_hours']) ? $_GET['opening_hours'] : $data['opening_hours'];
      $closing_hours = isset($_GET['closing_hours']) ? $_GET['closing_hours'] : $data['closing_hours'];
      $city = isset($_GET['city']) ? $_GET['city'] : $data['city'];
      $state = isset($_GET['state']) ? $_GET['state'] : $data['state'];
      $country = isset($_GET['country']) ? $_GET['country'] : $data['country'];
      $address = isset($_GET['address']) ? $_GET['address'] : $data['address'];
      $role = "Owner";
      $access = 1;

      $validation = $functions->vendor_validation($business_name, $details, $fullname, $email, $phone_number, $opening_hours, $closing_hours, $city, $state, $country, $address, $role, $access);

      if ($validation["error"] == true) {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = $validation["message"];
      }
      else {

        $stripped = $functions->strip_text($business_name);

        $sql2 = "SELECT business_name FROM vendors WHERE business_name=:business_name OR stripped=:stripped AND status=:status";
        $query2 = $conn->prepare($sql2);
        $query2->bindParam(":business_name", $business_name);
        $query2->bindParam(":stripped", $stripped);
        $query2->bindParam(":status", $active);
        $query2->execute();

        $sql3 = "SELECT fullname FROM vendor_users WHERE (email=:email OR phone_number=:phone_number) AND role=:role AND status=:status";
        $query3 = $conn->prepare($sql3);
        $query3->bindParam(":email", $email);
        $query3->bindParam(":phone_number", $phone_number);
        $query3->bindParam(":role", $role);
        $query3->bindParam(":status", $active);
        $query3->execute();

        if ($query2->rowCount() > 0) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Vendor already exists";
        }
        else if ($query3->rowCount() > 0) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "Vendor Owner already exists";
        }
        else {

          $vendor_unique_id = $functions->random_str(20);
          $vendor_users_unique_id = $functions->random_str(20);

          $the_details = in_array($details, $not_allowed_values) ? null : $details;
          $the_email = in_array($email, $not_allowed_values) ? null : $email;
          $the_phone_number = in_array($phone_number, $not_allowed_values) ? null : $phone_number;
          $the_profile_image = in_array($profile_image, $not_allowed_values) ? null : $profile_image;
          $the_cover_image = in_array($cover_image, $not_allowed_values) ? null : $cover_image;
          $the_cover_image_file = in_array($cover_image_file, $not_allowed_values) ? null : $cover_image_file;
          $the_opening_hours = in_array($opening_hours, $not_allowed_values) ? null : $opening_hours;
          $the_closing_hours = in_array($closing_hours, $not_allowed_values) ? null : $closing_hours;
          $the_city = in_array($city, $not_allowed_values) ? null : $city;
          $the_state = in_array($state, $not_allowed_values) ? null : $state;
          $the_country = in_array($country, $not_allowed_values) ? null : $country;
          $the_address = in_array($address, $not_allowed_values) ? null : $address;

          $sql = "INSERT INTO vendor_users (unique_id, user_unique_id, vendor_unique_id, fullname, email, phone_number, role, added_date, last_modified, access, status)
          VALUES (:unique_id, :user_unique_id, :vendor_unique_id, :fullname, :email, :phone_number, :role, :added_date, :last_modified, :access, :status)";
          $query = $conn->prepare($sql);
          $query->bindParam(":unique_id", $vendor_users_unique_id);
          $query->bindParam(":user_unique_id", $vendor_users_unique_id);
          $query->bindParam(":vendor_unique_id", $vendor_unique_id);
          $query->bindParam(":fullname", $fullname);
          $query->bindParam(":email", $the_email);
          $query->bindParam(":phone_number", $the_phone_number);
          $query->bindParam(":role", $role);
          $query->bindParam(":added_date", $date_added);
          $query->bindParam(":last_modified", $date_added);
          $query->bindParam(":access", $active);
          $query->bindParam(":status", $active);
          $query->execute();

          if ($query->rowCount() > 0) {

            $sql4 = "INSERT INTO vendors (unique_id, business_name, stripped, details, fullname, email, phone_number, profile_image, cover_image, cover_image_file, opening_hours, closing_hours, balance, service_charge, city, state, country, address, added_date, last_modified, access, subscription, status)
            VALUES (:unique_id, :business_name, :stripped, :details, :fullname, :email, :phone_number, :profile_image, :cover_image, :cover_image_file, :opening_hours, :closing_hours, :balance, :service_charge, :city, :state, :country, :address, :added_date, :last_modified, :access, :subscription, :status)";
            $query4 = $conn->prepare($sql4);
            $query4->bindParam(":unique_id", $vendor_unique_id);
            $query4->bindParam(":business_name", $business_name);
            $query4->bindParam(":stripped", $stripped);
            $query4->bindParam(":details", $the_details);
            $query4->bindParam(":fullname", $fullname);
            $query4->bindParam(":email", $the_email);
            $query4->bindParam(":phone_number", $the_phone_number);
            $query4->bindParam(":profile_image", $the_profile_image);
            $query4->bindParam(":cover_image", $the_cover_image);
            $query4->bindParam(":cover_image_file", $the_cover_image_file);
            $query4->bindParam(":opening_hours", $the_opening_hours);
            $query4->bindParam(":closing_hours", $the_closing_hours);
            $query4->bindParam(":balance", $zero);
            $query4->bindParam(":service_charge", $zero);
            $query4->bindParam(":city", $the_city);
            $query4->bindParam(":state", $the_state);
            $query4->bindParam(":country", $the_country);
            $query4->bindParam(":address", $the_address);
            $query4->bindParam(":added_date", $date_added);
            $query4->bindParam(":last_modified", $date_added);
            $query4->bindParam(":access", $active);
            $query4->bindParam(":subscription", $active);
            $query4->bindParam(":status", $active);
            $query4->execute();

            if ($query4->rowCount() > 0) {

              $unique_id = $functions->random_str(20);

              $sql5 = "INSERT INTO transactions (unique_id, vendor_unique_id, type, amount, transaction_status, added_date, last_modified, status)
              VALUES (:unique_id, :vendor_unique_id, :type, :amount, :transaction_status, :added_date, :last_modified, :status)";
              $query5 = $conn->prepare($sql5);
              $query5->bindParam(":unique_id", $unique_id);
              $query5->bindParam(":vendor_unique_id", $vendor_unique_id);
              $query5->bindParam(":type", $transaction_type);
              $query5->bindParam(":amount", $our_subscription_fee);
              $query5->bindParam(":transaction_status", $transaction_status);
              $query5->bindParam(":added_date", $date_added);
              $query5->bindParam(":last_modified", $date_added);
              $query5->bindParam(":status", $active);
              $query5->execute();

              if ($query5->rowCount() > 0) {

                $unique_id_2 = $functions->random_str(20);

                $sql6 = "INSERT INTO transactions (unique_id, vendor_unique_id, type, amount, transaction_status, added_date, last_modified, status)
                VALUES (:unique_id, :vendor_unique_id, :type, :amount, :transaction_status, :added_date, :last_modified, :status)";
                $query6 = $conn->prepare($sql6);
                $query6->bindParam(":unique_id", $unique_id_2);
                $query6->bindParam(":vendor_unique_id", $vendor_unique_id);
                $query6->bindParam(":type", $transaction_type);
                $query6->bindParam(":amount", $our_subscription_fee);
                $query6->bindParam(":transaction_status", $transaction_status_paid);
                $query6->bindParam(":added_date", $date_added);
                $query6->bindParam(":last_modified", $date_added);
                $query6->bindParam(":status", $active);
                $query6->execute();

                if ($query6->rowCount() > 0) {

                  $preferences_unique_id = $functions->random_str(20);

                  $sql7 = "INSERT INTO preferences (unique_id, user_unique_id, vendor_unique_id, last_modified)
                  VALUES (:unique_id, :user_unique_id, :vendor_unique_id, :last_modified)";
                  $query7 = $conn->prepare($sql7);
                  $query7->bindParam(":unique_id", $preferences_unique_id);
                  $query7->bindParam(":user_unique_id", $vendor_users_unique_id);
                  $query7->bindParam(":vendor_unique_id", $vendor_unique_id);
                  $query7->bindParam(":last_modified", $date_added);
                  $query7->execute();

                  if ($query7->rowCount() > 0) {
                    $returnvalue = new genericClass();
                    $returnvalue->engineMessage = 1;
                  }
                  else {
                    $returnvalue = new genericClass();
                    $returnvalue->engineError = 2;
                    $returnvalue->engineErrorMessage = "Not inserted (new preference)";
                  }

                }
                else {
                  $returnvalue = new genericClass();
                  $returnvalue->engineError = 2;
                  $returnvalue->engineErrorMessage = "Not inserted (new transaction paid)";
                }

              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not inserted (new transaction)";
              }

            }
            else{
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Not inserted (new vendor)";
            }

          }
          else{
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Not inserted (new vendor user)";
          }

        }

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
