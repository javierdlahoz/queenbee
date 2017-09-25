<?php

class ConvertKitApiCupg {

    /**
     * @var string URL to use for all requests.
     */
    protected $apiUrl = 'https://api.convertkit.com/';

    /**
     * @var string The API key for your account.
     */
    protected $apiKey;

    /**
     * @var int The API version you wish to use
     */
    protected $version;

    /**
     * @var resource The cURL resource
     */
    protected $ch;

    /**
     * @param string $apiKey The API key for your account.
     * @param int $version The API version you wish to use.
     */
    public function __construct($apiKey, $version = 2) {
        
            $this->apiKey = $apiKey;
            $this->version = $version;
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_USERAGENT, 'ConvertKit-PHP');
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($this->ch, CURLOPT_TIMEOUT, 600);
        
    }

    /**
     * @param array $arguments
     * @return string
     */
    protected function prepareQueryString(array $arguments = array()) {
        
            $arguments['k'] = $this->apiKey;
            $arguments['v'] = $this->version;
            return http_build_query($arguments);
        
    }

    /**
     * @param string $apiEndpoint
     * @param array $arguments
     * @param string $queryType
     * @return array Decoded JSON || Exception
     */
    protected function queryApi($apiEndpoint, array $arguments = array(), $queryType = 'GET') {
        
            $fullUrl = $this->apiUrl . $apiEndpoint;
            $queryString = $this->prepareQueryString($arguments);
            if ($queryType == 'GET') {
                $fullUrl .= '?' . $queryString;
            }
            if ($queryType == 'POST') {
                curl_setopt($this->ch, CURLOPT_POST, true);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $queryString);
            }
            curl_setopt($this->ch, CURLOPT_URL, $fullUrl);
            $responseBody = curl_exec($this->ch);
            try {
                return json_decode($responseBody, true);
            }
            catch (Exception $e) {
                return $e;
            }
    }
    

    /**
     * @return array Decoded JSON response || Exception
     */
    public function getCourses() {
            return $this->queryApi('courses');
    }
    
    /**
     * @param integer $courseId
     * @param string $email
     * @param string $firstName
     * @return array Decoded JSON response || Exception
     */
    public function subscribeToACourse($courseId, $email, $firstName = null) {
        
            $apiEndpoint = 'courses/' . $courseId . '/subscribe';
            return $this->queryApi($apiEndpoint, array(
                        'email' => $email,
                        'fname' => $firstName),
                        'POST'
            );
            
    }

    /**
     * @param integer $formId
     * @param string $email
     * @param string $firstName
     * @param string $courseOptedIn
     * @return array Decoded JSON response || Exception
     */
    public function subscribeToAForm($formId, $email, $firstName = null, $courseOptedIn = 'true') {
        
            // We want to use a string, not a boolean in the query.
            if (is_bool($courseOptedIn)) {
                $courseOptedIn = ($courseOptedIn == false) ? 'false' : 'true';
            }
            $apiEndpoint = 'forms/' . $formId . '/subscribe';
            return $this->queryApi(
                            $apiEndpoint, array(
                        'email' => $email,
                        'fname' => $firstName,
                        'course_opted' => $courseOptedIn
                            ), 'POST'
            );
            
    }

    /**
     * @param integer $formId
     * @return array Decoded JSON response || Exception
     */
    public function getFormDetails($formId) {
        
            $apiEndpoint = 'forms/' . $formId;
            return $this->queryApi($apiEndpoint);
        
    }

    /**
     * Closes the cURL connection.
     */
    public function __destruct() {
        
            if (is_resource($this->ch)) {
                curl_close($this->ch);
            }
            
    }

}