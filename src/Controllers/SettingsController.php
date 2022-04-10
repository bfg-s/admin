<?php

namespace LteAdmin\Controllers;

use LteAdmin;
use LteAdmin\Delegates\Buttons;
use LteAdmin\Delegates\Card;
use LteAdmin\Delegates\Column;
use LteAdmin\Delegates\Form;
use LteAdmin\Page;

class SettingsController extends Controller
{
    static protected array $map = [
        'global' => [
            'title' => 'Global',
            'description' => 'The global settings',
            'items' => [
                'lte.dark_mode' => [
                    'type' => 'switcher',
                    'title' => 'Dark mode',
                    'description' => 'The dark mode by default',
                    'value' => true,
                ]
            ]
        ],
    ];

    /**
     * @param  Page  $page
     * @param  Column  $column
     * @param  Card  $card
     * @param  Form  $form
     * @param  Buttons  $buttons
     * @return Page
     */
    public function index(
        Page $page,
        Column $column,
        Card $card,
        Form $form,
    ) {
        return $page
            ->title('Admin settings')
            ->icon_cog()
            ->column(
                $column->card(
                    $card->title('The list of settings'),
                    $card->form(
                        $form->withCollection(
                            static::$map,
                            fn($group) => [
                                $form->divider($group['title']),
                                $form->withCollection(
                                    $group['items'],
                                    fn($item, $name) => $form->{$item['type']}($name, $item['title'])
                                        ->value($item['value'])
                                        ->info($item['description'])
                                        ->label_width(6)
                                )
                            ]
//                            => $form->card(
//                                $card->title($group['title']),
//                                $card->form(
//                                    $form->withCollection(
//                                        $group['items'],
//                                        fn ($item, $name)
//                                        => $form->{$item['type']}($name, $item['title'])
//                                            ->value($item['value'])
//                                            ->info($item['description'])
//                                            ->label_width(6)
//                                    )
//                                ),
//                            )
                        ),
                    ),
                    $card->footer_form()
                ),
            );
    }
}
