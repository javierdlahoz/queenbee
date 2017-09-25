<?php
/**
 * Custom adapter for OntraPort API 
*/
class OntraPort_API {
    
        /**
         * Access credentials and request url
         */
        private $key;
        private $appid;
        private $url;


        public function __construct($key, $appid) {
                $this->key = $key;
                $this->appid = $appid;
                $this->url = "https://api.ontraport.com/cdata.php";
        }

        /**
         * Get lists from API
         * 
         *  @return mixed API response
         */
        public function getLists() {

            $reqType = "fetch_sequences";
            $postargs = "appid=" . $this->appid . "&key=" . $this->key . "&reqType=" . $reqType;
            
            return $this->execute_request($postargs);
        }

        /**
         * Add contact through API
         * 
         * @param string $email Email to subscribe
         * @param string $name Subscriber name
         * @param int $list_id List id
         * @return boolean True if contact added or already exists
         */
        public function addContact($email, $name, $list_id) {

            $data = '
            <contact>
                <Group_Tag name="Contact Information">
                    <field name="First Name">' . $name . '</field>
                    <field name="Email">' . $email . '</field>
                </Group_Tag>
                <Group_Tag name="Sequences and Tags">
                    <field name="Sequences">*/*' . $list_id . '*/*</field>
                </Group_Tag>
            </contact>';

            $reqType = "add";
            $postargs = "appid=" . $this->appid . "&key=" . $this->key . "&reqType=" . $reqType . "&data=" . urlencode($data);
            $response = $this->execute_request($postargs);
            if($response == 'Success') {
                return true;
            }
            
            return false;
        }
        
        /**
         * Make request to API
         * 
         * @param string $postargs url encoded request params
         * @return string | boolean
         */
        private function execute_request($postargs) {
            
                $session = curl_init($this->url);
                curl_setopt($session, CURLOPT_POST, true);
                curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
                curl_setopt($session, CURLOPT_HEADER, false);
                curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($session);
                curl_close($session);
                if (strstr($response, '<result>')) {
                    return simplexml_load_string($response);
                }
                return false;
            
        }
        
        
}