<?php

  date_default_timezone_set("Africa/Lagos");

  class Functions {

    public $not_allowed_values;
    public $rating_history_values;
    public $allow_null_values;
    public $shipment_table_values;
    public $user_addresses_table_values;
    public $vendor_user_allowed_values;
    public $preference_allowed_values;
    public $user_preference_allowed_values;
    public $authorized_users;
    public $today;
    public $today_alt;
    public $start_today;
    public $end_today;
    public $tomorrow;
    public $next_month;
    public $output;

    public $completed = "Completed";
    public $deleted = "Deleted";
    public $restored = "Restored";
    public $processing = "Processing";
    public $cancelled = "Cancelled";
    public $pending = "Pending";
    public $started = "Started";
    public $paid = "Paid";
    public $unpaid = "Unpaid";
    public $shipped = "Shipped";
    public $shipping = "Shipping";
    public $add_coupon = "Coupon Added";
    public $add_shipping_fee = "Shipping Fee Added";
    public $disputed = "Disputed";
    public $refunded = "Refunded";
    public $checked_out = "Checked Out";
    public $approved = "Approved";
    public $not_approved = "Not Approved";
    public $anonymous = "Anonymous";
    public $yes = "yes";
    public $Yes = "Yes";
    public $no = "no";
    public $No = "No";

    // Transaction types
    public $subscription = "Subscription";
    public $debt = "Service charge";
    public $withdrawal = "Withdrawal";
    public $deposit = "Deposit"; // Might not use this self sha
    public $debt_incurred = "Service charge incurred";
    public $debt_nullified = "Service charge nullified";
    public $debt_payed = "Service charge payed";

    // Vendor stuffs
    public $our_percentage = 5;
    public $our_subscription_fee = 1000;
    public $our_percentage_for_service_charge = 60;

    public $active = 1;
    public $not_active = 0;
    public $cart_checked_out = 2;
    public $null = null;
    public $null_alt = "null";
    public $cap_null = NULL;
    // public $undefined = undefined;
    // public $undefined_alt = "undefined";
    // public $cap_undefined = UNDEFINED;
    public $empty = "";
    public $start = 1;
    public $zero = 0;

    public $granted = 1;
    public $suspended = 2;
    public $revoked = 3;

    public $email_pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
    public $phone_number_pattern = "/^(?:\+(?:[0-9].?){6,14}[0-9])|([0][7-9][0-1][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])$/i"; // Use this one later for international numbers
    // public $phone_number_pattern = "/^([0][7-9][0-1][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])$/i";
    public $integer_pattern = "/^\d+$/i";

    public $check_length_1 = 1;
    public $check_length_2 = 2;
    public $check_length_3 = 3;
    public $check_length_5 = 5;
    public $check_length_10 = 10;
    public $check_length_11 = 11;
    public $check_length_20 = 20;
    public $check_length_25 = 25;
    public $check_length_30 = 30;
    public $check_length_50 = 50;
    public $check_length_100 = 100;
    public $check_length_150 = 150;
    public $check_length_200 = 200;
    public $check_length_250 = 250;
    public $check_length_255 = 255;
    public $check_length_300 = 300;
    public $check_length_500 = 500;
    public $check_length_1000 = 1000;
    public $check_length_2000 = 2000;
    public $check_length_3000 = 3000;
    public $check_length_5000 = 5000;
    public $check_length_TINYTEXT = 255;
    public $check_length_TEXT = 65535;
    public $check_length_MEDIUMTEXT = 16777215;
    public $check_length_LONGTEXT = 4294967295;

    public function __construct(){
      $this->not_allowed_values = array($this->null, $this->empty, $this->null_alt, $this->cap_null);
      $this->allow_null_values = array($this->empty);
      $this->rating_history_values = array($this->yes, $this->no);
      $this->shipment_table_values = array("id", "unique_id", "shipment_unique_id", "riders_unique_id", "current_location", "longitude", "latitude", "delivery_status", "added_date", "last_modified", "status", "rider_fullname");
      $this->user_addresses_table_values = array("address_first_name", "address_last_name", "address", "additional_information", "region", "city", "country");
      $this->vendor_user_allowed_values = array("Owner", "Administrator", "User");
      $this->preference_allowed_values = array("increase_stock");
      $this->user_preference_allowed_values = array("show_addresses");
      $this->authorized_users = array("deltorox", "oniranger", "xnychad");
      $this->today = date("Y-m-d H:i:s");
      $this->today_alt = date("Y-m-d");
      $this->start_today = $this->today_alt." "."00:00:00";
      $this->end_today = $this->today_alt." "."23:59:59";
      $datetime = new DateTime('tomorrow');
      $this->tomorrow = $datetime->format('Y-m-d H:i:s');
      $future_timestamp = strtotime("+1 month"); // get next month date from today
      $next_month_date = date('Y-m-d', $future_timestamp);
      $this->next_month = $next_month_date." "."00:00:00";
      $this->output = array('error' => false, 'message' => "");
    }

    public function random_str($len){
      $length = $len;
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public function convertObjectClass($objectA, $objectB, $patternA, $patternB, $final_class) {

      $new_object = new $final_class();

      // Initializing class properties
      foreach($objectA as $property => $value) {
        if (in_array($property,$patternA)) {
          $new_object->$property = $value;
        }
        else {
          $new_object->$property = null;
        }
      }

      foreach($objectB as $property => $value) {
        if (in_array($property,$patternB)) {
          $new_object->$property = $value;
        }
        else {
          $new_object->$property = null;
        }
      }

      return $new_object;
    }

    public function strip_text($string) {
      //Lower case everything
      $string = strtolower($string);
      //Make alphanumeric (removes all other characters)
      $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
      //Clean up multiple dashes or whitespaces
      $string = preg_replace("/[\s-]+/", " ", $string);
      //Convert whitespaces and underscore to dash
      $string = preg_replace("/[\s_]/", "-", $string);
      return $string;
    }

    public function strip_text_to_array($string) {
      //Lower case everything
      $string = strtolower($string);
      //Make alphanumeric (removes all other characters)
      // $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
      //Clean up multiple dashes or whitespaces
      $string = preg_replace("/[\s-]+/", " ", $string);
      //Convert whitespaces and underscore to dash
      $string = preg_replace("/[\s_]/", "-", $string);
      $splittedString = explode('-', $string);
      return $splittedString;
    }

    public function validateDate($date){
      $format = 'Y-m-d H:i:s';
      $d = DateTime::createFromFormat($format, $date);
      return $d && $d->format($format) == $date;
    }

    public function validateTime($date){
      $format = 'H:i';
      $d = DateTime::createFromFormat($format, $date);
      return $d && $d->format($format) == $date;
    }

    public function validateTimeGreaterThan($time1, $time2){
    	if(strtotime($time1) > strtotime($time2)){return true;}
    	else {return false;}
    }

    public function validateTimeLessThan($time1, $time2){
    	if(strtotime($time1) < strtotime($time2)){return true;}
    	else {return false;}
    }

    public function validateTimeEqual($time1, $time2){
    	if(strtotime($time1) == strtotime($time2)){return true;}
    	else {return false;}
    }

    public function validateInt($number){
      $convert_int_number = (int)$number;
      $convert_double_number = (double)$number;
      if ($convert_int_number != 0 && is_int($convert_int_number) == 1) {
        return true;
      }
      else if ($convert_double_number != 0 && is_double($convert_double_number) == 1){
        return true;
      }
      else {
        return false;
      }
    }

    public function validatePercentage($number){
      $convert_int_number = (int)$number;
      $convert_double_number = (double)$number;
      if ($convert_int_number != 0 && is_int($convert_int_number) == 1) {
        return true;
      }
      else if ($convert_double_number != 0 && is_double($convert_double_number) == 1){
        return true;
      }
      else {
        if ($convert_int_number > 0 && $convert_int_number <= 100) {
          return true;
        }
        else if ($convert_double_number > 0 && $convert_double_number <= 100){
          return true;
        }
        else {
          return false;
        }
      }
    }

    public function validateIntAlt($number){
      $convert_int_number = (int)$number;
      $convert_double_number = (double)$number;
      if ($convert_int_number != 0 && is_int($convert_int_number) == 1) {
        return true;
      }
      else if ($convert_double_number != 0 && is_double($convert_double_number) == 1){
        return true;
      }
      else if ($convert_int_number == 0 || $convert_double_number == 0){
        return true;
      }
      else {
        return false;
      }
    }

    public function validateString($string){
      if (is_string($string)) {
        return true;
      }
      else {
        return false;
      }
    }

    public function validateMoney($number){
      $convert_int_number = (int)$number;
      $convert_double_number = (double)$number;
      if ($convert_int_number != 0 && is_int($convert_int_number) == 1) {
        return true;
      }
      else if ($convert_double_number != 0 && is_double($convert_double_number) == 1){
        return true;
      }
      else {
        if ($number == 0) {
          return true;
        }
        else {
          return false;
        }
      }
    }

    public function validateMoneyAlt($number){
      $convert_int_number = (int)$number;
      $convert_double_number = (double)$number;
      if ($convert_int_number != 0 && is_int($convert_int_number) == 1) {
        return true;
      }
      else if ($convert_double_number != 0 && is_double($convert_double_number) == 1){
        return true;
      }
      else {
        if ($number == 0) {
          return true;
        }
        else {
          return false;
        }
      }
    }

    public function isJson($string) {
      if (is_array($string)) {
        return false;
      }
      else {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
      }
    }

    public function add_new_management_user_validation($fullname, $email, $phone_number, $role, $access){
      if (in_array($fullname,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Fullname is required";
        return $this->output;
      }
      else if (strlen($fullname) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Fullname max length reached";
        return $this->output;
      }
      else if (in_array($email,$this->not_allowed_values) && in_array($phone_number,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Email / Phone Number is required";
        return $this->output;
      }
      else if (!in_array($email,$this->not_allowed_values) && preg_match($this->email_pattern, $email) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Invalid email";
        return $this->output;
      }
      else if (!in_array($email,$this->not_allowed_values) && strlen($email) > $this->check_length_255) {
        $this->output['error'] = true;
        $this->output['message'] = "Email max length reached";
        return $this->output;
      }
      else if (!in_array($phone_number,$this->not_allowed_values) && preg_match($this->phone_number_pattern, $phone_number) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Invalid phone number";
        return $this->output;
      }
      else if (!in_array($phone_number,$this->not_allowed_values) && strlen($phone_number) > $this->check_length_20) {
        $this->output['error'] = true;
        $this->output['message'] = "Phone number max length reached";
        return $this->output;
      }
      else if (in_array($role,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Role is required";
        return $this->output;
      }
      else if ($this->validateInt($role) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Role is not a number";
        return $this->output;
      }
      else if (strlen($role) > $this->check_length_2) {
        $this->output['error'] = true;
        $this->output['message'] = "Role max length reached";
        return $this->output;
      }
      else if (in_array($access,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Access is required";
        return $this->output;
      }
      else if ($this->validateInt($access) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Access is not a number";
        return $this->output;
      }
      else if (strlen($access) > $this->check_length_2) {
        $this->output['error'] = true;
        $this->output['message'] = "Access max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_management_user_role_and_access_validation($value){
      if (in_array($value,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Value is required";
        return $this->output;
      }
      else if ($this->validateInt($value) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Value is not a number";
        return $this->output;
      }
      else if (strlen($value) > $this->check_length_2) {
        $this->output['error'] = true;
        $this->output['message'] = "Value max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_user_role_and_access_validation($role, $access){
      if (in_array($role,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Role is required";
        return $this->output;
      }
      else if ($this->validateInt($role) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Role is not a number";
        return $this->output;
      }
      else if (strlen($role) > $this->check_length_2) {
        $this->output['error'] = true;
        $this->output['message'] = "Role max length reached";
        return $this->output;
      }
      else if (in_array($access,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Access is required";
        return $this->output;
      }
      else if ($this->validateInt($access) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Access is not a number";
        return $this->output;
      }
      else if (strlen($access) > $this->check_length_2) {
        $this->output['error'] = true;
        $this->output['message'] = "Access max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_management_user_fullname_validation($fullname, $email, $phone_number){
      if (in_array($fullname,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Fullname is required";
        return $this->output;
      }
      else if (strlen($fullname) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Fullname max length reached";
        return $this->output;
      }
      else if (in_array($email,$this->not_allowed_values) && in_array($phone_number,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Email / Phone Number is required";
        return $this->output;
      }
      else if (!in_array($email,$this->not_allowed_values) && preg_match($this->email_pattern, $email) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Invalid email";
        return $this->output;
      }
      else if (!in_array($email,$this->not_allowed_values) && strlen($email) > $this->check_length_255) {
        $this->output['error'] = true;
        $this->output['message'] = "Email max length reached";
        return $this->output;
      }
      else if (!in_array($phone_number,$this->not_allowed_values) && preg_match($this->phone_number_pattern, $phone_number) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Invalid phone number";
        return $this->output;
      }
      else if (!in_array($phone_number,$this->not_allowed_values) && strlen($phone_number) > $this->check_length_20) {
        $this->output['error'] = true;
        $this->output['message'] = "Phone number max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function category_validation($name){
      if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_100) {
        $this->output['error'] = true;
        $this->output['message'] = "Name max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function sub_product_validation($product_unique_id, $name, $size, $description, $stock, $price){
      if (in_array($product_unique_id,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Product Unique ID is required";
        return $this->output;
      }
      else if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Product name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_200) {
        $this->output['error'] = true;
        $this->output['message'] = "Product name max length reached";
        return $this->output;
      }
      else if (strlen($size) > $this->check_length_30) {
        $this->output['error'] = true;
        $this->output['message'] = "Size max length reached";
        return $this->output;
      }
      else if (in_array($description,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Description is required";
        return $this->output;
      }
      else if (strlen($description) > $this->check_length_TEXT) {
        $this->output['error'] = true;
        $this->output['message'] = "Description max length reached";
        return $this->output;
      }
      // else if (in_array($stock,$this->not_allowed_values)) {
      //   $this->output['error'] = true;
      //   $this->output['message'] = "Stock is required";
      //   return $this->output;
      // }
      else if ($this->validateIntAlt($stock) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Stock is not a number";
        return $this->output;
      }
      else if (strlen($stock) > $this->check_length_11) {
        $this->output['error'] = true;
        $this->output['message'] = "Stock max length reached";
        return $this->output;
      }
      else if (in_array($price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is required";
        return $this->output;
      }
      else if ($this->validateMoney($price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_sub_product_validation($product_unique_id, $name, $size, $description){
      if (in_array($product_unique_id,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Product Unique ID is required";
        return $this->output;
      }
      else if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Product name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_200) {
        $this->output['error'] = true;
        $this->output['message'] = "Product name max length reached";
        return $this->output;
      }
      else if (strlen($size) > $this->check_length_30) {
        $this->output['error'] = true;
        $this->output['message'] = "Size max length reached";
        return $this->output;
      }
      else if (in_array($description,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Description is required";
        return $this->output;
      }
      else if (strlen($description) > $this->check_length_TEXT) {
        $this->output['error'] = true;
        $this->output['message'] = "Description max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function product_validation($name, $description){
      if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Product name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_200) {
        $this->output['error'] = true;
        $this->output['message'] = "Product name max length reached";
        return $this->output;
      }
      else if (in_array($description,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Description is required";
        return $this->output;
      }
      else if (strlen($description) > $this->check_length_TEXT) {
        $this->output['error'] = true;
        $this->output['message'] = "Description max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function sharing_validation($name, $description, $total_price, $split_price, $total_no_of_persons, $expiration, $starting_date, $expiry_date){
      if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Sharing item name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_200) {
        $this->output['error'] = true;
        $this->output['message'] = "Sharing item name max length reached";
        return $this->output;
      }
      else if (in_array($description,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Description is required";
        return $this->output;
      }
      else if (strlen($description) > $this->check_length_TEXT) {
        $this->output['error'] = true;
        $this->output['message'] = "Description max length reached";
        return $this->output;
      }
      else if (in_array($total_price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Total Price is required";
        return $this->output;
      }
      else if ($this->validateMoney($total_price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Total Price is not a number";
        return $this->output;
      }
      else if (in_array($split_price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Split Price is required";
        return $this->output;
      }
      else if ($this->validateMoney($split_price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Split Price is not a number";
        return $this->output;
      }
      else if (in_array($total_no_of_persons,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Total number of persons is required";
        return $this->output;
      }
      else if ($this->validateInt($total_no_of_persons) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Total number of persons is not a number";
        return $this->output;
      }
      // else if (in_array($expiration,$this->not_allowed_values)) {
      //   $this->output['error'] = true;
      //   $this->output['message'] = "Expiration is required";
      //   return $this->output;
      // }
      else if ($this->validateIntAlt($expiration) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Expiration is not a number";
        return $this->output;
      }
      else if ($this->validateDate($starting_date) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Starting date is invalid (eg. 2021-01-01 23:00:00)";
        return $this->output;
      }
      else if ($expiration == 1 && $this->validateDate($expiry_date) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Expiry date is invalid (eg. 2021-01-01 23:00:00)";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_product_stock_validation($stock){
      if (in_array($stock,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Stock is required";
        return $this->output;
      }
      else if ($this->validateInt($stock) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Stock is not a number";
        return $this->output;
      }
      else if (strlen($stock) > $this->check_length_11) {
        $this->output['error'] = true;
        $this->output['message'] = "Stock max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_product_price_validation($price){
      if (in_array($price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is required";
        return $this->output;
      }
      else if ($this->validateMoney($price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_product_prices_validation($price, $sales_price){
      if (in_array($price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is required";
        return $this->output;
      }
      else if ($this->validateMoney($price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is not a number";
        return $this->output;
      }
      // else if (in_array($sales_price,$this->not_allowed_values)) {
      //   $this->output['error'] = true;
      //   $this->output['message'] = "Sales Price is required";
      //   return $this->output;
      // }
      else if ($this->validateMoneyAlt($sales_price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Sales Price is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_product_sales_price_validation($sales_price){
      if (in_array($sales_price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Sales price is required";
        return $this->output;
      }
      else if ($this->validateMoney($sales_price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Sales price is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function brand_validation($name, $details){
      if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Name max length reached";
        return $this->output;
      }
      else if (in_array($details,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Details is required";
        return $this->output;
      }
      else if (strlen($details) > $this->check_length_500) {
        $this->output['error'] = true;
        $this->output['message'] = "Details max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function cart_validation($sub_product_unique_id, $quantity){
      if (in_array($sub_product_unique_id,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Sub Product ID is required";
        return $this->output;
      }
      else if (in_array($quantity,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Quantity is required";
        return $this->output;
      }
      else if ($this->validateInt($quantity) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Quantity is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function cart_services_validation($sub_product_unique_id){
      if (in_array($sub_product_unique_id,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Sub Product ID is required";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function add_new_user_validation($fullname, $email, $phone_number){
      if (in_array($fullname,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Fullname is required";
        return $this->output;
      }
      else if (strlen($fullname) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Fullname max length reached";
        return $this->output;
      }
      else if (in_array($email,$this->not_allowed_values) && in_array($phone_number,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Email / Phone Number is required";
        return $this->output;
      }
      else if (!in_array($email,$this->not_allowed_values) && preg_match($this->email_pattern, $email) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Invalid email";
        return $this->output;
      }
      else if (!in_array($email,$this->not_allowed_values) && strlen($email) > $this->check_length_255) {
        $this->output['error'] = true;
        $this->output['message'] = "Email max length reached";
        return $this->output;
      }
      else if (!in_array($phone_number,$this->not_allowed_values) && preg_match($this->phone_number_pattern, $phone_number) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Invalid phone number";
        return $this->output;
      }
      else if (!in_array($phone_number,$this->not_allowed_values) && strlen($phone_number) > $this->check_length_20) {
        $this->output['error'] = true;
        $this->output['message'] = "Phone number max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_user_access_validation($value){
      if (in_array($value,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Value is required";
        return $this->output;
      }
      else if ($this->validateInt($value) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Value is not a number";
        return $this->output;
      }
      else if (strlen($value) > $this->check_length_1) {
        $this->output['error'] = true;
        $this->output['message'] = "Value max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_user_vendor_role_validation($role){
      if (in_array($role,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Role is required";
        return $this->output;
      }
      else if (!in_array($role,$this->vendor_user_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Role doesn't exist";
        return $this->output;
      }
      else if (strlen($role) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Role max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function shipment_validation($current_location, $longitude, $latitude){
      if (in_array($current_location,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Current location is required";
        return $this->output;
      }
      else if (strlen($current_location) > $this->check_length_200) {
        $this->output['error'] = true;
        $this->output['message'] = "Current location max length reached";
        return $this->output;
      }
      else if (in_array($longitude,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Longitude is required";
        return $this->output;
      }
      else if (strlen($longitude) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Longitude max length reached";
        return $this->output;
      }
      else if (in_array($latitude,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Latitude is required";
        return $this->output;
      }
      else if (strlen($latitude) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Latitude max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function offered_services_validation($service, $details, $price){
      if (in_array($service,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Service is required";
        return $this->output;
      }
      else if (strlen($service) > $this->check_length_30) {
        $this->output['error'] = true;
        $this->output['message'] = "Service max length reached";
        return $this->output;
      }
      else if (in_array($details,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Details is required";
        return $this->output;
      }
      else if (strlen($details) > $this->check_length_100) {
        $this->output['error'] = true;
        $this->output['message'] = "Details max length reached";
        return $this->output;
      }
      // else if (in_array($price,$this->not_allowed_values)) {
      //   $this->output['error'] = true;
      //   $this->output['message'] = "Price is required";
      //   return $this->output;
      // }
      else if ($this->validateMoneyAlt($price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function offered_services_category_validation($service_category){
      if (in_array($service_category,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Service Category is required";
        return $this->output;
      }
      else if (strlen($service_category) > $this->check_length_30) {
        $this->output['error'] = true;
        $this->output['message'] = "Service Category max length reached";
        return $this->output;
      }
    }

    public function address_validation($firstname, $lastname, $address, $additional_information, $city, $state, $country){
      if (in_array($firstname,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "First name is required";
        return $this->output;
      }
      else if (strlen($firstname) > $this->check_length_25) {
        $this->output['error'] = true;
        $this->output['message'] = "First name max length reached";
        return $this->output;
      }
      else if (in_array($lastname,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Last name is required";
        return $this->output;
      }
      else if (strlen($lastname) > $this->check_length_25) {
        $this->output['error'] = true;
        $this->output['message'] = "Last name max length reached";
        return $this->output;
      }
      else if (in_array($address,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Address is required";
        return $this->output;
      }
      else if (strlen($address) > $this->check_length_200) {
        $this->output['error'] = true;
        $this->output['message'] = "Address max length reached";
        return $this->output;
      }
      else if (strlen($additional_information) > $this->check_length_150) {
        $this->output['error'] = true;
        $this->output['message'] = "Additional information max length reached";
        return $this->output;
      }
      else if (in_array($city,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "City is required";
        return $this->output;
      }
      else if (strlen($city) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "City max length reached";
        return $this->output;
      }
      else if (in_array($state,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "State is required";
        return $this->output;
      }
      else if (strlen($state) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "State max length reached";
        return $this->output;
      }
      else if (in_array($country,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Country is required";
        return $this->output;
      }
      else if (strlen($country) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Country max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function review_ratings_validation($rating){
      $rating = strtolower($rating);
      if (in_array($rating,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Rating is required";
        return $this->output;
      }
      else if (!in_array($rating,$this->rating_history_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Rating is either ".$this->yes." or ".$this->no;
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function review_validation($message){
      if (in_array($message,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Message is required";
        return $this->output;
      }
      else if (strlen($message) > $this->check_length_500) {
        $this->output['error'] = true;
        $this->output['message'] = "Message max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function edit_order_validation($quantity, $description){
      if (in_array($quantity,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Quantity is required";
        return $this->output;
      }
      else if ($this->validateInt($quantity) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Quantity is not a number";
        return $this->output;
      }
      else if (strlen($quantity) > $this->check_length_11) {
        $this->output['error'] = true;
        $this->output['message'] = "Quantity max length reached";
        return $this->output;
      }
      else if (strlen($description) > $this->check_length_5000) {
        $this->output['error'] = true;
        $this->output['message'] = "Description max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function order_validation($quantity){
      if (in_array($quantity,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Quantity is required";
        return $this->output;
      }
      else if ($this->validateInt($quantity) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Quantity is not a number";
        return $this->output;
      }
      else if (strlen($quantity) > $this->check_length_11) {
        $this->output['error'] = true;
        $this->output['message'] = "Quantity max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function coupon_validation($user_unique_id, $product_unique_id, $code, $name, $percentage, $total_count, $expiry_date){
      if (in_array($user_unique_id,$this->not_allowed_values) && in_array($product_unique_id,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Either USER_UNIQUE_ID or PRODUCT_UNIQUE_ID one of them is required";
        return $this->output;
      }
      else if (!in_array($user_unique_id,$this->not_allowed_values) && !in_array($product_unique_id,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Choose either USER_UNIQUE_ID or PRODUCT_UNIQUE_ID, can't choose both";
        return $this->output;
      }
      else if (in_array($code,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Code is required";
        return $this->output;
      }
      else if (strlen($code) > $this->check_length_30) {
        $this->output['error'] = true;
        $this->output['message'] = "Code max length reached";
        return $this->output;
      }
      else if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Name max length reached";
        return $this->output;
      }
      else if (in_array($percentage,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Percentage is required";
        return $this->output;
      }
      else if ($this->validatePercentage($percentage) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Percentage is not a number";
        return $this->output;
      }
      else if (in_array($total_count,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Total coupons is required";
        return $this->output;
      }
      else if ($this->validateInt($total_count) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Total coupons is not a number";
        return $this->output;
      }
      else if (strlen($total_count) > $this->check_length_11) {
        $this->output['error'] = true;
        $this->output['message'] = "Total coupons max length reached";
        return $this->output;
      }
      else if ($this->validateDate($expiry_date) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Expiry date is invalid (eg. 2021-01-01 23:00:00)";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function users_coupon_validation($code, $name, $percentage, $total_count, $expiry_date){
      if (in_array($code,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Code is required";
        return $this->output;
      }
      else if (strlen($code) > $this->check_length_30) {
        $this->output['error'] = true;
        $this->output['message'] = "Code max length reached";
        return $this->output;
      }
      else if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Name max length reached";
        return $this->output;
      }
      else if (in_array($percentage,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Percentage is required";
        return $this->output;
      }
      else if ($this->validatePercentage($percentage) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Percentage is not a number";
        return $this->output;
      }
      else if (in_array($total_count,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Total coupons is required";
        return $this->output;
      }
      else if ($this->validateInt($total_count) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Total coupons is not a number";
        return $this->output;
      }
      else if (strlen($total_count) > $this->check_length_1) {
        $this->output['error'] = true;
        $this->output['message'] = "Total coupons max length reached";
        return $this->output;
      }
      else if ($this->validateDate($expiry_date) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Expiry date is invalid (eg. 2021-01-01 23:00:00)";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function product_coupon_validation($code, $name, $percentage, $total_count, $expiry_date){
      if (in_array($code,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Code is required";
        return $this->output;
      }
      else if (strlen($code) > $this->check_length_30) {
        $this->output['error'] = true;
        $this->output['message'] = "Code max length reached";
        return $this->output;
      }
      else if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Name max length reached";
        return $this->output;
      }
      else if (in_array($percentage,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Percentage is required";
        return $this->output;
      }
      else if ($this->validatePercentage($percentage) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Percentage is not a number";
        return $this->output;
      }
      else if (in_array($total_count,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Total coupons is required";
        return $this->output;
      }
      else if ($this->validateInt($total_count) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Total coupons is not a number";
        return $this->output;
      }
      else if (strlen($total_count) > $this->check_length_11) {
        $this->output['error'] = true;
        $this->output['message'] = "Total coupons max length reached";
        return $this->output;
      }
      else if ($this->validateDate($expiry_date) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Expiry date is invalid (eg. 2021-01-01 23:00:00)";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function dispute_validation($message){
      if (in_array($message,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Message is required";
        return $this->output;
      }
      else if (strlen($message) > $this->check_length_500) {
        $this->output['error'] = true;
        $this->output['message'] = "Message max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function deals_validation($url){
      if (in_array($url,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "URL is required";
        return $this->output;
      }
      else if (strlen($url) > $this->check_length_300) {
        $this->output['error'] = true;
        $this->output['message'] = "URL max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function shipping_fees_validation($city, $state, $country, $price){
      if (in_array($city,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "City is required";
        return $this->output;
      }
      else if (strlen($city) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "City max length reached";
        return $this->output;
      }
      else if (in_array($state,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "State is required";
        return $this->output;
      }
      else if (strlen($state) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "State max length reached";
        return $this->output;
      }
      else if (in_array($country,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Country is required";
        return $this->output;
      }
      else if (strlen($country) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Country max length reached";
        return $this->output;
      }
      // else if (in_array($price,$this->not_allowed_values)) {
      //   $this->output['error'] = true;
      //   $this->output['message'] = "Price is required";
      //   return $this->output;
      // }
      else if ($this->validateMoneyAlt($price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function navigation_validation($nav_title, $nav_link, $nav_icon){
      if (in_array($nav_title,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Navigation Title is required";
        return $this->output;
      }
      else if (strlen($nav_title) > $this->check_length_30) {
        $this->output['error'] = true;
        $this->output['message'] = "Navigation Title max length reached";
        return $this->output;
      }
      else if (in_array($nav_link,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Navigation Link is required";
        return $this->output;
      }
      else if (strlen($nav_link) > $this->check_length_30) {
        $this->output['error'] = true;
        $this->output['message'] = "Navigation Link max length reached";
        return $this->output;
      }
      else if (in_array($nav_icon,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Navigation Icon is required";
        return $this->output;
      }
      else if (strlen($nav_icon) > $this->check_length_20) {
        $this->output['error'] = true;
        $this->output['message'] = "Navigation Icon max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function vendor_validation($business_name, $details, $fullname, $email, $phone_number, $opening_hours, $closing_hours, $city, $state, $country, $address, $role, $access){
      if (in_array($business_name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Business name is required";
        return $this->output;
      }
      else if (strlen($business_name) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Business name max length reached";
        return $this->output;
      }
      else if (!in_array($details,$this->not_allowed_values) && strlen($details) > $this->check_length_300) {
        $this->output['error'] = true;
        $this->output['message'] = "Details max length reached";
        return $this->output;
      }
      else if (in_array($fullname,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Fullname is required";
        return $this->output;
      }
      else if (strlen($fullname) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Fullname max length reached";
        return $this->output;
      }
      else if (in_array($email,$this->not_allowed_values) && in_array($phone_number,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Email & Phone Number is required";
        return $this->output;
      }
      else if (!in_array($email,$this->not_allowed_values) && preg_match($this->email_pattern, $email) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Invalid email";
        return $this->output;
      }
      else if (!in_array($email,$this->not_allowed_values) && strlen($email) > $this->check_length_255) {
        $this->output['error'] = true;
        $this->output['message'] = "Email max length reached";
        return $this->output;
      }
      else if (!in_array($phone_number,$this->not_allowed_values) && preg_match($this->phone_number_pattern, $phone_number) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Invalid phone number";
        return $this->output;
      }
      else if (!in_array($phone_number,$this->not_allowed_values) && strlen($phone_number) > $this->check_length_20) {
        $this->output['error'] = true;
        $this->output['message'] = "Phone number max length reached";
        return $this->output;
      }
      else if (!in_array($opening_hours,$this->not_allowed_values) && $this->validateTime($opening_hours) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Opening hours invalid (eg. 23:00:00)";
        return $this->output;
      }
      else if (!in_array($closing_hours,$this->not_allowed_values) && $this->validateTime($closing_hours) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Closing hours invalid (eg. 23:00:00)";
        return $this->output;
      }
      else if ((!in_array($opening_hours,$this->not_allowed_values) && !in_array($closing_hours,$this->not_allowed_values)) && $this->validateTimeEqual($closing_hours, $opening_hours) == true) {
        $this->output['error'] = true;
        $this->output['message'] = "Opening & Closing hours can't be equals";
        return $this->output;
      }
      else if ((!in_array($opening_hours,$this->not_allowed_values) && !in_array($closing_hours,$this->not_allowed_values)) && $this->validateTimeLessThan($closing_hours, $opening_hours) == true) {
        $this->output['error'] = true;
        $this->output['message'] = "Closing hours can't be less than Opening hours";
        return $this->output;
      }
      else if (!in_array($city,$this->not_allowed_values) && strlen($city) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "City max length reached";
        return $this->output;
      }
      else if (!in_array($state,$this->not_allowed_values) && strlen($state) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "State max length reached";
        return $this->output;
      }
      else if (!in_array($country,$this->not_allowed_values) && strlen($country) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Country max length reached";
        return $this->output;
      }
      else if (!in_array($address,$this->not_allowed_values) && strlen($address) > $this->check_length_200) {
        $this->output['error'] = true;
        $this->output['message'] = "Address max length reached";
        return $this->output;
      }
      else if (!in_array($role,$this->vendor_user_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Role doesn't exist";
        return $this->output;
      }
      else if (strlen($role) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Role max length reached";
        return $this->output;
      }
      else if (in_array($access,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Access is required";
        return $this->output;
      }
      else if ($this->validateInt($access) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Access is not a number";
        return $this->output;
      }
      else if (strlen($access) > $this->check_length_1) {
        $this->output['error'] = true;
        $this->output['message'] = "Access max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function edit_vendor_validation($business_name, $details, $opening_hours, $closing_hours, $city, $state, $country, $address){
      if (in_array($business_name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Business name is required";
        return $this->output;
      }
      else if (strlen($business_name) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Business name max length reached";
        return $this->output;
      }
      else if (!in_array($details,$this->not_allowed_values) && strlen($details) > $this->check_length_300) {
        $this->output['error'] = true;
        $this->output['message'] = "Details max length reached";
        return $this->output;
      }
      else if (!in_array($opening_hours,$this->not_allowed_values) && $this->validateTime($opening_hours) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Opening hours invalid (eg. 23:00:00)";
        return $this->output;
      }
      else if (!in_array($closing_hours,$this->not_allowed_values) && $this->validateTime($closing_hours) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Closing hours invalid (eg. 23:00:00)";
        return $this->output;
      }
      else if ((!in_array($opening_hours,$this->not_allowed_values) && !in_array($closing_hours,$this->not_allowed_values)) && $this->validateTimeEqual($closing_hours, $opening_hours) == true) {
        $this->output['error'] = true;
        $this->output['message'] = "Opening & Closing hours can't be equals";
        return $this->output;
      }
      else if ((!in_array($opening_hours,$this->not_allowed_values) && !in_array($closing_hours,$this->not_allowed_values)) && $this->validateTimeLessThan($closing_hours, $opening_hours) == true) {
        $this->output['error'] = true;
        $this->output['message'] = "Closing hours can't be less than Opening hours";
        return $this->output;
      }
      else if (!in_array($city,$this->not_allowed_values) && strlen($city) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "City max length reached";
        return $this->output;
      }
      else if (!in_array($state,$this->not_allowed_values) && strlen($state) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "State max length reached";
        return $this->output;
      }
      else if (!in_array($country,$this->not_allowed_values) && strlen($country) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Country max length reached";
        return $this->output;
      }
      else if (!in_array($address,$this->not_allowed_values) && strlen($address) > $this->check_length_200) {
        $this->output['error'] = true;
        $this->output['message'] = "Address max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function vendor_user_validation($fullname, $email, $phone_number, $role, $access){
      if (in_array($fullname,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Fullname is required";
        return $this->output;
      }
      else if (strlen($fullname) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Fullname max length reached";
        return $this->output;
      }
      else if (in_array($email,$this->not_allowed_values) && in_array($phone_number,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Email / Phone Number is required";
        return $this->output;
      }
      else if (!in_array($email,$this->not_allowed_values) && preg_match($this->email_pattern, $email) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Invalid email";
        return $this->output;
      }
      else if (!in_array($email,$this->not_allowed_values) && strlen($email) > $this->check_length_255) {
        $this->output['error'] = true;
        $this->output['message'] = "Email max length reached";
        return $this->output;
      }
      else if (!in_array($phone_number,$this->not_allowed_values) && preg_match($this->phone_number_pattern, $phone_number) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Invalid phone number";
        return $this->output;
      }
      else if (!in_array($phone_number,$this->not_allowed_values) && strlen($phone_number) > $this->check_length_20) {
        $this->output['error'] = true;
        $this->output['message'] = "Phone number max length reached";
        return $this->output;
      }
      else if (!in_array($role,$this->vendor_user_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Role doesn't exist";
        return $this->output;
      }
      else if (strlen($role) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Role max length reached";
        return $this->output;
      }
      else if (in_array($access,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Access is required";
        return $this->output;
      }
      else if ($this->validateInt($access) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Access is not a number";
        return $this->output;
      }
      else if (strlen($access) > $this->check_length_2) {
        $this->output['error'] = true;
        $this->output['message'] = "Access max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function vendor_product_validation($category_unique_id, $name, $description, $duration, $weight, $stock, $price, $sales_price){
      if (in_array($category_unique_id,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Category Unique ID is required";
        return $this->output;
      }
      else if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Item name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_200) {
        $this->output['error'] = true;
        $this->output['message'] = "Item name max length reached";
        return $this->output;
      }
      else if (in_array($description,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Description is required";
        return $this->output;
      }
      else if (strlen($description) > $this->check_length_3000) {
        $this->output['error'] = true;
        $this->output['message'] = "Description max length reached";
        return $this->output;
      }
      else if (!in_array($duration,$this->not_allowed_values) && strlen($duration) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Duration max length reached";
        return $this->output;
      }
      else if (!in_array($weight,$this->not_allowed_values) && strlen($weight) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Weight max length reached";
        return $this->output;
      }
      else if (in_array($stock,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Stock is required";
        return $this->output;
      }
      else if ($this->validateIntAlt($stock) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Stock is not a number";
        return $this->output;
      }
      else if (strlen($stock) > $this->check_length_11) {
        $this->output['error'] = true;
        $this->output['message'] = "Stock max length reached";
        return $this->output;
      }
      else if (in_array($price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is required";
        return $this->output;
      }
      else if ($this->validateMoney($price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is not a number";
        return $this->output;
      }
      else if (in_array($sales_price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Sales Price is required";
        return $this->output;
      }
      else if ($this->validateMoneyAlt($sales_price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Sales Price is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function update_vendor_product_validation($category_unique_id, $name, $description, $duration, $weight, $price, $sales_price){
      if (in_array($category_unique_id,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Category Unique ID is required";
        return $this->output;
      }
      else if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Item name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_200) {
        $this->output['error'] = true;
        $this->output['message'] = "Item name max length reached";
        return $this->output;
      }
      else if (in_array($description,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Description is required";
        return $this->output;
      }
      else if (strlen($description) > $this->check_length_3000) {
        $this->output['error'] = true;
        $this->output['message'] = "Description max length reached";
        return $this->output;
      }
      else if (!in_array($duration,$this->not_allowed_values) && strlen($duration) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Duration max length reached";
        return $this->output;
      }
      else if (!in_array($weight,$this->not_allowed_values) && strlen($weight) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Weight max length reached";
        return $this->output;
      }
      else if (in_array($price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is required";
        return $this->output;
      }
      else if ($this->validateMoney($price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is not a number";
        return $this->output;
      }
      else if (in_array($sales_price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Sales Price is required";
        return $this->output;
      }
      else if ($this->validateMoneyAlt($sales_price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Sales Price is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function vendor_menu_validation($name, $start_time, $end_time){
      if (in_array($name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Name is required";
        return $this->output;
      }
      else if (strlen($name) > $this->check_length_100) {
        $this->output['error'] = true;
        $this->output['message'] = "Name max length reached";
        return $this->output;
      }
      else if (!in_array($start_time,$this->not_allowed_values) && $this->validateTime($start_time) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Start time invalid (eg. 23:00:00)";
        return $this->output;
      }
      else if (!in_array($end_time,$this->not_allowed_values) && $this->validateTime($end_time) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "End time invalid (eg. 23:00:00)";
        return $this->output;
      }
      else if ((!in_array($start_time,$this->not_allowed_values) && !in_array($end_time,$this->not_allowed_values)) && $this->validateTimeEqual($end_time, $start_time) == true) {
        $this->output['error'] = true;
        $this->output['message'] = "Start & End time can't be equals";
        return $this->output;
      }
      else if ((!in_array($start_time,$this->not_allowed_values) && !in_array($end_time,$this->not_allowed_values)) && $this->validateTimeLessThan($end_time, $start_time) == true) {
        $this->output['error'] = true;
        $this->output['message'] = "End time can't be less than Start time";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function vendor_shipping_fees_validation($city, $state, $country, $price){
      if (in_array($city,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "City is required";
        return $this->output;
      }
      else if (strlen($city) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "City max length reached";
        return $this->output;
      }
      else if (in_array($state,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "State is required";
        return $this->output;
      }
      else if (strlen($state) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "State max length reached";
        return $this->output;
      }
      else if (in_array($country,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Country is required";
        return $this->output;
      }
      else if (strlen($country) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Country max length reached";
        return $this->output;
      }
      else if (in_array($price,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is required";
        return $this->output;
      }
      else if ($this->validateMoneyAlt($price) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Price is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function vendor_account_validation($account_name, $account_number, $bank){
      if (in_array($account_name,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Account name is required";
        return $this->output;
      }
      else if (strlen($account_name) > $this->check_length_150) {
        $this->output['error'] = true;
        $this->output['message'] = "Account name max length reached";
        return $this->output;
      }
      else if (in_array($account_number,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Account number is required";
        return $this->output;
      }
      else if (preg_match($this->integer_pattern, $account_number) !== 1) {
        $this->output['error'] = true;
        $this->output['message'] = "Account number is not an integer";
        return $this->output;
      }
      else if (strlen($account_number) > $this->check_length_10) {
        $this->output['error'] = true;
        $this->output['message'] = "Account number max length reached";
        return $this->output;
      }
      else if (in_array($bank,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Bank is required";
        return $this->output;
      }
      else if (strlen($bank) > $this->check_length_50) {
        $this->output['error'] = true;
        $this->output['message'] = "Bank max length reached";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

    public function vendor_withdrawal_validation($amount){
      if (in_array($amount,$this->not_allowed_values)) {
        $this->output['error'] = true;
        $this->output['message'] = "Amount is required";
        return $this->output;
      }
      else if ($this->validateMoneyAlt($amount) == false) {
        $this->output['error'] = true;
        $this->output['message'] = "Amount is not a number";
        return $this->output;
      }
      else{
        $this->output['error'] = false;
        $this->output['message'] = "Validated";
        return $this->output;
      }
    }

  }

?>
