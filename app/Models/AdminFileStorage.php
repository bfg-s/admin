<?php

namespace Admin\Models;

use Bfg\Dev\Traits\DumpedModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * Class AdminFileStorage
 *
 * @package Admin\Models
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage active()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage makeDumpedModel()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage query()
 * @mixin \Eloquent
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereDriverPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminFileStorage whereUpdatedAt($value)
 */
class AdminFileStorage extends Model
{
    use DumpedModel;

    /**
     * @var string
     */
    protected $table = "admin_file_storage";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['original_name', 'file_name', 'mime_type', 'size', 'form', 'field', 'driver', 'driver_path', 'active'];

    /**
     * @var string|null
     */
    public $result;

    /**
     * AdminFileStorage constructor.
     * @param array|UploadedFile $attributes
     */
    public function __construct($attributes = [])
    {
        if (is_array($attributes)) {

            parent::__construct($attributes);
        }

        else {

            parent::__construct([]);

            if ($attributes instanceof UploadedFile) {

                $this->createFile($attributes);
            }
        }
    }

    /**
     * Get all active menu
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

            $storage = config('admin.upload.disk');
        }

        return $this->where("original_name", $file->getClientOriginalName())
            ->where("mime_type", $file->getMimeType())
            ->where("size", $file->getSize())
            ->where("driver", $storage)
            ->exists();
    }

    /**
     * @param UploadedFile|null $file
     * @param string $storage
     * @param string $storage_path
     * @param string|null $field
     * @param string|null $form
     * @return string
     */
    public function createFile(UploadedFile $file = null, string $storage = null, string $storage_path = null, string $field = null, string $form = null)
    {
        if (!$file) {

            return null;
        }

        if (!$storage) {

            $storage = config('admin.upload.disk');
        }

        if (!$storage_path) {

            $storage_path = is_image($file->getPathname()) ?
                config('admin.upload.directory.image') :
                config('admin.upload.directory.file');
        }

        $this->result = $this->where("original_name", $file->getClientOriginalName())
            ->where("mime_type", $file->getMimeType())
            ->where("size", $file->getSize())
            ->where("driver", $storage)
            ->first();

        if (!$this->result) {

            $result = $file->store($storage_path, $storage);

            $path = trim(str_replace(env('APP_URL') . '/', '', config("filesystems.disks.{$storage}.url")), '/');

            $root = trim(config("filesystems.disks.{$storage}.url"), '/') . '/' . trim($storage_path, '/');

            if (!is_dir($root)) {

                mkdir($root, 0777, true);
            }

            /** @var AdminFileStorage $result */
            $this->result = $this->create([
                "original_name" => $file->getClientOriginalName(),
                "file_name" => $path . '/' . $result,
                "mime_type" => $file->getMimeType(),
                "size" => $file->getSize(),
                "form" => $form,
                "field" => $field,
                "driver" => $storage,
                "driver_path" => $storage_path
            ]);
        }

        return $this->result;
    }

    /**
     * @param  UploadedFile|string|null  $file
     * @param  string  $storage
     * @param  string  $storage_path
     * @param  string|null  $field
     * @param  string|null  $form
     * @return string
     */
    public static function store($file = null, string $storage = null, string $storage_path = null, string $field = null, string $form = null)
    {
        if (is_string($file) && request()->hasFile($file)) {

            $file = request()->file($file);
        }

        if (!$file instanceof UploadedFile) {

            return $file;
        }

        return (new static())->createFile($file, $storage, $storage_path, $field, $form);
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
