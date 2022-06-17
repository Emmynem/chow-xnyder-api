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
      $next_month = $functions->next_month;
      $not_allowed_values = $functions->not_allowed_values;

      $referral_user_unique_id = isset($_GET['referral_user_unique_id']) ? $_GET['referral_user_unique_id'] : $data['referral_user_unique_id'];
      $fullname = isset($_GET['fullname']) ? $_GET['fullname'] : $data['fullname'];
      $email = isset($_GET['email']) ? $_GET['email'] : $data['email'];
      $phone_number = isset($_GET['phone_number']) ? $_GET['phone_number'] : $data['phone_number'];
      $image = isset($_GET['image']) ? $_GET['image'] : $data['image'];

      $validation = $functions->add_new_user_validation($fullname, $email, $phone_number);

      if ($validation["error"] == true) {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = $validation["message"];
      }
      else {

        $access = $functions->granted;

        $sql2 = "SELECT email FROM users WHERE email=:email OR phone_number=:phone_number";
        $query2 = $conn->prepare($sql2);
        $query2->bindParam(":email", $email);
        $query2->bindParam(":phone_number", $phone_number);
        $query2->execute();

        if ($query2->rowCount() > 0) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = "User already exists";
        }
        else {

          $sql4 = "SELECT email FROM users WHERE unique_id=:unique_id";
          $query4 = $conn->prepare($sql4);
          $query4->bindParam(":unique_id", $referral_user_unique_id);
          $query4->execute();

          if ($query4->rowCount() > 0) {
            $referral_user_unique_id_alt = $referral_user_unique_id;
          }
          else {
            $referral_user_unique_id_alt = null;
          }

          $unique_id = $functions->random_str(20);

          $the_email = in_array($email, $not_allowed_values) ? null : $email;
          $the_phone_number = in_array($phone_number, $not_allowed_values) ? null : $phone_number;

          $sql = "INSERT INTO users (unique_id, fullname, email, phone_number, image, added_date, last_modified, access, status)
          VALUES (:unique_id, :fullname, :email, :phone_number, :image, :added_date, :last_modified, :access, :status)";
          $query = $conn->prepare($sql);
          $query->bindParam(":unique_id", $unique_id);
          $query->bindParam(":fullname", $fullname);
          $query->bindParam(":email", $the_email);
          $query->bindParam(":phone_number", $the_phone_number);
          $query->bindParam(":image", $image);
          $query->bindParam(":added_date", $date_added);
          $query->bindParam(":last_modified", $date_added);
          $query->bindParam(":access", $access);
          $query->bindParam(":status", $active);
          $query->execute();

          if ($query->rowCount() > 0) {

            $referral_unique_id = $functions->random_str(20);

            $the_referral_user_unique_id = in_array($referral_user_unique_id_alt, $not_allowed_values) ? "Default" : $referral_user_unique_id_alt;

            $user_referral_link = "https://auth.reestoc.com/signup/".$unique_id;

            $sql3 = "INSERT INTO referrals (unique_id, referral_user_unique_id, user_unique_id, user_referral_link, added_date, last_modified, status)
            VALUES (:unique_id, :referral_user_unique_id, :user_unique_id, :user_referral_link, :added_date, :last_modified, :status)";
            $query3 = $conn->prepare($sql3);
            $query3->bindParam(":unique_id", $referral_unique_id);
            $query3->bindParam(":referral_user_unique_id", $the_referral_user_unique_id);
            $query3->bindParam(":user_unique_id", $unique_id);
            $query3->bindParam(":user_referral_link", $user_referral_link);
            $query3->bindParam(":added_date", $date_added);
            $query3->bindParam(":last_modified", $date_added);
            $query3->bindParam(":status", $active);
            $query3->execute();

            if ($query3->rowCount() > 0) {

              $preferences_unique_id = $functions->random_str(20);

              $sql6 = "INSERT INTO users_preferences (unique_id, user_unique_id, last_modified)
              VALUES (:unique_id, :user_unique_id, :last_modified)";
              $query6 = $conn->prepare($sql6);
              $query6->bindParam(":unique_id", $preferences_unique_id);
              $query6->bindParam(":user_unique_id", $unique_id);
              $query6->bindParam(":last_modified", $date_added);
              $query6->execute();

              if ($query6->rowCount() > 0) {
                $returnvalue = new genericClass();
                $returnvalue->engineMessage = 1;
                $returnvalue->resultData = $unique_id;
              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not inserted (new user preference)";
              }

              // $the_code = "WELCOME-COUPON";
              // $name = "Welcome to Reestoc coupon";
              // $percentage = 5;
              // $total_count = 1;
              // $expiry_date = $next_month;
              //
              // $null = $functions->null;
              // $completion = $functions->processing;
              //
              // $coupons_unique_id = $functions->random_str(20);
              //
              // $sql = "INSERT INTO coupons (unique_id, user_unique_id, sub_product_unique_id, mini_category_unique_id, code, name, percentage, total_count, current_count, completion, expiry_date, added_date, last_modified, status)
              // VALUES (:unique_id, :user_unique_id, :sub_product_unique_id, :mini_category_unique_id, :code, :name, :percentage, :total_count, :current_count, :completion, :expiry_date, :added_date, :last_modified, :status)";
              // $query = $conn->prepare($sql);
              // $query->bindParam(":unique_id", $coupons_unique_id);
              // $query->bindParam(":user_unique_id", $unique_id);
              // $query->bindParam(":sub_product_unique_id", $null);
              // $query->bindParam(":mini_category_unique_id", $null);
              // $query->bindParam(":code", $the_code);
              // $query->bindParam(":name", $name);
              // $query->bindParam(":percentage", $percentage);
              // $query->bindParam(":total_count", $total_count);
              // $query->bindParam(":current_count", $total_count);
              // $query->bindParam(":completion", $completion);
              // $query->bindParam(":expiry_date", $expiry_date);
              // $query->bindParam(":added_date", $date_added);
              // $query->bindParam(":last_modified", $date_added);
              // $query->bindParam(":status", $active);
              // $query->execute();
              //
              // if ($query->rowCount() > 0) {
              //   $returnvalue = new genericClass();
              //   $returnvalue->engineMessage = 1;
              //   $returnvalue->resultData = $unique_id;
              // }
              // else{
              //   $returnvalue = new genericClass();
              //   $returnvalue->engineError = 2;
              //   $returnvalue->engineErrorMessage = "Not inserted (new coupon)";
              // }

            }
            else {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Not inserted (new user referral)";
            }
          }
          else{
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Not inserted (new user)";
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
