{* Custom edit template for log in user view *}

<form enctype="multipart/form-data" method="post" action={concat("/content/edit/",$object.id,"/",$edit_version,"/",$edit_language|not|choose(array($edit_language,"/"),''))|ezurl}>

<div class="log">
    <div class="edit">

        {include uri="design:content/edit_related.tpl"}

        <div class="object_header">
            <h1>{"Create new blog entry"|i18n("design/blog/layout")}</h1>
        </div>
	<input type="hidden" name="MainNodeID" value="{$main_node_id}" />

        {include uri="design:content/edit_validation.tpl"}

        {include uri="design:content/edit_info.tpl"}

        {include uri="design:content/edit_attribute.tpl"}

        <div class="controls">
            <input class="defaultbutton" type="submit" name="PublishButton" value="{'Post'|i18n('design/standard/content/edit')}" />
            <input class="button" type="submit" name="DiscardButton" value="{'Discard'|i18n('design/standard/content/edit')}" />
        </div>

    </div>
</div>


</form>