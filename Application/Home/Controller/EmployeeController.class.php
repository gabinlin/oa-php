<?php
/**
 * Created by IntelliJ IDEA.
 * User: linjiabin
 * Date: 15/8/23
 * Time: 下午8:16
 */

namespace Home\Controller;


use Home\Service\EmployeeServiceImpl;
use Home\Utils\RenderUtil;
use Think\Controller;

class EmployeeController extends BasicController
{
    public function viewList() {
        $this->display(T("employee/list"));
    }

    public function import() {
        $this->display(T("employee/import"));
    }

    public function data() {
        $_REQUEST["like_real_name"] = $_REQUEST["real_name"];
        $result = query("Employee");
        echo json_encode($result);
    }

    public function save($id = "") {
        $employee = array();
        if ($id) {
            $dao = M("Employee");
            $employee = $dao->find($id);
        }
        $employee["real_name"] = I("realName");
        $employee["attendance_cn"] = I("attendanceCn");
        $employee["department"] = I("department");
        $dao = M("Employee");
        if ($id) {
            $dao->save($employee);
        } else {
            $dao->add($employee);
        }
        echo json_encode(RenderUtil::success("操作成功！"));
    }

    public function edit($id = "") {
        if ($id) {
            $dao = M("Employee");
            $entity = $dao->find($id);
            $this->assign("entity", $entity);
        }
        $this->display(T("employee/edit"));
    }

    public function add() {
        $this->display(T("employee/edit"));
    }

    public function delete($ids) {
        $dao = M("Employee");
        $dao->delete($ids);
        echo json_encode(RenderUtil::success("删除员工成功！"));
    }

    public function excel() {
        import("Org.Util.PHPExcel");
        $filePath = $_FILES["file"]["tmp_name"];
        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($filePath)){
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)){
                echo 'no Excel';
                return ;
            }
        }

        $PHPExcel = $PHPReader->load($filePath);
        /**读取excel文件中的第一个工作表*/
        $currentSheet = $PHPExcel->getSheet(0);
        /**取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();
        $jsonTmp = array();
        $attendance = M("Employee");
        /**从第二行开始输出，因为excel表中第一行为列名*/
        for($currentRow = 4;$currentRow <= $allRow;$currentRow++){
            /**从第A列开始输出*/
            $data = array();
            $attendanceCn = $currentSheet -> getCell("A$currentRow") -> getValue();
            $realName = $currentSheet -> getCell("B$currentRow") -> getValue();
            $department = $currentSheet -> getCell("D$currentRow") -> getValue();
            $realName = $this -> encode($realName);
            $department = $this -> encode($department);
            $data["attendance_cn"] = $attendanceCn;
            $data["real_name"] = $realName;
            $data["department"] = $department;
            if ($data["real_name"] && !$jsonTmp[$data["real_name"]]) {
                $jsonTmp[$data["real_name"]] = 1;
                $attendance -> add($data);
            }
        }
        redirect("viewList");
    }

    function encode($str) {
        $str = iconv('utf-8','gbk', $str);
        $str = iconv('gbk','utf-8', $str);
        return $str;
    }

    public function getRealNames($real_name = "", $callback)
    {
        $service = new EmployeeServiceImpl();
        $service->suggestRealNames($real_name, $callback);
    }

    public function getAttendanceCn($attendance_cn = "", $callback)
    {
        $service = new EmployeeServiceImpl();
        $service->suggestAttendanceCn($attendance_cn, $callback);
    }

}