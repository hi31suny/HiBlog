<?php
/**
 * 博客模板资源
 *
 * @author chengxuan <i@chengxuan.li>
 */
namespace Model\Theme;
class Resource extends \Model\Abs {

    /**
     * 表名
     *
     * @var string
     */
    protected static $_table = 'theme_resource';

    /**
     * 通过TPL_ID获取模板资源
     * 
     * @param int    $tpl_id 模板ID
     * 
     * @return array
     */
    static public function showByTpl($tpl_id) {
        $where = ['tpl_id' => $tpl_id];
        return self::db()->wAnd($where)->fetchRow();
    }
    
    /**
     * 通过模板ID和名称获取资源
     * 
     * @param int    $tpl_id 模板ID
     * @param string $name   资源名称
     * 
     * @return array
     */
    static public function showByName($tpl_id, $name) {
        $where = ['tpl_id' => $tpl_id, 'resource_name' => $name];
        return self::db()->wAnd($where)->fetchRow();
    }
    
    /**
     * 复制模板资源内容（在第一次写入时触发）
     * 
     * @param int    $from_tpl_id 源模板ID
     * @param int    $to_tpl_id   目标模板ID
     * @param string $uid         当前登录用户
     * 
     * @return int
     */
    static public function copy($from_tpl_id, $to_tpl_id, $uid = false) {
        $to_tpl_id = (int)$to_tpl_id;
        self::validateAuth($to_tpl_id, $uid);
        
        $table = self::db()->showTable();
        $mysql = new \Comm\Db\Mysql();
        $sql = "INSERT IGNORE INTO {$table} (tpl_id, resource_name, content) SELECT {$to_tpl_id}, resource_name, content FROM {$table} WHERE tpl_id = ?";
        return $mysql->executeSql($sql, [$from_tpl_id]);
    }
    
    /**
     * 更新一条模板资源数据
     * 
     * @param int    $tpl_id        模板ID
     * @param string $resource_name 资源名称
     * @param string $content       模板内容
     * 
     * @param string $uid
     */
    static public function update($tpl_id, $resource_name, $content, $uid = false) {
        self::validateAuth($tpl_id);
        $data = array(
            'tpl_id'        => $tpl_id,
            'resource_name' => $resource_name,
            'content'       => $content,
        );
        return self::db()->iodu($data, true);
    }
    
    /**
     * 检查指定权限用户有无操作权限
     * 
     * @param int $tpl_id 模板ID
     * @param int $uid    当前登录用户UID
     * 
     * @return void
     */
    static public function validateAuth($tpl_id, $uid = false) {
        $uid || $uid = \Model\User::validateLogin();
        $tpl_main = Main::show($tpl_id);
        $validate_uid = isset($tpl_main['user_id']) ? $tpl_main['user_id'] : 0;
        \Model\User::validateAuth($validate_uid, $uid);
    }
}