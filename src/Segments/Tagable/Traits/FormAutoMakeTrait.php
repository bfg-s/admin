<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits;

use Illuminate\Database\Eloquent\Model;
use Lar\LteAdmin\Segments\Tagable\Field;

/**
 * Trait FormAutoMakeTrait
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait FormAutoMakeTrait {

    /**
     * Auto make form
     */
    public function autoMake()
    {
        if ($this->model && $this->model instanceof Model) {

            $fields = $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());

            foreach ($fields as $field) {

                if (
                    $field !== 'id' &&
                    $field !== 'created_at' &&
                    $field !== 'updated_at' &&
                    $field !== 'deleted_at'
                ) {
                    if (Field::has($field)) {

                        $this->{$field}($field, ucfirst($field))->reqquired();

                    } else {

                        $this->input($field, ucfirst($field))->reqquired();
                    }
                }
            }
        }
    }
}