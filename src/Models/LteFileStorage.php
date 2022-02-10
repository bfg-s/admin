<?php

namespace LteAdmin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use LteAdmin\Traits\DumpedModel;

/**
 * LteAdmin\Models\LteFileStorage.
 *
 * @property int $id
 * @property string $original_name
 * @property string $file_name
 * @property string $mime_type
 * @property string $size
 * @property string|null $form
 * @property string|null $field
 * @property string $driver
 * @property string $driver_path
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|LteFileStorage active()
 * @method static Builder|LteFileStorage makeDumpedModel()
 * @method static Builder|LteFileStorage newModelQuery()
 * @method static Builder|LteFileStorage newQuery()
 * @method static Builder|LteFileStorage query()
 * @method static Builder|LteFileStorage whereActive($value)
 * @method static Builder|LteFileStorage whereCreatedAt($value)
 * @method static Builder|LteFileStorage whereDriver($value)
 * @method static Builder|LteFileStorage whereDriverPath($value)
 * @method static Builder|LteFileStorage whereField($value)
 * @method static Builder|LteFileStorage whereFileName($value)
 * @method static Builder|LteFileStorage whereForm($value)
 * @method static Builder|LteFileStorage whereId($value)
 * @method static Builder|LteFileStorage whereMimeType($value)
 * @method static Builder|LteFileStorage whereOriginalName($value)
 * @method static Builder|LteFileStorage whereSize($value)
 * @method static Builder|LteFileStorage whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LteFileStorage extends Model
{
    use DumpedModel;

    /**
     * @var string|null
     */
    public $result;
    /**
     * @var string
     */
    protected $table = 'lte_file_storage';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'original_name', 'file_name', 'mime_type', 'size', 'form', 'field', 'driver', 'driver_path', 'active'
    ];
    /**
     * @var bool
     */
    protected $return_model = false;

    /**
     * AdminFileStorage constructor.
     * @param  array|UploadedFile  $attributes
     */
    public function __construct($attributes = [])
    {
        if (is_array($attributes)) {
            parent::__construct($attributes);
        } else {
            parent::__construct([]);

            if ($attributes instanceof UploadedFile) {
                $this->createFile($attributes);
            }
        }
    }

    /**
     * @param  UploadedFile|null  $file
     * @param  string|null  $storage
     * @param  string|null  $storage_path
     * @param  string|null  $field
     * @param  string|null  $form
     * @return string
     */
    public function createFile(
        UploadedFile $file = null,
        string $storage = null,
        string $storage_path = null,
        string $field = null,
        string $form = null
    ) {
        if (!$file) {
            return null;
        }

        if (!$storage) {
            $storage = config('lte.upload.disk');
        }

        if (!$storage_path) {
            $storage_path = is_image($file->getPathname()) ?
                config('lte.upload.directory.image') :
                config('lte.upload.directory.file');
        }

        $test = $this->where('original_name', $file->getClientOriginalName())
            ->where('mime_type', $file->getMimeType())
            ->where('size', $file->getSize())
            ->where('driver', $storage)
            ->first();

        if (!$test) {
            $result = $file->store($storage_path, $storage);

            $path = trim(str_replace(env('APP_URL').'/', '', config("filesystems.disks.{$storage}.url")), '/');

            $root = trim(config("filesystems.disks.{$storage}.url"), '/').'/'.trim($storage_path, '/');

            if (!is_dir($root)) {
                mkdir($root, 0777, true);
            }

            /** @var LteFileStorage $result */
            $result = $this->create([
                'original_name' => $file->getClientOriginalName(),
                'file_name' => $path.'/'.$result,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'form' => $form,
                'field' => $field,
                'driver' => $storage,
                'driver_path' => $storage_path,
            ]);

            if ($this->return_model) {
                return $result;
            }

            $this->result = $result->file_name;

            return $result->file_name;
        } else {
            if ($this->return_model) {
                return $test;
            }

            $this->result = $test->file_name;

            return $test->file_name;
        }
    }

    /**
     * @param  null  $file
     * @param  string|null  $storage
     * @param  string|null  $storage_path
     * @param  string|null  $field
     * @param  string|null  $form
     * @return string|null
     */
    public static function makeFile(
        $file = null,
        string $storage = null,
        string $storage_path = null,
        string $field = null,
        string $form = null
    ) {
        if (is_string($file) && request()->hasFile($file)) {
            $file = request()->file($file);
        }

        if (!$file instanceof UploadedFile) {
            return $file;
        }

        return (new static())->createFile($file, $storage, $storage_path, $field, $form);
    }

    /**
     * @return $this
     */
    public function returnModel(): static
    {
        $this->return_model = true;

        return $this;
    }

    /**
     * Get all active menu.
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->whereActive(1);
    }

    /**
     * @param  UploadedFile  $file
     * @param  string|null  $storage
     * @return bool
     */
    public function hasFile(UploadedFile $file, string $storage = null)
    {
        if (!$storage) {
            $storage = config('lte.upload.disk');
        }

        return $this->where('original_name', $file->getClientOriginalName())
            ->where('mime_type', $file->getMimeType())
            ->where('size', $file->getSize())
            ->where('driver', $storage)
            ->exists();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->result) {
            return $this->result;
        }

        return parent::__toString(); // TODO: Change the autogenerated stub
    }
}
