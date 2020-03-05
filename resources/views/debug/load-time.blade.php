@php
    function truncate($val, $f="0")
    {
        if(($p = strpos($val, '.')) !== false) {
            $val = floatval(substr($val, 0, $p + 1 + $f));
        }
        return $val;
    }
@endphp

<style>
* {
    text-align:center;
    font-family: system-ui,
    sans-serif;
    font-size: 1.5rem;
}

.message {
    position: absolute;
    margin:auto;
    top:0;
    bottom:0;
    left:0;
    right:0;
    height:100px;
}
</style>

<div class="message">
    This page took
    <span style="color:coral">
        {{ truncate(microtime(true) - LARAVEL_START, 3) }}
    </span> seconds&nbsp;to&nbsp;render.
</div>