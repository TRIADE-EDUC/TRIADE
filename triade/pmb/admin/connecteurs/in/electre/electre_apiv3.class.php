<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: electre_apiv3.class.php,v 1.2 2018-03-05 15:24:13 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))	die("no access");


require_once 'OpenIDConnectClient.php';
require_once $class_path.'/encoding_normalize.class.php';

class electre_apiv3 {
    private $clientID;
    private $clientSecret;
    private $clientCredentialsToken;
    private $acurl;
    private $maxresults;
    
    public function __construct($clientID, $clientSecret)
    {
         $this->clientID = $clientID;
         $this->clientSecret = $clientSecret;
         $this->__connect__();
    }
    
    public function set_maxresults($maxresults)
    {
        $this->maxresults = $maxresults;
    }
    
    private function __connect__()
    {
        $oauth = new \Jumbojett\OpenIDConnectClient('https://electre3test-idp.bvdep.com',$this->clientID, $this->clientSecret);
        $oauth->providerConfigParam(array('token_endpoint'=>'https://electre3test-idp.bvdep.com/connect/token'));
        $oauth->addScope('webapi');
        $this->clientCredentialsToken = $oauth->requestClientCredentialsToken()->access_token;
        $this->acurl = new Curl();
    }
    
    private function __buildContext__($opts,$method='GET', $protocol='http')
    {
        $context[$protocol]['method'] = $method;
        $context[$protocol]['header'] = array(
            'Authorization: Bearer '.$this->clientCredentialsToken,
            'Content-Type: application/json'
        ); 
        if(count($opts)){
            $content = encoding_normalize::json_encode($opts);
            $context[$protocol]['content'] = $content;
            $context[$protocol]['header'][] = 'Content-Length: '.strlen($content);
        }
        return $context;
    }
    
    private function __query__($uri, $method="GET", $opts=array(),$raw=false)
    {
        $response = '';
        $headers = array(
            'Authorization: Bearer '.$this->clientCredentialsToken,
            'Content-Type: application/json'
        );
        if(count($opts)){
            $content = encoding_normalize::json_encode($opts);
            $headers[] = 'Content-Length: '.strlen($content);
        }
        $this->acurl->set_option('HTTPHEADER',$headers);
        switch($method){
            case 'GET' :
                $response = $this->acurl->get($uri);
                break;
            case 'POST' :
                $response = $this->acurl->post($uri, $content);
                break;
        }
        if($raw){
            return $response->body;
        }
        $response = encoding_normalize::json_decode($response->body,true);
        return $this->cleanResponse($response);
    }
    
    public function search($mode,$user_query)
    {
        $response = $this->__query__('https://electre3test-api.bvdep.com/v1.0/searches/', "POST", array(
            'catalog' => "livre",
            'simple' => array(
                'fields' => (is_array($mode) ? $mode : array($mode)),
                'query' => $user_query
            ),
        ));
        if(isset($response['id'])){
            $result = $this->buildSearchResponse($response);
        }
        return $result;
    }
    
    private function buildSearchResponse($results)
    {
        $response = array();
        $response['id'] = $results['id'];
        if(isset($results['_links'])){
            for($i=0 ; $i<count($results['_links']) ; $i++){
                switch($results['_links'][$i]['rel']){
                    case "results" :
                        $response[$results['_links'][$i]['rel']] = $this->get($results['_links'][$i]['href']."?itemsPerPage=".$this->maxresults);
                        break;
                    case "self":
                    case "facets":
                        break;
                    default : 
                        break;
                }
            }
        }
        return $response;
    }
    
    public function fetch($catalog,$id,$what='')
    {
        $URI = '';
        switch($catalog){
            case 'livre':
                $URI = 'https://electre3test-api.bvdep.com/v1.0/books/';
                break;
            default:
                break;
        }
        if($URI){
            return $this->__query__($URI.$id);
        }
        return '';
    }
    
    private function cleanResponse($response,$flag=true)
    {
        if(!is_array($response)){
            return $response;
        }
        $result=array();
        foreach($response as $key=>$values){
            $new_key = $key;
            if(!is_numeric($key)){
                $new_key = convert_diacrit($key);
            }            
            $result[$new_key] = $this->cleanResponse($values,false);
        } 
        return $result;
    }
    
    public function fetchAllResults($searchId)
    {
        $results = array();
        $i = 0;
        $page = 0;
        $nbperPage = 500;
        $total = 0;
        do {
            $response = $this->get('https://electre3test-api.bvdep.com/v1.0/searches/'.$searchId.'/results?page='.$page.'&itemsPerPage='.$nbperPage);
            $results = array_merge($results,$response['list']);
            $total = $response['totalcount'];
            $i+=$nbperPage;
            $page++;
        } while ($i<$total);
       return $results;
    }
    
    public function get($uri,$raw=false)
    {
        return $this->__query__($uri,'GET',array(),$raw);
    }
    
    public function __get__($uri)
    {
        $response = '';
        $context = stream_context_create($this->__buildContext__($opts,$method));
        if ($fp = fopen($uri, 'r', false, $context)){
            while($row = fread($fp,4096)) {
                $response.= $row;
            }
            fclose($fp);           
        }
        return $response;
    }
}