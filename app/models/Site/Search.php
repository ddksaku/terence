<?php

namespace Models\Site;

class Search
{
    protected $totalEntries;
    
    protected $perPage;
    protected $totalPages;
    protected $currentPage;
    
    protected $resultsRaw = array();
    protected $results = array();
    
    protected $paginator;
    
    protected $query;
    
    protected $tag;
    protected $tagged;
    protected $taggedId;
    protected $tagFailed = false;
    
    protected $pdoParams = array();
    
    protected $filters = array();
    
    /* */
    
    public function setQuery($query)
    {
        $this->query = $query;
        
        return $this->query;
    }
    
    public function getTagged()
    {
        return $this->tag;
    }
    
    public function setTagged($tagged)
    {
        if (($this->tagged = $tagged)) {
            if (!($this->tag = \Models\Site\Tag::where('tag_url', '=', $this->tagged)->first())) {
                $this->tagFailed = true;
            } else {
                $this->taggedId = $this->tag->tag_id;
            }
        }
        
        return $this;
    }
    
    public function setFilter($filter)
    {
        $this->filters[$filter] = $filter;
        
        return $this;
    }
    
    /* */
    
    protected function passesFilter($type)
    {
        return (empty($this->filters) || isset($this->filters[$type]));
    }
    
    /* */
    
    protected function useParams()
    {
        $params = $this->pdoParams;
        
        $this->pdoParams = array();
        
        return $params;
    }
    
    protected function applyCriteria($query, $textFields, $tagTable = null, $linkField = null)
    {
        $fromTable = $query->from;
        
        /* Text search in text fields */

        if ($this->query) {
            if (is_string($textFields)) {
                $textFields = array($textFields);
            }

            if (!empty($textFields)) {
                $query->where(function ($where) use ($textFields, $fromTable, $tagTable, $linkField) {
                    foreach ($textFields as $field) {
                        $where->orWhere($field, 'LIKE', \DB::raw('?'));
                        $this->pdoParams[] = "%{$this->query}%";
                    }

                    /* Text search in tags */

                    if ($tagTable && $linkField) {
                        $where->orWhere(\DB::raw(0), '<', function ($innerWhere) use ($fromTable, $tagTable, $linkField) {
                            $innerWhere->select(\DB::raw('COUNT(*)'))
                                ->from($tagTable)
                                ->leftJoin('synergy_tags', function ($join) use ($tagTable) {
                                    $join->on('synergy_tags.tag_id', '=', "{$tagTable}.tag_id");
                                })
                                ->where($tagTable.'.'.$linkField, '=', \DB::raw("`{$fromTable}`.`{$linkField}`"))
                                ->where('synergy_tags.tag_name', 'LIKE', \DB::raw('?'));
                        });

                        $this->pdoParams[] = "%{$this->query}%";
                    }
                });
            }
        }
        
        /* Filter by tags */

        if ($tagTable && $linkField && $this->taggedId) {
            $query->where(\DB::raw(0), '<', function ($where) use ($fromTable, $tagTable, $linkField) {
                $where->select(\DB::raw('COUNT(*)'))
                    ->from($tagTable)
                    ->where($tagTable.'.'.$linkField, '=', \DB::raw("`{$fromTable}`.`{$linkField}`"))
                    ->where('tag_id', '=', \DB::raw('?'));
            });
            
            $this->pdoParams[] = $this->taggedId;
        }

        return $query;
    }
    
    /* */
    
    public function tagLookupFailed()
    {
        return $this->tagFailed;
    }
    
    public function prepareSearch($currentPage = 1, $perPage = 15)
    {
        /* Pagination logic pt 1 */

        $this->perPage = $perPage;
        
        /* Quit early if there's a dodgy tag lookup */
        
        if ($this->tagLookupFailed()) {
            return $this;
        }

        /* Create reusable conditionals */
        
        $applyPageConditions = function ($query) {
            $query->where('page_active', '=', \DB::raw(1));

            return $this->applyCriteria(
                $query,
                array('page_title', 'page_description'),
                'synergy_page_tag_links',
                'page_id'
            );
        };
        
        $applyServiceConditions = function ($query) {
            $query->where('service_active', '=', \DB::raw(1));
            
            return $this->applyCriteria(
                $query,
                array('service_title', 'service_description'),
                'synergy_service_tag_links',
                'service_id'
            );
        };
        
        $applyNewsConditions = function ($query) {
            $query->where('news_active', '=', \DB::raw(1))
                ->where('news_publish_date', '<=', \DB::raw(time()));
            
            return $this->applyCriteria(
                $query,
                array('news_title', 'news_description'),
                'synergy_news_tag_links',
                'news_id'
            );
        };
        
        $applyServiceCategoryConditions = function ($query) {
            $query->where('category_active', '=', \DB::raw(1));
            
            return $this->applyCriteria(
                $query,
                array('category_title', 'category_description'),
                'synergy_service_category_tag_links',
                'category_id'
            );
        };
        
        $applyNewsCategoryConditions = function ($query) {
            $query->where('category_active', '=', \DB::raw(1));
            
            return $this->applyCriteria(
                $query,
                array('category_title', 'category_description'),
                'synergy_news_category_tag_links',
                'category_id'
            );
        };
        
        $applyPortfolioConditions = function ($query) {
            $query->where('portfolio_active', '=', \DB::raw(1));
            
            return $this->applyCriteria(
                $query,
                array('portfolio_title', 'portfolio_description'),
                'synergy_portfolio_tag_links',
                'portfolio_id'
            );
        };
        
        $applyPortfolioCategoryConditions = function ($query) {
            $query->where('category_active', '=', \DB::raw(1));
            
            return $this->applyCriteria(
                $query,
                array('category_title', 'category_description'),
                'synergy_portfolio_category_tag_links',
                'category_id'
            );
        };
        
        $applyAlbumConditions = function ($query) {
            $query->where(\DB::raw('0'), '<', function($query)
            {
                $query->select(\DB::raw('COUNT(*)'))
                        ->from('synergy_pictures')
                        ->where('picture_album_id', '=', \DB::raw('`synergy_albums`.`album_id`'));
            });
            
            return $this->applyCriteria(
                $query,
                array('album_name')
            );
        };
        
        /* Build SQL to count total entries. */

        $queryUnions = array();
        
        if ($this->passesFilter('pages')) {
            $queryUnions[] = $applyPageConditions(
                \DB::table('synergy_pages')
                    ->addSelect(\DB::raw('COUNT(*) AS entry_count'))
            )->toSql();
        }
        
        if ($this->passesFilter('services')) {
            $queryUnions[] = $applyServiceConditions(
                \DB::table('synergy_services')
                    ->addSelect(\DB::raw('COUNT(*) AS entry_count'))
            )->toSql();
        }
        
        if ($this->passesFilter('news')) {
            $queryUnions[] = $applyNewsConditions(
                \DB::table('synergy_news')
                    ->addSelect(\DB::raw('COUNT(*) AS entry_count'))
            )->toSql();
        }

        if ($this->passesFilter('service_categories')) {
            $queryUnions[] = $applyServiceCategoryConditions(
                \DB::table('synergy_service_categories')
                    ->addSelect(\DB::raw('COUNT(*) AS entry_count'))
            )->toSql();
        }

        if ($this->passesFilter('news_categories')) {
            $queryUnions[] = $applyNewsCategoryConditions(
                \DB::table('synergy_news_categories')
                    ->addSelect(\DB::raw('COUNT(*) AS entry_count'))
            )->toSql();
        }
        
        if ($this->passesFilter('portfolio')) {
            $queryUnions[] = $applyPortfolioConditions(
                \DB::table('synergy_portfolio')
                    ->addSelect(\DB::raw('COUNT(*) AS entry_count'))
            )->toSql();
        }
        
        if ($this->passesFilter('portfolio_categories')) {
            $queryUnions[] = $applyPortfolioCategoryConditions(
                \DB::table('synergy_portfolio_categories')
                    ->addSelect(\DB::raw('COUNT(*) AS entry_count'))
            )->toSql();
        }
        
        if ($this->passesFilter('albums')) {
            $queryUnions[] = $applyAlbumConditions(
                \DB::table('synergy_albums')
                    ->addSelect(\DB::raw('COUNT(*) AS entry_count'))
            )->toSql();
        }
        
        if (!empty($queryUnions)) {
            $queryUnionsCompiled = implode(') UNION ALL (', $queryUnions);

            $entriesQuery = \DB::select(
                \DB::raw(
                    "SELECT
                        SUM(entry_count) AS total_entries
                    FROM (
                    ({$queryUnionsCompiled})
                        ) AS count_subquery"
                ),
                $this->useParams()
            );
        }

        $this->totalEntries = (!empty($entriesQuery))
                                ? reset($entriesQuery)->total_entries
                                : 0;

        /* Pagination logic pt 2 */

        $this->totalPages = ceil($this->totalEntries / $this->perPage);

        $this->currentPage = $currentPage;
        
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        } elseif ($this->currentPage > $this->totalPages) {
            $this->currentPage = $this->totalPages;
        }

        $offset = ($this->currentPage - 1) * $this->perPage;

        /* Build SQL to run the search. */
        
        $queryUnions = array();
        
        if ($this->passesFilter('pages')) {
            $queryUnions[] = $applyPageConditions(
                \DB::table('synergy_pages')
                    ->addSelect('page_id as entry_id')
                    ->addSelect('page_created as entry_date')
                    ->addSelect(\DB::raw("'page' AS entry_type"))
            )->toSql();
        }

        if ($this->passesFilter('services')) {
            $queryUnions[] = $applyServiceConditions(
                \DB::table('synergy_services')
                    ->addSelect('service_id as entry_id')
                    ->addSelect('service_created as entry_date')
                    ->addSelect(\DB::raw("'service' AS entry_type"))
            )->toSql();
        }
        
        if ($this->passesFilter('news')) {
            $queryUnions[] = $applyNewsConditions(
                \DB::table('synergy_news')
                    ->addSelect('news_id as entry_id')
                    ->addSelect(\DB::raw('FROM_UNIXTIME(`news_publish_date`) as entry_date'))
                    ->addSelect(\DB::raw("'news' AS entry_type"))
            )->toSql();
        }

        if ($this->passesFilter('service_categories')) {
            $queryUnions[] = $applyServiceCategoryConditions(
                \DB::table('synergy_service_categories')
                    ->addSelect('category_id as entry_id')
                    ->addSelect(\DB::raw('`synergy_service_categories`.`category_updated` as entry_date'))
                    ->addSelect(\DB::raw("'service_category' AS entry_type"))
            )->toSql();
        }

        if ($this->passesFilter('news_categories')) {
            $queryUnions[] = $applyNewsCategoryConditions(
                \DB::table('synergy_news_categories')
                    ->addSelect('category_id as entry_id')
                    ->addSelect(\DB::raw('`synergy_news_categories`.`category_updated` as entry_date'))
                    ->addSelect(\DB::raw("'news_category' AS entry_type"))
            )->toSql();
        }
        
        if ($this->passesFilter('portfolio')) {
            $queryUnions[] = $applyPortfolioConditions(
                \DB::table('synergy_portfolio')
                    ->addSelect('portfolio_id as entry_id')
                    ->addSelect('portfolio_created as entry_date')
                    ->addSelect(\DB::raw("'portfolio' AS entry_type"))
            )->toSql();
        }
        
        if ($this->passesFilter('portfolio_categories')) {
            $queryUnions[] = $applyPortfolioCategoryConditions(
                \DB::table('synergy_portfolio_categories')
                    ->addSelect('category_id as entry_id')
                    ->addSelect(\DB::raw('`synergy_portfolio_categories`.`category_updated` as entry_date'))
                    ->addSelect(\DB::raw("'portfolio_category' AS entry_type"))
            )->toSql();
        }
        
        if ($this->passesFilter('albums')) {
            $queryUnions[] = $applyAlbumConditions(
                \DB::table('synergy_albums')
                    ->addSelect('album_id as entry_id')
                    ->addSelect('album_created as entry_date')
                    ->addSelect(\DB::raw("'album' AS entry_type"))
            )->toSql();
        }
        
        if (!empty($queryUnions)) {
            $queryUnionsCompiled = implode(') UNION ALL (', $queryUnions);

            $this->resultsRaw = \DB::select(
                \DB::raw(
                    "({$queryUnionsCompiled})
                        ORDER BY entry_date DESC
                        LIMIT {$offset}, {$this->perPage}
                    "
                ),
                $this->useParams()
            );
        }

        return $this;
    }
    
    public function getResults()
    {
        $rawResults = $this->getRawResults();
        
        if (empty($this->results) && !empty($rawResults)) {
            /* Collect a list of IDs for fetching in bulk */
            
            $pageIds = array();
            $serviceIds = array();
            $newsIds = array();
            $serviceCategoryIds = array();
            $newsCategoryIds = array();
            $portfolioIds = array();
            $portfolioCategoryIds = array();
            $albumIds = array();
            
            foreach ($rawResults as $result) {
                switch ($result->entry_type) {
                    case 'page':
                    {
                        $pageIds[$result->entry_id] = $result->entry_id;
                        break;
                    }
                    case 'service':
                    {
                        $serviceIds[$result->entry_id] = $result->entry_id;
                        break;
                    }
                    case 'news':
                    {
                        $newsIds[$result->entry_id] = $result->entry_id;
                        break;
                    }
                    case 'service_category':
                    {
                        $serviceCategoryIds[$result->entry_id] = $result->entry_id;
                        break;
                    }
                    case 'news_category':
                    {
                        $newsCategoryIds[$result->entry_id] = $result->entry_id;
                        break;
                    }
                    case 'portfolio':
                    {
                        $portfolioIds[$result->entry_id] = $result->entry_id;
                        break;
                    }
                    case 'portfolio_category':
                    {
                        $portfolioCategoryIds[$result->entry_id] = $result->entry_id;
                        break;
                    }
                    case 'album':
                    {
                        $albumIds[$result->entry_id] = $result->entry_id;
                        break;
                    }
                }
            }
            
            /* Get collections for each type */
            
            $entryModels = array();
            
            if (!empty($pageIds)) {
                $models = \Synergy\Modules\Pages\Models\Page::whereIn('page_id', $pageIds)->get();

                foreach ($models as $model) {
                    $entryModels['page_'.$model->page_id] = $model;
                }
            }
            
            if (!empty($serviceIds)) {
                $models = \Synergy\Modules\Services\Models\Service::whereIn('service_id', $serviceIds)->get();

                foreach ($models as $model) {
                    $entryModels['service_'.$model->service_id] = $model;
                }
            }
            
            if (!empty($newsIds)) {
                $models = \Synergy\Modules\News\Models\NewsItem::whereIn('news_id', $newsIds)->get();

                foreach ($models as $model) {
                    $entryModels['news_'.$model->news_id] = $model;
                }
            }
            
            if (!empty($serviceCategoryIds)) {
                $models = \Synergy\Modules\Services\Models\ServiceCategory::whereIn('category_id', $serviceCategoryIds)->get();

                foreach ($models as $model) {
                    $entryModels['service_category_'.$model->category_id] = $model;
                }
            }
            
            if (!empty($newsCategoryIds)) {
                $models = \Synergy\Modules\News\Models\NewsCategory::whereIn('category_id', $newsCategoryIds)->get();

                foreach ($models as $model) {
                    $entryModels['news_category_'.$model->category_id] = $model;
                }
            }
            
            if (!empty($portfolioIds)) {
                $models = \Synergy\Modules\Portfolio\Models\Portfolio::whereIn('portfolio_id', $portfolioIds)->get();

                foreach ($models as $model) {
                    $entryModels['portfolio_'.$model->portfolio_id] = $model;
                }
            }
            
            if (!empty($portfolioCategoryIds)) {
                $models = \Synergy\Modules\Portfolio\Models\PortfolioCategory::whereIn('category_id', $portfolioCategoryIds)->get();

                foreach ($models as $model) {
                    $entryModels['portfolio_category_'.$model->category_id] = $model;
                }
            }
            
            if (!empty($albumIds)) {
                $models = \Synergy\Modules\Gallery\Models\Album::whereIn('album_id', $albumIds)->get();

                foreach ($models as $model) {
                    $entryModels['album_'.$model->album_id] = $model;
                }
            }

            // Build an ordered array of the results.
            
            foreach ($rawResults as $result) {
                $this->results[] = $entryModels[$result->entry_type.'_'.$result->entry_id];
            }
        }

        return $this->results;
    }
    
    public function getRawResults()
    {
        return $this->resultsRaw;
    }
    
    public function getPaginator()
    {
        if (is_null($this->paginator)) {
            $this->paginator = \Paginator::make(
                $this->getResults(),
                $this->totalEntries,
                $this->perPage
            );
        }
        
        return $this->paginator;
    }
}