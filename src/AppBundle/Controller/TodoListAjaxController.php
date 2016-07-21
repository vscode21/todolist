<?php
/**
 * Created by PhpStorm.
 * User: 21G
 * Date: 20/07/2016
 * Time: 12:30
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

class TodoListAjaxController extends Controller
{
    /**
     * @Route("/ajax", name="ajax_categories_update")
     */
    public function categoriesUpdateAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not an AJAX call!', 400);
        }
        //return new JsonResponse(array('data' => $request->request->get('data')));

        $doctrine = $this->getDoctrine();

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
        $categsRepo = $doctrine->getRepository('AppBundle:TodoCategory');
        $categs = $categsRepo->findall();
        if (count($categs) < 1) {
            if (!isset($em)) {
                $em = $doctrine->getManager();
            }
            $cinit = ['Common'];
            for ($i = 0; $i < count($cinit); ++$i) {
                $c = new TodoCategory();
                $c->setName($cinit[$i]);
                $em->persist($c);
            }
            $em->flush();
            $categs = $categsRepo->findall();
        }
        $categories = array();
        foreach ($categs as $val) {
            $categories[$val->getName()] = $val;
        }

        $html = $this->render('todo/todocategory-item.html.twig', [
            'categories' => $categories,
        ]);

        return new JsonResponse(array('data' => $html->getContent()));
    }
}