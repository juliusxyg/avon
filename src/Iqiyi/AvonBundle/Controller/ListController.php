<?php

namespace Iqiyi\AvonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Iqiyi\AvonBundle\Entity\AvonPhoto;
use Iqiyi\AvonBundle\Entity\AvonSubject;

use Iqiyi\AvonBundle\Controller\HomeController;

class ListController extends HomeController
{   
	/**
  *  @Template()
  */
  public function subjectListAction(Request $request)
  {
    $hash = array();
    $pagesize = 13;
    $page = $request->get('page', 1);
    $keyword = $request->get('keyword', '');
    $sharedId = $request->get('shared', '');
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery('SELECT COUNT(u.subjectId) AS total FROM IqiyiAvonBundle:AvonSubject u WHERE u.status=1 '.($keyword?" AND u.content LIKE '%".$keyword."%' ":''));
    $total = $query->getSingleResult();
    $totalpage = ceil($total['total']/$pagesize);
    if($totalpage<$page){
        $page = $totalpage<=0?1:$totalpage;
    }
    $offset = ($page-1)*$pagesize;
    $query = $em->createQuery('SELECT u FROM IqiyiAvonBundle:AvonSubject u WHERE u.status=1 '.($keyword?" AND u.content LIKE '%".$keyword."%' ":''))
                ->setMaxResults($pagesize)->setFirstResult($offset);
    $objects = $query->getResult();
    $hash['items'] = $objects;

    $hash['subjectForms'] = array();
    if($objects)
    {
        foreach($objects as $key=>$subject)
        {
            $hash['subjectForms'][$key] = $this->votemsgAction(new Request(array("id"=>$subject->getSubjectId())));
        }
    }
    $hash['sharedObject'] = "";
    if($sharedId)
    {
      $hash['sharedObject'] = $em->getRepository("IqiyiAvonBundle:AvonSubject")->find($sharedId);
      $hash['sharedForm'] = $this->votemsgAction(new Request(array("id"=>$sharedId)));
    }
    $hash['page'] = $this->paginating($page,$totalpage,10, ($keyword?array("keyword"=>$keyword):array()));

    $hash['keyword'] = $keyword;
    $hash['randAnswer'] = $this->randAnswers[rand(0, 20)];
    
    return $hash;
  }

  /**
  *  @Template()
  */
  public function photoListAction(Request $request)
  {
  	return array();
  }

  public function paginating($currpage=1, $totalpage=1, $pagespan=10, $get=array(), $pageNameInClause='page')
  {
    $ret=array();
    if($pagespan<1){
      $pagespan = 10;
    }
    if($currpage<1){
      $currpage = 1;
    }
    if($totalpage<1){
      $totalpage = 1;
    }
    $pageOffetRight = ceil($pagespan/2)?ceil($pagespan/2):1;
    $pageOffsetLeft = $pageOffetRight==1?1:$pageOffetRight-1;

    if($currpage>=$totalpage){
      $currpage=$totalpage;
      $ret['lastpage'] = '';
      $ret['nextpage'] = '';
    }else{
      $ret['lastpage'] = $totalpage;
      $ret['nextpage'] = $currpage+1;
    }
    if($currpage>1){
      $ret['firstpage'] = 1;
      $ret['prevpage'] = $currpage-1;
    }else{
      $ret['firstpage'] = '';
      $ret['prevpage'] = '';
    }
    $ret['currpage'] = $currpage;
    $ret['totalpage'] = $totalpage;
    
    $clauseArgs=array();
    $clause='';
    if(is_array($get)){
      foreach($get as $name=>$value){
        if($name != $pageNameInClause){
          $clauseArgs[] = $name.'='.$value;
        }
      }
      if(!empty($clauseArgs)){
        $clause = '?'.implode("&",$clauseArgs);
      }
    }
    $ret['clause']=$clause;
    
    $pagelist = array();
    if($currpage < 1+$pageOffsetLeft){
      $pagelist[] = 1;
      for($i=1;$i<$pagespan;$i++){
        if(($pagelist[0]+$i)<=$totalpage){
          $pagelist[] = $pagelist[0]+$i;
        }
      }
      $ret['pagelist'] = $pagelist;
    }elseif($currpage > $totalpage-$pageOffetRight){
      $pagelist[]=$totalpage-$pagespan+1;
      for($i=1;$i<$pagespan;$i++){
        if(($pagelist[0]+$i)<=$totalpage){
          $pagelist[] = $pagelist[0]+$i;
        }
      }
      $ret['pagelist'] = $pagelist;
    }else{
      for($i=$pageOffsetLeft;$i>=1;$i--){
        if(($currpage-$i)>=1){
          $pagelist[] = $currpage-$i;
        }
      }
      $pagelist[]=$currpage;
      for($i=1;$i<=$pageOffetRight;$i++){
        if(($currpage+$i)<=$totalpage){
          $pagelist[] = $currpage+$i;
        }
      }
      if(end($pagelist)==$totalpage){
        $ret['pagelist'] = array_slice($pagelist,0-$pagespan);
      }else{
        $ret['pagelist'] = array_slice($pagelist,0,$pagespan);
      }
    }
    
    return $ret;
  }
}