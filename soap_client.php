<?php
$so = new AmwareSoapClient( 'http://www.daytonfreight.com/WebServices_v1.1/TrackingService.asmx?wsdl', 'diamondmetals', 'login123' );
$params = array( 
								'billOfLadingNumber' => 'AMWE-1000001424',
								'originZipCode' => '44135',		
								'destinationZipCode' => '44460'		
							);
$so->soapCall( 'TrackByBillOfLading', $params );
echo '<pre>';
print_r( $so->getSoapResponse() );
?>
<?php
/*
SOAP Client Class
Author: Alvin Mark Tuballas (atuballas@github.com)
Created: Jan. 1, 2013
Amware.com
*/
class AmwareSoapClient{
	
	private $wsdl;
	private $username;
	private $password;
	private $sc_object;
	
	public $soap_result = true;
	public $soap_fault_code;
	public $soap_fault_desc;
	public $soap_response;
	
	public function __construct( $wsdl, $username, $password ){
		$this->wsdl = $wsdl;
		$this->username = $username;
		$this->password = $password;
		$this->createSoapClient();
		$this->setSoapHeaders();
	}
	
	private function createSoapClient(){
		$this->sc_object = new SoapClient( $this->wsdl );
	}
	
	private function setSoapHeaders(){
		$credentials = array(
											'Username' => $this->username,
											'Password' => $this->password
										);
		$headers = new SoapHeader( 'http://daytonfreight.com/webservices', 'UserCredentials', $credentials );
		$this->sc_object->__setSoapHeaders( 
																		array( 
																					$headers 
																				) 
																	 );												 
	}
	
	private function convertObjectToArray( $object ){
		foreach( (array)$object as $k=>$v ){
			if( 'object' == $v ){
				$this->convertObjectToArray( $v );
			}else{
				$object[$k] = (array)$v;
			}
		}
		return $object;
	}
	
	public function soapCall( $remote_function, $parameters ){
		$parameters = array( $parameters );
		try{
			$this->soap_response = $this->sc_object->__call( 
																								$remote_function, 
																								$parameters 
																						  );
		}catch( SoapFault $fault ){
			$this->soap_result = false;
			$this->soap_fault_code = $fault->faultcode;
			$this->soap_fault_desc = $fault->faultstring;
		}
	}
	
	public function getSoapResponse(){
		return $this->convertObjectToArray( $this->soap_response );
	}
}
?>