<?php

/**
 * 模板
 *
 * @package Comm
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Comm;
class View implements \Yaf_View_Interface {
    
    /**
     * 模板目录
     * 
     * @var string
     */
    protected $_template_dir = '';
    
    /**
     * 是否是返回而不展示模板
     * 
     * @var boolean
     */
    protected $_return = false;
    
    /**
     * 模板变量
     * 
     * @var array
     */
    protected $_vars = array();
    
    /**
     * 模板模块名称临时存储
     * 
     * @var string
     */
    protected $_block_name = '';
    
    /**
     * 模板加载的模块模板临时存储
     * 
     * @var string
     */
    protected $_block_tpl = '';
    
    /**
     * 区块数据
     * 
     * @var array
     */
    protected $_block_content = array();
    
    /**
     * 要展示的HTML数据
     * 
     * @var string
     */
    protected $_display_html = '';
    
    /**
     * The setScriptPath purpose
     *
     * @param string $template_dir 模板目录的绝对路径，默认的Yaf_Dispatcher会设置此目录为application.directory  . "/views".
     *
     * @return void
     */
    public function setScriptPath($template_dir) {
        $this->_template_dir = $template_dir;
    }
    
    /**
     * The getScriptPath purpose
     *
     * @return void
    */
    public function getScriptPath() {
        return $this->_template_dir;
    }
    
    /**
     * 为视图引擎分配一个模板变量
     *
     * @param string $name
     * @param string $value
     *
     * @return bool
    */
    public function assign($name, $value = null) {
        if(!$name) {
            return false;
        }
        
        if(is_array($name)) {
            $this->_vars = array_merge($this->_vars, $name);
        } else {
            $this->_vars[$name] = $value;
        }
        return true;
    }
    
    /**
     * 渲染一个视图模板, 并直接输出给请求端
     *
     * @param string $tpl
     * @param array $tpl_vars
     *
     * @return bool
    */
    public function display($tpl, $tpl_vars = null) {
        $this->_return = false;
        $this->_process($tpl);
        return true;
    }
    
    /**
     * 渲染一个视图模板
     *
     * @param string $tpl
     * @param array $tpl_vars
     *
     * @return string
    */
    public function render($tpl, $tpl_vars = null) {
        $this->_return = true;
        return $this->_process($tpl);
    }
    
    /**
     * 处理模板
     * 
     * @param string $tpl 模板文件路径
     * 
     * @return void;
     */
    protected function _process($tpl) {
        $this->_display_html = '';
        \extract($this->_vars);
        include "{$this->_template_dir}/{$tpl}.phtml";
        $display_html = $this->_display_html;
        $this->_clear();
        return $display_html;
    }
    
    /**
     * 预定义一个区块
     * 
     * @param string $name
     */
    protected function _blockConfig($name) {
        if(isset($this->_block_content[$name])) {
            echo $this->_block_content[$name];
        }
    }
    
    /**
     * 开始填充一个区块
     * 
     * @param string $name
     */
    protected function _block($name) {
        if($this->_block_name) {
            throw new \Exception\Program('View block is not end.');
        }
        $this->_block_name = $name;
        \ob_start();
    }
    
    /**
     * 结束填充一个区块
     */
    protected function _blockEnd() {
        $this->_block_content[$this->_block_name] = \ob_get_contents();
        \ob_clean();
        $this->_block_name = null;
    }
    
    /**
     * 加载一个区块模板
     * 
     * @param string $tpl
     */
    protected function _blockLoad($tpl) {
        if($this->_return) {
            \ob_start();
        }
        
        include "{$tihs->_template_dir}/{$tpl}.phtml";
        
        if($this->_return) {
            $this->_display_html = \ob_get_contents();
            \ob_clean();
        }
    }
    
    /**
     * 清理相关变量
     * 
     * @return void
     */
    protected function _clear() {
        $this->_block_content = array();
        $this->_block_name = '';
        $this->_block_tpl = '';
        $this->_display_html = '';
        $this->_vars = array();
    }
    


//     /**
//      * 获取模板对象
//      * @param	string	$tpl_path	模板路径，默认值为默认module下的views
//      * @return	\Yaf_View_Simple
//      */
//     static public function getView($tpl_path = '') {
//         $tpl_path || $tpl_path = TPL_PATH;
//         $view = new \Yaf_View_Simple($tpl_path);
//         return $view;
//     }
    
    /**
     * 加载CSS
     * @param string $path
     * @param boolean $return
     * @return \mixed
     */
    static public function css($path, $with_version = true, $return = false) {
        $href = $path;
        if($with_version) {
            $ver = self::cssVer();
            $href .= (strpos($href, '?') === false ? '?' : '&') . "version={$ver}";
        }
    
        $result = "<link href=\"{$href}\" type=\"text/css\" rel=\"stylesheet\" />";
        if ($return) {
            return $result;
        } else {
            echo $result;
            return null;
        }
    }
    
    /**
     * 获取图片路径（一般用户加载默认图片）
     *
     * @param string $path  相对路径
     *
     * @return \string
     */
    static public function img($path, $with_version = true) {
        $url = $path;
        if($with_version) {
            $ver = self::cssVer();
            $url .= (strpos($url, '?') === false ? '?' : '&') . "version={$ver}";
        }
        return $url;
    }
    
    /**
     * 加载JS
     * @param string $path
     * @param boolean $return
     * @return \mixed
     */
    static public function js($path, $with_version = true, $return = false) {
        $src = $path;
        if($with_version) {
            $ver = self::jsVer();
            $src .= (strpos($src, '?') === false ? '?' : '&') . "version={$ver}";
            $result = "<script type=\"text/javascript\" src=\"{$src}\"></script>";
        }
    
        if ($return) {
            return $result;
        } else {
            echo $result;
            return null;
        }
    }
    
    /**
     * 获取当前CSS版本号
     * @return \string
     */
    static public function cssVer() {
        static $ver = '';
        if (!$ver) {
            $conf = new \Yaf_Config_Ini(CONF_PATH . 'cdn');
            $ver = $conf->version->css;
        }
        return $ver;
    }
    
    /**
     * 获取JS版本号
     * @return \string
     */
    static public function jsVer() {
        static $ver = '';
        if (!$ver) {
            $conf = new \Yaf_Config_Ini(CONF_PATH . 'env');
            $ver = $conf->version->js;
        }
        return $ver;
    }
    
    /**
     * 加载CDN上的JS库
     *
     * @param string $lib_name
     * @param boolean $return
     *
     * @return \mixed
     */
    static public function jsLib($lib_name, $return = false) {
        $conf = new \Yaf_Config_Ini(CONF_PATH . 'env');
        $url = $conf->lib[$lib_name];
        return $url ? self::js($url, false, $return) : '';
    }
    
    /**
     * 加载CDN上的CSS库
     *
     * @param string $lib_name
     * @param boolean $return
     *
     * @return \mixed
     */
    static public function cssLib($lib_name, $return = false) {
        $conf = new \Yaf_Config_Ini(CONF_PATH . 'env');
        $url = $conf->lib[$lib_name];
        return $url ? self::css($url, false, $return) : '';
    }
    
    /**
     * 拼相对路径URL
     *
     * @param string $path
     * @return string
     */
    static public function path($path) {
        $conf = new \Yaf_Config_Ini(CONF_PATH . 'env');
    
        return $conf->setting->relative . $path;
    }
} 
