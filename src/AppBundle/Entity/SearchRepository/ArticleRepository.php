<?php

namespace AppBundle\Entity\SearchRepository;

use FOS\ElasticaBundle\Repository;
use AppBundle\Model\ArticleSearch;

class ArticleRepository extends Repository
{
    ///elasticsearch-2.4.0
    public function search(ArticleSearch $articleSearch)
    {

        // we create a query to return all the articles
        // but if the criteria title is specified, we use it
        if ($articleSearch->getTitle() != null && $articleSearch != '') {
            $query = new \Elastica\Query\Match();
            $query->setFieldQuery('article.title', $articleSearch->getTitle());
            $query->setFieldFuzziness('article.title', 0.7);
            $query->setFieldMinimumShouldMatch('article.title', '80%');
            //
        } else {
            $query = new \Elastica\Query\MatchAll();
        }
        
        $baseQuery = $query;

        // then we create filters depending on the chosen criterias
        $boolFilter = new \Elastica\Filter\Bool();

        /*
            Dates filter
            We add this filter only the getIspublished filter is not at "false"
        */
        if("false" != $articleSearch->getIsPublished()
           && null !== $articleSearch->getDateFrom()
           && null !== $articleSearch->getDateTo())
        {

            $boolFilter->addMust(new \Elastica\Filter\Range('publishedAt',
                array(
                    'gte' => \Elastica\Util::convertDate($articleSearch->getDateFrom()->getTimestamp()),
                    'lte' => \Elastica\Util::convertDate($articleSearch->getDateTo()->getTimestamp())
                )
            ));
        }

        // Published or not filter
        if($articleSearch->getIsPublished() !== null){
            $boolFilter->addMust(
                new \Elastica\Filter\Terms('published', array($articleSearch->getIsPublished()))
            );
        }

        $filtered = new \Elastica\Query\Filtered($baseQuery, $boolFilter);

        $query = \Elastica\Query::create($filtered);

        return $this->find($query);
    }

    ///elasticsearch 5.0 - https://github.com/FriendsOfSymfony/FOSElasticaBundle/blob/master/Resources/doc/usage.md
    public function newSearch(ArticleSearch $articleSearch)
    {

        // we create a query to return all the articles
        // but if the criteria title is specified, we use it
        if ($articleSearch->getTitle() != null && $articleSearch != '') {
            $query = new \Elastica\Query\Match();
            $query->setFieldQuery('title', $articleSearch->getTitle());
            //$query->setFieldFuzziness('article.title', 0.7);
            //$query->setFieldMinimumShouldMatch('article.title', '80%');
            //
        } else {
            $query = new \Elastica\Query\MatchAll();
        }
        
        $baseQuery = $query;

        // then we create filters depending on the chosen criterias
        //$boolFilter = new \Elastica\Filter\Bool();
        $boolFilter = new \Elastica\Query\BoolQuery();
        
        $boolFilter->addMust($baseQuery);

        /*
            Dates filter
            We add this filter only the getIspublished filter is not at "false"
        */
        if("false" != $articleSearch->getIsPublished()
           && null !== $articleSearch->getDateFrom()
           && null !== $articleSearch->getDateTo())
        {

            $boolFilter->addMust(new \Elastica\Query\Range('publishedAt',
                array(
                    'gte' => \Elastica\Util::convertDate($articleSearch->getDateFrom()->getTimestamp()),
                    'lte' => \Elastica\Util::convertDate($articleSearch->getDateTo()->getTimestamp())
                )
            ));
        }

        // Published or not filter
        if($articleSearch->getIsPublished() !== null){
            $boolFilter->addMust(
                new \Elastica\Filter\Terms('published', array($articleSearch->getIsPublished()))
            );
        }

       // $filtered = new \Elastica\Query\Filtered($baseQuery, $boolFilter);

        // $query = \Elastica\Query::create($filtered);
        //$query = \Elastica\Query::create($boolFilter);

        return $this->find($boolFilter);
    }

}