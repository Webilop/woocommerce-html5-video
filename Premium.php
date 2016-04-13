<?php
namespace WooCommerceHTML5Video;

class Premium {

  const HTTP_GET = 1;
  const HTTP_POST = 2;
  const REST_URL = 'http://dev.webilop.com/webilop-3.0';
  const PLUGIN = 'woocomerce-html5-video';

  public static function check_plugin() {
    if (!self::check_premium()) {
      $_SESSION['premium'] = false;
    }
    else {
      $_SESSION['premium'] = true;
    }
  }

  public static function check_premium() {
    $args = array(
      'domain'  => home_url(),
      'concept' => self::PLUGIN
    );
    $payment = self::connection(self::HTTP_POST, 'verify-payment', $args);
    if (200 != $payment[0]) {
      return false;
    }

    $payment = json_decode($payment[1], true);
    $payment = $payment['result'];
    if (!empty($payment) && !empty($payment[0])) {
      $option = get_option('webilop_pluigns_premium', array());
      $option['woocomerce-html5-video'] = date("Y-m-d H:i:s");
      update_option('webilop_pluigns_premium', $option);
      return true;
    }
    else {
      return false;
    }
  }

  public static function connection($method, $action, $args) {
    $fullUrl = self::REST_URL . '/' . $action ;
    if($method === self::HTTP_GET) {
      $fullUrl .= '?'. http_build_query($args);
    }

    $curl = curl_init();
    $options = array();
    $options[CURLOPT_URL] = $fullUrl;
    $options[CURLOPT_RETURNTRANSFER] = true;
    if($method === self::HTTP_POST) {
      $options[CURLOPT_POST] = 1;
      $options[CURLOPT_CUSTOMREQUEST] = "POST";
      $options[CURLOPT_POSTFIELDS] = $args;
      $options[CURLOPT_HTTPHEADER] = array(
        'Content-Type: multipart/form-data'
      );
    }

    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $err = curl_error($curl);

    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($err) {
      return array($httpcode, $err);
    }
    else if (200 != $httpcode) {
      return array($httpcode);
    }
    return array($httpcode, $response);
  }
}
