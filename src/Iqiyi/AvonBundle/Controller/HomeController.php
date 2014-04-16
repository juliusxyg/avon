<?php

namespace Iqiyi\AvonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Iqiyi\AvonBundle\Entity\AvonPhoto;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('IqiyiAvonBundle:Home:index.html.twig');
    }

    public function addmsgAction()
    {

    }

    /**
     * @Template()
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
}
