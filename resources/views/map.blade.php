<style type="text/css">
    /*.color-1 {*/
    /*    background-color: green;*/
    /*}*/
    .color--1 {
        background-color: red;
    }
    /*.color-2 {*/
    /*    background-color: yellow;*/
    /*}*/
    /*.color-3 {*/
    /*    background-color: purple;*/
    /*}*/
</style>
<table>
    @foreach($map as $row)
        <tr>
            @foreach($row as $i)
                <td class="color-{{$i}}">{!! $i > 0 ? $i : '&nbsp;&nbsp;' !!}</td>
            @endforeach
        </tr>
    @endforeach
</table>


