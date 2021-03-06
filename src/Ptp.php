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
     getBankList():
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
     createTransaction():
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
        } else {
          $errors[] = "The payer information is incomplete, ".implode($payerVerify["missing"], ", ").(count($payerVerify["missing"]) > 1 ? " fields are missing" : " field is missing");
        }
      } else {
        $errors[] = "The transaction information is incomplete, ".implode($tranVerify["missing"], ", ").(count($tranVerify["missing"]) > 1 ? " fields are missing" : " field is missing");
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

    /*
     createTransactionMulticredit()
     Crea una petición de transacción de multicrédito al WS de PTP y valida los campos obligatorios.
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
    public function createTransactionMulticredit(array $transaction) {
      $warnings = [];
      $errors = [];
      $result = false;
      $correct = true;
      $mandatoryFileds = Validation::getTransactionMultiCreditMandatoryFields();
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
          $creditMandatory = Validation::getCreditsMandatoryFields();
          foreach ($transaction["credits"] as $key => $credit) {
            $creditVerify = Validation::verifyFields($creditMandatory, $credit);
            if (!$creditVerify["status"]) {
              $correct = false;
              $errors[] = "The credit number ".($key + 1)." information is incomplete, ".implode($creditVerify["missing"], ", ").
                (count($creditVerify["missing"]) > 1 ? " fields are missing" : " field is missing");
            }
          }
          if ($correct) {
            $totalAmount = 0;
            $taxAmount = 0;
            foreach ($transaction["credits"] as $key => $value) {
              $totalAmount += $value["amountValue"];
              $taxAmount += $value["taxValue"];
            }
            $transaction["totalAmount"] = $totalAmount;
            $transaction["taxAmount"] = $taxAmount;
            $auth = $this->authentication->toArray();
            $client = new SoapClient($this->service);
            $response = $client->createTransactionMulticredit(["auth" => $auth, "transaction" => $transaction]);
            $result = $this->obj2array($response)["createTransactionMultiCreditResult"];
          }
        } else {
          $errors[] = "The payer information is incomplete, ".implode($payerVerify["missing"], ", ").(count($payerVerify["missing"]) > 1 ? " fields are missing" : " field is missing");
        }
      } else {
        $errors[] = "The transaction information is incomplete, ".implode($tranVerify["missing"], ", ").(count($tranVerify["missing"]) > 1 ? " fields are missing" : " field is missing");
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

    /*
     getTransacionInformation():
     Este método permite consultar la información asociada a la transacción cuyo ID sea el pasado por parámetro.
     Regresa un array con las siguientes claves:
     success: (bool) Indica si se realizó o no la petición al WS.
     data: (array) El resultado devuelto por el WS que contiene la siguiente información:
      transactionID: (int) Identificador único de la transacción con PTP.
      sessionID: (string[32]) Identificador único de la sesión con PTP.
      [reference] => (string[32]) Referencia única de pago.
      [requestDate] => (string) Fecha de solicitud o creación de la transacción acorde a ISO 8601.
      [bankProcessDate] => (string) Fecha de procesamiento de la transacción acorde a ISO 8601.
      [onTest] => (bool) Indicador de si la transacción es en modo de pruebas o no.
      [returnCode] => (string[30]) Código de respuesta de la transacción.
      [trazabilityCode] => (string[40]) Código único de seguimiento para la operación dado por la red ACH.
      [transactionCycle] => (int) Ciclo de compensación de la red.
      [transactionState] => (string[20]) Información del estado de la transacción [OK, NOT_AUTHORIZED, PENDING, FAILED].
      [responseCode] => (int) Estado de la operación en PTP.
      [responseReasonCode] => (string[3]) Código interno de respuesta de la operación en PlacetoPay
      [responseReasonText] => (string[255]) Mensaje asociado con el código de respuesta de la operación en PTP.
    */
    public function getTransactionInformation($transactionID) {
      $auth = $this->authentication->toArray();
      $client = new SoapClient($this->service);
      $response = $client->getTransactionInformation([
        "auth" => $auth,
        "transactionID" => $transactionID
      ]);
      $success = $response ? true : false;
      $result = $this->obj2array($response)["getTransactionInformationResult"];
      return ["success" => $success, "data" => $result];
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