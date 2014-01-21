              <h1 itemprop="name">{$title}</h1>

              {option:!items}
                <p>{$msgEventsNoItems}</p>
              {/option:!items}

              {option:items}
              <div id="events">
                {iteration:items}
                  <article {option:items.color}class="{$items.color}"{/option:items.color}>
                    <div class="eventdate">
                      <p class="label">{$lblFrom|ucfirst}</p>
                      {$items.begin_date|date:{$dateFormat}:{$LANGUAGE}}
                      <span>{$items.begin_date|date:{$timeFormat}:{$LANGUAGE}}</span>
                    </div>
                    <div class="eventdate">
                      <p class="label">{$lblTo|ucfirst}</p>
                      {$items.end_date|date:{$dateFormat}:{$LANGUAGE}}
                      <span>{$items.end_date|date:{$timeFormat}:{$LANGUAGE}}</span>
                    </div>
                    <div class="eventitem">
                      <h4><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h4>
                      {option:items.image}<img src="{$FRONTEND_FILES_URL}/blog/images/source/{$items.image}" alt="{$items.title}" />{/option:items.image}
                      {option:!items.introduction}{$items.text}{/option:!items.introduction}
                      {option:items.introduction}{$items.introduction}{/option:items.introduction}
                    </div>
                  </article>
                {/iteration:items}
              </div>
              {include:core/layout/templates/pagination.tpl}
              {/option:items}
          </div>

