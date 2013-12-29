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
