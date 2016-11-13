<?php

namespace AppBundle\Entity\SearchRepository;

use FOS\ElasticaBundle\Repository;
use AppBundle\Model\ArticleSearch;

class ArticleRepository extends Repository
{
    
    ///works on elasticsearch-2.4.0 and elasticsearch 5.0 - https://github.com/FriendsOfSymfony/FOSElasticaBundle/blob/master/Resources/doc/usage.md
    public function search(ArticleSearch $articleSearch)
    {

        // we create a query to return all the articles
        // but if the criteria title is specified, we use it
        if ($articleSearch->getTitle() != null && $articleSearch != '') {
            $query = new \Elastica\Query\Match();
            $query->setFieldQuery('title', $articleSearch->getTitle());
        } else {
            $query = new \Elastica\Query\MatchAll();
        }
        
        $baseQuery = $query;

        // then we create filters depending on the chosen criterias

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

        return $this->find($boolFilter);
        
    }

}