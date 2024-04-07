<?php

declare(strict_types=1);

namespace Admin\Core;

class CfgFile
{
    /**
     * File path.
     *
     * @var string
     */
    protected $file;

    /**
     * File data.
     *
     * @var array
     */
    protected $file_data = [];

    /**
     * CfgFile constructor.
     *
     * @param  null  $file
     */
    public function __construct($file = null)
    {
        if ($file) {
            $this->file($file);
        }
    }

    /**
     * Add file in to work.
     *
     * @param $path
     * @return $this
     */
    public function file($path)
    {
        $this->file = $path;

        $this->readFromFile();

        return $this;
    }

    /**
     * Read or reread from file.
     *
     * @return $this
     */
    protected function readFromFile(): static
    {
        if (is_file($this->file)) {
            $file_data = include $this->file;

            if (is_array($file_data)) {
                $this->file_data = $file_data;
            }
        }

        return $this;
    }

    /**
     * Open CFG file.
     *
     * @param $file
     * @return $this
     */
    public static function open($file): static
    {
        return (new static())->file($file);
    }

    /**
     * Get file data.
     *
     * @return array
     */
    public function data(): array
    {
        return $this->file_data;
    }

    /**
     * Check, has data or not.
     *
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        $this->readFromFile();

        return isset($this->file_data[$name]);
    }

    /**
     * @param  string  $name
     * @param  null  $value
     * @return $this
     */
    public function writeIfUnique(string $name, $value = null): static
    {
        if (isset($this->file_data[$name])) {
            if (json_encode($this->file_data[$name]) !== json_encode($value)) {
                $this->write($name, $value);
            }
        } else {
            $this->write($name, $value);
        }

        return $this;
    }

    /**
     * Write or rewrite data in to config list.
     *
     * @param $name
     * @param  null  $value
     * @return $this
     */
    public function write($name, $value = null): static
    {
        $this->readFromFile();

        if (is_string($name)) {
            $this->file_data[$name] = $value;
        } elseif (is_array($name)) {
            $this->file_data = array_merge($this->file_data, $name);
        }

        $this->writeInFile();

        return $this;
    }

    /**
     * Write all data in to file.
     *
     * @return $this
     */
    protected function writeInFile(): static
    {
        file_put_contents($this->file, config_file_wrapper($this->file_data));

        return $this;
    }

    /**
     * @param $group
     * @param $key
     * @param  null  $value
     * @return CfgFile
     */
    public function add_to_group($group, $key, $value = null): static
    {
        $this->readFromFile();

        if (is_array($this->file_data)) {
            $this->file_data[$group][$key] = $value;
        }

        $this->writeInFile();

        return $this;
    }

    /**
     * @param  array  $array
     * @return $this
     */
    public function recursiveMerge(array $array): static
    {
        $this->readFromFile();

        $this->file_data = array_merge_recursive($this->file_data, $array);

        $this->writeInFile();

        return $this;
    }

    /**
     * Remove data from config list.
     *
     * @param $name
     * @return $this
     */
    public function remove($name): static
    {
        $this->readFromFile();

        if (!is_array($name)) {
            $name = func_get_args();
        }

        $this->file_data = collect($this->file_data)->except($name)->toArray();

        $this->writeInFile();

        return $this;
    }
}
