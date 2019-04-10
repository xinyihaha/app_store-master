<?php
namespace app\index\controller;
use think\View;
use think\Controller;
$request = \think\Request::instance();
define('ACTION_NAME', $request->action());

use think\Config;
use think\Url;
use think\Validate;
use think\Model;
class Index extends Controller
{
    //打印首页
    public function index()
    {
        $condition=[
            'AppStatus'=>1,
            'CategoryID'=>9
        ];
        $total=db('apps')->where('AppStatus',1)->where('CategoryID','<>',9)->count();
        $apps=db('apps')->where('AppStatus',1)->where('CategoryID','<>',9)->limit(5)->order("AppID ASC")->select();
        //var_dump($apps);
        $game=db('apps')->where($condition)->limit(5)->order("AppID ASC")->select();
        $tota=db('apps')->where($condition)->count();
        $view = new View();
        $view->assign('total',$total);
        $view->assign('tota',$tota);
        $view->assign('apps',$apps);
        $view->assign('game',$game);
        return $view->fetch('index');
    }
     public function indexdata()
    {
       $start = Input('post.start');
       //echo($start);
       $list = db('apps')->where('AppStatus',1)->where('CategoryID','<>',9)->order('AppID asc')->limit($start, 5)->select();
       return (array( 'result'=>$list,'status'=>1, 'msg'=>'获取成功！'));
    }
    public function indexdatag()
    {
        $condition=[
            'AppStatus'=>1,
            'CategoryID'=>9
        ];
        $start = Input('post.start');
        //echo($start);
        $list = db('apps')->where($condition)->order('AppID asc')->limit($start, 5)->select();
        return (array( 'result'=>$list,'status'=>1, 'msg'=>'获取成功！'));
    }
    //打印排行页
    public function rank()
    {
        $condition=[
            'AppStatus'=>1,
            'CategoryID'=>9
        ];
       $total=db('apps')->where('AppStatus',1)->where('CategoryID','<>',9)->count();
       $apps=db('apps')->where('AppStatus',1)->where('CategoryID','<>',9)->order("DownloadCount DESC")->limit(5)->select();
        $game=db('apps')->where($condition)->limit(5)->order("DownloadCount DESC")->select();
        $tota=db('apps')->where($condition)->count();
        $view = new View();
       $view->assign('total',$total);
       $view->assign('apps',$apps);
        $view->assign('game',$game);
        $view->assign('tota',$tota);
       return $view->fetch('Rank/rank');
    }
     public function rankdata()
    {
       $start = Input('post.start');
       $list = db('apps')->where('AppStatus',1)->where('CategoryID','<>',9)->order('DownloadCount DESC')->limit($start, 5)->select();
       return (array( 'result'=>$list,'status'=>1, 'msg'=>'获取成功！'));
    }

    //打印详情页
    public function detail()
    {
        $id=input('get.id');
        //var_dump($id);
        $apps=db('apps')->where('AppID',$id)->select();
        //print_r($apps[0]['VersionID']);
        //die();
        $versions=db('versions')->where('VersionID',$apps[0]['VersionID'])->find();
        $user=db('users')->where('UserID',$apps[0]['UserID'])->find();
        $view = new View();
        $view->assign('apps',$apps);
        $view->assign('versions',$versions);
        $view->assign('user',$user);
        return $view->fetch('Detail/detail');

    }


    //打印分类页
    public function category()
    {
        $catee=db('category')->order('CategoryID ASC')->select();
        $count=db('category')->order('CategoryID ASC')->count();;
        for($i=0;$i<$count;$i++)
        {
            $id=$catee[$i]['CategoryID'];
            $num=db('apps')->where('CategoryID',$id)->count();
            $cate[$i]=[
                "CategoryID"=>$id,
                "CategoryName"=>$catee[$i]['CategoryName'],
                "CategoryDetail"=>$catee[$i]['CategoryDetail'],
                "num"=>$num
            ];
        }
        $view = new View();
        $view->assign('cate',$cate);
        return $view->fetch('Category/category');
    }
    //打印分类列表页
    public function catelist()
    {
        $id=input('get.id');
        $apps=db('apps')->where('CategoryID',$id)->order("DownloadCount DESC")->limit(5)->select();
        $appss=db('apps')->where('CategoryID',$id)->order("Time DESC")->limit(5)->select();
        $cate=db('category')->where('CategoryID',$id)->find();
        $total=db('apps')->where('CategoryID',$id)->count();
        $tota=db('apps')->where('CategoryID',$id)->order("Time DESC")->limit(5)->count();
        $view = new View();
        $view->assign('apps',$apps);
        $view->assign('cate',$cate);
        $view->assign('total',$total);
        $view->assign('id',$id);
        $view->assign('appss',$appss);
        $view->assign('tota',$tota);
        return $view->fetch('List/list');
    }
    public function listdata()
    {
        $start = Input('post.start');
        $id=input('post.id');
//        echo $id;
//        echo($start);
        $list = db('apps')->where('AppStatus',1)->where('CategoryID',$id)->order("DownloadCount DESC")->limit($start, 5)->select();
        return (array( 'result'=>$list,'status'=>1, 'msg'=>'获取成功！'));
    }
    public function listdataz()
    {
        $start = Input('post.start');
        $id=input('post.id');
//        echo $id;
//        echo($start);
        $list = db('apps')->where('AppStatus',1)->where('CategoryID',$id)->order("Time DESC")->limit($start, 5)->select();
        return (array( 'result'=>$list,'status'=>1, 'msg'=>'获取成功！'));
    }


    //打印必备页
    public function zjbb()
    {
        $view = new View();
        //$view->assign('apps',$apps);
        return $view->fetch('Zjbb/zjbb');
    }
    public function zjbbdata()
    {
        //$data=db('category')->alias('c')->join('apps a','c.CategoryID=a.CategoryID')->order("DownloadCount DESC")->select();
        $cate=db('category')->select();
        foreach($cate as $n=> $val){
             $con['CategoryID']=$val['CategoryID'];
             $apps[$n]=db('apps')->alias('a')->where($con)->where('AppStatus',1)->order("DownloadCount DESC")->limit(5)->select();
             $data[$n]=array(
             'category'=>$val,
             'app'=>$apps[$n],
             );
        }
        //var_dump($data[10]);
        return (array( 'result'=>$data,'status'=>1, 'msg'=>'获取成功！'));
    }
    public function download()
    {
        $name=$_GET['name'];//app的名称
        $Dao=db("apps");
        $result = $Dao->where('AppName','like',$name)->order("Time DESC")->find();
        if(!$result) {
            $this->error("数据查询出错");
        }
        else
        {
            $mode='/.zip$/';
			$mode1='/.rar$/';
            if(!preg_match($mode,$result['SymbolName'])&&!preg_match($mode1,$result['SymbolName']))
            {
                $this->error('该应用暂时无资源');
            }
            else
            {
                $filename=$_SERVER['DOCUMENT_ROOT']."/AppStore/public/Upload/".$result['UserID']."/".$result['AppID']."/".$result['SymbolName'];
				$filename1="./download/".$result['SymbolName'];
                header("content-disposition:attachment;filename=".$filename1);
                header("content-length:".filesize($filename));
                readfile($filename);
                $count=$result['DownloadCount']+1;
                //更新下载次数
                $down=array(
                    "DownloadCount"=>$count
                );
                $Dao->where('AppName','=',$name)->update($down);
            }
        }

    }
    public function search()
    {
        $name=$_GET['q'];
        $apps=db('apps')->where('AppName',$name)->select();
        //print_r($apps[0]['VersionID']);
        //die();
        if(!$apps)
        {
            $this->error("没有该应用");
//            $this->error("数据查询出错",Url('index'));
        }
        else
        {
            $versions=db('versions')->where('VersionID',$apps[0]['VersionID'])->find();
            $view = new View();
            $view->assign('apps',$apps);
            $view->assign('versions',$versions);
            return $view->fetch('Detail/detail');
        }
    }

}
