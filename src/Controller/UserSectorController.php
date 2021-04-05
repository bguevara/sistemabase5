<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\UsersSector;
use App\Form\UsersSectorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;


class UserSectorController extends AbstractController
{
  
    private $em;
    private $paginator;
    public function __construct(EntityManagerInterface $em,
                                PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->paginator = $paginator;
    }




     /**
     *  @Route("/app/admin/user_sectors", name="app_admin_user_sectors_index")
     */
    public function indexAction(Request $request)
    {  
        $em = $this->em;
        $query = "SELECT l FROM App\Entity\UsersSector l join l.users u "
                . "ORDER BY u.email DESC ";
        $entities = $em->createQuery($query);        
        $pagination = $this->paginator->paginate(
                $entities,
                //$this->get('request')->query->get('page',1),
                $request->query->get('page',1),
                20          
        );
        return $this->render('user_sector/index.html.twig', array(
            'pagination'=>$pagination
        ));
    }
    
    /**
     *  @Route("/app/admin/search/user_sectors", name="app_admin_user_sectors_search_index")
     */
    public function searchAction(Request $request)
    {  
        $em = $this->em;
        $text = $request->get('text');
        $query = "SELECT l FROM App\Entity\UsersSector l join l.users u "
                . "WHERE "
                . "u.email LIKE :text "
                . "ORDER BY u.name DESC ";
        $entities = $em->createQuery($query);        
        $entities->setParameter('text', '%'.$text.'%');
        $pagination = $this->paginator->paginate(
                $entities,
                $request->query->get('page',1),
                20          
        );
        return $this->render('user_sector/index.html.twig', array(
            'pagination'=>$pagination, 'texto'=>$text
        ));
    }
    
  
  
    
    /**
     * @Route("/app/admin/new/user_sectors", name="app_admin_user_sectors_new")
     */
    public function newAction()
    {
        $entity = new UsersSector();
        $form = $this->newForm($entity);
        return $this->render('user_sector/new.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    /**
     * @Route("/app/admin/create/user_sectors", name="app_admin_user_sectors_create", requirements={"_method":"POST"})
     */
    public function createAction(Request $request)
    {
        $em= $this->em;
        $entity = new UsersSector();
        $form = $this->newForm($entity);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $entityExists=$em->getRepository('App\Entity\UsersSector')->findOneBy(array('users'=>$form['users']->getData(),'sectors'=>$form['sectors']->getData()));
            if ($entityExists) {
                $this->get('session')->getFlashBag()->add('msg-error', 'Ya existe un Registro con el mismo nombre!');
                return $this->redirect($this->generateUrl('app_admin_user_sectors_index')); 
            } else {
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('msg-success', 'Registro creado con exito!');
                return $this->redirect($this->generateUrl('app_admin_user_sectors_index'));
            }
        }
        return $this->render('user_sector/new.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    private function newForm(UsersSector $entity)
    {
        $form = $this->createForm(UsersSectorType::class, $entity, array(
            'action'=>$this->generateUrl('app_admin_user_sectors_create'),
            'method'=>'POST'
        ))
                ->add('submit', SubmitType::class, array('label'=>'Guardar','attr'=>array('class'=>'btn btn-primary btn-flat')))
                ;
        return $form;
    }


    /**
     * @Route("/app/admin/remove/user_sectors/{id}", name="app_admin_user_sectors_remove", requirements={"_method":"POST"})
     */
    public function removeAction($id, Request $request)
    {
        $em = $this->em;
        $entity = $em->getRepository('App\Entity\UsersSector')->find($id);        
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
