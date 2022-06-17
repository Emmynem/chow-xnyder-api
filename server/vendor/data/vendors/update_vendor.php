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
      $role = "Owner";

      $user_unique_id = isset($_GET['user_unique_id']) ? $_GET['user_unique_id'] : $data['user_unique_id'];
      $vendor_unique_id = isset($_GET['vendor_unique_id']) ? $_GET['vendor_unique_id'] : $data['vendor_unique_id'];
      $business_name = isset($_GET['business_name']) ? $_GET['business_name'] : $data['business_name'];
      $details = isset($_GET['details']) ? $_GET['details'] : $data['details'];
      $opening_hours = isset($_GET['opening_hours']) ? $_GET['opening_hours'] : $data['opening_hours'];
      $closing_hours = isset($_GET['closing_hours']) ? $_GET['closing_hours'] : $data['closing_hours'];
      $city = isset($_GET['city']) ? $_GET['city'] : $data['city'];
      $state = isset($_GET['state']) ? $_GET['state'] : $data['state'];
      $country = isset($_GET['country']) ? $_GET['country'] : $data['country'];
      $address = isset($_GET['address']) ? $_GET['address'] : $data['address'];

      $sqlSearchUser = "SELECT unique_id FROM vendor_users WHERE unique_id=:unique_id AND vendor_unique_id=:vendor_unique_id AND role=:role AND status=:status";
      $querySearchUser = $conn->prepare($sqlSearchUser);
      $querySearchUser->bindParam(":unique_id", $user_unique_id);
      $querySearchUser->bindParam(":vendor_unique_id", $vendor_unique_id);
      $querySearchUser->bindParam(":role", $role);
      $querySearchUser->bindParam(":status", $active);
      $querySearchUser->execute();

      if ($querySearchUser->rowCount() > 0) {

        $validation = $functions->edit_vendor_validation($business_name, $details, $opening_hours, $closing_hours, $city, $state, $country, $address);

        if ($validation["error"] == true) {
          $returnvalue = new genericClass();
          $returnvalue->engineError = 2;
          $returnvalue->engineErrorMessage = $validation["message"];
        }
        else {

          $stripped = $functions->strip_text($business_name);

          $sql2 = "SELECT unique_id FROM vendors WHERE unique_id=:unique_id AND status=:status";
          $query2 = $conn->prepare($sql2);
          $query2->bindParam(":unique_id", $vendor_unique_id);
          $query2->bindParam(":status", $active);
          $query2->execute();

          if ($query2->rowCount() > 0) {

            $sql3 = "SELECT business_name FROM vendors WHERE (business_name=:business_name OR stripped=:stripped) AND unique_id!=:unique_id AND status=:status";
            $query3 = $conn->prepare($sql3);
            $query3->bindParam(":business_name", $business_name);
            $query3->bindParam(":stripped", $stripped);
            $query3->bindParam(":unique_id", $vendor_unique_id);
            $query3->bindParam(":status", $active);
            $query3->execute();

            if ($query3->rowCount() > 0) {
              $returnvalue = new genericClass();
              $returnvalue->engineError = 2;
              $returnvalue->engineErrorMessage = "Vendor already exists";
            }
            else {

              $the_details = in_array($details, $not_allowed_values) ? null : $details;
              $the_opening_hours = in_array($opening_hours, $not_allowed_values) ? null : $opening_hours;
              $the_closing_hours = in_array($closing_hours, $not_allowed_values) ? null : $closing_hours;
              $the_city = in_array($city, $not_allowed_values) ? null : $city;
              $the_state = in_array($state, $not_allowed_values) ? null : $state;
              $the_country = in_array($country, $not_allowed_values) ? null : $country;
              $the_address = in_array($address, $not_allowed_values) ? null : $address;

              $sql = "UPDATE vendors SET business_name=:business_name, stripped=:stripped, details=:details, opening_hours=:opening_hours, closing_hours=:closing_hours,
              city=:city, state=:state, country=:country, address=:address, last_modified=:last_modified WHERE unique_id=:unique_id";
              $query = $conn->prepare($sql);
              $query->bindParam(":business_name", $business_name);
              $query->bindParam(":stripped", $stripped);
              $query->bindParam(":details", $the_details);
              $query->bindParam(":opening_hours", $the_opening_hours);
              $query->bindParam(":closing_hours", $the_closing_hours);
              $query->bindParam(":city", $the_city);
              $query->bindParam(":state", $the_state);
              $query->bindParam(":country", $the_country);
              $query->bindParam(":address", $the_address);
              $query->bindParam(":unique_id", $vendor_unique_id);
              $query->bindParam(":last_modified", $date_added);
              $query->execute();

              if ($query->rowCount() > 0) {
                $returnvalue = new genericClass();
                $returnvalue->engineMessage = 1;
              }
              else {
                $returnvalue = new genericClass();
                $returnvalue->engineError = 2;
                $returnvalue->engineErrorMessage = "Not edited (vendor)";
              }
            }

          }
          else {
            $returnvalue = new genericClass();
            $returnvalue->engineError = 2;
            $returnvalue->engineErrorMessage = "Vendor not found";
          }

        }

      }
      else {
        $returnvalue = new genericClass();
        $returnvalue->engineError = 2;
        $returnvalue->engineErrorMessage = "Vendor owner not found";
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
