# Laravel Cascade Pro

A drop-in trait that extends Laravel soft-deletes so related models are deleted and restored automatically.

```php
use Stafe\CascadePro\CascadeSoftDeletes;

class Post extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected array $cascadeDeletes = ['comments'];
}
```

Publish the config file to tweak chunk sizes and queue strategy:

```
php artisan vendor:publish --tag=cascadepro-config
```
