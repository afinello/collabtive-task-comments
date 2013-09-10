{if $showhtml != "no"}
{include file="header.tpl" jsload="ajax" jsload1="tinymce"}
{include file="tabsmenue-project.tpl" msgstab = "active"}

<div id="content-left">
	<div id="content-left-in">
		<div class="msgs">
			<div class="breadcrumb">
				<a href="manageproject.php?action=showproject&amp;id={$project.ID}" title="{$project.name}"><img src="./templates/standard/images/symbols/projects.png" alt="" />{$project.name|truncate:25:"...":true}</a>
				<a href="managetask.php?action=showproject&amp;id={$project.ID}"><img src="./templates/standard/images/symbols/msgs.png" alt="" />{#messages#}</a>
				<a href="managetasklist.php?action=showtasklist&amp;id={$project.ID}&amp;tlid={$tasklist.ID}"><img src="./templates/standard/images/symbols/msgs.png" alt="" />{$message.name|truncate:50:"...":true}</a><span>&nbsp;/...</span>
			</div>

			<h1 class="second"><img src="./templates/standard/images/symbols/msgs.png" alt="" />{$message.name}</h1>
{/if}

			<div class="block_in_wrapper">
				<h2>{#reply#}</h2>
				
				<form class="main" method="post"  enctype="multipart/form-data" action="managetask.php?action=comment&amp;id={$pid}&amp;tid={$task.ID}" {literal}onsubmit="return validateCompleteForm(this);"{/literal}>
					<fieldset>
						<div class="row">
							<label for="text">{#text#}:</label>
							<div class="editor"><textarea name="text" id="text"  realname="{#text#}" rows="3" cols="1"></textarea></div>
						</div>
						
						{*Attach*}
						<div id = "files-attach" class="blinded" style = "display:none;clear:both;">
							<div class="row">
								<label for = "thefiles">{#attachfile#}:</label>
								<select name = "thefiles" id = "thefiles">
									<option value = "0">{#chooseone#}</option>
									{section name = file loop=$files}
									<option value = "{$files[file].ID}">{$files[file].name}</option>
									{/section}
									{section name = file loop=$myprojects[project].files}
									<option value = "{$myprojects[project].files[file].ID}">{$myprojects[project].files[file].name}</option>
									{/section}
								</select>
							</div>
						</div>
						
						<div class="row">
							<label for="tags">{#tags#}:</label>
							<input type="text" name="tags" id="tags" realname="{#tags#}" />
						</div>

						<input type = "hidden" name="sendto[]" id="sendto[]" value = "all" />
						<input type = "hidden" name="desc" id="desc" value = "" />

						<div class="row-butn-bottom">
							<label>&nbsp;</label>
							<button type="submit" onfocus="this.blur()">{#reply#}</button>
							{if $showhtml == "no"}
								{if $reply != "a"}
									<button onclick="blindtoggle('form_reply_b');toggleClass('add_replies','add-active','add');toggleClass('add_butn_replies','butn_link_active','butn_link');toggleClass('sm_replies','smooth','nosmooth');return false;" onfocus="this.blur()">{#cancel#}</button>
								{/if}
							{/if}
							{if $reply == "a"}
								<button onclick="blindtoggle('form_reply_a');toggleClass('add_reply_a','reply-active','reply');toggleClass('sm_replies_a','smooth','nosmooth');return false;" onfocus="this.blur()">{#cancel#}</button>
							{/if}
						</div>

					</fieldset>
				</form>
			</div> {*block_in_wrapper end*}

{if $showhtml != "no"}
			<div class="content-spacer"></div>
		</div> {*Msgs END*}
	</div> {*content-left-in END*}
</div> {*content-left END*}

{include file="sidebar-a.tpl"}
{include file="footer.tpl"}
{/if}