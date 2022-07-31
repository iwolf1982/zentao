<?php

$takeTime = <<<_HTML
<tr>
<th>{$lang->bug->take_time}</th>
<td>{$bug->take_time}</td>
</tr>
_HTML;

?>
<script language='Javascript'>
$(function()
{
    $('table.table-data').find('tr:eq(7)').after(`<?php echo $takeTime?>`);
})
</script>
