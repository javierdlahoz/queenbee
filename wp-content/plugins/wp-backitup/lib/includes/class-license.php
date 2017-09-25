<?php if (!defined ('ABSPATH')) die('No direct access allowed');


/**
 * WPBackItUp License Class
 *
 * @link       http://www.wpbackitup.com
 * @since      1.14.0
 *
 * @package    WPBackItUp
 *
 */


class WPBackItUp_License {
	
	private static $default_log = 'debug_activation';
	private $log_name;

	function __construct() {

		try {

			$this->log_name = self::$default_log; //default log name
			

		} catch ( Exception $e ) {
			WPBackItUp_LoggerV2::log_error( $this->log_name, __METHOD__, 'Constructor Exception: ' . $e );
			throw $e;
		}

	}

	/**
	 * Activate WPBackItUp License
	 *
	 * @param $license
	 *
	 * @return bool|mixed
	 */
	public function activate_license($license){

		$request_data = array(
			'license' 		=> $license,
			'item_name' 	=> urlencode( WPBACKITUP__ITEM_NAME ),
			'url'           => home_url()
		);

		$license_data =  $this->edd_license_api_request(WPBACKITUP__API_URL, 'activate_license', $request_data);

		//if false try using site directly
		if ( false === $license_data) {
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__, 'Unable to activate using Gateway  - attemtping direct');
			$license_data= $this->edd_license_api_request(WPBACKITUP__SECURESITE_URL,'activate_license', $request_data);
		}

		return $license_data;

	}

	/**
	 * Check WPBackItUp License
	 *
	 * @param $license
	 *
	 * @return bool|mixed
	 */
	public function check_license($license){

		$request_data = array(
			'license' 		=> $license,
			'item_name' 	=> urlencode( WPBACKITUP__ITEM_NAME ),
			'url'           => home_url()
		);

		$license_data =  $this->edd_license_api_request(WPBACKITUP__API_URL, 'check_license', $request_data);

		//if false try using site directly
		if ( false === $license_data) {
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__, 'Unable to activate using Gateway  - attemtping direct');
			$license_data= $this->edd_license_api_request(WPBACKITUP__SECURESITE_URL,'activate_license', $request_data);
		}

		return $license_data;

	}

	/**
	 * Deactivate WPBackItUp License for site identified in home_url value
	 *
	 * @param $license
	 *
	 * @return bool|mixed
	 */
	public function deactivate_license($license){

		$request_data = array(
			'license' 		=> $license,
			'item_name' 	=> urlencode( WPBACKITUP__ITEM_NAME ),
			'url'           => home_url()
		);

		$license_data =  $this->edd_license_api_request(WPBACKITUP__API_URL, 'deactivate_license', $request_data);

		//if false try using site directly
		if ( false === $license_data) {
			WPBackItUp_LoggerV2::log_error($this->log_name,__METHOD__, 'Unable to activate using Gateway  - attemtping direct');
			$license_data= $this->edd_license_api_request(WPBACKITUP__SECURESITE_URL,'activate_license', $request_data);
		}

		return $license_data;

	}


	/**
	 * Calls the API and, if successful, returns the object delivered by the API.
	 * http://docs.easydigitaldownloads.com/article/384-software-licensing-api
	 *  Action Types Supported:
	 *
	 *  activate_license - Used to remotely activate a license key
	 *  deactivate_license - Used to remotely deactivate a license key
	 *  check_license - Used to remotely check if a license key is activated, valid, and not expired
	 *  get_version - Used to remotely retrieve the latest version information for a product
	 *
	 *
	 */
	private function edd_license_api_request( $activation_url,$action, $request_data ) {

		$api_params = array(
			'edd_action' 	=> $action,
			'license' 		=> $request_data['license'],
			'item_name' 	=> $request_data['item_name'],
			'url'           => $request_data['url']
		);

		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__, 'Activate License Request Info:');
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__,'API URL:' .$activation_url);
		WPBackItUp_LoggerV2::log($this->log_name,$api_params);

		$response = wp_remote_get(
			add_query_arg( $api_params, $activation_url ),
			array( 'timeout' => 25,
			       'sslverify' => false
			)
		);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__, 'API Response:'. var_export( $response,true ));


		$response_code = wp_remote_retrieve_response_code($response);
		WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__, 'Response Code:'. $response_code);

		//IF no error
		if ( !is_wp_error( $response )  &&  200 == $response_code  ) {
			$response = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $response  && property_exists($response,'sections')) {
				$response->sections = maybe_unserialize( $response->sections );
			}

			return $response;

		} else { //Error
			WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__, 'Requesting Server Name:'. $_SERVER['SERVER_NAME']);
			WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__, 'Requesting IP:'. $_SERVER['SERVER_ADDR']);

			WPBackItUp_LoggerV2::log_info($this->log_name,__METHOD__, 'Validation Response:');
			WPBackItUp_LoggerV2::log($this->log_name,var_export($response,true));

			return false;
		}
	}
}