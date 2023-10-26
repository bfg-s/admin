<?php

namespace Admin\Traits;

trait SearchFormHelpersTrait
{
    /**
     * @return $this
     */
    public function id(): static
    {
        $this->numeric('id', 'admin.id', '=');

        return $this;
    }

    /**
     * @return $this
     */
    public function at(): static
    {
        return $this->updated_at()->created_at();
    }

    /**
     * @return $this
     */
    public function created_at(): static
    {
        $this->date_time_range('created_at', 'admin.created_at');

        return $this;
    }

    /**
     * @return $this
     */
    public function updated_at(): static
    {
        $this->date_time_range('updated_at', 'admin.updated_at');

        return $this;
    }
}
