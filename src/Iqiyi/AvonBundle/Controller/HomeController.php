<?php

namespace Iqiyi\AvonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Iqiyi\AvonBundle\Entity\AvonPhoto;
use Iqiyi\AvonBundle\Entity\AvonSubject;
use Iqiyi\AvonBundle\Entity\AvonSubjectVote;
use Iqiyi\AvonBundle\Controller\MobileDetect;

class HomeController extends Controller
{   
    public $sampleWords = array('毕业典礼上要拍出美美的毕业照',
        '与心仪男生的第一次约会',
        '好闺蜜的婚礼做温婉伴娘',
        '第一次得体见男友家长',
        '第一次相亲想要大方漂亮',
        '我的生日Party',
        '蜜月旅行甜蜜蜜的幸福时刻',
        '新公司上班第一天',
        '期待很久的公司年会表演',
        '学会化妆后第一次妆后见人');
    public $randAnswers = array('毕业典礼上要拍出美美的毕业照',
        '时隔五年的高中同学聚会',
        '与心仪男生的第一次约会',
        '好闺蜜的婚礼做温婉伴娘',
        '第一次得体见男友家长',
        '第一次相亲想要大方漂亮',
        '与异地男友期待很久的约会',
        '我的生日Party',
        '公司的集体旅游',
        '蜜月旅行甜蜜蜜的幸福时刻',
        '学院联谊派对要闪亮登场',
        '紧张的面试要适合的妆容应对',
        '新公司上班第一天',
        '期待很久的公司年会表演',
        '受邀参加男友的朋友聚会',
        '产后复出第一次见闺蜜',
        '第一次与客户见面',
        '学会化妆后第一次妆后见人',
        '与老公的结婚周年纪念',
        '参加孩子的入学典礼');

    public function prepareAction(Request $request)
    {
        $detect = new MobileDetect;
        if($detect->isMobile())
        {
            return $this->render("IqiyiAvonBundle:Home:prepare.m.html.twig");
        }
        return $this->render("IqiyiAvonBundle:Home:prepare.html.twig");
    }

    /**
    *  @Template()
    */
    public function indexAction()
    {
        $hash = array();

        $hash['myForm'] = $this->addmsgAction(new Request());
        $hash['subjects'] = $this->getRecentSubjectList(8);
        $hash['subjectForms'] = array();
        if($hash['subjects'])
        {
            foreach($hash['subjects'] as $key=>$subject)
            {
                $hash['subjectForms'][$key] = $this->votemsgAction(new Request(array("id"=>$subject->getSubjectId())));
            }
        }
        $hash['sampleWords'] = $this->sampleWords;
        return $hash;
    }

    public function getRecentSubjectList($size=8)
    {
        $em = $this->getDoctrine()->getManager();
        $objects = $em->getRepository("IqiyiAvonBundle:AvonSubject")
                  ->findBy(array('status'=>1), array('addTime'=>'desc'), $size, 0);
        return $objects;
    }

    public function addmsgAction(Request $request)
    {   
        $intention = "subject_form_".date("YmdH");
        $avonSubject = new AvonSubject();
        $form = $this->createFormBuilder($avonSubject, array("intention"=>$intention))
            ->setAction($this->generateUrl('iqiyi_avon_addmsg'))
            ->add('memName', 'text', array('label'=>'姓名：', 'max_length'=>45))
            ->add('memGender', 'choice', array('choices'   => array('0' => '男 ', '1' => '女 '),
                                                'expanded' => true,
                                                'required'  => true, 
                                                'label'=>'性别：'))
            ->add('memMobile', 'text', array( 'label'=>'手机：', 'max_length'=>15))
            ->add('memAddress', 'textarea', array( 'label'=>'地址：', 'max_length'=>60))
            ->add('content', 'textarea', array( 'label'=>'我的瞬间：'))
            ->getForm();

        if($request->isXmlHttpRequest())
        {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $avonSubject->setAddTime(time());

                $avonSubject->setFromType(0);
                $avonSubject->setStatus(0);
                $avonSubject->setTotalVote(0);
                $avonSubject->setMemZip(0);

                $em->persist($avonSubject);
                $em->flush();

                $csrf = $this->get('form.csrf_provider');
                $token = $csrf->generateCsrfToken($intention);
                $errors = array('success'=>1, 'id'=>$avonSubject->getSubjectId(), 'token'=>$token);
                return new JsonResponse($errors);
            }else{
                $errors = array('success'=>0);
                $errors['errorList'] = $this->getErrorMessages($form);
                
                return new JsonResponse($errors);
            }
        }

        return $form->createView();
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

    public function votemsgAction(Request $request)
    {   
        $id = $request->get('id');
        $intention = "vote_form_".date("YmdH");
        //普通赞
        $avonSubjectVote = new AvonSubjectVote();
        $formLike = $this->createFormBuilder($avonSubjectVote, array('validation_groups' => array('normal'), "intention"=>$intention))
            ->setAction($this->generateUrl('iqiyi_avon_votemsg', array('id'=>$id)))
            ->add('subjectId', 'hidden', array('data'=>$id, 'error_bubbling'=>false))
            ->add('voteType', 'hidden', array('data'=>0, 'error_bubbling'=>false))
            ->add('fromType', 'hidden', array('data'=>0, 'error_bubbling'=>false))
            ->getForm();

        $formQuestion = $this->createFormBuilder($avonSubjectVote, array('validation_groups' => array('normal'), "intention"=>$intention))
            ->setAction($this->generateUrl('iqiyi_avon_votemsg', array('id'=>$id)))
            ->add('subjectId', 'hidden', array('data'=>$id, 'error_bubbling'=>false))
            ->add('voteType', 'hidden', array('data'=>1, 'error_bubbling'=>false))
            ->add('fromType', 'hidden', array('data'=>0, 'error_bubbling'=>false))
            ->getForm();

        $formRedeem = $this->createFormBuilder($avonSubjectVote, array('validation_groups' => array('tmall'), "intention"=>$intention))
            ->setAction($this->generateUrl('iqiyi_avon_votemsg', array('id'=>$id)))
            ->add('subjectId', 'hidden', array('data'=>$id, 'error_bubbling'=>false))
            ->add('redeemCode', 'text', array('label'=>'我的天猫码：'))
            ->add('voteType', 'hidden', array('data'=>2, 'error_bubbling'=>false))
            ->add('fromType', 'hidden', array('data'=>0, 'error_bubbling'=>false))
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

                    $csrf = $this->get('form.csrf_provider');
                    $token = $csrf->generateCsrfToken($intention);

                    $avonSubject = $em->getRepository("IqiyiAvonBundle:AvonSubject")->find($avonSubjectVote->getSubjectId());
                    $errors = array('success'=>1, 'id'=>$avonSubjectVote->getSubjectId(), 'vote'=>$avonSubject->getTotalVote(), 'token'=>$token);
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

                    $csrf = $this->get('form.csrf_provider');
                    $token = $csrf->generateCsrfToken($intention);

                    $avonSubject = $em->getRepository("IqiyiAvonBundle:AvonSubject")->find($avonSubjectVote->getSubjectId());
                    $errors = array('success'=>1, 'id'=>$avonSubjectVote->getSubjectId(), 'vote'=>$avonSubject->getTotalVote(), 'token'=>$token);
                    return new JsonResponse($errors);
                }else{
                    $errors = array('success'=>0);
                    $errors['errorList'] = $this->getErrorMessages($formQuestion);
                    
                    return new JsonResponse($errors);
                }
            }
            if($formParams['voteType']==2)
            {
                $formRedeem->handleRequest($request);

                if ($formRedeem->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $avonSubjectVote->setVoteIp($request->getClientIp());
                    $avonSubjectVote->setVoteTime(time());
                    $avonSubjectVote->setStatus(0);

                    $em->persist($avonSubjectVote);
                    $em->flush();

                    $csrf = $this->get('form.csrf_provider');
                    $token = $csrf->generateCsrfToken($intention);

                    $avonSubject = $em->getRepository("IqiyiAvonBundle:AvonSubject")->find($avonSubjectVote->getSubjectId());
                    $errors = array('success'=>1, 'id'=>$avonSubjectVote->getSubjectId(), 'vote'=>$avonSubject->getTotalVote(), 'token'=>$token);
                    return new JsonResponse($errors);
                }else{
                    $errors = array('success'=>0);
                    $errors['errorList'] = $this->getErrorMessages($formRedeem);
                    
                    return new JsonResponse($errors);
                }
            }
        }

        return array('form_like' => $formLike->createView(),
                    'form_question' => $formQuestion->createView(),
                    'form_redeem' => $formRedeem->createView());
    }

    public function addphotoAction(Request $request)
    {
        $intention = "photo_form_".date("YmdH");
        $avonPhoto = new AvonPhoto();
        $form = $this->createFormBuilder($avonPhoto, array( 'intention' => $intention ))
            ->setAction($this->generateUrl('iqiyi_avon_addphoto'))
            ->add('memName', 'text', array('label'=>'姓名：', 'max_length'=>45))
            ->add('memGender', 'choice', array('choices'   => array('0' => '男', '1' => '女'),
                                                'required'  => true, 
                                                'label'=>'性别：'))
            ->add('memMobile', 'text', array( 'label'=>'手机：', 'max_length'=>15))
            ->add('memAddress', 'textarea', array( 'label'=>'地址：', 'max_length'=>60))
            ->add('file', 'file', array( 'label'=>'照片：'))
            ->add('content', 'textarea', array( 'label'=>'我的瞬间：'))
            ->getForm();

        if($request->isXmlHttpRequest())
        {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $avonPhoto->setAddTime(time());
                $avonPhoto->setStatus(0);
                $avonPhoto->setMemZip(0);
                $avonPhoto->setTotalVote(0);
                $em->persist($avonPhoto);
                $em->flush();

                $csrf = $this->get('form.csrf_provider');
                $token = $csrf->generateCsrfToken($intention);
                $errors = array('success'=>1, 'id'=>$avonPhoto->getPhotoId(), 'token'=>$token);
                return new JsonResponse($errors);
            }else{
                $errors = array('success'=>0);
                $errors['errorList'] = $this->getErrorMessages($form);
                
                return new JsonResponse($errors);
            }
        }

            

        return $form->createView();
    }

    public function votephotoAction()
    {
        $id = $request->get('id');
        $intention = "vote_form_".date("YmdH");
        //普通赞
        $avonPhotoVote = new AvonPhotoVote();
        $formLike = $this->createFormBuilder($avonPhotoVote, array('validation_groups' => array('normal'), "intention"=>$intention))
            ->setAction($this->generateUrl('iqiyi_avon_votephoto', array('id'=>$id)))
            ->add('photoId', 'hidden', array('data'=>$id, 'error_bubbling'=>false))
            ->getForm();

        if($request->isXmlHttpRequest())
        {
            $formParams = $request->get('form');
            $formLike->handleRequest($request);

            if ($formLike->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $avonPhotoVote->setVoteIp($request->getClientIp());
                $avonPhotoVote->setVoteTime(time());

                $em->persist($avonPhotoVote);
                $em->flush();

                $csrf = $this->get('form.csrf_provider');
                $token = $csrf->generateCsrfToken($intention);

                $avonPhoto = $em->getRepository("IqiyiAvonBundle:AvonPhoto")->find($avonPhotoVote->getPhotoId());
                $errors = array('success'=>1, 'id'=>$avonPhotoVote->getPhotoId(), 'vote'=>$avonPhoto->getTotalVote(), 'token'=>$token);
                return new JsonResponse($errors);
            }else{
                $errors = array('success'=>0);
                $errors['errorList'] = $this->getErrorMessages($formLike);
                
                return new JsonResponse($errors);
            }
            
        }

        return array('form_like' => $formLike->createView());
    }


}
