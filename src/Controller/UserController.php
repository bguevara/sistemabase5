<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use App\Form\UserType;
use App\Form\UserEditType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{
    private $em;
    private $paginator;
    private $encoder;

    public function __construct(EntityManagerInterface $em,
                                PaginatorInterface $paginator,
                                UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->paginator = $paginator;
        $this->encoder = $encoder;
    }




     /**
     *  @Route("/app/admin/users", name="app_admin_users_index")
     */
    public function indexAction(Request $request)
    {  
        $em = $this->em;
        $query = "SELECT l FROM App\Entity\Users l "
                . "ORDER BY l.name DESC ";
        $entities = $em->createQuery($query);        
        $pagination = $this->paginator->paginate(
                $entities,
                //$this->get('request')->query->get('page',1),
                $request->query->get('page',1),
                20          
        );
        return $this->render('user/index.html.twig', array(
            'pagination'=>$pagination
        ));
    }
    
    /**
     *  @Route("/app/admin/search/users", name="app_admin_users_search_index")
     */
    public function searchAction(Request $request)
    {  
        $em = $this->em;
        $text = $request->get('text');
        $query = "SELECT l FROM App\Entity\Users l "
                . "WHERE "
                . "l.name LIKE :text or l.email LIKE :text "
                . "ORDER BY l.name DESC ";
        $entities = $em->createQuery($query);        
        $entities->setParameter('text', '%'.$text.'%');
        $pagination = $this->paginator->paginate(
                $entities,
                $request->query->get('page',1),
                20          
        );
        return $this->render('user/index.html.twig', array(
            'pagination'=>$pagination, 'texto'=>$text
        ));
    }
    
    /**
     * @Route("/app/admin/edit/users/{id}", name="app_admin_users_edit")
     */
    public function editAction($id)
    {
        $em = $this->em;
        $entity = $em->getRepository('App\Entity\Users')->find($id);        
        $form = $this->editForm($entity);
        return $this->render('user/edit.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    /**
     * @Route("/app/admin/update/users/{id}", name="app_admin_users_update", requirements={"_method":"PUT"})
     */
    public function updateAction($id,Request $request)
    {
        $em = $this->em;
        $entity = $em->getRepository('App\Entity\Users')->find($id);        
        $form = $this->editForm($entity);
        $form->handleRequest($request);
        if($form->isValid())
        {
            ($request->get('selroles')=='ROLE_ADMIN') ?  $entity->setRoles(["ROLE_ADMIN"])  : $entity->setRoles(["ROLE_CLIENT"]); 

            
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('msg-success', 'Registro modificado con exito!');
            return $this->redirect($this->generateUrl('app_admin_users_index'));
        }
        
        return $this->render('user/edit.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    private function editForm(Users $entity)
    {
        $form = $this->createForm(UserEditType::class, $entity, array(
            'action'=>$this->generateUrl('app_admin_users_update', array('id'=>$entity->getId())),
            'method'=>'PUT'
        ))
                ->add('submit', submitType::class, array('label'=>'Guardar','attr'=>array('class'=>'btn btn-primary btn-flat')))
                ;
        return $form;
    }
    
    /**
     * @Route("/app/admin/new/users", name="app_admin_users_new")
     */
    public function newAction()
    {
        $entity = new Users();
        $form = $this->newForm($entity);
        return $this->render('user/new.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    /**
     * @Route("/app/admin/create/users", name="app_admin_users_create", requirements={"_method":"POST"})
     */
    public function createAction(Request $request)
    {
        $em= $this->em;
        $entity = new Users();
        $form = $this->newForm($entity);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $entityExists=$em->getRepository('App\Entity\Users')->findOneBy(array('name'=>$form['name']->getData()));
            if ($entityExists) {
                $this->get('session')->getFlashBag()->add('msg-success', 'Ya existe un Registro con el mismo nombre!');
                return $this->redirect($this->generateUrl('app_admin_users_index')); 
            } else {
                $encoded = $this->encoder->encodePassword($entity, $form['password']->getData());
                ($request->get('selroles')=='ROLE_ADMIN') ?  $entity->setRoles(["ROLE_ADMIN"])  : $entity->setRoles(["ROLE_CLIENT"]); 
                $entity->setLastConexion(new \Datetime());
                $entity->setPassword($encoded);
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('msg-success', 'Registro creado con exito!');
                return $this->redirect($this->generateUrl('app_admin_users_index'));
            }
        }
        return $this->render('user/new.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    private function newForm(Users $entity)
    {
        $form = $this->createForm(UserType::class, $entity, array(
            'action'=>$this->generateUrl('app_admin_users_create'),
            'method'=>'POST'
        ))
                ->add('submit', SubmitType::class, array('label'=>'Guardar','attr'=>array('class'=>'btn btn-primary btn-flat')))
                ;
        return $form;
    }


    /**
     * @Route("/app/admin/remove/users/{id}", name="app_admin_users_remove", requirements={"_method":"POST"})
     */
    public function removeAction($id, Request $request)
    {
        $em = $this->em;
        $entity = $em->getRepository('App\Entity\Users')->find($id);        
        if($entity)
        {
            $em->remove($entity);
            $em->flush();
    
            $data = array("status" => "success",
            "code" => 200,
            'guardado' => 1,
            "msg" => 'El proceso se realizo con exito..');
            return new Response(json_encode($data));

        }
    
        $data = array("status" => "error",
        "code" => 400,
        'guardado' => 0,
        "msg" => 'El proceso no se realizo, error al guardar..');
    return new Response(json_encode($data));

    }
   
}
