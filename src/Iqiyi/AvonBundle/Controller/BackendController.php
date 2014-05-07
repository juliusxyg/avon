<?php

namespace Iqiyi\AvonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Iqiyi\AvonBundle\Entity\AvonRedeemCode;


class BackendController extends Controller
{ 
  /**
  *  @Template()
  */
  public function redeemAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery('SELECT COUNT(u.redeemCodeId) AS total FROM IqiyiAvonBundle:AvonRedeemCode u WHERE u.status>=0');
    $totalUnused = $query->getSingleResult();
    return array("total_unused"=>$totalUnused['total']);
  }

  public function downloadRedeemAction(Request $request)
  {
    /*
    $filename="tmall-code.csv";
    $em = $this->getDoctrine()->getManager();
    $content = "";
    $objects = $em->getRepository("IqiyiAvonBundle:AvonRedeemCode")->findBy(array("status"=>0));
    if(!$objects){
      return new Response("未使用天猫码数量为0");
    }
    //内存需求太大，需要换个方式
    foreach($objects as $object)
    {
      $content .= $object->getCode()."\n";
    }
    
    $response = new Response();
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);
    $response->setContent($content);
    return $response;*/
  }

	public function generateRedeemCodeAction(Request $request)
  {
    if($request->isXmlHttpRequest())
    {
    	$em = $this->getDoctrine()->getManager();

    	for($i=0; $i<200; $i++){
  	  	for($j=0; $j<10; $j++){
  		  	$avonRedeemCode = new AvonRedeemCode();
  		    $avonRedeemCode->setCode($this->buildcode());
  		    $avonRedeemCode->setStatus(0);

  		    $em->persist($avonRedeemCode);
  		  }
  	    $em->flush();
  	    $em->clear();
  	  }
      return new JsonResponse(array("success"=>1, "msg"=>"生成了2000个码"));
    }
    return new JsonResponse(array("success"=>0, "error"=>"非法请求"));
  }

  public function buildcode($len=10)
  {
    $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'; 
    $string=''; 
    for(;$len>=1;$len--) 
    { 
      $position=mt_rand()%strlen($chars); 
      $string.=substr($chars,$position,1); 
    } 
    return $string;
  }
//####################################################################################################
  /**
  *  @Template()
  */
  public function subjectListAction(Request $request)
  {
    $pagesize = 20;
    $page = $request->get('page', 1);
    $mobile = $request->get('mobile', '');
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery('SELECT COUNT(u.subjectId) AS total FROM IqiyiAvonBundle:AvonSubject u WHERE '.($mobile?'u.memMobile='.$mobile.'':'u.status>=0'));
    $total = $query->getSingleResult();
    $totalpage = ceil($total['total']/$pagesize);
    if($totalpage<$page){
        $page = $totalpage<=0?1:$totalpage;
    }
    $offset = ($page-1)*$pagesize;
    if($mobile){
      $criteria = array("memMobile"=>$mobile);
    }else{
      $criteria = array("status"=>0);
    }
    $objects = $em->createQuery('SELECT u FROM IqiyiAvonBundle:AvonSubject u WHERE '.($mobile?'u.memMobile='.$mobile.'':'u.status>=0 ORDER BY u.addTime DESC'))
                  ->setMaxResults($pagesize)->setFirstResult($offset)
                  ->getResult();

    
    return array("items"=>$objects, "page"=>$this->paginating($page,$totalpage,10, ($mobile?array("mobile"=>$mobile):array())));
  }

  public function subjectApproveAction(Request $request)
  {
    if($request->isXmlHttpRequest())
    {
      $status = $request->get('status');
      $subjectId = $request->get('id');
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository("IqiyiAvonBundle:AvonSubject")->find($subjectId);
      if(!$entity)
      {
        return new JsonResponse(array("success"=>0, "error"=>"操作对象不存在"));
      }
      $entity->setStatus($status);
      $em->persist($entity);
      $em->flush();

      return new JsonResponse(array("success"=>1));
    }
    return new JsonResponse(array("success"=>0, "error"=>"非法请求"));
  }

  public function subjectRemoveAction(Request $request)
  {
    if($request->isXmlHttpRequest())
    {
      $subjectId = $request->get('id');
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository("IqiyiAvonBundle:AvonSubject")->find($subjectId);
      if(!$entity)
      {
        return new JsonResponse(array("success"=>0, "error"=>"操作对象不存在"));
      }
      $em->remove($entity);
      $em->flush();

      return new JsonResponse(array("success"=>1));
    }
    return new JsonResponse(array("success"=>0, "error"=>"非法请求"));
  }

  public function subjectVoteAction(Request $request)
  {
    if($request->isXmlHttpRequest())
    {
      $number = $request->get('number');
      if(!is_numeric($number))
      {
        return new JsonResponse(array("success"=>0, "error"=>"请填写合理数字"));
      }
      $subjectId = $request->get('id');
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository("IqiyiAvonBundle:AvonSubject")->find($subjectId);
      if(!$entity)
      {
        return new JsonResponse(array("success"=>0, "error"=>"操作对象不存在"));
      }
      $entity->setTotalVote($number);
      $em->persist($entity);
      $em->flush();

      return new JsonResponse(array("success"=>1));
    }
    return new JsonResponse(array("success"=>0, "error"=>"非法请求"));
  }
//####################################################################################################
  /**
  *  @Template()
  */
  public function photoListAction(Request $request)
  {
    $pagesize = 20;
    $page = $request->get('page', 1);
    $mobile = $request->get('mobile', '');
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery('SELECT COUNT(u.photoId) AS total FROM IqiyiAvonBundle:AvonPhoto u WHERE '.($mobile?'u.memMobile='.$mobile.'':'u.status>=0'));
    $total = $query->getSingleResult();
    $totalpage = ceil($total['total']/$pagesize);
    if($totalpage<$page){
        $page = $totalpage<=0?1:$totalpage;
    }
    $offset = ($page-1)*$pagesize;
    if($mobile){
      $criteria = array("memMobile"=>$mobile);
    }else{
      $criteria = array("status"=>0);
    }
    $objects = $em->createQuery('SELECT u FROM IqiyiAvonBundle:AvonPhoto u WHERE '.($mobile?'u.memMobile='.$mobile.'':'u.status>=0 ORDER BY u.addTime DESC'))
                  ->setMaxResults($pagesize)->setFirstResult($offset)
                  ->getResult();

    return array("items"=>$objects, "page"=>$this->paginating($page,$totalpage,10, ($mobile?array("mobile"=>$mobile):array())));
  }

  public function photoApproveAction(Request $request)
  {
    if($request->isXmlHttpRequest())
    {
      $status = $request->get('status');
      $photoId = $request->get('id');
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository("IqiyiAvonBundle:AvonPhoto")->find($photoId);
      if(!$entity)
      {
        return new JsonResponse(array("success"=>0, "error"=>"操作对象不存在"));
      }
      $entity->setStatus($status);
      $em->persist($entity);
      $em->flush();

      return new JsonResponse(array("success"=>1));
    }
    return new JsonResponse(array("success"=>0, "error"=>"非法请求"));
  }

  public function photoRemoveAction(Request $request)
  {
    if($request->isXmlHttpRequest())
    {
      $photoId = $request->get('id');
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository("IqiyiAvonBundle:AvonPhoto")->find($photoId);
      if(!$entity)
      {
        return new JsonResponse(array("success"=>0, "error"=>"操作对象不存在"));
      }
      $em->remove($entity);
      $em->flush();

      return new JsonResponse(array("success"=>1));
    }
    return new JsonResponse(array("success"=>0, "error"=>"非法请求"));
  }

  public function photoVoteAction(Request $request)
  {
    if($request->isXmlHttpRequest())
    {
      $number = $request->get('number');
      if(!is_numeric($number))
      {
        return new JsonResponse(array("success"=>0, "error"=>"请填写合理数字"));
      }
      $photoId = $request->get('id');
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository("IqiyiAvonBundle:AvonPhoto")->find($photoId);
      if(!$entity)
      {
        return new JsonResponse(array("success"=>0, "error"=>"操作对象不存在"));
      }
      $entity->setTotalVote($number);
      $em->persist($entity);
      $em->flush();

      return new JsonResponse(array("success"=>1));
    }
    return new JsonResponse(array("success"=>0, "error"=>"非法请求"));
  }
//####################################################################################################
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
//####################################################################################################
  /**
  *  @Template()
  */
  public function loginAction(Request $request)
  {
    $session = $request->getSession();

    // get the login error if there is one
    if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
        $error = $request->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR
        );
    } else {
        $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        $session->remove(SecurityContext::AUTHENTICATION_ERROR);
    }

    return array('last_username' => $session->get(SecurityContext::LAST_USERNAME),
                  'error'         => $error);
  }
//####################################################################################################
  /**
  *  @Template()
  */
  public function subjectVoteListAction(Request $request)
  {
    $pagesize = 20;
    $page = $request->get('page', 1);
    $start = $request->get('from', '');
    $end = $request->get('to', '');
    $criteria = array();
    $where = "WHERE 1=1 ";
    if($start){
      $criteria['from'] = $start;
      $start = strtotime($start." 00:00:00");
      $where .= "AND u.voteTime>=$start ";
    }
    if($end){
      $criteria['to'] = $end;
      $end = strtotime($end." 23:59:59");
      $where .= "AND u.voteTime<=$end ";
    }

    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery('SELECT count(DISTINCT u.subjectId) AS total FROM IqiyiAvonBundle:AvonSubjectVote u '.$where);
    $total = $query->getSingleResult();
    $totalpage = ceil($total['total']/$pagesize);
    if($totalpage<$page){
        $page = $totalpage<=0?1:$totalpage;
    }
    $offset = ($page-1)*$pagesize;
    $query = $em->createQuery('SELECT u.subjectId, count(u.subjectVoteId) AS total, v.memMobile, v.memName, v.content, v.totalVote FROM IqiyiAvonBundle:AvonSubjectVote u LEFT JOIN IqiyiAvonBundle:AvonSubject v WITH u.subjectId = v.subjectId '.$where.' GROUP BY u.subjectId ORDER BY total DESC')
                ->setMaxResults($pagesize)->setFirstResult($offset);
    $objects = $query->getResult();

    return array("from"=>isset($criteria['from'])?$criteria['from']:"", "to"=>isset($criteria['to'])?$criteria['to']:"", "items"=>$objects, "page"=>$this->paginating($page,$totalpage,10, $criteria));
  }
//####################################################################################################
  /**
  *  @Template()
  */
  public function photoVoteListAction(Request $request)
  {
    $pagesize = 20;
    $page = $request->get('page', 1);
    $start = $request->get('from', '');
    $end = $request->get('to', '');
    $criteria = array();
    $where = "WHERE 1=1 ";
    if($start){
      $criteria['from'] = $start;
      $start = strtotime($start." 00:00:00");
      $where .= "AND u.voteTime>=$start ";
    }
    if($end){
      $criteria['to'] = $end;
      $end = strtotime($end." 23:59:59");
      $where .= "AND u.voteTime<=$end ";
    }

    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery('SELECT count(DISTINCT u.photoId) AS total FROM IqiyiAvonBundle:AvonPhotoVote u '.$where);
    $total = $query->getSingleResult();
    $totalpage = ceil($total['total']/$pagesize);
    if($totalpage<$page){
        $page = $totalpage<=0?1:$totalpage;
    }
    $offset = ($page-1)*$pagesize;
    $query = $em->createQuery('SELECT u.photoId, count(u.photoVoteId) AS total, v.memMobile, v.memName, v.photoUrl, v.totalVote FROM IqiyiAvonBundle:AvonPhotoVote u LEFT JOIN IqiyiAvonBundle:AvonPhoto v WITH u.photoId = v.photoId '.$where.' GROUP BY u.photoId ORDER BY total DESC')
                ->setMaxResults($pagesize)->setFirstResult($offset);
    $objects = $query->getResult();

    return array("from"=>isset($criteria['from'])?$criteria['from']:"", "to"=>isset($criteria['to'])?$criteria['to']:"", "items"=>$objects, "page"=>$this->paginating($page,$totalpage,10, $criteria));
  }
//########################################################################################################
}
?>