<?php

namespace Admin\Controllers;

use Admin\Delegates\Column;
use Admin\Delegates\Row;
use Admin;
use Admin\Delegates\Card;
use Admin\Delegates\CardBody;
use Admin\Delegates\ChartJs;
use Admin\Delegates\SearchForm;
use Admin\Delegates\StatisticPeriod;
use Admin\Page;

class CalendarController extends Controller
{
    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  CardBody  $cardBody
     * @param  StatisticPeriod  $statisticPeriod
     * @param  ChartJs  $chartJs
     * @param  SearchForm  $searchForm
     * @param  Row  $row
     * @param  Column  $column
     * @return Page|mixed
     */
    public function index(
        Page $page,
        Card $card,
        CardBody $cardBody,
        StatisticPeriod $statisticPeriod,
        ChartJs $chartJs,
        SearchForm $searchForm,
        Admin\Delegates\Row $row,
        Admin\Delegates\Column $column,
    ) {
        return $page->row(
            $row->column(12)->card(
                $card->title(__('admin.user_statistics')),
                $card->card_body(
                    $cardBody->div()->setDatas(['load' => 'calendar'])
                ),
            ),
        );
    }
}
