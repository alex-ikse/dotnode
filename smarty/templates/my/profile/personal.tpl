<div id='leftblock' >
<img src='{$smarty.session.my_photo|default:'/photos/nop_en.png'}' alt='photo' />
</div>

<div id='home'>
<h2>{$smarty.session.my_fname|escape} {if $smarty.session.my_nick}"{$smarty.session.my_nick|escape}" {/if}{$smarty.session.my_lname|escape}</h2>
{if $my.profile.personal}
<table width='400'>
{foreach from=$my.profile.personal item=item key=key}
<tr class='{cycle name='parity' values='odd,even'}'><td align='right' nowrap='nowrap' class='label'>{$key} : </td>
<td class='value'>{$item|escape|nl2br|linkurl}
</td></tr>
{/foreach}
<tr class='{cycle name='parity' values='odd,even'}'><td colspan='2' align='right'><a class='button' href='/my/profile/personal/edit'>{t}Edit{/t}</a></td></tr>
</table>
{else}
<a class='button' href='/my/profile/personal/edit'>{t}Edit{/t}</a>
{/if}
</div>
<div style="clear:both"></div>

