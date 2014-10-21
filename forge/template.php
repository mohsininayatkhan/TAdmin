<?php namespace Forge;

/**
 * Class that parses html templates
 * It replaces template variables and functions (enclosed in {}) with php code.
 *
 * Variables:
 *    Print variable:   {$var_name}
 *    Set variable:     {set name="var_name" value="value"}
 *    Unset variable:   {unset name="$var_name"}
 *    
 * Control Structures:
 *    If:  {if expression} text1 {elseif expression} text2 {else} text3 {/if}
 *    Foreach:  {foreach from="array_name" [key="key"] value="value"}
 *              {if first}  ... {else} ... {/if}
 *              {if last}   ... {else} ... {/if}
 *              {if even}   ... {else} ... {/if}
 *              {if odd}    ... {else} ... {/if}
 *              {if !first} ... {elseif last} ... {/if}
 *              {if !last}  ... {elseif even} ... {/if}
 *              {/foreach}
 *    Cycle:  {cycle loops="number_of_loops"} ... {/cycle}
 *    
 * Includes:
 *    Include template:  {include_file="file_name" [relativeCahcePath="path_into_cache_dir"]}
 *    Include PHP-file:  {include_php="file_name.php"}
 *    
 * Miscellaneous:
 *    Comments:         {* the comment *}
 *    Insert PHP-code:  {php}  ... {/php}
 *    HTML to readable: {code} ... {/code}
 *    Internal debug:   {debug $var_name} or {dbg $var_name}
 **/
class Template
{
    /**
     * Template variables
     * @var array
     */
    var $vars = array();
    
    /**
     * Template custom functions
     * @var array
     */
    var $tpl_functions = array();
    
    /**
     * Extension of the file where current page will be compiled to
     * @var string
     */
    var $cache_file_extension = 'php';
    
    /**
     * Info of source which data/content feTpl currently parses
     * @var array
     */
    var $source_info = array();
    
    /**
     * Variable to store temporial class objects during page building
     * @var object
     */
    var $class_obj = null;
    
    /**
     * Cahce journal file name 
     * @var string
     */
    var $cache_journal_file_name = "cache_journal.cj";
    
    /**
     * Array of page elements (members) that will be written to a cache jornal.
     * If any member will be changed be CMS user, whole record in jornal and phisical 
     * cache file will be deleted. 
     * @var array
     */
    var $cache_journal_members = array();
    
    /**
     * Template namespaces
     * @var array
     */
    var $NS = array();
    
    /**
     * Template namespaces depth
     * @var int
     */
    var $NSi = 0;
    
    /**
     * Block of pages that do not need to be parsed is logged here and then just outputted when page is complete
     * @var array
     */
    var $non_parseable_blocks = array();
    
    /**
     * Data variables found in data blocks
     */
    
    var $data_variables = array();
    
    /**
     * List of PHP timezones identifiers
     */
    public static $php_timezones = array(
        '-1200' => 'Etc/GMT+12',
        '-1100' => 'Etc/GMT+11',
        '-1000' => 'Etc/GMT+10',
        '-0900' => 'Etc/GMT+9',
        '-0800' => 'Etc/GMT+8',
        '-0700' => 'Etc/GMT+7',
        '-0600' => 'Etc/GMT+6',
        '-0500' => 'Etc/GMT+5',
        '-0400' => 'Etc/GMT+4',
        '-0300' => 'Etc/GMT+3',
        '-0200' => 'Etc/GMT+2',
        '-0100' => 'Etc/GMT+1',
        '+0000' => 'Etc/GMT+0',
        '+0100' => 'Etc/GMT-1',
        '+0200' => 'Etc/GMT-2',
        '+0300' => 'Etc/GMT-3',
        '+0330' => 'Asia/Tehran',
        '+0400' => 'Etc/GMT-4',
        '+0500' => 'Etc/GMT-5',
        '+0600' => 'Etc/GMT-6',
        '+0700' => 'Etc/GMT-7',
        '+0800' => 'Etc/GMT-8',
        '+0900' => 'Etc/GMT-9',
        '+1000' => 'Etc/GMT-10',
        '+1100' => 'Etc/GMT-11',
        '+1200' => 'Etc/GMT-12',
    );
    
     /**
     * Constructor
     * 
     * Constructor builds a list of custom functions that 
     * will be called from tpls like {func_name func_params}
     * 
     * Sample:
     * First lets add function to current class:
     * 
     * [var $tpl_func_fname_alias = "alias";]  // 'fname' is a function name
     * function tpl_func_fname()              
     * {
     *     $args = func_get_args();
     *     ...
     *     echo 'something to show';
     * }
     * 
     * After that 'tpl_func_fname' function could be called 
     * from tpl as: {tpl_func_fname $params}. If 'var $tpl_func_fname_alias'
     * assigned before function, then this function will be called
     * from tpl as: {alias $params}.
     **/
    function __construct() 
    {
      // get all functions
      $functions = get_class_methods($this);
      
      // add 'tpl_func_funcname' functions
      foreach ($functions as $function) {
         
         if (preg_match('~^tpl_func_([a-zA-Z0-9_]+)~', $function, $alias) && isset($alias[1])) {
            
                $this->tpl_functions[] = array(
                    'name'      => $function, 
                    'alias'     => $alias[1]
                );

         }
      }
    }
     
    // ------------------------------ Main methods ------------------------------
    
    /**
     * Displays or returns parsed data
     * 
     * @param array $source         array that contains data to parse and information about
     *                              the source which data is going to be parsed.
     * @param string $cache_path    path in 'tmp/cache' where parsed data will be stored
     *                              as a file (with name of source) and used by caching system.
     * @param array $tpl_vars       variables that can be accessed from a template using {$var_name}.
     * @param bool $use_cache       when true passed data will be putted into a file in cache and 
     *                              then read by caching system. When false data will be shown
     *                              using 'echo' function.         
     * @return mixed
     **/
    
    // REMOVED CACHING
    //function display($source, $cache_path = '', $tpl_vars = array(), $use_cache = true) 
    
    /**
     * Checks if file in cache exists and it's modification date isn't older
     * then source file modification date
     * 
     * @param string $cache_path    path to a file in cache
     * @return bool
     **/
    function check_cache($cache_path) 
    {
        global $RC;   
        
        // check if tpl exists in cache
        if (!file_exists($cache_path)) {
            return false;            
        }
        
        // check file modification date
        if (!isset($this->source_info['m_dt']) || filemtime($cache_path) != $this->source_info['m_dt']) {
         return false;
        }
        
        return true;
    }
    
    /**
     * Writes data to a file in cache
     * 
     * @param string $data_to_write     data to be written
     * @param string $cache_path        path to a file in cache
     * @return void
     **/
    function write_cache($data_to_write, $cache_path)
    {
      global $RC;
      
        // check if cache file exists
        if (!file_exists($cache_path)) {
            
            // create dir in cache
            $relative_path = str_replace($RC->conf['paths']['cache'], '', dirname($cache_path));
            $dirs = explode("/", substr($relative_path, 1));
            $root_path = $RC->conf['paths']['cache']."/";
            
            $old_umask  = umask(0);
            foreach ($dirs as $dir) {
                if (!file_exists($root_path.$dir)) {
                    if (!mkdir($root_path.$dir, 0777)) {
                        $RC->log->write(RC_ERROR, "write_cache: failed to create dir '".$root_path.$dir."' (probalby wrong permissions set).", get_class());
                        return;
                    }
                }
                $root_path .= $dir."/";
            }
            umask ($old_umask);
        }
        
        // generate data that will be written to a cache file
        $data  = '<?php /*'; 
        $data .= $this->source_info['m_dt'];  // modification date should be the same as source has
        $data .= "*/ ?>\r\n";
        $data .= $data_to_write;              // parsed page data
        
        // create file in cache
        $fp = @fopen($cache_path, 'w');
        if (!$fp) {
         $RC->log->write(RC_ERROR, "write_cache: failed to create file '".$cache_path."'.", get_class());
            return;
        }
        if (!fwrite($fp, $data)) {
         $RC->log->write(RC_ERROR, "write_cache: failed to write to a file '".$cache_path."'.", get_class());
            return;
        }
        fclose($fp);
        
        // generate data that will be written to a cache journal
        /*$data = $cache_path." "."(".implode(");(", $this->cache_journal_members).");\n"; 
        
        // create cache journal
        $journal_path = $RC->conf['paths']['cache']."/".$this->cache_journal_file_name;
        $fp = @fopen($journal_path, 'a');
        if (!$fp) {
            $RC->log->write(RC_ERROR, "write_cache: failed to create file '".$cache_path."'.", get_class());
            return;
        }
        if (!fwrite($fp, $data)) {
            $RC->log->write(RC_ERROR, "write_cache: failed to write to a file '".$cache_path."'.", get_class());
            return;
        }
        fclose($fp);*/
        
        // set file umask
        $old_umask = umask(0);
        @chmod($cache_path, 0777);
        //@chmod($journal_path, 0777);
        umask($old_umask);
    }
    
    /**
     * Reads data from a file in cache
     * 
     * @param string $cache_path        path to a file in cache
     * @return void
     **/
    function read_cache($cache_path)
    {
        global $RC;
        
        if (!file_exists($cache_path)) {
         $RC->log->write(RC_ERROR, "read_cache: file '".$cache_path."' does not exist.", get_class());
            return '';
        }
        
        try {
            $data = implode('', @file($cache_path));
            // remove modification date (if exists)
            $data = preg_replace( '~^<\?php /\*\d+\*/ \?>~', '', $data);
        }catch (Exception $e) {
            $RC->log->write(RC_ERROR, "read_cache: failed to load file content from: '$cache_path'. Exception message: ".$e->getMessage(), get_class());
            return '';
        }
        
        return $data;
    }
    
    /**
     * Clears file(s) in cache
     * 
     * @param string $cache_path    path to a file in cache. If param not passed all cache
     *                              for current entity will be deleted
     * @return bool
     **/
    function clear_cache($cache_path = '')
    {
        global $RC;
        
        if ($cache_path) { // delete a single cache file
         return @unlink($cache_path);
        } else { // delete all cache files of current entity
            return $RC->delete_dir($RC->conf['paths']['cache']."/".$RC->entity);
        }
    }
    
    /**
     * Adds a record to a cache journal.
     * 
     * Cache journal is stored in tmp/cache and it's name defined in: 
     * $feTpl->$cache_journal_file_name propery.
     * 
     * Record format is: 
     *  path_in_cache ("e1p1","e1p2",...,"e1pM");...("eNp1","eNp2",...,"eNpM");
     *  where 'e' means element and 'p' means property
     * 
     * @param mixed $class_obj      class that stores an element that should be checked
     * @param string $source_type   source type (i.e. page, block, ...)
     * @param string $source_info   source info (i.e. name, entity, module)
     * @return bool
     **/
    function add_to_cache_journal($class_obj, $source_type, $source_info) 
    {
      global $RC;
      
        if (isset($class_obj->no_cache)) {
            return true;
        }
        
        $element_obj_name = "obj_".$source_type;
        $element_obj = $RC->get_object($class_obj->$element_obj_name, $source_info);
        
        $journal_member = '"'.implode('","', $element_obj).'"';
        
        if (!in_array($journal_member, $this->cache_journal_members)) {
            $this->cache_journal_members[] = $journal_member;
        }
        
        return true;
    }
    
    function parse($page_content, $iteration = 0)
    {
      // prepare regexp
        $search  = array(
            '!\\\\\{!',                           // '\{' to '&#123;'
            '!\\\\\}!',                           // '\{' to '&#125;'
            '!\\\\\$!',                           // '\$' to '&#36;'
            '!\\\\<!',                            // '\<' to '&lt;'
            '!\\\\>!',                            // '\>' to '&gt;'
            /*'!<\?php.*\?>!s',                   // cut PHP-code*/
            '!\{php\s*\}(.*?)\{/php\s*\}!is',     // {php} ... {/php}
            '!\{code\s*\}(.*?)\{/code\s*\}!is',   // {code} ... {/code}
            '!\$([a-z0-9_]+?)(\b)!i',             // {$varName} -> $RC->tpl->Vars['varName']
            '!(\[=([a-z0-9_]+?)\])!i',            // [=varName] -> $RC->tpl->data_variables['varName']
            // {if first}, {elseif first}, {if last}, {elseif last}, {if even}, {elseif even}, {if odd}, {elseif odd}
            // {if !first}, {elseif !first}, {if !last}, {elseif !last}, {if !even}, {elseif !even}, {if !odd}, {elseif !odd}
            '!\{(if|elseif)\s+(\!)?(first|last|even|odd)\s*\}!i',
            '!\{(\$[^}]+?)\s*\}!i',               // search for {$...} -> <?php echo $...
            '!\{if\s+([^}]+?)\}!i',               // search for {if expr} -> <?php if (expr);
            '!\{elseif\s+([^}]+)\}!i',            // search for {elseif expr}
            '!\{else\s*\}!i',                     // search for {else}
            '!\{/if\s*\}!i',                      // search for {/if}
            // {cycle loops="loops"}
            '!\{cycle\s+loops=(\'|")?(\d+?|[^\}]+?)(\'|")?\s*\}!i',
            // {/cycle}
            '!\{/cycle\s*\}!i', 
            // {set name="name" value="value"}
            '!\{set\s+name=(\'|")?([^\s]+?)(\'|")?\s+value=([^\}]+?)\s*\}!i',
            // {unset name="name"}
            '!\{unset\s+name=(\'|")?([^\}]+?)(\'|")?\s*\}!i',  
            '!\{\*([^\*]+?)\*\}!s',               // {* comment *} -> <?php/* comment */
            // {foreach from= value=}
            '!\{foreach\s+from=(\'|")?([a-z0-9_]+)([^\s]*?)(\'|")?\s+value=(\'|")?([^\s]+?)(\'|")?\s*\}!i',
            // {foreach from= key= value=}
            '!\{foreach\s+from=(\'|")?([a-z0-9_]+)([^\s]*?)(\'|")?\s+key=(\'|")?([^\s]+?)(\'|")?\s+value=(\'|")?([^\s]+?)(\'|")?\s*\}!i',
            // {/foreach}
            '!\{/foreach\s*\}!i',
            // {include file= relativeCachePath= }
            '!\{include\s+file=(\'|")?([^\s]+?)(\'|")?\s+relativeCachePath=(\'|")?([^\s]+?)(\'|")?\s*\}!i',
            // {include file= }
            '!\{include\s+file=(\'|")?([^\\1]+?)(\'|")?\s*\}!i',
            // {include php="fileName.php"}
            '!\{include\s+php=(\'|")?([^\s]+?)(\'|")?\s*\}!i',
            // {debug/dbg $varName}
            '!\{(debug|dbg)\s+([^\}]+?)\s*\}!i',
            '!&#36;!', //passing $
            // {#BLOCK_NAME} -> $RC->tpl->parse_page_blocks('BLOCK_NAME')
            '!\{#([a-z0-9_]+?)(\b)\}!i', 
            // {RT ROTATIOR_ID[:ELEMENTS_NUM]} -> $RC->tpl->parse_rotator('ROTATIOR_ID[:ELEMENTS_NUM]')
            '!\{RT\s(\d+)(:(\d+))?\}!i', 
            // {NL NEWS_LIST_ID[:ELEMENTS_PER_PAGE[:PAGES_LIMIT]]} -> $RC->tpl->parse_news(NEWS_LIST_ID, ELEMENTS_PER_PAGE, PAGES_LIMIT)
            '!\\{NL\s(\d+)(:(\d+))?(:(\d+))?\}!i',
            // {MN MENUS_ID[:START_LEVEL[:NUMBER_OF_LEVELS_TO_DIG]]} -> $RC->tpl->parse_news(MENUS_ID, START_LEVEL, NUMBER_OF_LEVELS_TO_DIG)
            '!\\{MN\s(\d+)(:(\d+))?(:(\d+))?\}!i',
            // {HP HELP_ID} -> $RC->tpl->parse_help('HELP_ID', 'START_LEVEL', 'OUTPUT_LEVELS')
            '~\{HP\s(\d+)(:(\d+))?(:(\d+))?\}~i', 
            // {CR VALUE:SOURCE_CURRENCY_CODE:DESTINATION_CURRENCY_CODE[:OUTPUT_DIGITS]} -> $RC->tpl->parse_currenct(VALUE, 'SOURCE_CURRENCY_CODE', 'DESTINATION_CURRENCY_CODE', OUTPUT_DIGITS)
            '~\{CR\s(\d+):([a-zA-Z0-9]+):([a-zA-Z0-9]+)(:(\d+))?\}~i',             
            // {FL FILES_ID[:NUMBER_OF_ITEMS_TO_OUTPUT[:ELEMENTS_ID]]} -> $RC->tpl->parse_files(FILES_ID, 'NUMBER_OF_ITEMS_TO_OUTPUT', 'ELEMENTS_ID')
            '~\{FL\s(\d+)(:([a-zA-Z0-9]+))?(:(\d+))?\}~i',             
        );

        $replace = array (
            '&#123;',   // \{ -> '&#123;'
            '&#125;',   // \} -> '&#125;'
            '&#36;',    // \$ -> '&#36;'
            '&lt;',     // \< to &lt;
            '&gt;',     // \> to &gt;
            /*'',         // cut PHP-code*/
            // {php} ... {/php}
            '<?php \\1 ?>',
            // {code} ... {/code}
            '<? echo htmlspecialchars("\\1", ENT_QUOTES); ?>',
            '$RC->tpl->vars[\'\\1\']\\2', // $varName -> $RC->tpl->vars['varName']
            '$RC->tpl->data_variables['.$iteration.'][\'\\2\']\\3', // =varName -> $RC->tpl->data_variables['iteration_number']['varName']
            // if | elseif  +  first | last | even | odd
            '<?php \\1 (\\2$RC->tpl->NS[$RC->tpl->NSi][\'\\3\']): ?>',
            // {$varName}
            '<?php echo \\1;?>',
            // {if expr}
            '<?php if (\\1): ?>',
            // {elseif expr}
            '<?php elseif (\\1): ?>',
            // {else}
            '<?php else: ?>',
            // {/if}
            '<?php endif; ?>',
            // {cycle loops="loops"}
            '<?php '.
                '$RC->tpl->NSi++; '.
                'for ($RC->tpl->NS[$RC->tpl->NSi][\'i\']=1;$RC->tpl->NS[$RC->tpl->NSi][\'i\']<=\\2;$RC->tpl->NS[$RC->tpl->NSi][\'i\']++) { '.
                '$RC->tpl->vars[\'iteration\'] = $RC->tpl->NS[$RC->tpl->NSi][\'i\']; ?>',
            // {/cycle}
            '<?php } '.
                'unset ($RC->tpl->NS[$RC->tpl->NSi]); '.
                '$RC->tpl->NSi--; ?>',
            // {set name="name" value="value"}
            '<?php $RC->tpl->vars[\'\\2\'] = \\4; ?>',
            // {unset name="name"}
            '<?php unset ($RC->tpl->vars[\'\\2\']); ?>',
            // {* comment *}
            '<?php /*\\1*/ ?>',
            // foreach without key:
            '<?php '.
                '$RC->tpl->NSi++; '.
                '$RC->tpl->NS[$RC->tpl->NSi][\'i\']=$RC->tpl->NS[$RC->tpl->NSi][\'first\']=1; '.
                '$RC->tpl->NS[$RC->tpl->NSi][\'_last\']=count($RC->tpl->vars[\'\\2\']\\3); '.
                'foreach ($RC->tpl->vars[\'\\2\']\\3 as $RC->tpl->vars[\'\\6\']) { '.
                                '$RC->tpl->NS[$RC->tpl->NSi][\'even\'] = ($RC->tpl->NS[$RC->tpl->NSi][\'i\']%2 == 0); '.
                                '$RC->tpl->NS[$RC->tpl->NSi][\'odd\'] = ($RC->tpl->NS[$RC->tpl->NSi][\'i\']%2 == 1); '.
                                '$RC->tpl->NS[$RC->tpl->NSi][\'last\'] = ($RC->tpl->NS[$RC->tpl->NSi][\'_last\'] == $RC->tpl->NS[$RC->tpl->NSi][\'i\']); '.
                                '$RC->tpl->vars[\'iteration\'] = $RC->tpl->NS[$RC->tpl->NSi][\'i\']; ?>',
            // foreach with key
            '<?php '.
                 '$RC->tpl->NSi++; '.
                 '$RC->tpl->NS[$RC->tpl->NSi][\'i\']=$RC->tpl->NS[$RC->tpl->NSi][\'first\']=1; '.
                 '$RC->tpl->NS[$RC->tpl->NSi][\'_last\']=count($RC->tpl->vars[\'\\2\']\\3); '.
                 'foreach ($RC->tpl->vars[\'\\2\']\\3 as $RC->tpl->vars[\'\\6\']=>$RC->tpl->vars[\'\\9\']) { '.
                     '$RC->tpl->NS[$RC->tpl->NSi][\'even\'] = ($RC->tpl->NS[$RC->tpl->NSi][\'i\']%2 == 0); '.
                     '$RC->tpl->NS[$RC->tpl->NSi][\'odd\'] = ($RC->tpl->NS[$RC->tpl->NSi][\'i\']%2 == 1); '.
                     '$RC->tpl->NS[$RC->tpl->NSi][\'last\'] = ($RC->tpl->NS[$RC->tpl->NSi][\'_last\'] == $RC->tpl->NS[$RC->tpl->NSi][\'i\']); '.
                     '$RC->tpl->vars[\'iteration\'] = $RC->tpl->NS[$RC->tpl->NSi][\'i\']; ?>',
            // {/foreach}
            '<?php '.
                  '$RC->tpl->NS[$RC->tpl->NSi][\'i\']++; '.
                  '$RC->tpl->NS[$RC->tpl->NSi][\'first\']=0; } '.
                  'unset ($RC->tpl->NS[$RC->tpl->NSi]); '.
                  '$RC->tpl->NSi--; ?>',
            // {include file="templateName" relativeCachePath="relCachePath"}
            '<?php '.
                   '$incTpl=$this; '.
                   '$prevCP=$incTpl->setRelativeCachePath(\\4\\5\\4); '.
                   'echo $incTpl->execute (\\1\\2\\1, $this->vars); '.
                   '$incTpl->setRelativeCachePath($prevCP); '.
                   'unset ($incTpl); ?>',
            // {include file="templateName"}
            '<?php '.
                    '$RC->tpl->include_file(\\1\\2\\1, $RC->tpl->vars); ?>',
            // {include php="php_file"}
            '<?php '.
                   'if (!file_exists(\\1\\2\\1)) { echo \'File `\\2` is not found.\'; } else { '.
                   'include (\'\\2\'); } ?>',
            //{debug/dbg $varName}
            '<?php $RC->tpl->_debug(\\2); ?>',
            '$',
            // {#BLOCK_NAME}
            '<?php $RC->tpl->parse_page_blocks(\'\\1\'); ?>',
            // {RT ROTATIOR_ID[:ELEMENTS_NUM]}
            '<?php $RC->tpl->parse_rotator(\'\\1\', \'\\3\'); ?>',
            // {NL NEWS_LIST_ID[:ELEMENTS_PER_PAGE[:PAGES_LIMIT]]}
            '<?php $RC->tpl->parse_news(\'\\1\', \'\\3\', \'\\5\'); ?>',
            // {MN MENUS_ID[:START_LEVEL[:NUMBER_OF_LEVELS_TO_DIG]]}
            '<?php $RC->tpl->parse_menus(\'\\1\', \'\\3\', \'\\5\'); ?>',
            // {HP HELP_ID[:START_LEVEL[:OUTPUT_LEVELS]]}
            '<?php $RC->tpl->parse_help(\'\\1\', \'\\3\', \'\\5\'); ?>',
            // {CR VALUE:SOURCE_CURRENCY_CODE:DESTINATION_CURRENCY_CODE[:OUTPUT_DIGITS]} 
            '<?php $RC->tpl->parse_currency(\\1, \'\\2\', \'\\3\', \'\\5\'); ?>',
            // {FL FILES_ID[:NUMBER_OF_ITEMS_TO_OUTPUT[:ELEMENTS_ID]]} 
            '<?php $RC->tpl->parse_files(\\1, \'\\3\', \'\\5\'); ?>',            
        );
        
        // replace tpl functions
        foreach ($this->tpl_functions as $value) {
            $name       = ($value['alias'] ? $value['alias'] : $value['name']);
            $search[]   = '!\{\s*'.$name.'\s+([^\}]+?)\s*\}!i';
            $replace[]  = '<?php $RC->tpl->'.$value['name'].'(\\1); ?>';
        }
        
        // parse data block, if found
        $page_content = $this->parse_data_block($page_content);
        
        // parse template content
        if (is_null($res = preg_replace($search, $replace, $page_content))) {
            $RC->log->write(RC_ERROR, "parse: failed to parse data of source: ".print_r($this->source_info, true).".", get_class());
            return '';
        }
        
        return $res;
    }
    
 
    /**
     * Parses page blocks
     *
     * @param string $block_name
     * @return parsed block
     */
    function parse_page_blocks($block_name)
    {
      global $RC;
      
      $module = 'Pages';
      
      if (!isset($this->class_obj[$module])) {
         // should be made more flexible
         
         if (!$RC->check_destination($RC->conf['paths']['classes']."/".$RC->entity."/".strtolower($module)."/class.".ucfirst($module).".php", ucfirst($module), 'get_page')) {
             $RC->maintenance->show();
         }
            $class_name = ucfirst($module);
            $this->class_obj[$module] = new $class_name();
      }
      
         $this->class_obj[$module]->get_page($RC->view->current_page, $block_name);

        return;
    }

    /**
     * Parses a rotator
     *
     * @param string $block_name
     * @return parsed block
     */
    function parse_rotator($id, $elements_num)
    {
        global $RC;
        
        // should be made more flexible too
        $module = 'Rotators';
        
        if (!isset($this->class_obj[$module])) {       
           if (!$RC->check_destination($RC->conf['paths']['classes']."/".$RC->entity."/".strtolower($module)."/class.".ucfirst($module).".php", ucfirst($module), 'get_rotator')) {
            $RC->log->write(RC_FATAL, "get_rotator: falied to access module '$module'.", get_class());
              $RC->maintenance->show();
           }
           $class_name = ucfirst($module);
         $class_obj = new $class_name();
        }
        
        $class_obj->get_rotator($id, $elements_num);

        return;
    }    
    
    /**
     * Parses news list
     *
     * @param string $block_name
     * @return parsed block
     */
    function parse_news($id, $elements_per_page = 0, $pages_per_output = 0)
    {
      global $RC;
      
      // should be made more flexible
      $module = 'News';
      
      if (!isset($this->class_obj[$module])) {
         
         if (!$RC->check_destination($RC->conf['paths']['classes']."/".$RC->entity."/".strtolower($module)."/class.".ucfirst($module).".php", ucfirst($module), 'get_news')) {
             $RC->maintenance->show();
         }
            $class_name = ucfirst($module);
            $this->class_obj[$module] = new $class_name();
      }
      
         $this->class_obj[$module]->get_news($id, $elements_per_page, $pages_per_output);

        return;
    }    
    
    /**
     * Parses news menus
     *
     * @param string $block_name
     * @return parsed block
     */
    function parse_menus($id, $start_level = 1, $output_levels = 0)
    {
      global $RC;
      
      // should be made more flexible
      $module = 'Menus';
      
      if (!isset($this->class_obj[$module])) {
         
         if (!$RC->check_destination($RC->conf['paths']['classes']."/".$RC->entity."/".strtolower($module)."/class.".ucfirst($module).".php", ucfirst($module), 'get_menus')) {
             $RC->maintenance->show();
         }
            $class_name = ucfirst($module);
            $this->class_obj[$module] = new $class_name();
      }
      
         $this->class_obj[$module]->get_menus($id, $start_level, $output_levels);

        return;
    } 
    
    /**
     * Parses help object
     */
    function parse_help($id, $start_level = 1, $output_levels = 0)
    {
        global $RC;
        
        // should be made more flexible
        $module = 'Help';
        
        if (!isset($this->class_obj[$module])) {
            
            if (!$RC->check_destination($RC->conf['paths']['classes']."/".$RC->entity."/".strtolower($module)."/class.".ucfirst($module).".php", ucfirst($module), 'get_help')) {
                $RC->maintenance->show();
            }
            $class_name = ucfirst($module);
            $this->class_obj[$module] = new $class_name();
        }
        
        $this->class_obj[$module]->get_help($id, $start_level, $output_levels);
        
        return;
    }         
    
    function parse_currency($value, $source, $destination, $digits)
    {
        global $RC;
        
        $digits = (int)$digits;
        
        // Set default round digits to 2
        $digits = $digits ? $digits : 2;
        
        // should be made more flexible
        $module = 'Currencies';
        
        if (!isset($this->class_obj[$module])) {
            
            if (!$RC->check_destination($RC->conf['paths']['classes']."/".$RC->entity."/".strtolower($module)."/class.".ucfirst($module).".php", ucfirst($module), 'convert_currency')) {
                $RC->maintenance->show();
            }
            $class_name = ucfirst($module);
            $this->class_obj[$module] = new $class_name();
        }
        
        $this->class_obj[$module]->convert_currency($value, $source, $destination, $digits);

        return;
    }   

    
           /**
     * Parses news list
     *
     * @param string $block_name
     * @return parsed block
     */
    function parse_files($id, $output_limit = 0, $id_files_elements = 0)
    {
      global $RC;
      
      // should be made more flexible
      $module = 'Files';
      
      if (!isset($this->class_obj[$module])) {
         
         if (!$RC->check_destination($RC->conf['paths']['classes']."/".$RC->entity."/".strtolower($module)."/class.".ucfirst($module).".php", ucfirst($module), 'get_files')) {
             $RC->maintenance->show();
         }
            $class_name = ucfirst($module);
            $this->class_obj[$module] = new $class_name();
      }
      
         $this->class_obj[$module]->get_files($id, $output_limit, $id_files_elements);

        return;
    }    

    
    function include_file($file_name) 
    {
        global $RC;
        
        $path = $RC->conf['paths']['classes']."/".$RC->entity."/".$RC->view->current_module."/tpls/$file_name";

        if (file_exists($path)) {
            if (!$fp = fopen($path, 'r')) {
                $RC->log->write(RC_CRITICAL, "include_file: cannot read file $path permission denied.", get_class());
                echo "[failure to include template $file_name]";
                return;
            }

            $data = file_get_contents($path);
            
            echo $data;
            return;
        }

        $path = $RC->conf['paths']['classes']."/".$RC->entity."/_tpls/$file_name";
        
        if (file_exists($path)) {
            if (!$fp = fopen($path, 'r')) {
                $RC->log->write(RC_CRITICAL, "include_file: cannot read file $path permission denied.", get_class());
                echo "[failure to include template $file_name]";
                return;
            }
            $data = file_get_contents($path);
            
            echo $data;
            return;
        }
        
        $RC->log->write(RC_CRITICAL, "include_file: cannot find file to include with name $file_name", get_class());
        echo "[failure to find file $file_name]";
        return;
    }
    
    function lock_non_parseable_blocks($page_content) 
    {
        global $RC;
        
         // prepare regexp
        $search  ='!\{non_parseable_block\s*\}(.*?)\{/non_parseable_block\s*\}!is';     // {non_parseable_block} ... {/non_parseable_block}
        
        if (is_null(preg_match_all($search, $page_content, $matches))) {
            $RC->log->write(RC_ERROR, "lock_non_parseable_block: failed to parse data of source: ".print_r($this->source_info, true).".", get_class());
            return false;
        }
        
        $i = count($this->non_parseable_blocks);
        if (isset($matches[1]) && $matches[1]) {
            foreach ($matches[1] as $block) {
                $this->non_parseable_blocks[$i] = $block;
                $page_content = str_replace('{non_parseable_block}'.$block.'{/non_parseable_block}', "{^non_parseable_block_$i}", $page_content);
                $i++;
            }
        }
        return $page_content;
    }
    
    function unlock_non_parseable_blocks($page_content) 
    {
        global $RC;
        if (!count($this->non_parseable_blocks)) {
            return $page_content;
        }
        
         // prepare regexp
        $search  = '!\{\^non_parseable_block_(\d*)\}!i';     // {non_parseable_block_100}
        
        if (is_null(preg_match_all($search, $page_content, $matches))) {
            $RC->log->write(RC_ERROR, "unlock_non_parsable_block: failed to parse data of source: ".print_r($this->source_info, true).".", get_class());
            return false;
        }
        
        if (!isset($matches[1]) || !$matches[1]) {
            $RC->log->write(RC_ERROR, "unlock_non_parsable_block: non parseable blocks are available, but cannot find any in template. Printout: ".print_r($this->non_parseable_blocks, 1), get_class());
            return $page_content;
        }
        
        foreach ($matches[1] as $block_number) {
            $page_content = str_replace("{^non_parseable_block_$block_number}", $this->non_parseable_blocks[$block_number], $page_content);
            //should be changed
       //unset($this->non_parseable_blocks[$block_number]);
        }
        
        return $page_content;

    }    
    
    // ------------------------------ Additional methods ------------------------------
    
    /**
     * Assigns multiple template variables passed as array
     *
     * @param mixed $vars variables array
     * @return void
     **/
    function assign($vars)
    {
        $this->vars = array_merge($this->vars, $vars);
    }

    /**
     * Assigns single template variable
     *
     * @param string $var
     * @param mixed $value
     * @return void
     **/
    function assignVar($var, $value)
    {
        $this->vars[$var] = $value;
    }

    /**
     * Adds reference to template
     *
     * @param string  $varName
     * @param mixed   &$value
     * @return void
     **/
    function assignRef($varName, &$value)
    {
        $this->vars[$varName] = $value;
    }

    /**
     * Determines if a variable is set
     *
     * @param string $varName
     * @return bool
     **/
    function is_set($varName)
    {
        return isset($this->vars[$varName]) ? true : false;
    }
    
    /**
     * Returns tpl function by name or alias
     *
     * @param string $name_or_alias
     * @return mixed
     **/
    /*function get_tpl_function($name_or_alias)
    {
        foreach ($this->tpl_functions as $func) {
         if ($func['name'] == "tpl_func_".$name_or_alias || $func['alias'] == $name_or_alias) {
            return $func;
         }
        }
        
        return false;
    }*/
    
    function parse_data_block($page_content) 
    {
        global $RC;
        
        $search = '~{data_block}([^\*]+?){/data_block}~';  // {data_block} ... {/data_block}
        
        if (is_null(preg_match_all($search, $page_content, $data_blocks))) {
            $RC->log->write(RC_ERROR, "parse_data_block: failed to parse data of source: ".print_r($this->source_info, true).".", get_class());
            return false;
        }
        
        if (!isset($data_blocks[1][0])) {
            return $page_content;
        }
        
        $data_chunks = preg_split($search, $page_content);

        unset($data_blocks[0]);
        $search_vars = '~\[([^\*]+?)\]={([^\*]+?)}=~i';   // [VARIABLE]={data}=
        unset($this->data_variables);
        
        foreach ($data_blocks[1] as $block_number=>$data_block) {
            
            if (is_null(preg_match_all($search_vars, $data_block, $variables))) {
                $RC->log->write(RC_ERROR, "parse_data_block: failed to parse data during second pass of source: ".print_r($this->source_info, true).".", get_class());
                return false;
            }
            
            if (!isset($variables[1])) {
                continue;
            }
            
            foreach ($variables[1] as $key=>$variable) {
                $this->data_variables[$block_number][$variable] = $variables[2][$key];
            }

            if (count($data_blocks[1]) > 1) {
                $data_chunks[$block_number] = $this->parse($data_chunks[$block_number], $block_number);
            } 
        }
        
        $page_content = implode('', $data_chunks);

        return $page_content;
    }
    
    
    function attach_header_footer($page_content)
    {
        return '{[=HEADER]}'.$page_content.'{[=FOOTER]}';
    }
    
    function deattach_header_footer($page_content)
    {
        return str_replace('{[=HEADER]}', '', str_replace('{[=FOOTER]}', '', $page_content));
    }
    
    // ------------------------------ Template functions ------------------------------
    // Rules of names creation is "tpl_func_" + alias name
    // Example: tpl_func_alias will match {alias(....)}
    
    // Locales
    function tpl_func__()
    {
      global $RC;
      
        $args = func_get_args();
        
        if (is_array($args) && count($args) > 1 && $args[1] == true) {
            echo call_user_func_array(array($RC->L, 'get'), $args[0]);
            return;
        }
        unset($args[1]);
        
        if ($RC->entity == 'admin') {
         echo call_user_func_array(array($RC->L, 'get'), $args);
            return;
        }
        
        if (!$RC->toolbar->is_editable('locales')) {
         $locale = call_user_func_array(array($RC->L, 'get'), $args);
            echo preg_replace("~^\[phrase \'[a-zA-Z0-9_ ]*\' ((not found)|(is empty))\]$~", '', trim($locale));
            return;    
        } 

        $locale = call_user_func_array(array($RC->L, 'get'), array_merge($args, array(true)));
        if (!is_array($locale)) {
         echo $locale;
            return;
        }
        $RC->tpl->vars['cms_locales'][] = array(
            'id'                => $locale['id'],
            'content'           => $locale['value'],
            'display_name'      => (strlen($locale['name']) < 33 ? $locale['name'] : substr($locale['name'], 0, 30).'...'),
            'auto_generated'    => $locale['auto_generated']
        );
        echo "{include file='cms_locale.tpl'}";
        
        return; 
    }
    
    function tpl_func_numberFormat()
    {
        $args = func_get_args();
        $args[1] = isset($args[1]) ? $args[1] : 2;
        $args[2] = ',';
        $args[3] = ' ';
        echo call_user_func_array('number_format', $args);
    }    
    
    function tpl_func_dateFormat()
    {
        $args = func_get_args();
        $time = $args[0];
        $format = isset($args[1]) ? $args[1] : null;
        $timezone = isset($args[2]) ? $args[2] : null;
        
        if (!$format) {
            //$format = '%Y-%m-%d %H:%M:%S%z'; We do not need timezone here
            $format = '%Y-%m-%d %H:%M:%S';
        }

        if (!$time) {
            $time = time();
        } elseif (!is_numeric($time)) {
            $time = @strtotime($time);
        }

        if ($timezone) {
            $_prev = date_default_timezone_get();
            date_default_timezone_set($this->php_timezones[$timezone]);
        }

        // replace timezone
        $format = str_replace(array('%z', '%Z'), date('O'), $format);

        // format time
        $res = @strftime($format, $time);

        if (isset($_prev)) {
            date_default_timezone_set($_prev);
        }

        echo $res;
        
    }  
    
    /**
     * Build block of params for POST
     *
     * @param array $data       array of params
     * @param string $prefix    prefix for name of params
     * @return string
     */
    function tpl_func_paramsPost($data, $prefix = '')
    {
        echo $this->params_build('post', $data, $prefix);
    }
    
    /**
     * Builds block of params for GET
     *
     * @param array $data       array of params
     * @param string $prefix    prefix for name of params
     * @param string $start     char to start with (&, ? or empty) - ? is default
     * @return string
     */
    function tpl_func_paramsGet($data, $prefix = '', $start = '?', $return = true)
    {
      
        $r = $this->params_build('get', $data, $prefix, $start);
        $r = $r ? $start.$r : '';
        if ($return) {
            return $r;
        } else {
            echo $r;
        }
    }
    
    /**
     * Makes tootip
     *
     * @param string $title     title of tooltip
     * @param string $hint      hint of tooltip
     * @param string $apply_locales in case of false tooltip will display contents without localization
     * @return string
     */
    function tpl_func_hint($title, $hint, $apply_locales = true, $params = 'class="tooltip"', $do_not_parse = false)
    {
        global $RC;
        
        if ($apply_locales) {
            echo ($do_not_parse ? '{non_parseable_block}' : '').'<a '.$params.' title="'.$RC->L->get($hint).'">'.$RC->L->get($title).'</a>'.($do_not_parse ? '{/non_parseable_block}' : '');
        } else {
            echo ($do_not_parse ? '{non_parseable_block}' : '').'<a '.$params.' title="'.str_replace('/', '', $hint).'">'.$title.'</a>'.($do_not_parse ? '{/non_parseable_block}' : '');
        }
    }
    
    function tpl_func_trafficFormat() {
      global $RC;
      
        $args = func_get_args();
        // size of Kbyte
        $args[1] = isset($args[1]) ? $args[1] : 1024;
        // Type of data
        $args[2] = isset($args[2]) ? $args[2] : 'b';
        
        if (!$args[1]) {
         echo 0;
         return;
        }
        
        if (!$args[0]) {
         echo '0 '.$args[2];
         return;
        }
        
        $kbytes = floor($args[0]/$args[1]);
        $mbytes = floor($kbytes/$args[1]);
        $gbytes = floor($mbytes/$args[1]);
        $tbytes = floor($gbytes/$args[1]);
        $pbytes = floor($tbytes/$args[1]);
        
        if ($pbytes) {
         echo number_format($tbytes, 0, ' ', '.').' '.$RC->L->get('P').$args[2];
         return;
        }
        if ($tbytes) {
         echo number_format($gbytes, 0, ' ', '.').' '.$RC->L->get('T').$args[2];
         return;
        }
        if ($gbytes) {
         echo number_format($mbytes, 0, ' ', '.').' '.$RC->L->get('G').$args[2];
         return;
        }
        if ($mbytes) {
         echo number_format($kbytes, 0, ' ', '.').' '.$RC->L->get('M').$args[2];
         return;
        }
        if ($kbytes) {
         echo number_format($args[0], 0, ' ', '.').' '.$RC->L->get('K').$args[2];
         return;
        }
        
        echo $args[0].' '.$args[2];
    }
    
    
    /**
     * Main handler for parameters building
     *
     * @param string $type      type: get / post
     * @param array $data       array of params
     * @param string $prefix    prefix for name of params
     * @param integer $_recurse recursion level
     * @return string
     */
    function params_build($type, $data, $prefix = '', $_recurse = 0)
    {
        if (!is_array($data)) {
            return null;
        }
        $r = array();
        
        foreach ($data as $k => $v) {
            if (is_null($v)) {
                continue;
            }
            $name = $prefix ? $prefix.'['.$k.']' : $k;
            if (is_array ($v)) {
                $r = array_merge($r, array($this->params_build($type, $v, $name, $_recurse+1)));
            } else {
                $v = (string)$v;
                if ($type == 'get') {
                    $r[] = urlencode($name).'='.urlencode($v);
                } else {
                    $r[] = '<input type="hidden" name="'.htmlspecialchars($name).'" value="'. htmlspecialchars($v) .'">';
                }
            }
        }
        if ($type == 'get') {
            $r = implode('&', $r);
        } else {
            $r = implode("\n", $r);
        }
        
        return $r;
    }      
    
    
}