<?php

namespace Iqiyi\AvonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
}
?>