<?php

namespace http\components;

class FloatingInputFactory
{
    public static function email($name='email', $label='email', $id='email', $required=false, $value = '')
    {
        static::input('email', $name, $label, $id, $required, $value);
    }

    public static function text($name='text', $label='text', $id='text', $required=false,  $value = '')
    {
        static::input('text', $name, $label, $id, $required, $value);
    }

    public static function password($name='password', $label='password', $id='password', $required = false,  $value = '')
    {
        static::input('password', $name, $label, $id, $required, $value);
    }

    public static function number($name='number', $label='number', $id='number', $required = false,  $min = '0', $max = '100', $value = '10')
    {
        ?>
        <div class="form-floating">
            <input type="number" class="form-control" id="<?= $id ?? '' ?>" name="<?= $name ?? '' ?>" <?= $required? 'required' : ''?> value="<?= $value ?>" min="<?= $min ?>" max="<?= $max ?>" placeholder="some text">
            <label for="<?= $id ?? '' ?>"><?= $label ?? '' ?></label>
        </div>
        <?php
    }

    public static function input($type, $name, $label, $id, $required, $value = '')
    {
        ?>
        <div class="form-floating">
            <input type="<?= $type ?>" class="form-control" id="<?= $id ?? '' ?>" name="<?= $name ?? '' ?>" <?= $required? 'required' : ''?> value="<?= $value ?>" placeholder="some text">
            <label for="<?= $id ?? '' ?>"><?= $label ?? '' ?></label>
        </div>
        <?php
    }

    public static function date($name, $label, $id, $required, $value = '')
    {
        ?>
        <div class="form-floating">
            <input type="date" class="form-control" id="<?= $id ?? '' ?>" name="<?= $name ?? '' ?>" <?= $required? 'required' : ''?> value="<?= $value ?>" placeholder="some text">
            <label for="<?= $id ?? '' ?>"><?= $label ?? '' ?></label>
        </div>
        <?php
    }
}