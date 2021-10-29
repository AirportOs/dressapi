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

namespace DressApi\Core\Response\CHtmlView;

use DressApi\Core\DBMS\CMySqlComposer as CSqlComposer;
use DressApi\Core\DBMS\CMySqlDB as CDB;
use DressApi\Modules\Base\CBaseController;

class CHtmlView
{
    protected mixed $data = null;
    protected array $page_info = [];
    protected array $modules = [];
    protected array $html_names = [];
    protected array $css_names = [];
    protected array $filenames = [];

    public function __construct(mixed $data, string $module = 'Base', $html_name = 'Default', $css_name = 'default')
    {
        global $cache;

        $this->data = $data;

        $this->modules = [$module=>$module];

        $this->html_names = ['Default'=>'Default'];
        $this->html_names[$html_name] = $html_name;

        $this->css_names = ['default'=>'default'];
        $this->css_names[$css_name] = $css_name;

        // INFO => app => module => element

        $this->page_info['app'] = ['title'=>DEFAULT_PAGE_TITLE,'description'=>DEFAULT_PAGE_DESCRIPTION];

        if ($cache)
        {
            $cache->setArea($module);
            $this->page_info['module'] = $cache->get('module_info');
        }

        if (!$this->page_info['module'])
        {
            // Info of web site
            $sql = new CSqlComposer();
            $sql->from('module');        
            $sql->select('title,description');
            $sql->where("name='$module'");

            $db = new CDB();
            $db->query($sql);
            $this->page_info['module'] = $db->getFetchAssoc();

            if (!$this->page_info['module'])
                $this->page_info['module'] =  $this->page_info['app'];
            
            if ($cache) 
                $cache->set('module_info',$this->page_info['module']);
        }

        if (isset($data['elements']) && count($data['elements'])==1)
            $this->page_info['element'] = ['title'=>$data['elements'][0]['title'],'description'=>$data['elements'][0]['description']];
        else
            $this->page_info['element'] = $this->page_info['module'];
    }


    /**
     * Accoda un template alla lista di quelli da visualizzare
     *
     * @param string $path della cartella del server in cui si trovato i template HTML/XML
     * @param string $area dell'area di riferimento
     * @param array|string|null $files o array contenente i nomi dei frammenti di codice HTML da visualizare
     */
    public function add(array|string|null $files, bool $both = false)
    {
        if ($files !== null)
        {
            if (is_array($files)) 
                $filenames = $files;
            else
                $filenames = [$files];

            foreach ( $this->modules as $module_name)
            {
                foreach ( $this->html_names as $html_name)
                {
                    foreach ($filenames as $filename)
                    {
                        foreach([$module_name,'Base'] as $module)
                        {
                            $path = realpath(__DIR__.'/../../../Modules/'.$module.'/View/'.$html_name);
                            if ($path)
                            {
                                $fullpath_filename = $path . '/' . $filename;
                                if (file_exists($fullpath_filename))
                                {
                                    $this->filenames[] = $fullpath_filename;  
                                    break;
                                }
                            }
                        }
                    }        
                }
            }
        }
    }


    /**
     * Spedisce il codice HTML
     *
     * @param string|null $cachefilename
     */
    public function send() : string
    {
        global $user, $controller;

        $output = '';

//        if ($this->with_adjust_html) 
//          ob_start("AdjustHtmlCode");
//        else //        if ( !ob_start("ob_gzhandler") ) 
          // ob_start("sanitize_output");
        ob_start();

        foreach($this->filenames as $filename)
        {
            include($filename);
            $output .= ob_get_contents();
            ob_flush(); // scarica il buffer ad ogni include per non far attendere troppo
        }
        
        ob_end_clean();

        return $output;
    }
};


