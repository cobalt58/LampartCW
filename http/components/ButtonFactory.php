<?php

namespace http\components;

class ButtonFactory
{
    public static function button($type, $id = null, $text, array $classes = [], $value = null): string
    {
        $baseClasses = ['btn'];
        $classes = array_merge($baseClasses, $classes);

        $attributes = [];
        if ($id) {
            $attributes['id'] = $id;
        }
        if ($value) {
            $attributes['value'] = $value;
        }

        $attributesString = '';
        foreach ($attributes as $key => $val) {
            $attributesString .= " $key='$val'";
        }

        return "<button type='$type' class='" . implode(' ', $classes) . "' $attributesString>$text</button>";
    }

    public static function buttonPrimary($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-primary'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

// Similar implementations for buttonSecondary, buttonSuccess, etc. (code omitted for brevity)

    public static function buttonInfo($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-info'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonDanger($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-danger'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonLight($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-light'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonDark($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-dark'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonLink($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-link'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonOutlinePrimary($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-outline-primary'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonOutlineSecondary($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-outline-secondary'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonOutlineSuccess($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-outline-success'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonOutlineDanger($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-outline-danger'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonOutlineWarning($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-outline-warning'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonOutlineInfo($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-outline-info'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonOutlineLight($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-outline-light'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

    public static function buttonOutlineDark($type, $id = null, $text, array $classes = [], $value = null)
    {
        $baseClasses = ['btn-outline-dark'];
        return self::button($type, $id, $text, array_merge($baseClasses, $classes), $value);
    }

}