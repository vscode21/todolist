<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Todo;
use AppBundle\Entity\TodoCategory;
use AppBundle\Form\TodoType;
use AppBundle\Form\TodoCategoryType;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TodoListController extends Controller
{
    private function itemManager(Request $request, $twig, $todo = false, $button = 'Save', $message = '')
    {
        $id = 0;
        
        if (empty($todo) || !($todo instanceof Todo)) {
            $persist = true;
            
            $todo = new Todo();
            
            $dateDue = new \DateTime('tomorrow');
            $todo->setDateDue($dateDue);
        }
        else {
            $persist = false;
            
            $id = $todo->getId();
        }
        
        $categs = $this->getDoctrine()
            ->getRepository('AppBundle:TodoCategory')
            ->findall();
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
        
        $categories = array();
        foreach ($categs as $val) {
            $categories[$val->getName()] = $val;
        }
        
        $priors = $this->getDoctrine()
            ->getRepository('AppBundle:TodoPriority')
            ->findall();
/*
0 => TodoPriority {
    id: 1
    name: "Normal"
    todos: PersistentCollection {
        ...
    }
}
...
*/
        
        $priorities = array();
        foreach ($priors as $val) {
            $priorities[$val->getName()] = $val;
        }
        
        $form = $this->createForm(TodoType::class, $todo, [
            'categories' => $categories,
            'priorities' => $priorities,
            'submit' => ($id > 0) ? 'Update ToDo' : 'Create ToDo',
        ]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $dateDue = $form['date_due']->getData();
            
            $dateCreate = new \DateTime('now');
            
            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDateDue($dateDue);
            $todo->setDateCreated($dateCreate);
            
            $em = $this->getDoctrine()
                ->getManager();
            
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
    public function indexAction(Request $request)
    {
        $todos = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->findall();
        
        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
        ]);
    }
    
    /**
    * @Route("/create", name="create")
    */
    public function createAction(Request $request)
    {
        return $this->itemManager($request, 'todo/create.html.twig', false, 'Create ToDo', 'ToDo Created!');
    }
    
    /**
    * @Route("/delete/{id}", name="delete", defaults={ "id": 0 }, requirements={ "id": "\d+" })
    */
    public function deleteAction(Request $request, $id)
    {
        $todo = $this->getDoctrine()
            ->getRepository('AppBundle:Todo')
            ->find($id);
        
        $em = $this->getDoctrine()
            ->getManager();
        
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
    */
    public function detailsAction(Request $request, $id)
    {
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
            
            return $this->itemManager($request, 'todo/edit.html.twig', $todo, 'Update ToDo', 'ToDo Updated!');
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
        $categories = $this->getDoctrine()
            ->getRepository('AppBundle:TodoCategory')
            ->findall();
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
        $category = new TodoCategory();
        
        $form = $this->createForm(TodoCategoryType::class, $category);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $category->setName($form['name']->getData());
                
                $em = $this->getDoctrine()
                    ->getManager();
                
                $em->persist($category);
                
                $em->flush();
                
                if ($id < 1) {
                    return $this->redirectToRoute('create');
                }
                else {
                    return $this->redirectToRoute('edit', ['id' => $id]);
                }
            }
        }
        
        return $this->render('todo/todocategory.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'id' => $id,
        ]);
    }
}