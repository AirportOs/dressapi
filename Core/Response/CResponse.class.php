<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * User authentication and authorization 
 */

namespace DressApi\Core\Response;

use DressApi\Core\Request\CRequest;

class CResponse
{
    public const HTTP_STATUS_OK = 200;
    public const HTTP_STATUS_CREATED = 201;
    public const HTTP_STATUS_NOT_MODIFIED = 304;
    public const HTTP_STATUS_BAD_REQUEST = 400;
    public const HTTP_STATUS_UNAUTHORIZED = 401;
    public const HTTP_STATUS_FORBIDDEN = 403; // Access Denied
    public const HTTP_STATUS_NOT_FOUND = 404;
    public const HTTP_STATUS_METHOD_NOT_ALLOWED = 405;
    public const HTTP_STATUS_METHOD_NOT_ACCEPTABLE = 406;
    public const HTTP_STATUS_UNSUPPORTED_MEDIA_TYPE = 415;

    public const HTTP_STATUS_INTERNAL_SERVER_ERROR = 500;

    protected int $status_code = CResponse::HTTP_STATUS_OK; // OK as default
    protected string $message_error = '';
    protected string $format = DEFAULT_FORMAT_OUTPUT;         // JSON, XML, TXT 

    public function __construct()
    {
        $this->status_code = 0;
        $this->message_error = '';
        $this->format = CRequest::getFormat();
    } 


    public function setStatusCode(int $status_code)
    {
        $this->status_code = $status_code;
    } 
    
    
    public function getStatusCode()
    {
        return $this->status_code;
    } 


    public function setMessageError(string $message_error = '')
    {
        $this->message_error = $message_error;
    } 


    public function getMessageError()
    {
        return $this->message_error;
    } 


    /**
     * encode all data in XML format
     * 
     * @param array|object|null $data array or object to send to client
     * @param bool $full if true, it also exposes empty data
     *
     * @return dati transforms the data into a string containing XML format
     */
    protected function asXML(array|object|null $data, bool $full = true): string
    {
        header('Content-Type: application/xml; charset=UTF-8');

        $table = 'result';

        $xmldata = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<$table>\n";
        $space = "  ";
        if ($data !== null)
            foreach ($data as $dataname => $row)
            {
                if ($row && count($row) > 0)
                {
                    if (preg_match('/\d+/', $dataname) > 0)
                        $dataname = 'record';
                    $xmldata .= "$space<$dataname>\n";
                    foreach ($row as $name => $value)
                        if ($full || ($value !== '' && $value !== null))
                            $xmldata .= "$space$space<$name>" . str_replace(array('<', '>'), array('&lt;', '&gt;'), $value) . "</$name>\n";
                    $xmldata .= "$space</$dataname>\n";
                }
            }
        $xmldata .= "</$table>\n";

        return $xmldata; // $xml->asXML();
    }


    /**
     * encode all data in Text format (for debug)
     * 
     * @param array|object|null $data array or object to send to client
     *
     * @return string data transformed into text format (for debugging)
     */
    protected function asTEXT(array|object|null $data): string
    {
        header('Content-Type: text/plain; charset=UTF-8');
        if ($data === null)
            $data = ['message' => 'Empty'];
        return print_r($data, true) . "\n";
    }


    /**
     * encode all data in JSON format
     *
     * @param array|object|null $data array or object to send to client
     *
     * @return string data transformed into JSON format
     */
    protected function asJSON(array|object|null $data): string
    {
        header('Content-Type: application/json; charset=UTF-8');
        if ($data === null)
            $data = ['message' => 'Empty'];
        return json_encode($data);
    }



    /**
     * Method output
     *
     * Return the data results in the required format
     * 
     * @param array|object|null $result data to tranform
     *  
     * @ string all data results
     */
    public function output(array|object|null $result): string
    {
        $formatMethod = 'as'.strtoupper($this->format);

        if (method_exists($this, $formatMethod))
            $data_format = $this->$formatMethod($result);
        else
            $this->status_code = self::HTTP_STATUS_METHOD_NOT_ACCEPTABLE;

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header('Access-Control-Allow-Methods: GET, HEAD, PUT, PATCH, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        http_response_code( $this->status_code);

        return $data_format;
    }    



    /**
     * Method error
     *
     * Return an error message
     * 
     * @param int $code
     * @param string $message messaggio da inviare
     *  
     * @ string with error
     */
    public function error(int $code, string $message): string
    {
        if ($this->getStatusCode() == CResponse::HTTP_STATUS_OK)
            $this->setStatusCode($code);
    
        return $this->output(["ERROR" => $message]);
    }    

}