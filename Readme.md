PTP Library!
===================


A continuación se describe una pequeña documentación para el uso de la librería:

----------

[TOC]

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
 Al instanciar un objeto de la clase Ptp, se debe pasar por parámetros la información correspondiente al servicio a consumir (WSDL de Place To Pay), el identificador de login y la llave transactional dada por el proveedor respectivamente:
 
  use Comiguel\Ptp\Ptp;

  $ptp = new Ptp(
      'https://test.placetopay.com/soap/pse/?wsdl',
      '6dd490faf9cb87a9862245da41170ff2',
      '024h1IlD'
  );

  $result = $ptp->getBankList();
  
  $result = $ptp->createTransaction($transaction);
  
  $result = $ptp->createTransactionMulticredit($transaction);
  
  $result = $ptp->getTransactionInformation($transactionID);
    
<i class="icon-file"></i>API
-------------

## getBankList() ##
Este método permite obtener el listado de bancos disponibles.
El resultado de este llamado se debe almacenar en caché de manera que sólo se consulte esta información una sola vez por día.
### Datos de entrada
No necesita ningún parámetro.

### Salida
El resultado de este método es un array con la siguientes claves:

> - **success: (boolean)** Indica si se pudo realizar o no la petición al WS.
> - **data: (array)** Si "success" tiene el valor de true, aquí se devuelve la información de los bancos que tiene la estructura:

>        > - **BankCode: String\[4]** Código del banco
>        > - **BankName: String\[60]** Nombre del Banco

## createTransaction($transaction) ##
Solicita la creación de una transacción. En los datos de la solicitud se especifica quién es el pagador, el comprador y el despacho. Así mismo para cuál de los bancos habilitados se hace la petición y a que URL de retorno debe el banco redirigir al cuenta habiente.
### Datos de entrada
El método recibe un array asociativo con la información de la transacción a crear. La estructura de este array tiene los siguientes claves obligatorias:
> - **bankCode: string\[4]** Código de la entidad financiera con la cual
realizar la transacción.
> - **bankInterface: string\[1]** Tipo de interfaz del banco a desplegar \[0 = PERSONAS, 1 = EMPRESAS].
> - **returnURL: string\[255]** URL de retorno especificada para la entidad financiera.
> - **reference: string\[32]** Referencia única de pago.
> - **description: string\[255]** Descripción del pago.
> - **language: string\[2]** Idioma esperado para las transacciones acorde a ISO 631-1, mayúscula sostenida.
> - **currency: string\[3]** Moneda a usar para el recaudo acorde a ISO 4217.
> - **totalAmount: (double)** Valor total a recaudar.
> - **taxAmount: (double)** Discriminación del impuesto aplicado.
> - **devolutionBase: (double)** Base de devolución para el impuesto.
> - **tipAmount: (double)** Propina u otros valores exentos de impuesto (tasa aeroportuaria) y que deben agregarse al valor total a pagar.
> - **payer: (array)** Información del pagador, debe tener las siguientes claves obligatorias:

>   > -   **document: string\[12]**
>      > -    **documentType: string\[3]**
>      > -    **firstName: string\[60]**
>      > -    **lastName: string\[60]**
>      > -    **company: string\[60]**
>      > -    **emailAddress: string\[80]**
>      > -    **address: string\[100]**
>      > -    **city: string\[50]**
>      > -    **province: string\[50]**
>      > -    **country: string\[2]**
>      > -    **phone: string\[30]**
>      > -    **mobile: string\[30]**
> - **ipAddress: string\[15]** Dirección IP desde la cual realiza la transacción el pagador.
> - **userAgent: string\[255]** Agente de navegación utilizado por el pagador.
> - **additionalData: (array)** Datos adicionales para ser almacenados con la transacción.
Puede ser uno o mas arrays clave valor con las siguientes claves:

>    > - **name: string\[30]**: Código para referenciar el atributo.
>    > - **value: string\[128]**: Valor que asume el atributo.

Las claves opcionales son:
> - **buyer: (array)** Este array debe contener la información del comprador los cuáles son los mismos campos del pagador (buyer).
Si no se da esta información se utiliza la misma información del pagador (buyer).
> - **shipping: (array)** Este array debe contener la información del receptor los cuáles son los mismos campos del pagador (buyer).
Si no se da esta información se utiliza la misma información del pagador (buyer).
### Salida
Regresa un array con las claves:
> - **success: (bool)** Indica si se pudo realizar o no la petición al WS.
> - **warnings: (array)** Indica los warnings que hubo durante la petición.
> - **errors: (array)** Indica los errores que evitaron hacer la petición al WS.
> - **data: (array)** Regresa un array con la respuesta del WS que tiene las claves:

>        > - **returnCode: (string\[30])** Código de respuesta de la transacción.
Puede ser uno de los siguientes valores:

>      >    > - SUCCESS
>       >    > - FAIL_ENTITYNOTEXISTSORDISABLED
>       >    > - FAIL_BANKNOTEXISTSORDISABLED
>       >    > - FAIL_SERVICENOTEXISTS
>       >    > - FAIL_INVALIDAMOUNT
>       >    > - FAIL_INVALIDSOLICITDATE
>       >    > - FAIL_BANKUNREACHEABLE
>       >    > - FAIL_NOTCONFIRMEDBYBANK
>       >    > - FAIL_CANNOTGETCURRENTCYCLE
>       >    > - FAIL_ACCESSDENIED
>       >    > - FAIL_TIMEOUT
>       >    > - FAIL_DESCRIPTIONNOTFOUND
>       >    > - FAIL_EXCEEDEDLIMIT
>       >    > - FAIL_TRANSACTIONNOTALLOWED
>       >    > - FAIL_RISK
>       >    > - FAIL_NOHOST
>       >    > - FAIL_NOTALLOWEDBYTIME
>       >    > - FAIL_ERRORINCREDITS
>        > - **bankURL: (string\[255])** URL a la cual remitir la solicitud para iniciar la interfaz del banco, sólo disponible cuando returnCode = SUCCESS.
>        > - **trazabilityCode: (string\[40])** Código único de seguimiento para la operación dado por la red ACH.
>        > - **transactionCycle: (integer)** Ciclo de compensación de la red.
>        > - **transactionID: (integer)** Identificador único de la transacción con PTP.
>        > - **sessionID: (string\[32])** Identificador único de la sesión con PTP.
>        > - **bankCurrency: (string\[3])** Moneda aceptada por el banco acorde a ISO 4217.
>        > - **bankFactor: (float)** Factor de conversión de la moneda.
>        > - **responseCode: (integer)** Estado de la operación en PTP [0=FAILED, 1=APPROVED, 2=DECLINED, 3=PENDING].
>        > - **responseReasonCode: (string\[3])** Código interno de respuesta de la operación en PTP.
>        > - **responseReasonText: (string\[255])** Mensaje asociado con el código de respuesta de la operación.

## createTransactionMultiCredit($transaction) ##
Solicita la creación de una transacción con dispersión de fondos. En los datos de la solicitud se especifica quién es el pagador, el comprador y el despacho. Así mismo para cuál de los bancos habilitados se hace la petición y a que URL de retorno debe el banco redirigir al cuenta habiente. Así como cada uno de los créditos a aplicar para cada uno de los servicios asociados. Tenga en cuenta que un servicio multicrédito tiene asociado unos servicios dependientes. Siempre deberá cobrar a todos los servicios dependientes así el valor para una de los créditos sea cero.
### Datos de entrada
El método recibe un array asociativo con la información de la transacción a crear. La estructura de este array tiene los siguientes claves obligatorias:
> - **bankCode: string\[4]** Código de la entidad financiera con la cual
realizar la transacción.
> - **bankInterface: string\[1]** Tipo de interfaz del banco a desplegar \[0 = PERSONAS, 1 = EMPRESAS].
> - **returnURL: string\[255]** URL de retorno especificada para la entidad financiera.
> - **reference: string\[32]** Referencia única de pago.
> - **description: string\[255]** Descripción del pago.
> - **language: string\[2]** Idioma esperado para las transacciones acorde a ISO 631-1, mayúscula sostenida.
> - **currency: string\[3]** Moneda a usar para el recaudo acorde a ISO 4217.
> - **totalAmount: (double)** Valor total a recaudar.
> - **taxAmount: (double)** Discriminación del impuesto aplicado.
> - **devolutionBase: (double)** Base de devolución para el impuesto.
> - **tipAmount: (double)** Propina u otros valores exentos de impuesto (tasa aeroportuaria) y que deben agregarse al valor total a pagar.
> - **payer: (array)** Información del pagador, debe tener las siguientes claves obligatorias:

>   > -   **document: string\[12]**
>      > -    **documentType: string\[3]**
>      > -    **firstName: string\[60]**
>      > -    **lastName: string\[60]**
>      > -    **company: string\[60]**
>      > -    **emailAddress: string\[80]**
>      > -    **address: string\[100]**
>      > -    **city: string\[50]**
>      > -    **province: string\[50]**
>      > -    **country: string\[2]**
>      > -    **phone: string\[30]**
>      > -    **mobile: string\[30]**
> - **ipAddress: string\[15]** Dirección IP desde la cual realiza la transacción el pagador.
> - **userAgent: string\[255]** Agente de navegación utilizado por el pagador.
> - **additionalData: (array)** Datos adicionales para ser almacenados con la transacción.
Puede ser uno o mas arrays clave valor con las siguientes claves:

>    > - **name: string\[30]**: Código para referenciar el atributo.
>    > - **value: string\[128]**: Valor que asume el atributo.
> - **credits: (array)** Detalle de la dispersión a realizar. La estructura de este array debe tener las siguientes claves:

>      > -    **entityCode: string\[12]** Código de la entidad del tercero para dispersión.
>      > -    **serviceCode: string\[12]** Código del servicio del tercero.
>      > -    **amountValue: double** Valor total a recaudar a favor de la entidad.
>      > -    **taxValue: double** Discriminación del impuesto aplicado a favor de la entidad.
>      > -    **description: string\[60]** Descripción el concepto cobrado.

Las claves opcionales son:
> - **buyer: (array)** Este array debe contener la información del comprador los cuáles son los mismos campos del pagador (buyer).
Si no se da esta información se utiliza la misma información del pagador (buyer).
> - **shipping: (array)** Este array debe contener la información del receptor los cuáles son los mismos campos del pagador (buyer).
Si no se da esta información se utiliza la misma información del pagador (buyer).
### Salida
Regresa un array con las claves:
> - **success: (bool)** Indica si se pudo realizar o no la petición al WS.
> - **warnings: (array)** Indica los warnings que hubo durante la petición.
> - **errors: (array)** Indica los errores que evitaron hacer la petición al WS.
> - **data: (array)** Regresa un array con la respuesta del WS que tiene las claves:

>        > - **returnCode: (string\[30])** Código de respuesta de la transacción.
Puede ser uno de los siguientes valores:

>      >    > - SUCCESS
>       >    > - FAIL_ENTITYNOTEXISTSORDISABLED
>       >    > - FAIL_BANKNOTEXISTSORDISABLED
>       >    > - FAIL_SERVICENOTEXISTS
>       >    > - FAIL_INVALIDAMOUNT
>       >    > - FAIL_INVALIDSOLICITDATE
>       >    > - FAIL_BANKUNREACHEABLE
>       >    > - FAIL_NOTCONFIRMEDBYBANK
>       >    > - FAIL_CANNOTGETCURRENTCYCLE
>       >    > - FAIL_ACCESSDENIED
>       >    > - FAIL_TIMEOUT
>       >    > - FAIL_DESCRIPTIONNOTFOUND
>       >    > - FAIL_EXCEEDEDLIMIT
>       >    > - FAIL_TRANSACTIONNOTALLOWED
>       >    > - FAIL_RISK
>       >    > - FAIL_NOHOST
>       >    > - FAIL_NOTALLOWEDBYTIME
>       >    > - FAIL_ERRORINCREDITS
>        > - **bankURL: (string\[255])** URL a la cual remitir la solicitud para iniciar la interfaz del banco, sólo disponible cuando returnCode = SUCCESS.
>        > - **trazabilityCode: (string\[40])** Código único de seguimiento para la operación dado por la red ACH.
>        > - **transactionCycle: (integer)** Ciclo de compensación de la red.
>        > - **transactionID: (integer)** Identificador único de la transacción con PTP.
>        > - **sessionID: (string\[32])** Identificador único de la sesión con PTP.
>        > - **bankCurrency: (string\[3])** Moneda aceptada por el banco acorde a ISO 4217.
>        > - **bankFactor: (float)** Factor de conversión de la moneda.
>        > - **responseCode: (integer)** Estado de la operación en PTP [0=FAILED, 1=APPROVED, 2=DECLINED, 3=PENDING].
>        > - **responseReasonCode: (string\[3])** Código interno de respuesta de la operación en PTP.
>        > - **responseReasonText: (string\[255])** Mensaje asociado con el código de respuesta de la operación.


## getTransactionInformation($transactionID) ##

Obtiene la información de una transacción, debe ser consultado para cualquier operación previamente creada con el método createTransaction o createTransactionMultiCredit y a sea cuando regresa del banco o cuando al menos han transcurrido 7 minutos desde que el cliente fue redirigido a la interfaz del banco. Deberá consumirse en intervalos de al menos cada 12 minutos hasta que tenga un estado de transacción transactionState diferente a PENDING.

### Datos de entrada
El método recibe como parámetro únicamente el transactionID de la transacción que se quiere consultar:

> - **transactionID: (integer)** Identificador único de la transacción en PlacetoPay, equivale al retornado en la creación de la transacción.

### Salida
Regresa un array con las claves:
> - **success: (bool)** Indica si se pudo realizar o no la petición al WS.
> - **data: (array)** Regresa un array con la respuesta del WS que tiene las claves:

>    > - **transactionID: (int)** Identificador único de la transacción con PTP.
>    > - **sessionID: (string[32])** Identificador único de la sesión con PTP.
>    > - **reference: (string[32])** Referencia única de pago.
>    > - **requestDate: (string)** Fecha de solicitud o creación de la transacción acorde a ISO 8601.
>    > - **bankProcessDate: (string)** Fecha de procesamiento de la transacción acorde a ISO 8601.
>    > - **onTest: (bool)** Indicador de si la transacción es en modo de pruebas o no.
>    > - **returnCode: (string[30])** Código de respuesta de la transacción.
>    > - **trazabilityCode: (string[40])** Código único de seguimiento para la operación dado por la red ACH
>    > - **transactionCycle: (int)** Ciclo de compensación de la red.
>    > - **transactionState: (string[20])** Información del estado de la transacción [OK, NOT_AUTHORIZED, PENDING, FAILED].
>    > - **responseCode: (int)** Estado de la operación en PTP.
>    > - **responseReasonCode: (string[3])** Código interno de respuesta de la operación en PlacetoPay
>    > - **responseReasonText: (string[255])** Mensaje asociado con el código de respuesta de la operación en PTP.

Test
-------------

  composer test

License
-------------
The MIT License (MIT). Please see [License File](https://github.com/thephpleague/skeleton/blob/master/LICENSE.md)  for more information.
