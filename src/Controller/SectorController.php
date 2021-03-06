<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Sectors;
use App\Form\SectorsType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;

class SectorController extends AbstractController
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
     *  @Route("/app/admin/sectors", name="app_admin_sectors_index")
     */
    public function indexAction(Request $request)
    {  
        $em = $this->em;
        $query = "SELECT l FROM App\Entity\Sectors l "
                . "ORDER BY l.name DESC ";
        $entities = $em->createQuery($query);        
        $pagination = $this->paginator->paginate(
                $entities,
                //$this->get('request')->query->get('page',1),
                $request->query->get('page',1),
                20          
        );
        return $this->render('sector/index.html.twig', array(
            'pagination'=>$pagination
        ));
    }
    
    /**
     *  @Route("/app/admin/search/sectors", name="app_admin_sectors_search_index")
     */
    public function searchAction(Request $request)
    {  
        $em = $this->em;
        $text = $request->get('text');
        $query = "SELECT l FROM App\Entity\Sectors l "
                . "WHERE "
                . "l.name LIKE :text "
                . "ORDER BY l.name DESC ";
        $entities = $em->createQuery($query);        
        $entities->setParameter('text', '%'.$text.'%');
        $pagination = $this->paginator->paginate(
                $entities,
                $request->query->get('page',1),
                20          
        );
        return $this->render('sector/index.html.twig', array(
            'pagination'=>$pagination, 'texto'=>$text
        ));
    }
    
    /**
     * @Route("/app/admin/edit/sectors/{id}", name="app_admin_sectors_edit")
     */
    public function editAction($id)
    {
        $em = $this->em;
        $entity = $em->getRepository('App\Entity\Sectors')->find($id);        
        $form = $this->editForm($entity);
        return $this->render('sector/edit.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    /**
     * @Route("/app/admin/update/sectors/{id}", name="app_admin_sectors_update", requirements={"_method":"PUT"})
     */
    public function updateAction($id,Request $request)
    {
        $em = $this->em;
        $entity = $em->getRepository('App\Entity\Sectors')->find($id);        
        $form = $this->editForm($entity);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('msg-success', 'Registro modificado con exito!');
            return $this->redirect($this->generateUrl('app_admin_sectors_index'));
        }
        
        return $this->render('sector/edit.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    private function editForm(Sectors $entity)
    {
        $form = $this->createForm(SectorsType::class, $entity, array(
            'action'=>$this->generateUrl('app_admin_sectors_update', array('id'=>$entity->getId())),
            'method'=>'PUT'
        ))
                ->add('submit', submitType::class, array('label'=>'Guardar','attr'=>array('class'=>'btn btn-primary btn-flat')))
                ;
        return $form;
    }
    
    /**
     * @Route("/app/admin/new/sectors", name="app_admin_sectors_new")
     */
    public function newAction()
    {
        $entity = new Sectors();
        $form = $this->newForm($entity);
        return $this->render('sector/new.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    /**
     * @Route("/app/admin/create/sectors", name="app_admin_sectors_create", requirements={"_method":"POST"})
     */
    public function createAction(Request $request)
    {
        $em= $this->em;
        $entity = new Sectors();
        $form = $this->newForm($entity);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $entityExists=$em->getRepository('App\Entity\Sectors')->findOneBy(array('name'=>$form['name']->getData()));
            if ($entityExists) {
                $this->get('session')->getFlashBag()->add('msg-success', 'Ya existe un Registro con el mismo nombre!');
                return $this->redirect($this->generateUrl('app_admin_sectors_index')); 
            } else {
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('msg-success', 'Registro creado con exito!');
                return $this->redirect($this->generateUrl('app_admin_sectors_index'));
            }
        }
        return $this->render('sector/new.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    private function newForm(Sectors $entity)
    {
        $form = $this->createForm(SectorsType::class, $entity, array(
            'action'=>$this->generateUrl('app_admin_sectors_create'),
            'method'=>'POST'
        ))
                ->add('submit', SubmitType::class, array('label'=>'Guardar','attr'=>array('class'=>'btn btn-primary btn-flat')))
                ;
        return $form;
    }


    /**
     * @Route("/app/admin/remove/sectors/{id}", name="app_admin_sectors_remove", requirements={"_method":"POST"})
     */
    public function removeAction($id, Request $request)
    {
        $em = $this->em;
        $entity = $em->getRepository('App\Entity\Sectors')->find($id);        
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


     /**
     * @Route("/app/admin/get/sectors", name="app_admin_sectors_get", requirements={"_method":"POST"})
     */
    public function getSectorsAction(Request $request)
    {
        $em = $this->em;

        $roleUser=$this->getUser()->getRoles()[0];
        $userId=$this->getUser()->getId();
     
        if ($roleUser=='ROLE_ADMIN') {
            $query = "SELECT s.id,s.name FROM App\Entity\Sectors s "
            .  "ORDER BY s.name DESC ";

        } else {
             $query = "SELECT s.id,s.name FROM App\Entity\Companies l join l.sector s "
             .  "WHERE  "
             . " s.id in (SELECT se.id FROM App\Entity\UsersSector k join k.users u join k.sectors se  where u.id='$userId' )"
             .  "ORDER BY l.name DESC ";
        }    
        
        $entities = $em->createQuery($query);        
      
        if($entities)
        {
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


      /**
     * @Route("/app/get/sectors", name="app_sectors_get", requirements={"_method":"POST"})
     */
    public function optgetSectors(Request $request)
    {
        $em = $this->em;
        $roleUser=$this->getUser()->getRoles()[0];
        $userId=$this->getUser()->getId();
        if ($roleUser=='ROLE_ADMIN') {
            $query = "SELECT distinct s.id,s.name FROM App\Entity\Sectors s "
            .  "ORDER BY s.name ASC ";

        } else {
             $query = "SELECT distinct s.id,s.name FROM App\Entity\Companies l join l.sector s "
             .  "WHERE  "
             . " s.id in (SELECT se.id FROM App\Entity\UsersSector k join k.users u join k.sectors se  where u.id='$userId' )"
             .  "ORDER BY l.name ASC ";
        }    
        $response=[];
        $entities = $em->createQuery($query)->getResult();        
        if ($entities) {
            foreach ($entities as $value) {
                $dato['id'] = $value['id'];
                $dato['text'] = trim($value['name']);
                $dato['seleccionado'] = 0;
                $response[] = $dato;
            }
        } else {
            $dato['id'] = 0;
            $dato['text'] = 'cliente no tiene asociados sectores';
            $dato['seleccionado'] = 0;
            $response[] = $dato;
        }
        $respuesta = new Response(json_encode($response));
        $respuesta->headers->set('Content-Type', 'application/json');
        return $respuesta;
    }

   
}
