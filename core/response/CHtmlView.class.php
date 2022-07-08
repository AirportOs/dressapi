<?php
/**
 * 
 * DressAPI
 * @version 1.1
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * User authentication and authorization 
 */

namespace DressApi\core\response;

use DressApi\core\dbms\CMySqlComposer as CSqlComposer;
use DressApi\core\dbms\CMySqlDB as CDB;
use DressApi\modules\base\CBaseController;

class CHtmlView
{
    protected mixed $data = null; // data elements
    protected array $tv = [];   // template variables for replacing in the html code
    protected array $page_info = [];
    protected array $modules = [];
    protected array $html_foldernames = [];
    protected array $filenames = [];
    protected array $inline_begin_js = []; // inline javascript on header
    protected array $inline_end_js = [];   // inline javascript on end of body
    protected array $inline_css = [];      // inline css files
    protected string $css_folder = 'default';

    public function __construct(mixed $data, string $module_name = 'base', $html_foldername = 'default', $css_folder =  'default')
    {
        global $cache;

        $this->data = $data;

        $this->modules = [$module_name=>$module_name];

        $this->html_foldernames = ['default'=>'default'];
        $this->html_foldernames[$html_foldername] = $html_foldername;

        $this->css_folder = $css_folder;

        // INFO => app => module => element

        $this->page_info['app'] = ['title'=>DEFAULT_PAGE_TITLE,'description'=>DEFAULT_PAGE_DESCRIPTION];

        if ($cache)
        {
            $cache->setArea($module_name);
            $this->page_info['module'] = $cache->get('module_info');
        }

        if (!$this->page_info['module'])
        {
            // Info of web site
            $sql = new CSqlComposer();
            $sql->from('module');        
            $sql->select('title,description');
            $sql->where("name='$module_name'");

            $db = new CDB();
            $db->query($sql);
            $this->page_info['module'] = $db->getFetchAssoc();

            if (!$this->page_info['module'])
                $this->page_info['module'] =  $this->page_info['app'];
            
            if ($cache) 
                $cache->set('module_info',$this->page_info['module']);
        }

        foreach(($this->data['structure'] ?? []) as $elem_struct)
            if ($elem_struct['ref'])
            {
                // TO DO (example: "nodetype:id-name")
                list($rel_table,$sitems) = explode(':', $elem_struct['ref']);

                $items = explode('-',$sitems);
                // Info of web site
                $sql = new CSqlComposer();
                $sql->from($rel_table);        
                $sql->select(implode(',',$items));
                $sql->paging(1, MAX_ELEMENTS_RELATED_TABLE);

                $db = new CDB();
                $db->query($sql);
                $this->data['related_tables'][$rel_table] = $db->getArrayByName($items[0],$items[1]);
            }

        if (isset($data['elements']) && count($data['elements'])==1)
            $this->page_info['element'] = $data['elements'][0];
        else
            $this->page_info['element'] = $this->page_info['module'];
    }


    /**
     * Accoda un template alla lista di quelli da visualizzare
     *
     * @param array|string|null $files o array contenente i nomi dei frammenti di codice HTML da visualizare
     */
    public function add(array|string|null $files)
    {
        if ($files !== null)
        {
            if (is_array($files)) 
                $filenames = $files;
            else
                $filenames = [$files];

            foreach ( $this->modules as $module_name)
            {
                foreach ( $this->html_foldernames as $html_foldername)
                {
                    foreach ($filenames as $filename)
                    {
                        $founds = ['html'=>'','css'=>'','begin_js'=>'','end_js'=>''];
                        foreach([$module_name,'base'] as $current_module)
                        {
                            $path = realpath(__DIR__.'/../../modules/'.$current_module.'/views/'.$html_foldername);
                            if ($path)
                            {
                                // import php code before HTML TEMPLATE if exists

                                if (!$founds['html'])
                                {
                                    $fullpath_filename = $path.'/'.$filename; 
                                    if(file_exists($fullpath_filename))
                                        $founds['html'] = $fullpath_filename;
                                }

                                if (!$founds['css'])
                                {
                                    $css_file = $path.'/inline-assets/css/'.$this->css_folder.'/'.str_replace('tmpl.php','css',$filename);
                                    if (file_exists($css_file))
                                        $founds['css'] = $css_file;
                                }

                                if (!$founds['begin_js'])
                                {
                                    $js_file = str_replace('.tmpl.php','-Begin.js',$path.'/inline-assets/js/'.$filename);
                                    if (file_exists($js_file))
                                        $founds['begin_js'] = $js_file;
                                }

                                if (!$founds['end_js'])
                                {
                                    $js_file = str_replace('.tmpl.php','-End.js',$path.'/inline-assets/js/'.$filename);
                                    if (file_exists($js_file))
                                        $founds['end_js'] = $js_file;
                                }
                            }
                        }
                        if ($founds['html']) 
                            $this->filenames[] = $founds['html'];
                        if ($founds['css']) 
                            $this->inline_css[] = $founds['css'];
                        if ($founds['begin_js']) 
                            $this->inline_begin_js[] = $founds['begin_js'];
                        if ($founds['end_js']) 
                            $this->inline_end_js[] = $founds['end_js'];

                    }        
                }
            }
        }
    }


    /**
     * Transform CSS inline code
     * Quick minimization
     */
    protected function parseCssCode( string &$code )
    {
        $code = str_replace(["\n","   ","  ",]," ",$code);
        $code = str_replace([" {","} ","{ "," }",": ","; "],["{","}","{","}",":",";"],$code);
        if (trim($code)!="")
            $code = "\n<style>$code</style>\n";
    }


    /**
     * Transform JS inline code
     * Remove comments
     */
    protected function parseJsCode( string &$code )
    {
        $pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/';
        $code = trim(preg_replace($pattern, '', $code));
        if ($code!="")
            $code = "\n<script>\n$code\n</script>\n";
    }

    /**
     * Replace TAGS with appropriate HTML code
     * string $output the html code to transform
     */
    function replaceTags(string $output)
    {
        // Replace PHP code for print variable
        preg_match_all('/\{\{(.*?)\}\}/m', $output, $matches, PREG_SET_ORDER, 0);
        if ($matches)
        {
            foreach($matches as $m)
                if (isset($this->page_info['element'][$m[1]]))
                    $replacements[$m[0]] = $this->page_info['element'][$m[1]];
                else
                    $replacements[$m[0]] = '';
            
            if (count($replacements)>0)
                $output = str_replace(array_keys($replacements), array_values($replacements), $output);
        }
        
        // REPLACE CSS INLINE CODE
        $css_code = '';
        foreach($this->inline_css as $css_filename )
            $css_code .= file_get_contents($css_filename,true);
        $this->parseCssCode( $css_code );

        $js_begin_code = '';
        foreach($this->inline_begin_js as $js_filename )
            $js_begin_code .= file_get_contents($js_filename,true);
        $this->parseJsCode( $js_begin_code );

        $js_end_code = '';
        foreach($this->inline_end_js as $js_filename )
            $js_end_code .= file_get_contents($js_filename,true);
        $this->parseJsCode( $js_end_code );

        $output = str_replace([ '<!-- [[INLINE-STYLES]] -->',
                                '<!-- [[INLINE-BEGIN-JS]] -->',
                                '<!-- [[INLINE-END-JS]] -->'],
                                
                                [$css_code,
                                 $js_begin_code,
                                 $js_end_code,
                                ], 
                                
                            $output);
    

        // PostProcessor
        foreach ( $this->modules as $module_name)
            foreach ( $this->html_foldernames as $html_foldername)
                foreach([$module_name,'base'] as $current_module)
                {
                    $fullpath_filename = realpath(__DIR__.'/../../modules/'.$current_module.'/views/'.$html_foldername.'/processors/PostProcess.php');
                    if (file_exists($fullpath_filename))
                    {
                        include($fullpath_filename);
                        break;
                    }
                }
    
        // $output = ob_gzhandler( $output, 9 );
        return $output;
    }


    /**
     * Send the HTML code
     */
    public function send() : string
    {
        global $user, $cache, $controller, $request, $response;

        // PreProcessor
        foreach ( $this->modules as $module_name)
            foreach ( $this->html_foldernames as $html_foldername)
                foreach([$module_name,'base'] as $current_module)
                {
                    $fullpath_filename = realpath(__DIR__.'/../../modules/'.$current_module.'/views/'.$html_foldername.'/processors/PreProcess.php');
                    if ($fullpath_filename)
                    {
                        include($fullpath_filename);
                        break;
                    }
                }


        //
        // Start HTML Code
        //
        $output = '';

        ob_start(['self','replaceTags']);

        foreach($this->filenames as $filename)
        {
            include($filename);
            $output .= ob_get_contents();
            ob_flush(); // scarica il buffer ad ogni include per non far attendere troppo
        }
        
        ob_end_clean();
        //
        // End HTML Code
        //

        return '';
    }

    /**
     * Spedisce il codice HTML
     *
     * @param string|null $cachefilename
     */
    public function get() : string
    {
        $output = '';

        foreach($this->filenames as $filename)
            $output .= file_get_contents($filename);

        return $output;
    }    
};

