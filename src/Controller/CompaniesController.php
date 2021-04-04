<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Companies;
use App\Form\CompaniesType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;

class CompaniesController extends AbstractController
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
     *  @Route("/app/admin/companies", name="app_admin_companies_index")
     */
    public function indexAction(Request $request)
    {  
        $em = $this->em;
        $query = "SELECT l FROM App\Entity\Companies l "
                . "ORDER BY l.name DESC ";
        $entities = $em->createQuery($query);        
        $pagination = $this->paginator->paginate(
                $entities,
                //$this->get('request')->query->get('page',1),
                $request->query->get('page',1),
                20          
        );
        return $this->render('companies/index.html.twig', array(
            'pagination'=>$pagination
        ));
    }
    
    /**
     *  @Route("/app/admin/search/companies", name="app_admin_companies_search_index")
     */
    public function searchAction(Request $request)
    {  
        $em = $this->em;
        $text = $request->get('text');
        $query = "SELECT l FROM App\Entity\Companies l "
                . "WHERE "
                . "l.name LIKE :text or l.email like :text or l.phone like :text   "
                . "ORDER BY l.name DESC ";
        $entities = $em->createQuery($query);        
        $entities->setParameter('text', '%'.$text.'%');
        $pagination = $this->paginator->paginate(
                $entities,
                $request->query->get('page',1),
                20          
        );
        return $this->render('companies/index.html.twig', array(
            'pagination'=>$pagination, 'texto'=>$text
        ));
    }
    
    /**
     * @Route("/app/admin/edit/companies/{id}", name="app_admin_companies_edit")
     */
    public function editAction($id)
    {
        $em = $this->em;
        $entity = $em->getRepository('App\Entity\Companies')->find($id);        
        $form = $this->editForm($entity);
        return $this->render('companies/edit.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    /**
     * @Route("/app/admin/update/companies/{id}", name="app_admin_companies_update", requirements={"_method":"PUT"})
     */
    public function updateAction($id,Request $request)
    {
        $em = $this->em;
        $entity = $em->getRepository('App\Entity\Companies')->find($id);        
        $form = $this->editForm($entity);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('msg-success', 'Registro modificado con exito!');
            return $this->redirect($this->generateUrl('app_admin_companies_index'));
        }
        
        return $this->render('companies/edit.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    private function editForm(Companies $entity)
    {
        $form = $this->createForm(CompaniesType::class, $entity, array(
            'action'=>$this->generateUrl('app_admin_companies_update', array('id'=>$entity->getId())),
            'method'=>'PUT'
        ))
                ->add('submit', submitType::class, array('label'=>'Guardar','attr'=>array('class'=>'btn btn-primary btn-flat')))
                ;
        return $form;
    }
    
    /**
     * @Route("/app/admin/new/companies", name="app_admin_companies_new")
     */
    public function newAction()
    {
        $entity = new Companies();
        $form = $this->newForm($entity);
        return $this->render('companies/new.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    /**
     * @Route("/app/admin/create/companies", name="app_admin_companies_create", requirements={"_method":"POST"})
     */
    public function createAction(Request $request)
    {
        $em= $this->em;
        $entity = new Companies();
        $form = $this->newForm($entity);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $entityExists=$em->getRepository('App\Entity\Companies')->findOneBy(array('name'=>$form['name']->getData()));
            if ($entityExists) {
                $this->get('session')->getFlashBag()->add('msg-success', 'Ya existe un Registro con el mismo nombre!');
                return $this->redirect($this->generateUrl('app_admin_companies_index')); 
            } else {
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('msg-success', 'Registro creado con exito!');
                return $this->redirect($this->generateUrl('app_admin_companies_index'));
            }
        }
        return $this->render('companies/new.html.twig', array(
            'form'=>$form->createView()
        ));
    }
    
    private function newForm(Companies $entity)
    {
        $form = $this->createForm(CompaniesType::class, $entity, array(
            'action'=>$this->generateUrl('app_admin_companies_create'),
            'method'=>'POST'
        ))
                ->add('submit', SubmitType::class, array('label'=>'Guardar','attr'=>array('class'=>'btn btn-primary btn-flat')))
                ;
        return $form;
    }


    /**
     * @Route("/app/admin/remove/companies/{id}", name="app_admin_companies_remove", requirements={"_method":"PUT"})
     */
    public function removeAction($id, Request $request)
    {
        $em = $this->em;
        $entity = $em->getRepository('App\Entity\Companies')->find($id);        
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

