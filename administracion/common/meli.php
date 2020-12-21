<?php
class Meli{
const VERSION='1.1.0';
protected static $API_ROOT_URL='https://api.mercadolibre.com';
protected static $OAUTH_URL='/oauth/token';
public static $AUTH_URL=array(
"MLA" => 'https://auth.mercadolibre.com.ar',
"MLB" => 'https://auth.mercadolivre.com.br',
"MCO" => 'https://auth.mercadolibre.com.co',
"MCR" => 'https://auth.mercadolibre.com.cr',
"MEC" => 'https://auth.mercadolibre.com.ec',
"MLC" => 'https://auth.mercadolibre.cl',
"MLM" => 'https://auth.mercadolibre.com.mx',
"MLU" => 'https://auth.mercadolibre.com.uy',
"MLV" => 'https://auth.mercadolibre.com.ve',
"MPA" => 'https://auth.mercadolibre.com.pa',
"MPE" => 'https://auth.mercadolibre.com.pe',
"MPT" => 'https://auth.mercadolibre.com.pt',
"MRD" => 'https://auth.mercadolibre.com.do'
);
public static $CURL_OPTS = array(
CURLOPT_USERAGENT=>"MELI-PHP-SDK-1.1.0",
CURLOPT_SSL_VERIFYPEER=>true,
CURLOPT_CONNECTTIMEOUT=>10,
CURLOPT_RETURNTRANSFER=>1,
CURLOPT_TIMEOUT=>60
);
protected $client_id;
protected $client_secret;
protected $redirect_uri;
protected $refresh_token;
protected $access_token;
public function __construct($client_id,$client_secret,$access_token=null,$refresh_token=null){
$this->client_id=$client_id;
$this->client_secret=$client_secret;
$this->access_token=$access_token;
$this->refresh_token=$refresh_token;
}
public function getAuthUrl($redirect_uri, $auth_url){
$this->redirect_uri=$redirect_uri;
$params=array("client_id"=>$this->client_id,"response_type"=>"code","redirect_uri"=>$redirect_uri);
$auth_uri=$auth_url."/authorization?".http_build_query($params);
return $auth_uri;
}
public function authorize($code, $redirect_uri){
if($redirect_uri)
$this->redirect_uri=$redirect_uri;
$body=array(
"grant_type"=>"authorization_code",
"client_id"=>$this->client_id,
"client_secret"=>$this->client_secret,
"code"=>$code,
"redirect_uri"=>$this->redirect_uri
);
$opts=array(
CURLOPT_POST=>true,
CURLOPT_POSTFIELDS=>$body
);
$request=$this->execute(self::$OAUTH_URL,$opts);
if($request["httpCode"]==200){
$this->access_token=$request["body"]->access_token;
if($request["body"]->refresh_token)
$this->refresh_token=$request["body"]->refresh_token;
return $request;
}else{return $request;}
}
public function refreshAccessToken(){
if($this->refresh_token){
$body=array(
"grant_type"=>"refresh_token",
"client_id"=>$this->client_id,
"client_secret"=>$this->client_secret,
"refresh_token"=>$this->refresh_token
);
$opts=array(
CURLOPT_POST=>true,
CURLOPT_POSTFIELDS=>$body
);
$request=$this->execute(self::$OAUTH_URL,$opts);
if($request["httpCode"]==200){
$this->access_token=$request["body"]->access_token;
if($request["body"]->refresh_token)
$this->refresh_token=$request["body"]->refresh_token;
return $request;
}else{return $request;}
}else{
$result=array(
'error'=>'Offline-Access is not allowed.',
'httpCode'=>null
);
return $result;
}
}
public function get($path,$params=null,$assoc=false){
$exec=$this->execute($path,null,$params,$assoc);
return $exec;
}
public function post($path,$body=null,$params=array()){
$body=json_encode($body);
$opts=array(
CURLOPT_HTTPHEADER=>array('Content-Type: application/json'),
CURLOPT_POST=>true,
CURLOPT_POSTFIELDS=>$body
);
$exec=$this->execute($path,$opts,$params);
return $exec;
}
public function put($path,$body=null,$params=array()){
$body=json_encode($body);
$opts=array(
CURLOPT_HTTPHEADER=>array('Content-Type: application/json'),
CURLOPT_CUSTOMREQUEST=>"PUT",
CURLOPT_POSTFIELDS=>$body
);
$exec=$this->execute($path,$opts,$params);
return $exec;
}
public function delete($path, $params){
$opts=array(CURLOPT_CUSTOMREQUEST=>"DELETE");
$exec=$this->execute($path,$opts,$params);
return $exec;
}
public function options($path,$params=null){
$opts=array(CURLOPT_CUSTOMREQUEST=>"OPTIONS");
$exec=$this->execute($path,$opts,$params);
return $exec;
}
public function execute($path,$opts=array(),$params=array(),$assoc=false){
$uri=$this->make_path($path, $params);
$ch=curl_init($uri);
curl_setopt_array($ch, self::$CURL_OPTS);
if(!empty($opts))
curl_setopt_array($ch, $opts);
$return["body"]=json_decode(curl_exec($ch),$assoc);
$return["httpCode"]=curl_getinfo($ch,CURLINFO_HTTP_CODE);
curl_close($ch);
return $return;
}
public function make_path($path, $params = array()){
if(!preg_match("/^http/", $path)){
if(!preg_match("/^\//", $path)){$path='/'.$path;}
$uri=self::$API_ROOT_URL.$path;
}else{$uri=$path;}
if(!empty($params)){
$paramsJoined=array();
foreach($params as $param => $value){$paramsJoined[]="$param=$value";}
$params='?'.implode('&',$paramsJoined);
$uri=$uri.$params;
}
return $uri;
}
}
