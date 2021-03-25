{* -- By default, check admin login ----------------------------------------- *}

{block name="check-auth"}
    {check_auth role="ADMIN" resource="{block name="check-resource"}{/block}" module="{block name="check-module"}{/block}" access="{block name="check-access"}{/block}" login_tpl="/admin/login"}
{/block}

{* -- Define some stuff for Smarty ------------------------------------------ *}
{config_load file='variables.conf'}

{* -- Declare assets directory, relative to template base directory --------- *}
{declare_assets directory='assets'}

{* Set the default translation domain, that will be used by {intl} when the 'd' parameter is not set *}
{default_translation_domain domain='bo.default'}

{block name="no-return-functions"}{/block}

<!DOCTYPE html>
<html lang="{$lang_code}">
<head>
    <meta charset="utf-8">

    <title>{block name="page-title"}Default Page Title{/block} - {intl l='Thelia Back Office'}</title>

    <link rel="shortcut icon" href="{image file='assets/img/favicon.ico'}" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    {block name="meta"}{/block}

    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />
    {* -- Bootstrap CSS section --------------------------------------------- *}

    {block name="before-bootstrap-css"}{/block}

    <link rel="stylesheet" href="{stylesheet file='assets/sass/main.scss' filters='scssphp'}">

    {block name="after-bootstrap-css"}{/block}

    {* -- Admin CSS section ------------------------------------------------- *}

    {block name="before-admin-css"}{/block}

    {block name="after-admin-css"}{/block}

    {* Modules css are included here *}

    {hook name="main.head-css" location="head_css" }

    {* HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries *}
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    {javascripts file='assets/js/libs/respond.min.js'}
    <script src="{$asset_url}"></script>
    {/javascripts}
    <![endif]-->
</head>

<body>
    <div id="wrapper">

        {* display top bar only if admin is connected *}
        {loop name="top-bar-auth" type="auth" role="ADMIN"}

            {* -- Brand bar section ------------------------------------------------- *}

            {hook name="main.before-topbar" location="before_topbar" }

            <!-- Navigation -->
            <nav class="navbar navbar-expand-xl navbar-dark navbar-static-top p-0" role="navigation">

                    <div class="row flex-grow-1 justify-content-between">
                        <div class="col-12 col-xl-auto justify-content-between">
                            <a class="navbar-brand mr-xl-auto" href="{url path='/admin/home'}">
                                {images file='assets/img/logo-white.png'}
                                    <img src="{$asset_url}" alt="{intl l='Version %ver' ver="{$THELIA_VERSION}"}">
                                    <span>{intl l='Version %ver' ver="{$THELIA_VERSION}"}</span>
                                {/images}
                            </a>
                            <button type="button" class="navbar-toggler float-right mt-3 mr-3" data-toggle="collapse" data-target=".navbar-collapse">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                        </div>
                        <div class="col-12 col-xl-auto">

                            <ul class="nav navbar-top-links navbar-right">
                                {hook name="main.topbar-top" }

                                <li class="nav-item">
                                    <a href="{navigate to="index"}" title="{intl l='View site'}" target="_blank" class="nav-link"><i class="fas fa-eye"></i> {intl l="View shop"}</a>
                                </li>
                                <li class="dropdown nav-item">
                                    <button class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fas fa-user"></i> {admin attr="firstname"} {admin attr="lastname"}
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a class="profile dropdown-item" href="{url path='admin/configuration/administrators/view'}"><i class="fas fa-edit"></i> {intl l="Profil"}</a></li>
                                        <li><a class="logout dropdown-item" href="{url path='admin/logout'}" title="{intl l='Close administation session'}"><i class="fas fa-power-off"></i> {intl l="Logout"}</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown nav-item">
                                    {loop type="lang" name="ui-lang" id={lang attr='id'} backend_context="1"}
                                    <button class="dropdown-toggle" data-toggle="dropdown">
                                        <img src="{image file="assets/img/flags/{$CODE}.png"}" alt="{$TITLE}" /> {$CODE|ucfirst}
                                        <span class="caret"></span>
                                    </button>
                                    {/loop}

                                    <ul class="dropdown-menu">
                                        {loop type="lang" name="ui-lang" backend_context="1"}
                                            <li><a href="{url path="{navigate to="current"}" lang={$CODE}}" class="dropdown-item"><img src="{image file="assets/img/flags/{$CODE}.png"}" alt="{$TITLE}" /> {$CODE|ucfirst}</a></li>
                                        {/loop}
                                    </ul>
                                </li>
                            </ul>
                        </div>

                        <div class="col-12">

                            <div class="navbar-default sidebar" role="navigation">
                                <div class="sidebar-nav collapse navbar-collapse">

                                    {include file="includes/main-menu.html"}

                                    {hook name="main.inside-topbar" location="inside_topbar" }
                                </div>
                                <!-- /.sidebar-collapse -->
                            </div>
                        </div>
                    </div>


                <!-- /.navbar-static-side -->

                {hook name="main.after-topbar" location="after_topbar" }
            </nav>

            <div id="page-wrapper">

                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">{block name="page-title"}{/block}</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->

                {* -- Main page content section ----------------------------------------- *}
                {hook name="main.before-content" location="before_content"}

                <div class="row">
                    {block name="main-content"}Put here the content of the template{/block}
                </div>

                {hook name="main.after-content" location="after_content"}

            </div>
        {/loop}

        {hook name="main.before-footer" location="before_footer" }

        <footer class="footer">
            <div class="text-center">
                <p class="text-center">&copy; Thelia <time datetime="{'Y-m-d'|date}">{'Y'|date}</time>
                - <a href="http://www.openstudio.fr/" target="_blank">{intl l='Published by OpenStudio'}</a>
                - <a href="http://thelia.net/forum" target="_blank">{intl l='Thelia support forum'}</a>
                - <a href="http://thelia.net/modules" target="_blank">{intl l='Thelia contributions'}</a>
                </p>

                {hook name="main.in-footer" location="in_footer" }

            </div>
            <ul id="follow-us" class="list-unstyled list-inline">
                <li class="list-inline-item">
                    <a href="https://twitter.com/theliaecommerce" target="_blank">
                        <span class="icon-twitter"></span>
                    </a>
                </li>
                <li class="list-inline-item">
                    <a href="https://www.facebook.com/theliaecommerce" target="_blank">
                        <span class="icon-facebook"></span>
                    </a>
                </li>
                <li class="list-inline-item">
                    <a href="https://github.com/thelia/thelia" target="_blank">
                        <span class="icon-github"></span>
                    </a>
                </li>
            </ul>
        </footer>

        {hook name="main.after-footer" location="after_footer" }
    </div> <!-- #wrapper -->

	{* -- Javascript section ------------------------------------------------ *}

	{block name="before-javascript-include"}{/block}
    <!-- <script src="//code.jquery.com/jquery-2.0.3.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script>
        if (typeof jQuery == 'undefined') {
            {javascripts file='assets/js/libs/jquery.js'}
            document.write(unescape("%3Cscript src='{$asset_url}' %3E%3C/script%3E"));
            {/javascripts}
        }
    </script>

	{block name="after-javascript-include"}{/block}

    {javascripts file='assets/js/bootstrap/bootstrap.bundle.js'}
        <script src="{$asset_url}"></script>
    {/javascripts}

    <!-- plus utilisé dans le code -->
    <!-- {javascripts file='assets/js/libs/jquery.toolbar.min.js'}
        <script src="{$asset_url}"></script>
    {/javascripts} -->

    <!--{javascripts file='assets/js/libs/metisMenu.min.js'}
        <script src="{$asset_url}"></script>
    {/javascripts}-->

    {block name="javascript-initialization"}{/block}

    {javascripts file='assets/js/main.js'}
        <script src="{$asset_url}"></script>
    {/javascripts}

	{* Modules scripts are included now *}
    {hook name='main.footer-js' location="footer_js"}

    {block name="javascript-last-call"}{/block}
    </body>
</html>
