<?php

namespace Iqiyi\AvonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Iqiyi\AvonBundle\Entity\AvonPhoto;
use Iqiyi\AvonBundle\Entity\AvonSubject;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('IqiyiAvonBundle:Home:index.html.twig');
    }

    /**
    *  @Template()
    */
    public function addmsgAction(Request $request)
    {
        $avonSubject = new AvonSubject();
        $form = $this->createFormBuilder($avonSubject)
            ->setAction($this->generateUrl('iqiyi_avon_addmsg'))
            ->add('memName', 'text', array('label'=>'姓名：', 'max_length'=>45))
            ->add('memGender', 'choice', array('choices'   => array('0' => '男', '1' => '女'),
                                                'required'  => true, 
                                                'label'=>'性别：'))
            ->add('memMobile', 'text', array( 'label'=>'手机：', 'max_length'=>15))
            ->add('memAddress', 'text', array( 'label'=>'地址：', 'max_length'=>60))
            ->add('memZip', 'text', array( 'label'=>'邮编：', 'max_length'=>15))
            ->add('content', 'textarea', array( 'label'=>'我的瞬间：'))
            ->add('save', 'submit', array( 'label'=>'发布'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $avonSubject->setAddTime(time());
            $avonSubject->setFromType(0);
            $em->persist($avonSubject);
            $em->flush();

            return $this->redirect($this->generateUrl('iqiyi_avon_homepage'));
        }

        return array('form' => $form->createView());
    }

    public function votemsgAction()
    {
        //投票要限制IP
        //天猫投票不限制IP
    }

    /**
    *  @Template()
    */
    public function addphotoAction(Request $request)
    {
        $avonPhoto = new AvonPhoto();
        $form = $this->createFormBuilder($avonPhoto)
            ->setAction($this->generateUrl('iqiyi_avon_addphoto'))
            ->add('memName', 'text', array('label'=>'姓名：', 'max_length'=>45))
            ->add('memGender', 'choice', array('choices'   => array('0' => '男', '1' => '女'),
                                                'required'  => true, 
                                                'label'=>'性别：'))
            ->add('memMobile', 'text', array( 'label'=>'手机：', 'max_length'=>15))
            ->add('memAddress', 'text', array( 'label'=>'地址：', 'max_length'=>60))
            ->add('memZip', 'text', array( 'label'=>'邮编：', 'max_length'=>15))
            ->add('file', 'file', array( 'label'=>'照片：'))
            ->add('save', 'submit', array( 'label'=>'发布'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $avonPhoto->setAddTime(time());
            $em->persist($avonPhoto);
            $em->flush();

            return $this->redirect($this->generateUrl('iqiyi_avon_homepage'));
        }

        return array('form' => $form->createView());
    }

    public function votephotoAction()
    {

    }
}
