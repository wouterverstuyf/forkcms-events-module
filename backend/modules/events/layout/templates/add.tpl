{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblEvents|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<label for="title">{$lblTitle|ucfirst}</label>
	{$txtTitle} {$txtTitleError}

	<div id="pageUrl">
		<div class="oneLiner">
			{option:detailURL}<p><span><a href="{$detailURL}/{$item.url}">{$detailURL}/<span id="generatedUrl"></span></a></span></p>{/option:detailURL}
			{option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
		</div>
	</div>


	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td id="leftColumn">

						<div class="box">
							<div class="heading">
								<h3>
									<label for="body">{$lblText|ucfirst}</label>
								</h3>
							</div>
							<div class="optionsRTE">
								{$txtText} {$txtTextError}
							</div>
						</div>
						<div class="box">
							<div class="heading">
								<div class="oneLiner">
									<h3>
										<label for="introduction">{$lblSummary|ucfirst}</label>
									</h3>
								</div>
							</div>
							<div class="optionsRTE">
								{$txtIntroduction} {$txtIntroductionError}
							</div>
						</div>						
						<div class="box">
							<div class="heading">
								<h3>{$lblImage|ucfirst}</h3>
							</div>
							<div class="options">
								<p>
									<label for="image">{$lblImage|ucfirst}</label>
									{$fileImage} {$fileImageError}
								</p>
							</div>
						</div>

					</td>

					<td id="sidebar">

							<div class="box">
								<div class="heading">
									<h3>
										<label for="beginDateDate">{$lblBeginDate|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
									</h3>
								</div>
								<div class="options">
									<div class="oneLiner">
										<p>{$txtBeginDateDate} {$txtBeginDateDateError}</p>
										<p><label for="beginDateTime">{$lblAt|ucfirst}</label></p>
										<p>{$txtBeginDateTime} {$txtBeginDateTimeError}</p>
									</div>
								</div>
							</div>

							<div class="box">
								<div class="heading">
									<h3>
										<label for="endDateDate">{$lblEndDate|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
									</h3>
								</div>
								<div class="options">
									<div class="oneLiner">
										<p>{$txtEndDateDate} {$txtEndDateDateError}</p>
										<p><label for="endDateTime">{$lblAt|ucfirst}</label></p>
										<p>{$txtEndDateTime} {$txtEndDateTimeError}</p>
									</div>
								</div>
							</div>

							<div class="box">
								<div class="heading">
									<h3>
										<label for="categoryId">{$lblCategory|ucfirst}</label>
									</h3>
								</div>
								<div class="options">
									{$ddmCategoryId} {$ddmCategoryIdError}
								</div>
							</div>


					</td>
				</tr>
			</table>
		</div>

		<div id="tabSEO">
			{include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblPublish|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}