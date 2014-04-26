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
	public function generateRedeemCodeAction()
  {
  	$em = $this->getDoctrine()->getManager();

  	for($i=0; $i<200; $i++){
	  	for($j=0; $j<10; $j++){
		  	$avonRedeemCode = new AvonRedeemCode();
		    $avonRedeemCode->setCode(md5(uniqid(mt_rand(), true)));
		    $avonRedeemCode->setStatus(0);

		    $em->persist($avonRedeemCode);
		  }
	    $em->flush();
	    $em->clear();
	  }
    return new Response("生成了2000个码");
  }

  /**
  *  @Template()
  */
  public function subjectListAction(Request $request)
  {
    $pagesize = 20;
    $page = $request->get('page', 1);
    $mobile = $request->get('mobile', '');
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery('SELECT COUNT(u.subjectId) AS total FROM IqiyiAvonBundle:AvonSubject u WHERE '.($mobile?'u.memMobile='.$mobile.'':'u.status=0'));
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
    $objects = $em->getRepository("IqiyiAvonBundle:AvonSubject")
                  ->findBy($criteria, array('addTime'=>'desc'), $pagesize, $offset);
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
}
?>