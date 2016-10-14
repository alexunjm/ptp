<?php

  namespace Comiguel\Ptp;
  use SoapClient;

  class Ptp {
    private $service;
    private $authentication;

    public function __construct($service, $login, $trankey, $additional = []) {
      $this->service = $service;
      $this->authentication = new Authentication($login, $trankey, $additional);
    }

    /*
     Este método permite obtener el listado de bancos diponibles.
     * En caso de éxito regresa un array de la forma ["success" => true, "data" => $banksList[]]
     * En caso de error regresa un array con la clave success en false ["success" => false]
    */
    public function getBankList() {
      $auth = $this->authentication->toArray();
      $client = new SoapClient($this->service);
      $response = $client->getBankList(["auth" => $auth]);
      if ($response) {
        $result = $this->obj2array($response);
        return ["success" => true, "data" => $result["getBankListResult"]["item"]];
      } else {
        return ["success" => false];
      }
    }
    
    public function getAuthentication() {
      return $this->authentication;
    }

    public function obj2array($obj) {
      $out = array();
      foreach ($obj as $key => $val) {
        switch(true) {
            case is_object($val):
             $out[$key] = $this->obj2array($val);
             break;
          case is_array($val):
             $out[$key] = $this->obj2array($val);
             break;
          default:
            $out[$key] = $val;
        }
      }
      return $out;
    }
  }

?>
