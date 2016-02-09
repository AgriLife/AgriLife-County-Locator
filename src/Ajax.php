<?php

namespace AgriLife\OfficeLocator;

class Ajax {

  public function __construct() {

    $applicationID = 3;
    $method = 'units';
    $data = array(
      'site_id' => $applicationID,
      'entity_id' => 2,
      'limit_to_active' => 1,
      'validation_key' => base64_encode( md5( $applicationID . AGRILIFE_API_KEY, true ) ),
    );
    $transientname = 'county_office_locator';
    
    $transient = get_transient( $transientname );
    
    if (!$transient){
      try {
        $apidata = $this->make_people_api_call( $method, $data );
        $results = $apidata['json'];

        if ($results['status'] == 200){
          // Loop through each entry and remove if its [unit_name] does not include the word "County Office".
          $aResults = $results['units'];

          $filtered = array_filter( $aResults, function( $item ) {
            if ( strpos( $item['unit_name'], 'County Office' ) === false) {
              return false;
            }

            return true;
          } );

          foreach($filtered as $key => $value){
            // Change phone number to desired format.
            $filtered[$key]['phone_number'] = str_replace( '.', '-', $value['phone_number'] );
          }

          // Add filtered array to database as transient
          set_transient( $transientname, $filtered, 4*WEEK_IN_SECONDS );

        }
      }
      catch (\Exception $e) {
        // echo $e->getMessage();
      }
    }
  }

  private function make_people_api_call( $method, $data ){

    $url = 'https://agrilifepeople.tamu.edu/api/';

    switch ($method){
      
      case "units" :
        $data = array_merge( array(
          'limit_to_active' =>  0,
          'entity_id' => null,
          'parent_unit_id' => null,
          'search_string' => null,
          'limited_units' => null,
          'exclude_units' => null,
        ), $data );
        break;
        
      case "people" :
        $data = array_merge( array(
          'person_active_status' => null,
          'restrict_to_public_only' => 1,
          'search_specializations' => null,
          'limited_units' => null,
          'limited_entity' => null,
          'limited_personnel' => null,
          'limited_roles' => null,
          'include_directory_profile' => 0,
          'include_specializations' => 1,
        ), $data );
        break;
        
      default: 
        exit("$function is not defined in the switch statement");
    }

    $url .= $method;

    if (!empty($data))
      $url = sprintf("%s?%s", $url, http_build_query($data));
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
      $info = curl_getinfo($curl);
      curl_close($curl);
      
      echo "<pre>Error occurred during curl exec.<br/>Additional info:<br/>";
      echo "Curl Response:<br/>";
      print_r($curl_response);
      echo "Info:<br/>";
      print_r($info);
      die('</pre>');
    }
    
    $response = array(
      'url' => $url,
      'json' => json_decode($curl_response, true),
      'raw' => $curl_response,
    );
    
    curl_close($curl);
    
    return $response;
  }
}