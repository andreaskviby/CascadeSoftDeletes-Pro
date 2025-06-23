![Laravel Cascade Pro](8CFBF222-9389-4334-B853-4D1F2B777FE0.png)
# Laravel Cascade Pro

A drop-in trait that extends Laravel soft deletes so related models are deleted and restored automatically.

## Features

- Automatic cascading of soft deletes and restores
- Works with pivot tables that store a `deleted_at` column
- Jobs and chunking for large datasets or async processing
- Fires events during delete and restore cycles
- Artisan commands to manage cascaded records
- Configurable chunk size, queue connection and strategy

## Installation

```bash
composer require andreaskviby/CascadeSoftDeletes-Pro
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=cascadepro-config
```

Key options in `config/cascadepro.php`:

- `default_strategy` – process records synchronously or through the queue
- `chunk_size` – number of models processed before jobs are chunked
- `queue_connection` – queue connection used when dispatching jobs
- `pivot_tables` – pivot tables containing soft delete columns

## Usage

Apply the trait to models and list which relations should cascade.

```php
use Stafe\CascadePro\CascadeSoftDeletes;

class Post extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected array $cascadeDeletes = ['comments'];
}
```

## Events

The package emits events you can listen for:

- `DeletingCascade` and `DeletedCascade`
- `RestoringCascade` and `RestoredCascade`

## Commands

```bash
php artisan cascade:flush {model}  # hard delete soft-deleted trees
php artisan cascade:scan           # list models missing cascade mapping
```

## Contributing

Contributions are welcome! Feel free to open issues or submit pull requests.

## About Me

This package is maintained by [Andreas Kviby](https://github.com/andreaskviby).
I enjoy building tools for the Laravel community and appreciate any feedback.

## License

The MIT License. See [LICENSE.md](LICENSE.md) for details.
