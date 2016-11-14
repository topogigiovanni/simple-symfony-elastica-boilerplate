<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ArticleSearchType;
use AppBundle\Form\Type\ArticleSaveType;
use AppBundle\Model\ArticleSearch;
use AppBundle\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
	/**
     * @Route("/search", name="article_search")
     */
    public function listAction(Request $request)
    {

        $articleSearch = new ArticleSearch();

        $articleSearchForm = $this->get('form.factory')
            ->createNamed(
                '',
                //'article_search_type',
                'AppBundle\Form\Type\ArticleSearchType',
                $articleSearch,
                array(
                    //'action' => $this->generateUrl('obtao-article-search'),
                    'action' => $this->generateUrl('article_search'),
                    'method' => 'GET'
                )
            );

        $articleSearchForm->handleRequest($request);
        $articleSearch = $articleSearchForm->getData();
        
        $elasticaManager = $this->container->get('fos_elastica.manager');
        // $results = $elasticaManager->getRepository('AppBundle:Article')->search($articleSearch);
        $results = $elasticaManager->getRepository('AppBundle:Article')->search($articleSearch);

        Dump($results);

        return $this->render('article/list.html.twig',array(
            'results' => $results,
            'form' => $articleSearchForm->createView(),
        ));

    }

    /**
     * @Route("/create", name="article_create")
     */
    public function createAction(Request $request)
    {

        $article = new Article();

        $articleSearchForm = $this->get('form.factory')
            ->createNamed(
                '',
                'AppBundle\Form\Type\ArticleSaveType',
                $article,
                array(
                    //'action' => $this->generateUrl('obtao-article-search'),
                    'action' => $this->generateUrl('article_create'),
                    'method' => 'POST'
                )
            );

        $articleSearchForm->handleRequest($request);
        
        if ($articleSearchForm->isSubmitted() && $articleSearchForm->isValid()) {
        	$article = $articleSearchForm->getData();
	        $em = $this->getDoctrine()->getManager();

	        // handle entity
	        $article->setPublishedAt(new \DateTime());

	        $em->persist($article);
	        $em->flush();

	        $this->addFlash(
	            'success',
	            'Saved!'
	        );
        }

        return $this->render('article/save.html.twig',array(
            'form' => $articleSearchForm->createView(),
        ));

    }

    /**
     * @Route("/edit/{id}", name="article_edit")
     */
    public function editAction(Article $article, Request $request)
    {

        $articleSearchForm = $this->get('form.factory')
            ->createNamed(
                '',
                'AppBundle\Form\Type\ArticleSaveType',
                $article,
                array(
                    //'action' => $this->generateUrl('obtao-article-search'),
                    'action' => $this->generateUrl('article_edit', array('id' => $article->getId())),
                    'method' => 'POST'
                )
            ); 


        $articleSearchForm->handleRequest($request);       
        
        if ($articleSearchForm->isSubmitted() && $articleSearchForm->isValid()) {
        	
        	$article = $articleSearchForm->getData();
	        $em = $this->getDoctrine()->getManager();

	        $em->merge($article);
			$em->flush();

	        $this->addFlash(
	            'success',
	            'Edited!'
	        );
        }

        return $this->render('article/save.html.twig',array(
            'form' => $articleSearchForm->createView(),
        ));

    }

    /**
     * @Route("/delete/{id}", name="article_delete")
     */
    public function deleteAction(Article $article, Request $request)
    {
      
        
        if (!is_null($article)) {
        	
	        $em = $this->getDoctrine()->getManager();

	        $em->remove($article);
	        $em->flush();

	        $this->addFlash(
	            'success',
	            'Deleted!'
	        );

        }

        return $this->redirectToRoute('article_search', [
		    'request' => $request
		], 301);


    }
}