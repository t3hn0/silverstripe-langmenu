#About
Common function for building LangMenu dropdown for use in template.

#How to use
```html
<% if $LangMenu %>
<select onChange="window.location.href=this.value" class="langswitcher">
	<% loop $LangMenu %>
	<option value="$Page.Link"<% if $Selected %> disabled selected<% end_if %>>$i18n.Lang</option>
    <% end_loop %>
</select>
<% end_if %>
```


#Todo
Add sort order