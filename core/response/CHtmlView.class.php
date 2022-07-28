<?php
/**
 * 
 * DressAPI
 * @version 2.0 alpha
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
            $this->page_info['module'] = $cache->getGlobal('module_info',$module_name);

        if (!($this->page_info['module'] ?? false))
        {
            // Info of web site
            $sql = new CSqlComposer();
            $sql->from(MODULE_TABLE);        
            $sql->select('title,description');
            $sql->where("name='$module_name'");

            $db = new CDB();
            $db->query($sql);
            $this->page_info['module'] = $db->getFetchAssoc();

            if (!$this->page_info['module'])
                $this->page_info['module'] =  $this->page_info['app'];
            
            if ($cache && $this->page_info['module']) 
                $cache->setGlobal('module_info', $this->page_info['module'], $module_name);
        }

        foreach(($this->data['structure'] ?? []) as $elem_struct)
            if ($elem_struct['ref'] ?? false)
            {
                // TO DO (example: "cmsnodetype:id-name")
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
     * Queues a template to the list of those to be displayed
     *
     * @param array|string|null $files filename or array containing the names of the HTML code snippets to be visualized
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
     * Replace {{TAGS}} with appropriate HTML code
     * @param string $output the html code to transform
     * @return string the $output with interpreted code
     */
    protected function replaceConditions(string $output)
    {
        $matches = [];


        // Permissions
        $re = '/\{\{([if]+)\s(permission::can_[read|update|insert|delete]+?)\}\}(.*?)\{\{end\s\1\s\2\}\}/s';
        preg_match_all($re, $output, $matches, PREG_SET_ORDER, 0);

        if ($matches)
        {
            foreach($matches as $m)
            {
                if ($m[1]=='if')
                {
                    list($dummy,$permission) = explode('::',trim($m[2]));
                    $code = ((isset($this->data['permissions'][$permission]))?(trim($m[3])):(''));

                    $output = str_replace($m[0], $code, $output);
                }
            }
        }

        return $output;
    }


    /**
     * Replace {{TAGS}} with appropriate HTML code
     * @param string $output the html code to transform
     * @return string the $output with interpreted code
     */
    protected function replaceBlocks(string $output, array $data = [], int $level = 1)
    {

        $matches = [];

        /* 
        * Parserize foreach
        * ie:
        {{foreach data::columns name}}
        <th>{{name}}</th>
        {{end foreach name}}
        */
        $re = '/\{\{([foreach|if]+)\s([:_a-zA-Z]*?)\s([_a-zA-Z]*?)\}\}(.*?)\{\{end\s\1+\s\3\}\}/s';
        preg_match_all($re, $output, $matches, PREG_SET_ORDER, 0);

        if ($matches)
        {
            foreach($matches as $m)
            {
                if ($m[1]=='foreach')
                {
                    $template = ltrim($m[4]);
                    $a = explode('::', $m[2]);
                    $res = '';
                    if ($data==[] && count($a)>1 && isset($this->{$a[0]}) && isset($this->{$a[0]}[$a[1]]))
                        $elements = $this->{$a[0]}[$a[1]];
                    else
                        $elements = $data;
                    if ($elements!=[])
                    {
                        foreach($elements as $name=>$elem)
                        {
                            $name = _T(ucwords(trim(str_replace(['id__','_'],' ',$name))));
                            if (is_array($elem))
                            {
                                $a = $this->replaceBlocks($template, $elem, $level+1);
                                foreach($elem as $nm=>$val)
                                {
                                    $label = $m[3].'::'.$nm;
                                    $a = str_replace(['{{'.$label.'}}','{{'.$m[3].'::name}}','{{'.$m[3].'::value}}'],
                                                     [$val,$nm,$val], 
                                                     $a);    
                                }

                                $res .= $a;
                            }
                            else
                                if (str_contains($template,'::'))
                                    $res .= str_replace(['{{'.$m[2].'::'.$m[3].'}}','{{'.$m[3].'::name}}','{{'.$m[3].'::value}}'],
                                                        [$elem,$name,$elem], $template);
                                else
                                    $res .= str_replace('{{'.$m[3].'}}',$elem, $template);
                        }
                        $output = str_replace($m[0], $res, $output);
                    }
                }
            }
        }
        
        return $output;
    }


    /**
     * Replace PHP code for print variable
     * @param string $output the html code to transform
     * @return string the $output with interpreted code
     */
    protected function replaceVars(string $output) : string
    {
        preg_match_all('/\{\{(.*?)\}\}/m', $output, $matches, PREG_SET_ORDER, 0);
        if ($matches)
        {
            foreach($matches as $m)
            {
                if (($m[1][0]=="'" && substr($m[1],-1)=="'"))
                    $output = str_replace($m[0],_T(trim($m[1],"'")),$output); // string
                elseif (substr($m[1],0,2)=="\'" && substr($m[1],-2)=="\'")
                    $output = str_replace($m[0],_T(trim($m[1],"\'")),$output); // string
                else
                {
                    $splitted = explode('::',$m[1]);
                    if (count($splitted)==3 && 
                        isset($this->{$splitted[0]})  && 
                        isset($this->{$splitted[0]}[$splitted[1]])  && 
                        isset($this->{$splitted[0]}[$splitted[1]][$splitted[2]])
                    )
                    {
                        $res = $this->{$splitted[0]}[$splitted[1]][$splitted[2]];
                        if (is_array($res) && isset($splitted[3]) && isset($res[$splitted[3]]) )
                            $replacements[$m[0]] = $res[$splitted[3]];
                        else
                            $replacements[$m[0]] = $res; // ie: from "data::elements::0::title" to $this->data[elements][0][title]
                    }
                    else
                        $replacements[$m[0]] = '';                    
                }

            }
            
            if (count($replacements)>0)
                $output = str_replace(array_keys($replacements), array_values($replacements), $output);
        }

        return $output;
    }

    
    /**
     * Replace TAGS with appropriate HTML code
     * string $output the html code to transform
     */
    protected function replaceTags(string $output)
    {
        // blocks as foreach
        $output = $this->replaceBlocks($output);

        // conditions
        $output = $this->replaceConditions($output);

        // variables and labels
        $output = $this->replaceVars($output);

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


    protected function createMenu( array &$menu, CSqlComposer $sql, $level = 1 )
    {
        global $user;

        // Create Menu
        $db = new CDB();
        $db->query($sql);
        $db->getDataTable($menu,CDB::DB_ASSOC,'name');

        if ($menu)
        {
            foreach($menu as &$item1)
            {
                $item1['submenu'] = [];

                $sql->select('*')
                ->where('id_parent='.$item1['id']);
                $db->query($sql);

                $db->getDataTable($item1['submenu'],CDB::DB_ASSOC,'name');
                $this->createMenu($item1['submenu'], $sql);


                if (isset($item1['query']) && $item1['query']!='')
                {
                    $q = $item1['query'];
                    if (str_contains($q,'{{TABLE LIST}}') || str_contains($q,'|'))
                    {
                        $qsql = new CSqlComposer();
                        if (str_contains($q,'{{TABLE LIST}}'))
                        {
                            $qsql = str_replace(['{{','}}'],'',$q);
                            $all_tables = [];
                            $db->getAllTables($all_tables);
                            foreach(array_keys($all_tables) as $tab)
                                $item1['submenu'][$tab] = ['name'=>$tab];
                        }
                        else
                        {
                            $v = explode('|',$q);
                            $qsql->select('*')
                                ->from($v[0])
                                ->where($v[1])
                                ->orderBy([$v[2] ?? 'id ASC']);
                            $db->query($qsql);    
                            $add_dynamic_voices = [];
                            $db->getDataTable($add_dynamic_voices, CDB::DB_ASSOC, 'name');
                            $this->createMenu( $add_dynamic_voices, $qsql);
                            $item1['submenu'] = array_merge($item1['submenu'],$add_dynamic_voices);
                        }
                        
                        foreach($item1['submenu'] as &$it)
                        {
                            if (!isset($it['url']))
                                $it['url'] = $item1['url'];
    
                            if (str_contains($it['url'],'{{'))
                                foreach($it as $nm=>$val)
                                    $it['url'] = str_replace('{{'.$nm.'}}', urlencode(str_replace(' ','-',$val)), $it['url']);
                        }
                    }
                }
    

                if (isset($item1['submenu']) && $item1['submenu']==[]) 
                    unset($item1['submenu']);
            }
        }

        if (isset($menu['submenu']) && $menu['submenu']==[]) 
            unset($menu['submenu']);

        return true;
    }


    /**
     * Send the HTML code
     */
    public function send() : string
    {
        global $user, $cache, $controller, $request, $response;


        if ($cache && $user->isAnonymous())
            $menu = $cache->getGlobal('menu');
        else
            $menu = null;
        if (!$menu)
        {
            $sql = new CSqlComposer();
            $sql->from(CMS_MENU_TABLE);        
            $sql->where('id_parent IS NULL');
            $sql->orderBy(['priority ASC']);
            
            $menu = [];
    
            $this->createMenu($menu, $sql);
            if ($cache && $user->isAnonymous()) 
                $cache->setGlobal('menu', $this->page_info['menu']);
        }

        // Config Data
        $sql = new CSqlComposer();
        $sql->from(CONFIG_TABLE);
        $db = new CDB();        
        $db->query($sql);
        $CONFIG = $db->getArrayByName('name','val');

        // PreProcessor
        foreach ( $this->modules as $module_name)
            foreach ( $this->html_foldernames as $html_foldername)
                foreach(['base',$module_name] as $current_module)
                {
                    $fullpath_filename = realpath(__DIR__.'/../../modules/'.$current_module.'/views/'.$html_foldername.'/processors/PreProcess.php');
                    if ($fullpath_filename)
                        include($fullpath_filename);
                }


        //
        // Start HTML Code
        //
        $output = '';

        ob_start(['self','replaceTags']);

        foreach($this->filenames as $frame_filename)
        {
            include($frame_filename);
            $output .= ob_get_contents();
            ob_flush(); // Empties the buffer for each include so that it doesn't wait too long
        }
        
        ob_end_clean();
        //
        // End HTML Code
        //

        return '';
    }

    /**
     * Sends out the HTML code
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


