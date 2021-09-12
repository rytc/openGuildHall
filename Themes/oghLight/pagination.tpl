<div class="paginator">
	<ol class="paginator">
	
		{if $currentPage == 1}
			<li>First</li>
			<li>&laquo;&nbsp;Previous</li>
			
		{else}
			{$previousPage = $currentPage - 1}
			<li><a href="{$this->url}1">First</a></li>
			<li><a href="{$url}{$previousPage}">&laquo;&nbsp;Previous</a></li>
			
			{$firstPage = $currentPage - 1}
			{if $firstPage < 1} {$firstPage = 1} {/if}
			{if $firstPage - 1 > 1} <li>...</li> {/if}
				
			{for $page = $currentPage to $currentPage - 2 step -1}
				{if $page <= $totalPages and $page != 0}
					<li><a href="{$url}{$page}">{$page}</a></li>
				{/if}
			{/for}
		{/if}
		
		<li class="active">{$currentPage}</li>
		{$lastPage = $currentPage + 2}
		
		{if $lastPage > $totalPages} 
			{$lastPage = $lastPage - ($lastPage - $totalPages)}
		{/if}
		
		{if $lastPage > $currentPage}

			{for $page = $currentPage to $currentPage + 2}
				{if $page <= $totalPages}
					<li><a href="{$url}{$page}">{$page}</a></li>
				{/if}
			{/for}
		{/if}
		
		{if $currentPage >= $totalPages}
			<li>Next&nbsp;&raquo;</li>
			<li>Last</li>
		{else}
			{$nextPage = $curentPage + 2}
			<li><a href="{$url}{$nextPage}">Next&nbsp;&raquo;</a></li>
			<li><a href="{$url}{$totalPages}">Last</a></li>
		{/if}
	</ol>
</div>