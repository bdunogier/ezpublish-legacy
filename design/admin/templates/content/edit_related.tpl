{literal}
<script language="JavaScript1.2" type="text/javascript">
<!--
function selectAll()
{
    with (document.editform)
	{
        for (var i=0; i < elements.length; i++)
        {
            if (elements[i].type == 'checkbox' && elements[i].name == 'DeleteRelationIDArray[]' && elements[i].disabled == "")
            elements[i].checked = true;
	    }
    }
}

function deSelectAll()
{
    with (document.editform)
	{
        for (var i=0; i < elements.length; i++)
	    {
            if (elements[i].type == 'checkbox' && elements[i].name == 'DeleteRelationIDArray[]' && elements[i].disabled == "")
            elements[i].checked = false;
	    }
    }
}
//-->
</script>
{/literal}


{* DO NOT EDIT THIS FILE! Use an override template instead. *}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h2 class="context-title">{'Related objects'|i18n( 'design/standard/content/edit' )}</h2>

{* DESIGN: Subline *}<div class="header-subline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{section show=$related_contentobjects|count|gt( 0 )}


    {section show=$grouped_related_contentobjects.images|count|gt( 0 )}

    <table class="list-thumbnails" cellspacing="0">
    <tr>
        {section var=related_object loop=$grouped_related_contentobjects.images}
        <td>
        {related_view_gui view=thumbnail related_object=$related_object}
        </td>
        {delimiter modulo=4}
        </tr><tr>
        {/delimiter}
        {/section}

    </tr>
    </table>

    {/section}

    {section show=$grouped_related_contentobjects.files|count|gt( 0 )}
        <div class="file-detail-list">

            <table class="list" cellspacing="0">
            <tr>
                <th class="checkbox"><img src={'toggle-button-16x16.gif'|ezimage} alt="Toggle selection" onclick="togglestuff( document.children, 'DeleteIDArray[]' ); return false;"/></th>
                <th class="name">{'Attached files'|i18n( 'design/standard/content/edit' )}:</th>
                <th class="class">{'Type'|i18n( 'design/standard/content/edit' )}:</th>
                <th class="filesize">{'Size'|i18n( 'design/standard/content/edit' )}:</th>
                <th class="code">{'XML code'|i18n( 'design/standard/content/edit' )}:</th>
            </tr>

            {section var=related_object loop=$grouped_related_contentobjects.files sequence=array( bglight, bgdark )}
                {related_view_gui view=file related_object=$related_object}
            {/section}

            </table>
        </div>
    {/section}

    {section show=$grouped_related_contentobjects.objects|count|gt( 0 )}
        <div class="related-detail-list">

            <table class="list" cellspacing="0">
            <tr>
                <th class="checkbox"><img src={'toggle-button-16x16.gif'|ezimage} alt="Toggle selection" onclick="togglestuff( document.children, 'DeleteIDArray[]' ); return false;"/></th>
                <th class="name">{'Related content'|i18n( 'design/standard/content/edit' )}:</th>
                <th class="class">{'Type'|i18n( 'design/standard/content/edit' )}:</th>
                <th class="code">{'XML code'|i18n( 'design/standard/content/edit' )}:</th>
            </tr>

            {section var=related_object loop=$grouped_related_contentobjects.objects sequence=array( bglight, bgdark )}
                {related_view_gui view=object related_object=$related_object}
            {/section}

            </table>
        </div>
    {/section}

    {/section}

{* DESIGN: Content END *}</div></div></div>

        <div class="controlbar">

{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

            <div class="block">
                <input class="button" type="submit" name="DeleteRelationButton" value="{'Remove selected'|i18n('design/standard/content/edit')}" title="{'Remove the selected items from the list(s) above. It is only the relations that will be removed. The items will not be deleted.'|i18n( 'design/admin/layout' )}" />
                <input class="button" type="Submit" name="BrowseObjectButton" value="{'Add existing'|i18n('design/standard/content/edit')}" title="{'Add an existing item as a related object.'|i18n( 'design/admin/layout' )}" />
                <input class="button" type="submit" name="UploadFileRelationButton" value="{'Add new'|i18n('design/standard/content/edit')}" title="{'Upload a file. The file will be placed within the location that is specified using the dropdown menu to the left. In addition, the file will be automatically related to the item that is currently being edited.'|i18n( 'design/admin/layout' )}" />
            </div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

        </div>

</div>

{*

<div class="context-block">

<div class="box-header">
<div class="box-tc"><div class="box-ml"><div class="box-mr">
<div class="box-tl"><div class="box-tr">

<h2 class="context-title">{'Upload new related object'|i18n( 'design/standard/content/edit' )}</h2>

<div class="header-subline"></div>

</div></div>
</div></div></div>
</div>

<div class="controlbar">

<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">
        <label>{'Location'|i18n( 'design/standard/content/edit' )}:</label>
        <select	name="UploadRelationLocationChoice" class="combobox locationchoice">
            <option value="auto">{'Automatic'|i18n( 'design/standard/content/edit' )}</option>
        {let root_node_value=ezini( 'LocationSettings', 'RootNode', 'upload.ini' )
             root_node=cond( $root_node_value|is_numeric, fetch( content, node, hash( node_id, $root_node_value ) ),
                             fetch( content, node, hash( node_path, $root_node_value ) ) )}
        {section var=node loop=fetch( content, tree,
                                      hash( parent_node_id, $root_node.node_id,
                                            class_filter_type, include,
                                            class_filter_array, ezini( 'LocationSettings', 'ClassList', 'upload.ini' ),
                                            depth, ezini( 'LocationSettings', 'MaxDepth', 'upload.ini' ),
                                            limit, ezini( 'LocationSettings', 'MaxItems', 'upload.ini' ) ) )}
            <option value="{$node.node_id}">{'&nbsp;'|repeat( sub( $node.depth, $root_node.depth, 1 ) )}{$node.name|wash}</option>
        {/section}
        {/let}
      	</select>

        <input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
        <input class="button" name="UploadRelationFile" type="file" />
        &nbsp;
        <input class="button" type="submit" name="UploadFileRelationButton" value="{'Add new'|i18n('design/standard/content/edit')}" title="{'Upload a file. The file will be placed within the location that is specified using the dropdown menu to the left. In addition, the file will be automatically related to the item that is currently being edited.'|i18n( 'design/admin/layout' )}" />
</div>

</div></div></div></div></div></div>

</div>

</div>

*}

