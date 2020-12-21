<?php
$curl=curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://apimbu.mercantilbanco.com/mercantil-banco/prod/api-pagos-b2c/REALIZAR_PAGO_CREDITO",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"
    HEADER_PAGO_REQUEST\":
    {\"IDENTIFICADOR_UNICO_GLOBAL\":\"6916773590859776\",\"IDENTIFICACION_CANAL\":\"7125617854119936\",
      \"SIGLA_APLICACION\":\"mibkip\",\"IDENTIFICACION_USUARIO\":\"5568964547575808\",
      \"DIRECCION_IP_CONSUMIDOR\":\"3528127163816190\",\"DIRECCION_IP_CLIENTE\":\"30025297603830\",
      \"FECHA_ENVIO_MENSAJE\":\"hipgaf\",\"HORA_ENVIO_MENSAJE\":\"zeew\",
      \"ATRIBUTO_PAGINEO\":\"wikju\",\"CLAVE_BUSQUEDA\":\"lednanu\",
      \"CANTIDAD_REGISTROS\":94289001
    },
    \"BODY_PAGO_REQUEST\":
    {\"IDENTIFICADOR_COMERCIO\":64.68850502,\"TIPO_TRANSACCION\":\"6374468862948654\",
      \"MONTO_TRANSACCION\":4.20652234,\"NUMERO_FACTURA\":44.92934167,
      \"IDENTIFICACION_TARJETAHABIENTE\":\"1447025239392256\",
      \"NOMBRE_TARJETAHABIENTE\":\"kueduih\",\"NUMERO_TARJETA\":\"fownelak\",
      \"FECHA_VENCIMIENTO_TARJETA\":38.20536411,\"CODIGO_SEGURIDAD_TARJETA\":88.47497888,
      \"NUMERO_LOTE\":\"kojokene\"}
    }",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "content-type: application/json",
    "x-ibm-client-id: REPLACE_THIS_KEY"
  ),
));
$response=curl_exec($curl);
$err=curl_error($curl);
curl_close($curl);
if($err){echo "cURL Error #:" . $err;}else{echo $response;}
/*
Respuesta dada por la api
{
  "HEADER_PAGO_RESPONSE": {
    "TIPO_MENSAJE": "23.118.221.108",
    "MENSAJE_PROGRAMADOR_SISTEMA": "ipezifo",
    "CODIGO_MENSAJE_PROGRAMADOR": "kobuwalu",
    "MENSAJE_USUARIO": "romutbug",
    "CODIGO_MENSAJE_USUARIO": "piflamzo",
    "FECHA_SALIDA_MENSAJE": "5418026606264320",
    "HORA_SALIDA_MENSAJE": "7754944847282176"
  },
  "BODY_PAGO_RESPONSE": {
    "CODIGO_RETORNO": "vozvaf",
    "DESCRIPCION_RETORNO": "Ot ovos ohunouw zoc ufcopu nev gu dozogzo cuuhuwel ge nencotra unain tubapal ru rejalbi wudor ehmec.",
    "NUMERO_CONFIRMACION": "sahlogka"
  }
}*/
/*
Definiciones
HEADER_PAGO_REQUEST
{
    "type": "object",
    "properties": {
        "IDENTIFICADOR_UNICO_GLOBAL": {
            "type": "string"
        },
        "IDENTIFICACION_CANAL": {
            "type": "string"
        },
        "SIGLA_APLICACION": {
            "type": "string"
        },
        "IDENTIFICACION_USUARIO": {
            "type": "string"
        },
        "DIRECCION_IP_CONSUMIDOR": {
            "type": "string"
        },
        "DIRECCION_IP_CLIENTE": {
            "type": "string"
        },
        "FECHA_ENVIO_MENSAJE": {
            "type": "string"
        },
        "HORA_ENVIO_MENSAJE": {
            "type": "string"
        },
        "ATRIBUTO_PAGINEO": {
            "type": "string"
        },
        "CLAVE_BUSQUEDA": {
            "type": "string"
        },
        "CANTIDAD_REGISTROS": {
            "type": "integer",
            "format": "int32"
        }
    },
    "required": [
        "IDENTIFICADOR_UNICO_GLOBAL",
        "IDENTIFICACION_CANAL",
        "SIGLA_APLICACION",
        "IDENTIFICACION_USUARIO",
        "DIRECCION_IP_CONSUMIDOR",
        "DIRECCION_IP_CLIENTE",
        "FECHA_ENVIO_MENSAJE",
        "HORA_ENVIO_MENSAJE",
        "ATRIBUTO_PAGINEO",
        "CLAVE_BUSQUEDA",
        "CANTIDAD_REGISTROS"
    ]
}


BODY_PAGO_REQUEST
{
    "type": "object",
    "properties": {
        "IDENTIFICADOR_COMERCIO": {
            "type": "number"
        },
        "TIPO_TRANSACCION": {
            "type": "string"
        },
        "MONTO_TRANSACCION": {
            "type": "number"
        },
        "NUMERO_FACTURA": {
            "type": "number"
        },
        "IDENTIFICACION_TARJETAHABIENTE": {
            "type": "string"
        },
        "NOMBRE_TARJETAHABIENTE": {
            "type": "string"
        },
        "NUMERO_TARJETA": {
            "type": "string"
        },
        "FECHA_VENCIMIENTO_TARJETA": {
            "type": "number"
        },
        "CODIGO_SEGURIDAD_TARJETA": {
            "type": "number"
        },
        "NUMERO_LOTE": {
            "type": "string"
        }
    },
    "required": [
        "IDENTIFICADOR_COMERCIO",
        "TIPO_TRANSACCION",
        "MONTO_TRANSACCION",
        "NUMERO_FACTURA",
        "IDENTIFICACION_TARJETAHABIENTE",
        "NOMBRE_TARJETAHABIENTE",
        "NUMERO_TARJETA",
        "FECHA_VENCIMIENTO_TARJETA",
        "CODIGO_SEGURIDAD_TARJETA",
        "NUMERO_LOTE"
    ]
}


PAGO_CREDITO_REQUEST
{
    "properties": {
        "HEADER_PAGO_REQUEST": {
            "$ref": "#\/definitions\/HEADER_PAGO_REQUEST"
        },
        "BODY_PAGO_REQUEST": {
            "$ref": "#\/definitions\/BODY_PAGO_REQUEST"
        }
    },
    "additionalProperties": false,
    "required": [
        "HEADER_PAGO_REQUEST",
        "BODY_PAGO_REQUEST"
    ]
}


HEADER_PAGO_RESPONSE
{
    "type": "object",
    "properties": {
        "TIPO_MENSAJE": {
            "type": "string"
        },
        "MENSAJE_PROGRAMADOR_SISTEMA": {
            "type": "string"
        },
        "CODIGO_MENSAJE_PROGRAMADOR": {
            "type": "string"
        },
        "MENSAJE_USUARIO": {
            "type": "string"
        },
        "CODIGO_MENSAJE_USUARIO": {
            "type": "string"
        },
        "FECHA_SALIDA_MENSAJE": {
            "type": "string"
        },
        "HORA_SALIDA_MENSAJE": {
            "type": "string"
        }
    },
    "required": [
        "TIPO_MENSAJE",
        "MENSAJE_PROGRAMADOR_SISTEMA",
        "CODIGO_MENSAJE_PROGRAMADOR",
        "MENSAJE_USUARIO",
        "CODIGO_MENSAJE_USUARIO",
        "FECHA_SALIDA_MENSAJE",
        "HORA_SALIDA_MENSAJE"
    ]
}


BODY_PAGO_RESPONSE
{
    "type": "object",
    "properties": {
        "CODIGO_RETORNO": {
            "type": "string"
        },
        "DESCRIPCION_RETORNO": {
            "type": "string"
        },
        "NUMERO_CONFIRMACION": {
            "type": "string"
        }
    },
    "required": [
        "CODIGO_RETORNO",
        "DESCRIPCION_RETORNO",
        "NUMERO_CONFIRMACION"
    ]
}


PAGO_CREDITO_RESPONSE
{
    "properties": {
        "HEADER_PAGO_RESPONSE": {
            "$ref": "#\/definitions\/HEADER_PAGO_RESPONSE"
        },
        "BODY_PAGO_RESPONSE": {
            "$ref": "#\/definitions\/BODY_PAGO_RESPONSE"
        }
    },
    "additionalProperties": false,
    "required": [
        "HEADER_PAGO_RESPONSE",
        "BODY_PAGO_RESPONSE"
    ]
}


FAULT
{
    "type": "object",
    "properties": {
        "faultcode": {
            "type": "string"
        },
        "faultstring": {
            "type": "string"
        },
        "faultactor": {
            "type": "string"
        }
    },
    "required": [
        "faultcode",
        "faultstring",
        "faultactor"
    ]
}


EXCEPTION_DETAIL
{
    "type": "object",
    "properties": {
        "exceptionMessage": {
            "type": "string"
        },
        "exceptionTrace": {
            "type": "string"
        },
        "exceptionType": {
            "type": "string"
        }
    },
    "required": [
        "exceptionMessage",
        "exceptionTrace",
        "exceptionType"
    ]
}


PAGO_CREDITO_RESPONSE_FAULT
{
    "properties": {
        "FAULT": {
            "$ref": "#\/definitions\/FAULT"
        },
        "EXCEPTION_DETAIL": {
            "$ref": "#\/definitions\/EXCEPTION_DETAIL"
        }
    },
    "additionalProperties": false,
    "required": [
        "FAULT",
        "EXCEPTION_DETAIL"
    ]
}*/
 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <div class="">
      <?php echo $response; ?>
    </div>
  </body>
</html>
