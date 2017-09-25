<?php
/**
 * AweberApp - custom adapter between plugin and AweberAPI
 */
class AweberApp {

    private function __construct($account) {
            $this->account = $account;
    }
    
    /**
     * Class factory function
     * 
     * @param array $access_data API access keys
     * @return \AweberApp | string
     */
    public static function getAweberApp($access_data) {
        
            require_once ('API/aweber_api.php');
            list($consumerKey, $consumerSecret, $accessToken, $accessSecret) = $access_data;
            try {
                $api = new AWeberAPI($consumerKey, $consumerSecret);
                $account = $api->getAccount($accessToken, $accessSecret);
                return new AweberApp($account);
            }
            catch(Exception $ex) {
                return $ex->getMessage();
            }
        
    }

    /**
     * Get lists from API
     * 
     * @return mixed
     */
    public function getLists() {
        
            try {
                if ($this->account->lists->data['total_size'] > 0) {
                    return $this->account->lists->data['entries'];
                } 
                else {
                    return array();
                }
            }
            catch (Exception $ex) {
                return $ex->getMessage();
            }
            
    }

    /**
     * Add subscriber through API 
     * 
     * @param type $email Subscriber email
     * @param string $name Subscriber name
     * @param type $list_id Aweber list id
     * @param type $hidden_field Plugin hidden field value
     * @param type $hidden_field_name Plugin hidden field name
     * @return string Subscription status or error
     */
    public function addSubscriber($email, $name, $list_id, $hidden_field, $hidden_field_name) {
        
            try {
                $lists = $this->account->lists->find(array('name' => $list_id));

                $check = $lists[0]->subscribers->find(array('email' => $email, 'status' => 'subscribed'));
                if ($check->data['total_size'] > 0) {
                    return 'subscribed';
                }

                $params = array('email' => $email, 'name' => $name);

                if (strlen($hidden_field) > 0) {
                    try {
                        $lists[0]->custom_fields->create(array('name' => $hidden_field_name));
                    }
                    catch (AWeberAPIException $ex) {}
                    $params['custom_fields'] = array($hidden_field_name => $hidden_field);
                }
                
                $lists[0]->subscribers->create($params);
                return 'created';
            }
            catch (Exception $ex) {
                return $ex->getMessage();
            }
            
    }
    
    
}