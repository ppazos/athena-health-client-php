<?php

// globals
$token = login();
$practiceid = 195900;
$departmentid = 1;
$patientid = 2455; // GET  /:practiceid/patients
$providerid = 72; // GET  /:practiceid/providers
$appointmenttypeid = 2;

// known ids for testing
$encounterid = 34960; //34956;
$physicalexam_templateid = 307; // /:practiceid/chart/encounter/:encounterid/physicalexam/templates
/**
 * Get token.
 */
function login()
{
  $url = 'https://api.athenahealth.com/oauthpreview/token';

  // Equivalent curl command:
  // curl 'https://api.athenahealth.com/oauthpreview/token' -X POST -d 'grant_type=client_credentials' -u 4mgzdee2hr35cs5e29hefas7:x3JVnKXguhS22Fu -H 'Content-Type: application/x-www-form-urlencoded'

  // Given after registering as dev at Athena Health
  $auth_un = 'YOUR_USERNAME';
  $auth_pw = 'YOUR_PASSWORD';

  $post_params = array('grant_type'=>'client_credentials');

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_params));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($curl, CURLOPT_USERPWD, "$auth_un:$auth_pw");

  // If there is a problem with the credentials, will return:
  // {"error":"invalid_client"}
  $response = curl_exec($curl);

  // error in the connection, returns string, emty if no error
  $err = curl_error($curl);

  curl_close($curl);

  $token = null;
  if (!$err)
  {
    $json = json_decode($response);

    if (property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your parameters';
    }
    else
    {
      $token = $json->access_token;
    }
  }
  else
  {
    echo 'There was an error contacting the server, verify your internet connection, error: '. $err;
  }

  return $token;
}

function create_patient($data)
{
  global $token;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/195900/patients",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => http_build_query($data), //"firstname=Pablo&lastname=pazos&dob=10%2F24%2F1981&departmentid=1&email=pablo.pazos%40cabolabs.com",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token",
      "content-type: application/x-www-form-urlencoded"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $patientid = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    echo $response; // [{"patientid":"29897"}]
    $json = json_decode($response);
    if (!is_array($json) && property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $patientid = $json[0]->patientid;
    }
  }

  return $patientid;
}

function list_patients($firstname_search)
{
  global $token;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/195900/patients?firstname=$firstname_search",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $patients = array();
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    echo $response;
    $json = json_decode($response);
    if (property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $patients = $json->patients;
      /*
      {"patients":[{"racename":"Alaskan Athabascan","departmentid":"150","homephone":"5555312838","portalaccessgiven":true,"driverslicense":false,"lastemail":"demo@athenahealth.com","zip":"55404","guarantoraddresssameaspatient":true,"portaltermsonfile":true,"status":"prospective","lastname":"Huitt","city":"MINNEAPOLIS","ssn":"*****7829","sex":"M","privacyinformationverified":false,"primarydepartmentid":"150","balances":[{"balance":0,"departmentlist":"1,21,102,145,148,150,157,162","providergroupid":1,"cleanbalance":true}],"race":["1739-2"],"language6392code":"ara","primaryproviderid":"71","patientphoto":false,"registrationdate":"04\/23\/2014","caresummarydeliverypreference":"PORTAL","firstname":"Pablo","state":"MN","patientid":"2455","dob":"05\/18\/1949","address1":"8762 Stoneridge Ct","maritalstatus":"S","countrycode":"USA","maritalstatusname":"SINGLE","consenttotext":false,"countrycode3166":"US"},{"email":"hansen.francesca@mills.org","departmentid":"157","guarantorstate":"IL","driverslicense":false,"guarantorssn":"*****0613","guarantordob":"09\/13\/1934","zip":"20502","guarantoraddresssameaspatient":false,"portaltermsonfile":false,"status":"active","lastname":"Hickle","guarantorfirstname":"Meredith","city":"HEGMANNMOUTH","ssn":"*****4082","guarantoremail":"weston72@wyman.com","guarantorcity":"NEW MARLENE","guarantorzip":"99839","privacyinformationverified":false,"primarydepartmentid":"157","balances":[{"balance":0,"departmentlist":"1,21,102,145,148,150,157,162","providergroupid":1,"cleanbalance":true}],"emailexists":true,"race":[],"primaryproviderid":"","patientphoto":false,"registrationdate":"10\/20\/2015","guarantorlastname":"Kassulke","firstname":"Pablo","guarantorcountrycode":"USA","state":"MD","patientid":"5769","dob":"10\/28\/1970","guarantorrelationshiptopatient":"13","address1":"11595 Kihn Mall Suite 190","guarantorphone":"2606454782","countrycode":"USA","guarantoraddress1":"50134 Yvonne Mountain","consenttotext":false,"countrycode3166":"US","guarantorcountrycode3166":"US"},{"email":"pablo.pazos@cabolabs.com","departmentid":"1","driverslicense":false,"zip":"55102","guarantoraddresssameaspatient":false,"portaltermsonfile":false,"status":"active","lastname":"Pazos","city":"MINNEAPOLIS","privacyinformationverified":false,"primarydepartmentid":"1","balances":[{"balance":0,"departmentlist":"1,21,102,145,148,150,157,162","providergroupid":1,"cleanbalance":true}],"emailexists":true,"race":[],"primaryproviderid":"","patientphoto":false,"registrationdate":"06\/02\/2017","firstname":"Pablo","state":"MN","patientid":"29708","dob":"10\/24\/1981","guarantorrelationshiptopatient":"1","countrycode":"USA","consenttotext":false,"countrycode3166":"US"},{"email":"a@b.com","departmentid":"1","driverslicense":false,"guarantoraddresssameaspatient":false,"portaltermsonfile":false,"status":"active","lastname":"Pazos","privacyinformationverified":false,"primarydepartmentid":"1","balances":[{"balance":0,"departmentlist":"1,21,102,145,148,150,157,162","providergroupid":1,"cleanbalance":true}],"emailexists":true,"race":[],"primaryproviderid":"","patientphoto":false,"registrationdate":"06\/21\/2017","firstname":"Pablo","patientid":"29875","dob":"10\/24\/1981","guarantorrelationshiptopatient":"1","countrycode":"USA","consenttotext":false,"countrycode3166":"US"}],"totalcount":4}
      */
    }
  }

  return $patients;
}

function get_patient($patientid)
{
  global $token;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/195900/patients/$patientid",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $patient = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    echo $response;
    /*
      [{"email":"milton@vsee.com","departmentid":"1","portalaccessgiven":false,"driverslicense":false,"guarantoraddresssameaspatient":false,"portaltermsonfile":false,"status":"active","lastname":"Chen","privacyinformationverified":false,"primarydepartmentid":"1","balances":[{"balance":0,"departmentlist":"1,21,102,145,148,150,157,162","providergroupid":1,"cleanbalance":true}],"emailexists":true,"patientphoto":false,"registrationdate":"06\/23\/2017","firstname":"Milton","guarantorcountrycode":"USA","patientid":"29897","dob":"06\/25\/1978","guarantorrelationshiptopatient":"1","countrycode":"USA","consenttotext":false,"countrycode3166":"US","guarantorcountrycode3166":"US"}]
    */

    $json = json_decode($response);
    if (!is_array($json) && property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $patient = $json[0];
    }
  }

  return $patient;
}

function create_open_appointments($data = array())
{
  global $token, $practiceid, $departmentid;

  // GET https://api.athenahealth.com/preview1/195900/appointmenttypes
  $appointment_type_office_visit  = 2; // duration = 10
  $appointment_type_new_patient   = 8; // duration = 30
  $appointment_type_physical_exam = 4; // duration = 30

  $data['departmentid'] = $departmentid;
  $data['appointmenttypeid'] = $appointment_type_office_visit;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/appointments/open",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => http_build_query($data), //"departmentid=1&appointmentdate=07%2F15%2F2017&appointmenttime=15%3A00&providerid=104&appointmenttypeid=2",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token",
      "content-type: application/x-www-form-urlencoded"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $appointment_ids = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    {
      "appointmentids": {
        "860298": "09:30",
        "860300": "10:30",
        "860299": "10:00"
      }
    }
    */
    $json = json_decode($response);
    if (property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $appointment_ids = $json->appointmentids;
    }
  }
  return $appointment_ids;
}

function schedule_open_appointment($appointmentid, $data = array())
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/appointments/$appointmentid",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token",
      "content-type: application/x-www-form-urlencoded"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $appointment = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    [
        {
            "date": "07\/15\/2017",
            "appointmentid": "860299",
            "starttime": "10:00",
            "departmentid": "1",
            "appointmentstatus": "f",
            "patientid": "2455",
            "duration": 10,
            "appointmenttypeid": "2",
            "appointmenttype": "Office Visit",
            "providerid": "21",
            "chargeentrynotrequired": false,
            "patientappointmenttypename": "Office Visit"
        }
    ]
    */
    $json = json_decode($response);

    if (!is_array($json) && property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $appointment = $json[0];
    }
  }
  return $appointment;
}

/**
 * Returns the bookingnote used on the schedule_open_appointment
 */
function appointment_get_notes($appointmentid)
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/appointments/$appointmentid/notes",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $notes = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    {
    	"notes": [
    		{
    			"notetext": "API (CaboLabs - ppazos - cloudehrserver (MDP Partner)) appointment created.  Reported reason for booking: constant headache",
    			"noteid": "55268",
    			"created": "07\/12\/2017 15:13:58",
    			"createdby": "API-6232"
    		}
    	],
    	"totalcount": 1
    }
    */
    $json = json_decode($response);

    if (property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $notes = $json;
    }
  }
  return $notes;
}

function appointment_start_checkin($appointmentid, $data = array())
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/appointments/$appointmentid/startcheckin",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token",
      "content-type: application/x-www-form-urlencoded"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $result = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    {
      "success": true
    }
    */
    $json = json_decode($response);
    if (property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $result = $json;
    }
  }
  return $result;
}

function appointment_complete_checkin($appointmentid, $data = array())
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/appointments/$appointmentid/checkin",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token",
      "content-type: application/x-www-form-urlencoded"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $result = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    {
      "success": true
    }
    */
    $json = json_decode($response);
    if (property_exists($json, 'error'))
    {
      // {"error":"Check-in requirements not met for Insurance."}
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $result = $json;
    }
  }
  return $result;
}

function appointment_get($appointmentid)
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/appointments/$appointmentid",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $appointment = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    [{
    	"date": "07\/17\/2017",
    	"appointmentid": "860331",
    	"patientlocationid": "21",
    	"starttime": "10:00",
    	"departmentid": "1",
    	"appointmentstatus": "2",
    	"patientid": "2455",
    	"duration": 10,
    	"encounterid": "34958",
    	"startcheckin": "07\/12\/2017 18:12:06",
    	"copay": 0,
    	"renderingproviderid": 72,
    	"appointmenttypeid": "2",
    	"appointmenttype": "Office Visit",
    	"encounterstatus": "READYFORSTAFF",
    	"providerid": "72",
    	"stopcheckin": "07\/12\/2017 18:12:18",
    	"chargeentrynotrequired": false,
    	"patientappointmenttypename": "Office Visit"
    }]
    */
    $json = json_decode($response);
    if (!is_array($json) && property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $appointment = $json[0];
    }
  }
  return $appointment;
}

function encounter_get($encounterid)
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/chart/encounter/$encounterid",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $encounter = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    //echo $response;
    /*
    [{
    	"encountertype": "VISIT",
    	"patientstatusid": 1,
    	"stage": "INTAKE",
    	"status": "OPEN",
    	"appointmentid": 860338,
    	"patientlocationid": 21,
    	"diagnoses": [], // without diagnoses
    	"providerid": 72,
    	"encounterdate": "07\/15\/2017",
    	"encountervisitname": "Office Visit",
    	"patientlocation": "Patient Rm. 1",
    	"providerlastname": "Avallone",
    	"encounterid": 34960,
    	"lastupdated": "07\/12\/2017",
    	"providerfirstname": "Shayna",
    	"providerphone": "(555) 916-7897",
    	"patientstatus": "Ready For Staff"
    }]
    */
    $json = json_decode($response);

    if (!is_array($json) && property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $encounter = $json[0];
    }
  }
  return $encounter;
}

function encounter_add_diagnoses($encounterid, $data = array())
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  $sdata = http_build_query($data); // snomedcode=46113002&icd10codes%5B0%5D=I11.0&icd10codes%5B1%5D=I50.9
  $sdata = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $sdata); // icd10codes=I11.0&icd10codes=I50.9&snomedcode=46113002

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/chart/encounter/$encounterid/diagnoses",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $sdata,
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token",
      "content-type: application/x-www-form-urlencoded"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $result = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    {
    	"success": true
    }
    */
    $json = json_decode($response);

    if (property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $result = $json;
    }
  }
  return $result;
}

function encounter_add_vitals($encounterid, $data = array())
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  // https://developer.athenahealth.com/docs/read/encounter/Vitals_Overview
  $vitals = json_encode($data);
  $sdata = http_build_query(array('vitals'=>$vitals));

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/chart/encounter/$encounterid/vitals",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $sdata,
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token",
      "content-type: application/x-www-form-urlencoded"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $result = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    {
    	"success": true
    }
    */
    $json = json_decode($response);

    if (property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $result = $json;
    }
  }
  return $result;
}

function encounter_get_vitals($encounterid)
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/chart/encounter/$encounterid/vitals",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $vitals = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    [
    	{
    		"ordering": 0,
    		"abbreviation": "BP",
    		"readings": [
    			[
    				{
    					"source": "ENCOUNTER",
    					"value": "70",
    					"readingid": 1,
    					"clinicalelementid": "VITALS.BLOODPRESSURE.DIASTOLIC",
    					"codedescription": "Diastolic blood pressure",
    					"sourceid": 34960,
    					"readingtaken": "07\/15\/2017",
    					"codeset": "LOINC",
    					"vitalid": 27562,
    					"code": "8462-4"
    				},
    				{
    					"source": "ENCOUNTER",
    					"value": "120",
    					"readingid": 1,
    					"clinicalelementid": "VITALS.BLOODPRESSURE.SYSTOLIC",
    					"codedescription": "Systolic blood pressure",
    					"sourceid": 34960,
    					"readingtaken": "07\/15\/2017",
    					"codeset": "LOINC",
    					"vitalid": 27560,
    					"code": "8480-6"
    				}
    			]
    		],
    		"key": "BLOODPRESSURE"
    	},
    	{
    		"ordering": 8,
    		"abbreviation": "T",
    		"readings": [
    			[
    				{
    					"source": "ENCOUNTER",
    					"value": "98.6",
    					"readingid": 0,
    					"clinicalelementid": "VITALS.TEMPERATURE",
    					"codedescription": "Body temperature",
    					"sourceid": 34960,
    					"readingtaken": "07\/15\/2017",
    					"codeset": "LOINC",
    					"vitalid": 27564,
    					"code": "8310-5"
    				}
    			]
    		],
    		"key": "TEMPERATURE"
    	}
    ]
    */
    $json = json_decode($response);

    if (!is_array($json) && property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $vitals = $json;
    }
  }
  return $vitals;
}

function encounters_get_all($patientid, $data = array())
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/chart/$patientid/encounters?departmentid=$departmentid&showallstatuses=true",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 60, // needs time!
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $encounters = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    {
    	"encounters": [{
    		"encountertype": "VISIT",
    		"patientstatusid": 1,
    		"stage": "INTAKE",
    		"status": "OPEN",
    		"appointmentid": 860285,
    		"patientlocationid": 21,
    		"providerid": 72,
    		"encounterdate": "07\/20\/2017",
    		"encountervisitname": "New Patient Appointment",
    		"patientlocation": "Patient Rm. 1",
    		"providerlastname": "Avallone",
    		"encounterid": 34956,
    		"lastupdated": "07\/11\/2017",
    		"providerfirstname": "Shayna",
    		"providerphone": "(555) 916-7897",
    		"patientstatus": "Ready For Staff"
    	},
      ...
      ],
    	"totalcount": 12
    }
    */
    $json = json_decode($response);

    if (property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $encounters = $json;
    }
  }
  return $encounters;
}

function encounter_add_physical_exam($encounterid, $data = array())
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  $physicalexam = json_encode($data['physicalexam']);
  $templateids  = json_encode($data['templateids']);
  $sdata = http_build_query(array('physicalexam'=>$physicalexam, 'templateids'=>$templateids));

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/chart/encounter/$encounterid/physicalexam",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_POSTFIELDS => $sdata,
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token",
      "content-type: application/x-www-form-urlencoded"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $result = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    {
    	"success": true
    }
    */
    $json = json_decode($response);

    if (property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $result = $json;
    }
  }
  return $result;
}

function encounter_get_physical_exam($encounterid)
{
  global $token, $practiceid, $departmentid;

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.athenahealth.com/preview1/$practiceid/chart/encounter/$encounterid/physicalexam?showstructured=true",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $physicalexam = null;
  if ($err)
  {
    echo "cURL Error #:" . $err;
  }
  else
  {
    /*
    {
    	"physicalexam": [
    		{
    			"paragraphname": "Constitutional",
    			"paragraphid": -3,
    			"sentences": [
    				{
    					"sentenceid": -1,
    					"sentencename": "General Appearance:",
    					"findings": [
    						{
    							"findingname": "healthy-appearing",
    							"freetext": "healthy appearing, looking good",
    							"findingid": -15,
    							"findingtype": "NORMAL",
    							"medcinid": 9308,
    							"findingnote": "my note here"
    						}
    					],
    					"sentencenote": "General appearance"
    				}
    			]
    		}
    	],
    	"templates": [
    		"General Adult Exam"
    	]
    }
    */
    $json = json_decode($response);

    if (property_exists($json, 'error'))
    {
      echo 'There was an error in '.__FUNCTION__.', verify your token and parameters';
    }
    else
    {
      $physicalexam = $json;
    }
  }
  return $physicalexam;
}


/*

echo $token;
echo "\n\n";

// ===================================================================================================
// Patients
// ===================================================================================================

$patientid = create_patient(array(
  'firstname' => 'Milton',
  'lastname' => 'Chen',
  'dob' => '06/25/1978',
  'departmentid' => $departmentid,
  'email' => 'milton@vsee.com'
));
echo $patientid;
echo "\n\n";

$patients = list_patients('Milton');
foreach ($patients as $patient)
{
  echo $patient->firstname ." ". $patient->lastname ." ". $patient->patientid ."\n";
}
echo "\n\n";

$patient = get_patient($patientid);
echo $patient->firstname ." ". $patient->lastname ." ". $patient->patientid ."\n";
echo "\n\n";

*/

// ===================================================================================================
// Appointments
// ===================================================================================================

$appointment_ids = create_open_appointments(array(
  'appointmentdate' => '07/18/2017',
  'appointmenttime' => '08:00,08:30,09:00',
  'providerid' => $providerid
));

$appointmentid = null;
echo "Created open appointments:\n\n";
foreach (get_object_vars($appointment_ids) as $_appointmendid => $_appointment_time) {
  if ($appointmentid == null) $appointmentid = $_appointmendid;
  echo "$_appointmendid : $_appointment_time\n";
}
echo "\n";

echo "Schedule the first open appointment: $appointmentid \n\n";
$reason_for_encounter = 'Constant headache';
$appointment = schedule_open_appointment($appointmentid, array(
  'patientid' => $patientid,
  'ignoreschedulablepermission' => true,
  'bookingnote' => $reason_for_encounter,
  'appointmenttypeid' => $appointmenttypeid
));
assert($appointment->appointmentstatus == 'f'); // f=scheduled, o=open


 // note[0] has the $reason_for_encounter / bookingnote
$notes = appointment_get_notes($appointmentid);
$saved_note = $notes->notes[0]->notetext;
assert( strripos($saved_note, $reason_for_encounter) !== false );
echo "Get reason for encounter: $saved_note \n\n";


// ===================================================================================================
// Encounter
// ===================================================================================================

echo "Scheduled appointment checkin creates the encounter\n\n";
// checkin, creates the encounter
$result = appointment_start_checkin($appointment->appointmentid);
assert($result->success);

$result = appointment_complete_checkin($appointment->appointmentid);
assert($result->success);

echo "Get appointment (it has the encounterid) \n\n";
$appointment = appointment_get($appointment->appointmentid);
print_r($appointment);

$encounterid = $appointment->encounterid;
assert($encounterid != null);

echo "Checking encounter status is OPEN so we can add clinical data\n\n";
$encounter = encounter_get($encounterid);
assert($encounter->status == 'OPEN'); // if it is PEND, won't accept inserting data into the encounter



// ===================================================================================================
// Encounter Diagnoses
// ===================================================================================================

echo "Adding diagnose to encounter: $encounterid \n\n";
$result = encounter_add_diagnoses($encounterid, array(
  'snomedcode' => 46113002,
  'icd10codes' => array('I11.0', 'I50.9')
));
assert($result->success);


echo "Get saved diagnose from encounter, SNOMED CT code should be 46113002 \n\n";
$encounter = encounter_get($encounterid); // get the same encounter to see the diagnoses there
assert($encounter->diagnoses[0]->snomedcode == 46113002);
//print_r($encounter);
/*
{
	"encountertype": "VISIT",
	"patientstatusid": 1,
	"stage": "INTAKE",
	"status": "OPEN",
	"appointmentid": 860338,
	"patientlocationid": 21,
	"diagnoses": [{
		"diagnosisid": 14076,
		"icdcodes": [{
			"codeset": "ICD10",
			"description": "Hypertensive heart disease with heart failure",
			"code": "I11.0"
		}, {
			"codeset": "ICD10",
			"description": "Heart failure, unspecified",
			"code": "I50.9"
		}],
		"snomedcode": 46113002,
		"description": "Hypertensive heart failure"
	}],
	"providerid": 72,
	"encounterdate": "07\/15\/2017",
	"encountervisitname": "Office Visit",
	"patientlocation": "Patient Rm. 1",
	"providerlastname": "Avallone",
	"encounterid": 34960,
	"lastupdated": "07\/12\/2017",
	"providerfirstname": "Shayna",
	"providerphone": "(555) 916-7897",
	"patientstatus": "Ready For Staff"
}
*/


// https://developer.athenahealth.com/docs/read/encounter/Vitals_Overview

// ===================================================================================================
// Encounter Vitals
// ===================================================================================================
echo "Adding vitals to the encounter: $encounterid \n\n";
$result = encounter_add_vitals($encounterid, array(
    array(
        array(
            'clinicalelementid'=>'VITALS.BLOODPRESSURE.SYSTOLIC',
            'value'=>'160'
        ),
        array(
            'clinicalelementid'=>'VITALS.BLOODPRESSURE.DIASTOLIC',
            'value'=>'120'
        )
    ),
    array(
        array(
            'clinicalelementid'=>'VITALS.TEMPERATURE',
            'value'=>'98.6'
        )
    )
));
assert($result->success);

echo "Get vitals types saved on the encounter\n\n";
$vitals = encounter_get_vitals($encounterid);
foreach ($vitals as $vital)
{
  echo "vital: ". $vital->key;
  echo "\n";
}
echo "\n";

/*
$encounters = encounters_get_all($patientid);
foreach ($encounters->encounters as $i => $encounter) {
  echo "app: ". $encounter->appointmentid .", enc: ". $encounter->encounterid;
  echo "\n\n";
}
*/


// ===================================================================================================
// Encounter Physical Exam
// ===================================================================================================

/* physicalexam, template structure for template 307, GET /:practiceid/chart/encounter/:encounterid/physicalexam
[{
   "paragraphid": -3,
   "sentences": [{
      "findings": [{
         "findingid":  -15,
         "findingnote": "my note here",
         "freetext": "healthy appearing, looking good"
      }],
      "sentenceid": -1,
      "sentencenote": "General appearance"
   }]
}]
*/

echo "Adding physical exam to encounter\n\n";
$result = encounter_add_physical_exam($encounterid, array(
  'templateids' => array($physicalexam_templateid),
  'physicalexam' => array(
    array(
      'paragraphid' => -3,
      'sentences' => array(
        array(
          'findings' => array(
             array(
              'findingid' => -15,
              'findingnote' => 'my note here',
              'freetext' => 'healthy appearing, looking good'
            )
          ),
          'sentenceid' => -1,
          'sentencenote' => 'General appearance note'
        )
      )
    )
  )
));
assert($result->success);

echo "Getting physical exam from encounter\n\n";
$physicalexam = encounter_get_physical_exam($encounterid);
echo $physicalexam->physicalexam[0]->sentences[0]->findings[0]->freetext;
echo "\n\n";
assert($physicalexam->physicalexam[0]->sentences[0]->findings[0]->freetext == 'healthy appearing, looking good');

?>
