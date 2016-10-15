<?php

  namespace Comiguel\Ptp;

  class Validation {

    public static function getTransactionMandatoryFields() {
      return [
        "bankCode",
        "bankInterface",
        "returnURL",
        "reference",
        "description",
        "language",
        "currency",
        "totalAmount",
        "taxAmount",
        "devolutionBase",
        "tipAmount",
        "payer",
        "ipAddress",
        "userAgent",
        "additionalData"
      ];
    }

    public static function getTransactionMultiCreditMandatoryFields() {
      $mandatories = Validation::getTransactionMandatoryFields();
      $mandatories[] = "credits";
      unset($mandatories[array_search("totalAmount", $mandatories)], $mandatories[array_search("taxAmount", $mandatories)]);
      return $mandatories;
    }

    public static function getTransactionOptionalsFields() {
      return [
        "buyer",
        "shipping",
        "additionalData"
      ];
    }

    public static function getPersonMandatoryFields() {
      return [
        "document",
        "documentType",
        "firstName",
        "lastName",
        "company",
        "emailAddress",
        "address",
        "city",
        "province",
        "country",
        "phone",
        "mobile"
      ];
    }

    public static function getCreditsMandatoryFields() {
      return [
        "entityCode",
        "serviceCode",
        "amountValue",
        "taxValue",
        "description"
      ];
    }

    /*
    * Verifica si un Array dado tiene todos los campos obligatorios.
    Recibe el array con las claves obligatorias y el array a verificar
    Devuelve status => true si los tiene todos, status => false y missing => con los campos faltantes
    */
    public static function verifyFields($mandatories, $given) {
      $missing = [];
      foreach ($mandatories as $key => $value) {
        if (!array_key_exists($value, $given)){
          $missing[] = $value;
        }
      }
      if (count($missing) > 0) {
        return ["status" => false, "missing" => $missing];
      } else {
        return ["status" => true];
      }
    }
  }
?>