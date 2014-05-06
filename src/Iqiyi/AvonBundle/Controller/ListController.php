<?php

namespace Iqiyi\AvonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Iqiyi\AvonBundle\Entity\AvonPhoto;
use Iqiyi\AvonBundle\Entity\AvonSubject;

class ListController extends Controller
{   
	/**
  *  @Template()
  */
  public function subjectListAction(Request $request)
  {
  	return array();
  }

  /**
  *  @Template()
  */
  public function photoListAction(Request $request)
  {
  	return array();
  }
}