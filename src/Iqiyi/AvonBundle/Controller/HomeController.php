<?php

namespace Iqiyi\AvonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Iqiyi\AvonBundle\Entity\AvonPhoto;
use Iqiyi\AvonBundle\Entity\AvonSubject;
use Iqiyi\AvonBundle\Entity\AvonSubjectVote;

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

        if($request->isXmlHttpRequest())
        {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $avonSubject->setAddTime(time());

                $avonSubject->setFromType(0);
                $avonSubject->setStatus(0);

                $em->persist($avonSubject);
                $em->flush();

                $errors = array('success'=>1, 'id'=>$avonSubject->getSubjectId());
                return new JsonResponse($errors);
            }else{
                $errors = array('success'=>0);
                $errors['errorList'] = $this->getErrorMessages($form);
                
                return new JsonResponse($errors);
            }
        }

        return array('form' => $form->createView());
    }

    private function getErrorMessages($form)
    {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
                $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = implode('、', $this->getErrorMessages($child));
            }
        }

        return $errors;
    }

    /**
    *  @Template()
    */
    public function votemsgAction(Request $request)
    {
        //普通赞
        $avonSubjectVote = new AvonSubjectVote();
        $formLike = $this->createFormBuilder($avonSubjectVote, array('validation_groups' => array('normal')))
            ->setAction($this->generateUrl('iqiyi_avon_votemsg'))
            ->add('subjectId', 'hidden', array('data'=>1, 'error_bubbling'=>false))
            ->add('voteType', 'hidden', array('data'=>0, 'error_bubbling'=>false))
            ->add('fromType', 'hidden', array('data'=>0, 'error_bubbling'=>false))
            ->add('save', 'submit', array( 'label'=>'赞'))
            ->getForm();

        $formQuestion = $this->createFormBuilder($avonSubjectVote, array('validation_groups' => array('normal')))
            ->setAction($this->generateUrl('iqiyi_avon_votemsg'))
            ->add('subjectId', 'hidden', array('data'=>1, 'error_bubbling'=>false))
            ->add('question', 'choice', array('choices'   => array('0' => '嘻嘻嘻嘻嘻嘻想', '1' => '美丽瞬间'),
                                                'label'=>'ta的瞬间是：'))
            ->add('voteType', 'hidden', array('data'=>1, 'error_bubbling'=>false))
            ->add('fromType', 'hidden', array('data'=>0, 'error_bubbling'=>false))
            ->add('save', 'submit', array( 'label'=>'投ta'))
            ->getForm();

        $formRedeem = $this->createFormBuilder($avonSubjectVote, array('validation_groups' => array('normal')))
            ->setAction($this->generateUrl('iqiyi_avon_votemsg'))
            ->add('subjectId', 'hidden', array('data'=>1, 'error_bubbling'=>false))
            ->add('redeemCode', 'text', array('label'=>'我的天猫码：'))
            ->add('voteType', 'hidden', array('data'=>2, 'error_bubbling'=>false))
            ->add('fromType', 'hidden', array('data'=>0, 'error_bubbling'=>false))
            ->add('save', 'submit', array( 'label'=>'投票'))
            ->getForm();

        if($request->isXmlHttpRequest())
        {
            $formParams = $request->get('form');
            if($formParams['voteType']==0)
            {
                $formLike->handleRequest($request);

                if ($formLike->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $avonSubjectVote->setVoteIp($request->getClientIp());
                    $avonSubjectVote->setVoteTime(time());
                    $avonSubjectVote->setStatus(0);
                    $avonSubjectVote->setRedeemCode('');

                    $em->persist($avonSubjectVote);
                    $em->flush();

                    $errors = array('success'=>1, 'id'=>$avonSubjectVote->getSubjectVoteId());
                    return new JsonResponse($errors);
                }else{
                    $errors = array('success'=>0);
                    $errors['errorList'] = $this->getErrorMessages($formLike);
                    
                    return new JsonResponse($errors);
                }
            }
            if($formParams['voteType']==1)
            {
                $formQuestion->handleRequest($request);

                if ($formQuestion->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $avonSubjectVote->setVoteIp($request->getClientIp());
                    $avonSubjectVote->setVoteTime(time());
                    $avonSubjectVote->setStatus(0);
                    $avonSubjectVote->setRedeemCode('');

                    $em->persist($avonSubjectVote);
                    $em->flush();

                    $errors = array('success'=>1, 'id'=>$avonSubjectVote->getSubjectVoteId());
                    return new JsonResponse($errors);
                }else{
                    $errors = array('success'=>0);
                    $errors['errorList'] = $this->getErrorMessages($formQuestion);
                    
                    return new JsonResponse($errors);
                }
            }
        }

        return array('form_like' => $formLike->createView(),
                    'form_question' => $formQuestion->createView(),
                    'form_redeem' => $formRedeem->createView());
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
            ->add('content', 'textarea', array( 'label'=>'我的瞬间：'))
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
