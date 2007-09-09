{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{def $file_results=$test_result[2]
     $res_elements_by_err=$file_results.result_elements_by_error_code}

{* {$file_results.safe_mode} *}

{*if count( $file_results.result_elements_by_error_code.1 )}All is ok{/if*}

<h3>{$result_number}. {'Insufficient directory permissions'|i18n( 'design/standard/setup/tests' )}</h3>

{* Insufficient directory permissions section *}
{if and( is_set( $res_elements_by_err.3 ), count( $res_elements_by_err.3 ) )}
<h3>{'Insufficient directory permissions'|i18n( 'design/standard/setup/tests' )}</h3>

{set-block variable=dir_list scope=root}{foreach $res_elements_by_err.3 as $res}{delimiter} {/delimiter}<i>{$res.file}</i>{/foreach}{/set-block}

<p>{'eZ publish cannot write to some important directories, without this the setup cannot finish and parts of eZ publish will fail.'|i18n( 'design/standard/setup/tests' )}<br/>
{'The affected directories are: %dir_list'|i18n( 'design/standard/setup/tests',,hash( '%dir_list', $dir_list ) )}</p>

{set-block variable=dir_list scope=root}{foreach $res_elements_by_err.3 as $res}{delimiter} {/delimiter}{$res.file}{/foreach}{/set-block}

{if $file_results.user_info.has_extension}
<h3>{'Shell commands'|i18n( 'design/standard/setup/tests' )}</h3>
<p>{"These shell commands will give proper permission to the webserver."|i18n( 'design/standard/setup/tests' )}</p>
<pre class="example">cd {$file_results.current_path}
chmod -R ug+rwx {$dir_list}
chown -R {$file_results.user_info.user_name}:{$file_results.user_info.group_name} {$dir_list}</pre>

<h3>{'Alternative shell commands'|i18n( 'design/standard/setup/tests' )}</h3>
<p>{"If you don't have permissions to change the ownership you can try these commands."|i18n( 'design/standard/setup/tests' )}</p>
<pre class="example">cd {$file_results.current_path}
chmod -R a+rwx {$dir_list}</pre>
{else}
<p>{"eZ publish could not detect the user and group of the webserver.
If you know the user and group of the web server it is recommended to change the ownership of the files to match this user and group.
To do this you need to change the %chown commands under Alternative shell commands."|i18n( 'design/standard/setup/tests',, hash( '%chown', '<tt class="note">chown</tt>' ) )}</p>

<h3>{'Shell commands'|i18n( 'design/standard/setup/tests' )}</h3>
<p>{"These shell commands will give proper permission to the webserver."|i18n( 'design/standard/setup/tests' )}</p>
<pre class="example">cd {$file_results.current_path}
chmod -R a+rwx {$dir_list}</pre>

<h3>{'Alternative shell commands'|i18n( 'design/standard/setup/tests' )}</h3>
<p>{"These commands will setup the permission more correctly, but require knowledge about the running webserver."|i18n( 'design/standard/setup/tests' )}</p>
<pre class="example">chmod -R og+rwx {$dir_list}
chown -R nouser:nouser {$dir_list}</pre>

<blockquote class="note">
<p><b>{'Note'|i18n( 'design/standard/setup/tests' )}:</b> {'The %user_expr must be changed to your webserver username and groupname.'|i18n( 'design/standard/setup/tests',, hash( '%user_expr', '<tt>nouser:nouser</tt>' ) )}</p>
</blockquote>
{/if}
{/if}

{* Nonexistent directories section *}
{if and( is_set( $res_elements_by_err.2 ), count( $res_elements_by_err.2 ) )}
<h3>{'Missed some directories'|i18n( 'design/standard/setup/tests' )}</h3>
{set-block variable=dir_list scope=root}{foreach $res_elements_by_err.2 as $res}{delimiter} {/delimiter}<i>{$res.file}</i>{/foreach}{/set-block}
<p>{'eZ publish cannot create some important directories, without this the setup cannot finish and parts of eZ publish will fail.'|i18n( 'design/standard/setup/tests' )}<br/>
{'The nonexistent directories are: %dir_list'|i18n( 'design/standard/setup/tests',,hash( '%dir_list', $dir_list ) )}</p>

{set-block variable=dir_list scope=root}{foreach $res_elements_by_err.2 as $res}{delimiter} \<br />{/delimiter}{$res.file}{/foreach}{/set-block}
<p>{"You can try the following shell commands to create necessary directories:"|i18n( 'design/standard/setup/tests' )}</p>
<pre class="example">cd {$file_results.current_path}
mkdir -p {$dir_list}</pre>
{/if}

{* Existent directories which are a files *}
{if and( is_set( $res_elements_by_err.4 ), count( $res_elements_by_err.4 ) )}
<h3>{'Files instead necessary directories'|i18n( 'design/standard/setup/tests' )}</h3>
{set-block variable=dir_list scope=root}{foreach $res_elements_by_err.4 as $res}{delimiter} {/delimiter}<i>{$res.file}</i>{/foreach}{/set-block}
<p>{'eZ Publish cannot create some important directories, because there are an files instead of these directories in the same places with the same names.
You should replace these files with appropriate directories and give necessary permissions to them.
Without this the setup cannot finish and parts of eZ Publish will fail.'|i18n( 'design/standard/setup/tests' )}<br/>
{'The affected directories (files) are: %dir_list'|i18n( 'design/standard/setup/tests',,hash( '%dir_list', $dir_list ) )}</p>
{/if}

