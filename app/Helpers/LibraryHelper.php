<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;

if (!function_exists('return_library')) {
    /**
     * Convert an Eloquent collection or query to a key-value associative array.
     */
    function return_library($object, $key_col = 'id', $value_col = 'name'): array
    {
        if (is_array($object)) {
            return $object;
        }

        if (method_exists($object, 'pluck')) {
            return $object->pluck($value_col, $key_col)->toArray();
        }

        $data = [];
        foreach ($object as $item) {
            $data[$item->$key_col] = $item->$value_col;
        }
        return $data;
    }
}

if (!function_exists('lib_all_category')) {
    function lib_all_category(): array
    {
        try {
            return Category::where('status', '1')->pluck('name', 'id')->toArray();
        } catch (\Throwable $e) {
            return Category::pluck('name', 'id')->toArray();
        }
    }
}

if (!function_exists('lib_category')) {
    function lib_category(): array
    {
        try {
            return Category::where('status', '1')->pluck('name', 'id')->toArray();
        } catch (\Throwable $e) {
            return Category::pluck('name', 'id')->toArray();
        }
    }
}

if (!function_exists('lib_book_category')) {
    function lib_book_category(): array
    {
        try {
            return Category::where('status', '1')->pluck('name', 'id')->toArray();
        } catch (\Throwable $e) {
            return Category::pluck('name', 'id')->toArray();
        }
    }
}

if (!function_exists('lib_brand')) {
    function lib_brand(): array
    {
        try {
            return Brand::where('status', '1')->pluck('name', 'id')->toArray();
        } catch (\Throwable $e) {
            return Brand::pluck('name', 'id')->toArray();
        }
    }
}

if (!function_exists('lib_serviceMan')) {
    function lib_serviceMan(): array
    {
        try {
            return User::where('status', '1')->pluck('name', 'id')->toArray();
        } catch (\Throwable $e) {
            return User::pluck('name', 'id')->toArray();
        }
    }
}

if (!function_exists('lib_salesMan')) {
    function lib_salesMan(): array
    {
        try {
            return User::where('status', '1')->pluck('name', 'id')->toArray();
        } catch (\Throwable $e) {
            return User::pluck('name', 'id')->toArray();
        }
    }
}

if (!function_exists('lib_publisher')) {
    function lib_publisher(): array
    {
        return [];
    }
}

if (!function_exists('lib_writer')) {
    function lib_writer(): array
    {
        return [];
    }
}

if (!function_exists('lib_subject')) {
    function lib_subject(): array
    {
        return [];
    }
}

if (!function_exists('lib_deliveryCharge')) {
    function lib_deliveryCharge(): array
    {
        return [];
    }
}

if (!function_exists('lib_districts')) {
    function lib_districts(): array
    {
        return [];
    }
}

if (!function_exists('lib_areas')) {
    function lib_areas(): array
    {
        return [];
    }
}
