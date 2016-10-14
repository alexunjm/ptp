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

    /*
     Crea una petición de transacción al WS de PTP y valida los campos obligatorios.
     Regresa un array con las claves:
       success: (bool) Indica si se realizó o no la petición al WS.
       warnings: (array) Indica los warnings que hubo durante la petición.
       errors: (array) Indica los errores que evitaron hacer la petición al WS.
       data: (array) Regresa un array con la respuesta del WS que tiene las claves:
        returnCode: (string[30]) Código de respuesta de la transacción.
        bankURL: (string[255]) URL a la cual remitir la solicitud para iniciar la interfaz del banco, sólo disponible cuando returnCode = SUCCESS.
        trazabilityCode: (string[40]): Código único de seguimiento para la operación dado por la red ACH.
        transactionCycle: (int) Ciclo de compensación de la red.
        transactionID: (int) Identificador único de la transacción con PTP.
        sessionID: (string[32]) Identificador único de la sesión con PTP.
        bankCurrency: (string[3]) Moneda aceptada por el banco acorde a ISO 4217.
        bankFactor: (float) Factor de conversión de la moneda.
        responseCode: (int) Estado de la operación en PTP [0=FAILED, 1=APPROVED, 2=DECLINED, 3=PENDING].
        responseReasonCode: (string[3]) Código interno de respuesta de la operación en PTP.
        responseReasonText: (string[255]) Mensaje asociado con el código de respuesta de la operación.
    */
    public function createTransaction(array $transaction) {
      $warnings = [];
      $errors = [];
      $result = false;
      $mandatoryFileds = Validation::getTransactionMandatoryFields();
      $tranVerify = Validation::verifyFields($mandatoryFileds, $transaction);
      if ($tranVerify["status"]) {
        $personMandatory = Validation::getPersonMandatoryFields();
        $payerVerify = Validation::verifyFields($personMandatory, $transaction['payer']);
        if ($payerVerify["status"]) {
          if (!isset($transaction['buyer']) || !Validation::verifyFields($personMandatory, $transaction['buyer'])["status"]) {
            $transaction['buyer'] = $transaction['payer'];
            $warnings[]="The buyer information is incomplete, was replaced for payer information";
          }
          if (!isset($transaction['shipping']) || !Validation::verifyFields($personMandatory, $transaction['shipping'])["status"]) {
            $transaction['shipping'] = $transaction['payer'];
            $warnings[]="The shipping information is incomplete, was replaced for payer information";
          }
          $auth = $this->authentication->toArray();
          $client = new SoapClient($this->service);
          $response = $client->createTransaction(["auth" => $auth, "transaction" => $transaction]);
          $result = $this->obj2array($response)["createTransactionResult"];
          // $response = ["transactionID" => "1s3d5465f1", "bankURL" => "https://bancolombia.com"];
        } else {
          $errors[] = "The payer information is incomplete, ".implode($payerVerify["missing"], ", ")." field(s) are missing";
        }
      } else {
        $errors[] = "The transaction information is incomplete, ".implode($tranVerify["missing"], ", ")." field(s) are missing";
      }
      $success = (count($errors) === 0 && $response);
      return [
        "success" => $success,
        "errors" => $errors,
        "warnings" => $warnings,
        "errors" => $errors,
        "data" => $result
        ];
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
