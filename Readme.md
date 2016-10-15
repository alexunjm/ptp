PTP Library!
===================


A continuación se describe una pequeña documentación para el uso de la librería:

----------


Instalación
-------------

La instalación se realiza mediante composer ejecutando en la terminal el comando:

  composer require comiguel/ptp

o simplemente añadir la librería dentro de la clave "require" del composer.json

  "require": {
      "comiguel/ptp": "dev-master"
  },

Uso
-------------

  use Comiguel\Ptp\Ptp;

  $ptp = new Ptp(
      'https://test.placetopay.com/soap/pse/?wsdl',
      '6dd490faf9cb87a9862245da41170ff2',
      '024h1IlD'
  );

  $ptp->getBankList();
  
  $ptp->createTransaction($transaction);
  
  $ptp->createTransactionMulticredit($transaction);
  
  $ptp->getTransactionInformation($transactionID);
    
<i class="icon-file"></i>API
-------------

## getBankList() ##
Este método permite obtener el listado de bancos disponibles.
El resultado de este llamado se debe almacenar en caché de manera que sólo se consulte esta información una sola vez por día.

### Return
El resultado de este método es un array con la siguientes claves:

> - **success: (boolean)** Indica si se realizó o no la petición al WS.
> - **data: (array)** Si "success" tiene el valor de true, aquí se devuelve la información de los bancos que tiene la estructura:

>        > - **BankCode**: String\[4]
>        > - **BankName**: String\[60]