<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ArticleSearchType;
use AppBundle\Model\ArticleSearch;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
	/**
     * @Route("/pesquisa", name="article_search")
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
        $results = $elasticaManager->getRepository('AppBundle:Article')->newSearch($articleSearch);

        Dump($results);

        return $this->render('article/list.html.twig',array(
            'results' => $results,
            'form' => $articleSearchForm->createView(),
        ));
    }
}