<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TaskController extends Controller
{
    /**
     * @Route("/task", name="task-page")
     * @param TaskRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(TaskRepository $repository)
    {
        $tasks = $repository->findAll();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @param Task $task
     * @Route("/task/{id}", name="task-show")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Task $task)
    {
        return $this->render('task/show.html.twig', [
            'task' => $task
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/task-new", name="task-new")
     */
    public function new(Request $request)
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('task-show', [
                'id' => $task->getId()
            ]);
        }

        return $this->render('task/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/task/{id}/edit", name="task-edit")
     * @param Request $request
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, Task $task)
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('task-show', [
                'id' => $task->getId()
            ]);
        }
        $search = $request->get('search');
        if (isset($search)) {
            var_dump('empty');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param TaskRepository $repository
     * @return JsonResponse
     * @Route("/search-tag", name="search-tag", methods={"GET"})
     */
    public function searchTag(Request $request, TaskRepository $repository)
    {
        if ($request->isXmlHttpRequest()) {
            $search = $request->get('q');

            if (!isset($search)) {
                return new JsonResponse(['success' => false]);
            }

            $tags = $repository->searchTag($search);

            return new JsonResponse([
                'success' => true,
                'search' => $search,
                'tags' => $tags
            ]);
        }
    }
}