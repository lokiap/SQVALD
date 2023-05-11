<?php


namespace App\Classe;

use App\Entity\CategoryNews;
use App\Entity\News;


class SearchNews
{
    /**
     * @var string
     */
    public $string ="";
    /**
     * @var CategoryNews []
     */
    public $categoriesNews = [];
    /**
     * @var \DateTime
     */
    public $startCreatedAt ;

    /**
     * @var \DateTime
     */
    public $endCreatedAt;



}