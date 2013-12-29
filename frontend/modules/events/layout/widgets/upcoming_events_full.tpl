{*
	variables that are available:
	- {$widgetUpcomingEventsFull}:
*}

{option:widgetUpcomingEventsFull}
	<section id="UpcomingEvents">
		<header>
			<h3>{$lblUpcomingEvents|ucfirst}</h3>
		</header>
		<ul>
			{iteration:widgetUpcomingEventsFull}
				<li>
					<div class="date">
						<time itemprop="datePublished" datetime="{$widgetUpcomingEventsFull.begin_date|date:'d-M'}"><span>{$widgetUpcomingEventsFull.begin_date|date:'d':{$LANGUAGE}}</span>{$widgetUpcomingEventsFull.begin_date|date:'M':{$LANGUAGE}}</time>
					</div>
					<h4><a href="{$widgetUpcomingEventsFull.full_url}" title="{$widgetUpcomingEventsFull.title}">{$widgetUpcomingEventsFull.title}</a></h4>
					{$widgetUpcomingEventsFull.introduction}
					<p class="more">
						<a href="{$widgetUpcomingEventsFull.full_url}" title="{$widgetUpcomingEventsFull.title}">{$lblReadMore|ucfirst}</a>
					</p>
				</li>
			{/iteration:widgetUpcomingEventsFull}
		</ul>
		<footer>
			<p>
				<a href="{$var|geturlforblock:'events'}" class="buttongrey">{$lblAllEvents|ucfirst}</a>
			</p>
		</footer>
	</section>
{/option:widgetUpcomingEventsFull}
