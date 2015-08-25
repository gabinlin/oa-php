<?php
namespace Home\Model;
use Think\Model\RelationModel;

/**
 * Created by IntelliJ IDEA.
 * User: linjiabin
 * Date: 15/8/15
 * Time: 下午4:21
 */
class AttendanceModel extends RelationModel
{
    protected $_link = array(
        'employee'=>array(
            'mapping_type'      => self::BELONGS_TO,
            'class_name'        => 'Employee',
            'foreign_key'   => 'e_id',
            'relation_foreign_key' => 'e_id',
//            'mapping_name'  => 'employee'
            // 定义更多的关联属性
//            'as_fields' => 'real_name,nickname',
            )
        );
    protected $_map = array(
        'workDate' =>'work_date',
        'amTime'  =>'am_time',
        'pmTime'  =>'pm_time',
    );
    var $id;
    var $department;
}