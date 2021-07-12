<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * User authentication and authorization 
 */

namespace DressApi\Core\Request;

require_once __DIR__ . '/../../config.php';

use Exception;

class CRequest
{
    protected string $module;            // name of the module (or table) to display
    protected string $method;            // method get, head, post, puth, patch, delete OR options 
    
    protected string $sets;              // input parameters
    protected array  $params;            // values ​​to register input / update
    protected array $filters;            // filters or input/update parameters
    protected string $request;           // request table and filters
    protected bool $with_relations;      // table with names instead of indexes

    protected array $order_by = [];      // Order table by Item and type order (ASC or DESC) - i.e.: order-by/id-DESC

    protected int $page = 1;
    protected int $items_per_page = 20;

    protected string $charset = 'UTF-8';

    public function __construct()
    {
        $this->with_relations = false;
        $this->module = '';

        $this->setMethod();
        $this->setFormat();
        $this->setParameters();
        $this->setFilters();
    }


    protected function setMethod()
    {
        $this->method = ((isset($_SERVER['REQUEST_METHOD'])) ? (strtoupper($_SERVER['REQUEST_METHOD'])) : ('GET'));
        if ($this->method=='POST' && isset($_FILES) && count($_FILES))
            $this->method=='UPLOAD'; // Virtual method: is a POST but with file in uploaded
    }

    
    protected function setParameters()
    {
        // curl -d "params...."
        $this->sets = file_get_contents('php://input');

        if ($this->sets)
        {
            $this->params = [];
            try
            {
                parse_str($this->sets, $this->params);
            }
            catch (Exception)
            {
            }
        }        
    }


    protected function setFilters()
    {
        // request/table/filters
        $this->request =  ((isset($_SERVER['QUERY_STRING'])) ? ($_SERVER['QUERY_STRING']) : (''));
        
        if ($this->request)
        {
            if (strpos($this->request, '/') === false) // if only one filter is the module/table
            {
                $this->module = $this->request;
            }
            else
            {
                $filt = explode('/', $this->request);
                $this->module = array_shift($filt); // first is an table/controller

                if (count($filt) > 0) // second is an id ("*" for all id)
                {
                    $probably_id = $filt[0];
                    if ($probably_id == '*' || preg_match('/^[\d,]+$/', $probably_id) === 1)
                    {
                        array_shift($filt);
                        $this->filters[str_replace('[table]', $this->module, ITEM_ID)] = ['=', $probably_id];
                    }
                }
                $next_is_page = false;
                if (count($filt) > 0)
                {
                    foreach ($filt as $f)
                    {
                        if ($f == 'with-relations' || $f == 'wr')
                        {
                            $this->with_relations = true;
                            continue;
                        }
                        if ($f == 'page' || $f == 'p')
                        {
                            $next_is_page = true;
                            continue;
                        }
                        if ($next_is_page)
                        {
                            if (strpos($f, ','))
                                list($this->page, $this->items_per_page) = explode(',', $f);
                            else
                            {
                                $this->page = $f;
                                $this->items_per_page = DEFAULT_ITEMS_PER_PAGE;
                            }
                            if ($this->page < 1 && preg_match('/\d+/', $this->page) > 0)
                            {
                                $this->page = 1;
                                throw new Exception('Page error request');
                                // $this->response->output(null, CResponse::HTTP_STATUS_BAD_REQUEST);
                                break;
                            }


                            if ($this->items_per_page > MAX_ITEMS_PER_PAGE)
                                $this->items_per_page = MAX_ITEMS_PER_PAGE;

                            $next_is_page = false;
                            continue;
                        }

                        if ($f == 'order-by' || $f == 'ob')
                        {
                            $next_is_order = true;
                            continue;
                        }

                        if (!empty($next_is_order))
                        {
                            if (strpos($f, '-')) // only one order by 
                            {
                                $this->order_by = explode('-', $f);
                                if (isset($this->order_by[1]))
                                    $this->order_by[1] = strtoupper($this->order_by[1]);
                                if ($this->order_by[1] != 'ASC' && $this->order_by[1] != 'DESC')
                                    throw new Exception('The order mode is wrong: it must ASC or DESC');
                            }
                            else
                                $this->order_by = [$f, DEFAULT_ORDER];

                            $next_is_order = false;
                            continue;
                        }

                        // Other filters
                        $operators = ['<=', '>=', '<>', '=', '~', '<', '>'];

                        foreach ($operators as $operator)
                            if (strpos($f, $operator) !== false) // must contain the operator
                            {
                                list($name, $value) = explode($operator, $f);
                                $this->filters[$name] = [$operator, $value];
                                break;
                            }
                    }
                }
            }
        }
    }

    
    /**
     * Set format type of output
     * 
     * @param string the format type name (json, xml, html OR text) default is declared in config.php as DEFAULT_FORMAT_OUTPUT
     *               NOTE: If it is null it gets the value from HTTP_ACCEPT if it is valid otherwise it is the default value 
     */
    protected function setFormat(string $format = null): void
    {
        if ($format == null)
        {
            $format = DEFAULT_FORMAT_OUTPUT;
            if (isset($_SERVER['HTTP_ACCEPT']))
            {
                list($app, $format) = explode('/', $_SERVER['HTTP_ACCEPT']);    
                if (strpos($format,';'))
                {
                    list($format,$charset) = explode(';', $format);    
                    if (strpos($charset,'charset=')) list($app,$this->charset) = explode('=', $charset);    
                }

            }
        }

        $this->format = ( ($format == '*') ? (DEFAULT_FORMAT_OUTPUT) : (strtolower($format)) );
    }


    public function getRequest() : string      { return $this->request; }
    public function getModule() : string       { return $this->module; }
    public function getMethod() : string       { return $this->method; }
    public function getSets() : string         { return $this->sets; }
    public function getFilters() : ?array      { return $this->filters ?? null; }
    public function getParameters() : ?array   { return $this->params ?? null; }
    public function getWithRelations() : bool  { return $this->with_relations; }
    public function getCurrentPage() : int     { return $this->page; }
    public function getItemsPerPage() : int    { return $this->items_per_page; }
    public function getFilter($name) : ?string { return $this->filters[$name] ?? null; }
    public function getParameter($name) : ?string { return $this->params[$name] ?? null; }
    public function getCharset() : string { return $this->charset; }
    public function getOrderBy() : ?array     { return $this->order_by ?? null; }

}