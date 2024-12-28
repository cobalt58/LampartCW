<?php

namespace http\components;
class HtmlHelper {
    private static function arrayToAttributes($array): string
    {
        $attributes = '';
        foreach ($array as $key => $value) {
            $attributes .= " $key=\"$value\"";
        }
        return $attributes;
    }

    static function select($name, $items, $value, $isEmptyRow = false) {
        ?>
        <select name="<?=$name?>" class="form-select">
            <?php
            if ($isEmptyRow) {
                echo '<option value="null"></option>';
            }
            foreach($items as $item) {
                if (is_string($item) || is_numeric($item)) {
                    echo '<option ' . (($item == $value) ? 'selected' : '') . '>' . $item . '</option>';
                } else{
                    echo '<option ' . (($item['id'] == $value) ? 'selected' : '') . ' value="'.$item['id'].'">' . $item['name'] . '</option>';
                }
            }
            ?>
        </select>
        <?php
    }
    static function inputText($name, $value = '', $attrs = null, $type='text') {
        ?>
        <input class="form-control" type="<?=$type?>" id="<?=$name?>" name="<?=$name?>" <?= ($attrs)? HtmlHelper::arrayToAttributes($attrs):'' ?>  value="<?=(!empty($value))?htmlspecialchars($value):''?>">
        <?php
    }

    static function inputNumber($name, $value = 0, $min=null, $max=null, $step=null, $attrs = null) {
        ?>
        <input class="form-control" type="number"
               id="<?=$name?>"
               name="<?=$name?>"
            <?= ($min!=null)?' min="'.$min.'" ':'' ?>
            <?= ($max!=null)?' min="'.$max.'" ':'' ?>
            <?= ($step!=null)?' step="'.$step.'" ':'' ?>
            <?= ($attrs)? HtmlHelper::arrayToAttributes($attrs):'' ?>
               value="<?=(!empty($value))?htmlspecialchars($value):''?>">
        <?php
    }

    static function textArea($name, $value='', $cols=30, $rows=5){
        ?>
        <textarea name="<?= $name; ?>" class="form-control" id="<?= $name; ?>" cols="<?= $cols; ?>" rows="<?= $rows; ?>"><?=(!empty($value))?htmlspecialchars($value):''?></textarea>
        <?php
    }

    static function inputFile($name, $accept, $multiple=false, $attrs = []){
        ?>
        <input type="file" class="form-control" name="<?= $name; ?>" accept="<?= $accept; ?>" <?= ($attrs)? HtmlHelper::arrayToAttributes($attrs):'' ?> <?= ($multiple)?'multiple':''; ?>>
        <?php
    }

    public static function toast($title, $body){
        echo '
        <button type="button" class="btn btn-primary" id="toast-btn" hidden>Show live toast</button>
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                        <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
                        <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
                    </svg>
                    <strong class="me-auto">'.$title.'</strong>
                    <small>1s ago</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    '.$body.'
                </div>
            </div>
        </div>
        <script>
            const toastTrigger = document.getElementById("toast-btn")
            const toastWindow = document.getElementById("liveToast")
            if (toastTrigger) {
                toastTrigger.addEventListener("click", () => {
                    const toast = new bootstrap.Toast(toastWindow)
                    toast.show()
                })
            }
        </script>
        ';
    }

    public static function simClick($id){
        echo '<script>
                document.getElementById("'.$id.'").click();
            </script>';
    }

}
