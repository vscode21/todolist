<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Proxies\__CG__\AppBundle\Entity\TodoPriority;

use AppBundle\Entity\Todo;
use AppBundle\Entity\TodoCategory;
use AppBundle\Form\TodoType;
use AppBundle\Form\TodoCategoryType;

class TodoListController extends Controller
{
    const KAJAX4CATEGORY = false;

    private function itemManager(Request $request, $twig, $todo = false, $message = '')
    {
        $doctrine = $this->getDoctrine();

        if (empty($todo) || !($todo instanceof Todo)) {
            $persist = true;
            
            $todo = new Todo();
            
            $dateDue = new \DateTime('tomorrow');
            $todo->setDateDue($dateDue);

            $id = 0;
        }
        else {
            $persist = false;
            
            $id = $todo->getId();
        }

        $categories = $doctrine->getRepository('AppBundle:TodoCategory')
            ->findAllItems(true);

        $priorities = $doctrine->getRepository('AppBundle:TodoPriority')
            ->findAllItems(true);

        $form = $this->createForm(TodoType::class, $todo, [
            'categories' => $categories,
            'priorities' => $priorities,
            'submit' => ($id > 0) ? 'Update ToDo' : 'Create ToDo',
        ]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setName($form['name']->getData());
            $todo->setCategory($form['category']->getData());
            $todo->setDescription($form['description']->getData());
            $todo->setPriority($form['priority']->getData());
            $todo->setDateDue($form['date_due']->getData());
            $todo->setDateCreated(new \DateTime('now'));

            if (!isset($em)) {
                $em = $doctrine->getManager();
            }
            
            if ($persist) {
                $em->persist($todo);
            }
            
            $em->flush();
            
            $this->addFlash(
                'notice', 
                $message
            );
            
            return $this->redirectToRoute('homepage');
        }
        
        return $this->render($twig, [
            'form' => $form->createView(),
            'link' => ($id > 0) ? '/category/'.$id : '/category',
        ]);
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $repo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo');

        $repo->createTable();

        $todos = $repo->findall();

        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
        ]);
    }
    
    /**
     * @Route("/create", name="create")
     */
    public function createAction(Request $request)
    {
        return $this->itemManager($request, 'todo/create.html.twig', false, 'ToDo Created!');
    }
    
    /**
     * @Route("/delete/{id}", name="delete", defaults={ "id": 0 }, requirements={ "id": "\d+" })
     */
    public function deleteAction($id)
    {
        $doctrine = $this->getDoctrine();

        $todo = $doctrine->getRepository('AppBundle:Todo')
            ->find($id);
        
        $em = $doctrine->getManager();
        
        $em->remove($todo);
        
        $em->flush();
        
        $this->addFlash(
            'notice',
            'ToDo Removed!'
        );
        
        return $this->redirectToRoute('homepage');
    }
    
    /**
     * @Route("/details/{id}", name="details", defaults={ "id": 0 }, requirements={ "id": "\d+" })
     * Security("has_role('ROLE_ADMIN')")
     */
    public function detailsAction($id)
    {
        /*if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');*/

        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);
        
        return $this->render('todo/details.html.twig', [
            'todo' => $todo,
        ]);
    }
    
    /**
     * @Route("/edit/{id}", name="edit", defaults={ "id": 0 }, requirements={ "id": "\d+" })
     */
    public function editAction(Request $request, $id)
    {
        if ($id < 1) {
            return $this->createAction($request);
        }
        else {
            $todo = $this->getDoctrine()
                ->getRepository('AppBundle:Todo')
                ->find($id);
            
            return $this->itemManager($request, 'todo/edit.html.twig', $todo, 'ToDo Updated!');
        }
    }
    
    /**
     * @Route("/category", name="category")
     */
    public function categoryAction(Request $request)
    {
        return $this->categoryAction_($request, 0);
    }
    
    /**
     * @Route("/category/{id}", name="category", defaults={ "id": 0 }, requirements={ "id": "\d+" })
     */
    public function categoryAction_(Request $request, $id)
    {
        /*
        0 => TodoCategory {
            id: 1
            name: "Work"
            todos: PersistentCollection {
                ...
            }
        }
        ...
        */
        $doctrine = $this->getDoctrine();

        $categories = $doctrine->getRepository('AppBundle:TodoCategory')
            ->findall();

        $category = new TodoCategory();
        
        $form = $this->createForm(TodoCategoryType::class, $category);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $category->setName($form['name']->getData());
                
                $em = $doctrine->getManager();
                
                $em->persist($category);
                
                $em->flush();

                if (!self::KAJAX4CATEGORY) {
                    if ($id < 1) {
                        return $this->redirectToRoute('create');
                    } else {
                        return $this->redirectToRoute('edit', ['id' => $id]);
                    }
                }
            }
        }
        
        return $this->render('todo/todocategory.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'id' => $id,
            'ajax' => self::KAJAX4CATEGORY,
        ]);
    }
}