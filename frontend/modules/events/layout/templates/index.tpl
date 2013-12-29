{*
	variables that are available:
	- {$items}: contains an array with all posts, each element contains data about the post
*}

<div class="categories">
	{iteration:categories}
	<span class="{$categories.color}">{$categories.label}</span>
	{/iteration:categories}
</div>

<div class="filter">
{form:filter}
    <a class="previous" href="{$prevYearUrl}" title="{$lblPreviousYear|ucfirst}">{$lblPreviousYear|ucfirst}</a>
    <a class="previous" href="{$prevMonthUrl}" title="{$lblPreviousMonth|ucfirst}">{$lblPreviousMonth|ucfirst}</a>
		{$ddmMonth} {$ddmMonthError}
		{$ddmYear} {$ddmYearError}
    <a class="next" href="{$nextYearUrl}" title="{$lblNextYear|ucfirst}">{$lblNextYear|ucfirst}</a>
    <a class="next" href="{$nextMonthUrl}" title="{$lblNextMonth|ucfirst}">{$lblNextMonth|ucfirst}</a>
		<input id="search" class="inputButton button mainButton" type="submit" name="search" value="{$lblFilter|ucfirst}" />
{/form:filter}
</div>

{option:emptydays}
  {iteration:emptydays}
  <div class="emptyday">
    &nbsp;
  </div>
  {/iteration:emptydays}
{/option:emptydays}

{option:days}
  {iteration:days}
  <div class="day">
    <span>{$days.day}</span>
    {iteration:days.items}
    <p class="{$days.items.color}">
      <a href="{$days.items.full_url}" title="{$days.items.title}">{$days.items.title}</a>
    </p>
    {/iteration:days.items}
  </div>
  {/iteration:days}
{/option:days}
