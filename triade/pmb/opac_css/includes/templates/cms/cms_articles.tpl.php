<?php

global $cms_articles_list, $cms_articles_list_item;

$cms_articles_list ="
	<h3>!!cms_articles_list_title!!</h3>
<table class='cms_articles_list'>
	!!items!!
</table>

";

$cms_articles_list_item ="
	<tr class='cms_article'>
		<td class='cms_article_logo'>
			<img src='!!cms_article_logo_src!!' alt='!!cms_article_title!!' title='!!cms_article_title!!' />
		</td>
		<td>
			!!cms_article_title!!
		</td>
	</tr>
";