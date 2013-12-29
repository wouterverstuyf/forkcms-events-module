{include:core/layout/templates/head.tpl}

<body class="{$LANGUAGE} subpage" itemscope itemtype="http://schema.org/WebPage">
    <div id="topWrapper">
        <div class="container">
            <header>
                {* Logo *}
                <div id="logo">
                    <h2><a href="/"><img src="/frontend/themes/vek/core/layout/images/logo.png" alt="VEK - Vlaams Economische Kring" /></a></h2>
                </div>

                {* Navigation *}
                <nav id="mainmenu">
                    {$var|getnavigation:'page':0:2}
                </nav>
            </header>
        </div>
    </div>
    <div id="middleWrapper">
        <div class="container">
          {* Banner *}
          <div id="banner">
          {iteration:positionBanner}
              {option:positionBanner.blockIsHTML}
                  {$positionBanner.blockContent}
              {/option:positionBanner.blockIsHTML}
              {option:!positionBanner.blockIsHTML}
                  {$positionBanner.blockContent}
              {/option:!positionBanner.blockIsHTML}
          {/iteration:positionBanner}
          </div>

          <div id="left">
              <nav id="submenu">
                  {$var|getsubnavigation:'page':{$page.id}:2}
              </nav>
          </div>

          <div id="right">
              {* Breadcrumb *}
              {include:core/layout/templates/breadcrumb.tpl}

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
        </div>
    </div>
    {include:core/layout/templates/footer.tpl}

    <noscript>
        <div class="message notice">
            <h4>{$lblEnableJavascript|ucfirst}</h4>
            <p>{$msgEnableJavascript}</p>
        </div>
    </noscript>

    {* General Javascript *}
    {iteration:jsFiles}
        <script src="{$jsFiles.file}"></script>
    {/iteration:jsFiles}

    {* Theme specific Javascript *}
    <script src="{$THEME_URL}/core/js/triton.js"></script>

    {* Site wide HTML *}
    {$siteHTMLFooter}
</body>
</html>
