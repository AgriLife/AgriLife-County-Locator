<?php

namespace AgriLife\OfficeLocator;

class Ajax {

  public function __construct() {
    
    $applicationID = 3;
    $method = 'getUnits';
    $location = "https://agrilifepeople.tamu.edu/api/v4.cfc?wsdl";
    $transient = get_transient('county_office_locator');

    function associateAPI($apiResults) {

      if (is_object($apiResults)){
        $aColumnList = $apiResults->columnList;
        $aData = $apiResults->data;
          
      } else if (is_array($apiResults)) {
        $aColumnList = $apiResults['columnList'];
        $aData = $apiResults['data'];
          
      } else {
        return false;
      }
    
      $aReturn = array();
      $currentRow = 0;
      foreach ($aData as &$row){
        $aRow = array();
    
        for ($counter = 0; $counter < count($aColumnList); $counter++){
    
          if (is_array($row[$counter]) || is_object($row[$counter])){
            $aRow[$aColumnList[$counter]] = ($row[$counter]);
          } else {
            $aRow[$aColumnList[$counter]] = $row[$counter];
          }
        }
    
        $aReturn[$currentRow] = $aRow;
        $currentRow++;
      }
    
      return $aReturn;
    }

    if (!$transient){

      $client = new \SoapClient("https://agrilifepeople.tamu.edu/api/v4.cfc?wsdl");
      
      $arguments = array(
        'SiteID' => $applicationID,
        'ValidationKey' => base64_encode( md5( $applicationID . AGRILIFE_API_KEY . $method, true ) ),
        'UnitIDs' => null,
        'EntityIDs' => 2,
        'ParentUnitIDs' => null,
        'CountyIDs' => null,
        'DistrictIDs' => null,
        'ActiveOnly' => true
      );
      
      try {
        $results = $client->__call($method,$arguments);

        if ($results['ResultCode'] == 200){
          $dataObj = $results['ResultQuery']->enc_value;
          
          // Loop through each entry and remove if its [unitname] does not include the word "County Office".
          $aResults = associateAPI($dataObj);

          $filtered = array_filter( $aResults, function( $item ) {
            if ( strpos( $item['unitname'], 'County Office' ) === false) {
              return false;
            }

            return true;
          } );

          foreach($filtered as $key => $value){
            // Change phone number to desired format.
            $filtered[$key]['unitphonenumber'] = str_replace( '.', '-', $value['unitphonenumber'] );
          }

          // Add filtered array to database as transient
          set_transient( 'county_office_locator', $filtered, 4*WEEK_IN_SECONDS );
        }
      }
      catch (\Exception $e) {     
        // echo $e->getMessage();
      }
    }
  }
}