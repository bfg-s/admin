<div x-data="{show: false}">
    <span x-show="! show">
        <i x-on:click="show=!show" class='fas fa-eye' style='cursor:pointer'></i>
        {{ $stars }}
    </span>
    <span style='display:none' x-show="show">
        <i x-on:click="show=!show" class='fas fa-eye-slash' style='cursor:pointer'></i>
        {{ $value }}
    </span>
</div>
