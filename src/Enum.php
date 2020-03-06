<?php namespace Tdx;

use Illuminate\Support\Collection;
use \Illuminate\Support\Str;

class Enum {
    public static function exists($key) : bool {
        return in_array($key, static::values());
    }

    public static function key($value) {
        return static::toInvertedArray()[$value];
    }

    public static function value($key) {
        return static::toArray()[$key];
    }

    public static function className() : string {
        return array_last(explode('\\', static::class));
    }

    public static function collectValues() : Collection {
        return collect(static::values());
    }

    public static function keys() : array {
        return array_keys(static::toArray());
    }

    public static function values() : array {
        return array_values(static::toArray());
    }

    public static function toInvertedArray() : array {
        $invertedList = [];
        foreach (static::toArray() as $constKey => $constValue) {
            $invertedList[$constValue] = $constKey;
        }
        return $invertedList;
    }

    public static function toArray() : array {
        return (new \ReflectionClass(static::class))->getConstants();
    }

    public static function translated($defaultOption = null) : Collection {
        $translationPrefix = Str::snake(static::className());
        $translatedDefaultOption = static::translation("$translationPrefix.all");

        $translated = static::collectValues()->map(function($enumKey) use ($translationPrefix) {
            return static::translation("$translationPrefix.$enumKey");
        });

        return $defaultOption ? $translated->prepend($translatedDefaultOption) : $translated;
    }

    protected abstract static function translation(string $translationKey) : string;
}