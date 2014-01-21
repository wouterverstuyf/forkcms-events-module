    <h1 itemprop="name">{$item.title}</h1>
    <article  {option:item.color}class="{$item.color}"{/option:item.color}>
        <p class="meta">
          {* Category*}
          {$lblWritten} {$lblIn} {$lblThe} {$lblCategory} <a href="{$item.category_full_url}" title="{$item.category_title}">{$item.category_title}</a>{option:!item.tags}.{/option:!item.tags}
        </p>

        <div class="eventdate">
          {$lblFrom|ucfirst}
          {$item.begin_date|date:{$dateFormat}:{$LANGUAGE}}
          {$item.begin_date|date:{$timeFormat}:{$LANGUAGE}}
        </div>
        <div class="eventdate">
          {$lblTo|ucfirst}
          {$item.end_date|date:{$dateFormat}:{$LANGUAGE}}
          {$item.end_date|date:{$timeFormat}:{$LANGUAGE}}
        </div>
        {option:item.image}<img src="{$FRONTEND_FILES_URL}/blog/images/source/{$item.image}" alt="{$item.title}" itemprop="image" />{/option:item.image}
        {$item.text}
    </article>
