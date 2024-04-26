
<!doctype html>
<html lang="{{ $app('i18n')->locale }}" class="uk-height-1-1 app-page-login" data-base="@base('/')" data-route="@route('/')" data-locale="{{ $app('i18n')->locale }}">
<head>
    <meta charset="UTF-8">
    <title>@lang('Authenticate Please!')</title>
    <link rel="icon" href="@base('/favicon.png')" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <style>

        .login-container {
            width: 480px;
            max-width: 90%;
        }

        .login-dialog {
            box-shadow: 0 30px 75px 0 rgba(10, 25, 41, 0.2);
        }

        .login-image {
            background-image: url(@url('assets:app/media/logo-plain.svg'));
            background-repeat: no-repeat;
            background-size: contain;
            background-position: 50% 50%;
            height: 80px;
        }

        svg path,
        svg rect,
        svg circle {
            fill: currentColor;
        }

    </style>

    {{ $app->assets($app['app.assets.base'], $app['debug'] ? time() : $app['cockpit/version']) }}
    {{ $app->assets(['assets:lib/uikit/js/components/form-password.min.js'], $app['debug'] ? time() : $app['cockpit/version']) }}


    @trigger('app.login.header')

</head>
<body class="login-page uk-height-viewport uk-flex uk-flex-middle uk-flex-center">

    <div class="uk-position-relative login-container uk-animation-scale uk-container-vertical-center" role="main" riot-view>

        <form class="uk-form" method="post" action="@route('/auth/check')" onsubmit="{ submit }">

            <div class="uk-panel-space uk-nbfc uk-text-center uk-animation-slide-bottom" if="{$user}">

                <p>
                    <cp-gravatar email="{ $user.email }" size="80" alt="{ $user.name || $user.user }" if="{$user}"></cp-gravatar>
                </p>
                <hr class="uk-width-1-2 uk-container-center">
                <p class="uk-text-center uk-text-bold uk-text-muted uk-text-upper uk-margin-top">
                    @lang('Welcome back!')
                </p>

            </div>

            <div id="login-dialog" class="login-dialog uk-panel-box uk-panel-space uk-nbfc" show="{!$user}">

                <div name="header" class="uk-panel-space uk-text-bold uk-text-center">

                    <div class="uk-margin login-image"></div>

                    <h2 class="uk-text-bold uk-text-truncate"><span>{{ $app['app.name'] }}</span></h2>

                    <div class="uk-animation-shake uk-margin-top" if="{ error }">
                        <span class="uk-badge uk-badge-outline uk-text-danger">{ error }</span>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-text-small uk-text-bold uk-text-upper uk-margin-small-bottom">@lang('Username')</label>
                    <input ref="user" class="uk-form-large uk-width-1-1" type="text" aria-label="@lang('Username')" placeholder="" autofocus required>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-password uk-width-1-1">
                        <label class="uk-text-small uk-text-bold uk-text-upper uk-margin-small-bottom">@lang('Password')</label>
                        <input ref="password" class="uk-form-large uk-width-1-1" type="password" aria-label="@lang('Password')" placeholder="" required>
                        <a href="#" class="uk-form-password-toggle" data-uk-form-password>@lang('Show')</a>
                    </div>
                </div>

                <div class="uk-margin-large-top">
                    <button class="uk-button uk-button-outline uk-button-large uk-text-primary uk-width-1-1">@lang('Authenticate')</button>
                     <a class="uk-button uk-button-default uk-button-large uk-text-primary uk-width-1-1"  href="@route('/OIDC_Keycloak_Cockpit/oauth2_keycloak.php')" if="{!$user}" style="margin-top:10px">
			<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" fill="#000000" width="20" height="20"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <circle cx="512" cy="512" r="512" style="fill:#008aaa"></circle> <path d="M786.2 395.5h-80.6c-1.5 0-3-.8-3.7-2.1l-64.7-112.2c-.8-1.3-2.2-2.1-3.8-2.1h-264c-1.5 0-3 .8-3.7 2.1l-67.3 116.4-64.8 112.2c-.7 1.3-.7 2.9 0 4.3l64.8 112.2 67.2 116.5c.7 1.3 2.2 2.2 3.7 2.1h264.1c1.5 0 3-.8 3.8-2.1L702 630.6c.7-1.3 2.2-2.2 3.7-2.1h80.6c2.7 0 4.8-2.2 4.8-4.8V400.4c-.1-2.7-2.3-4.9-4.9-4.9zM477.5 630.6l-20.3 35c-.3.5-.8 1-1.3 1.3-.6.3-1.2.5-1.9.5h-40.3c-1.4 0-2.7-.7-3.3-2l-60.1-104.3-5.9-10.3-21.6-36.9c-.3-.5-.5-1.1-.4-1.8 0-.6.2-1.3.5-1.8l21.7-37.6 65.9-114c.7-1.2 2-2 3.3-2H454c.7 0 1.4.2 2.1.5.5.3 1 .7 1.3 1.3l20.3 35.2c.6 1.2.5 2.7-.2 3.8l-65.1 112.8c-.3.5-.4 1.1-.4 1.6 0 .6.2 1.1.4 1.6l65.1 112.7c.9 1.5.8 3.1 0 4.4zm202.1-116.7L658 550.8l-5.9 10.3L592 665.4c-.7 1.2-1.9 2-3.3 2h-40.3c-.7 0-1.3-.2-1.9-.5-.5-.3-1-.7-1.3-1.3l-20.3-35c-.9-1.3-.9-2.9-.1-4.2l65.1-112.7c.3-.5.4-1.1.4-1.6 0-.6-.2-1.1-.4-1.6l-65.1-112.8c-.7-1.2-.8-2.6-.2-3.8l20.3-35.2c.3-.5.8-1 1.3-1.3.6-.4 1.3-.5 2.1-.5h40.4c1.4 0 2.7.7 3.3 2l65.9 114 21.7 37.6c.3.6.5 1.2.5 1.8 0 .4-.2 1-.5 1.6z" style="fill:#fff"></path> </g></svg>
			&nbsp;@lang('Sign in with Keycloak')
		     </a>
		 </div>
            </div>

            <p class="uk-text-center" if="{!$user}">
		<a class="uk-button uk-button-link uk-link-muted" href="@route('/auth/forgotpassword')">@lang('Forgot Password?')</a>
	    </p>

        </form>

        @trigger('app.login.footer')


        <script type="view/script">

            this.error = false;
            this.$user  = null;

            var redirectTo = '{{ htmlspecialchars($redirectTo, ENT_QUOTES, 'UTF-8') }}';

	    submit(e) {

                e.preventDefault();

                this.error = false;

                App.request('/auth/check', {
                    auth : {user:this.refs.user.value, password:this.refs.password.value },
		    csrf : "{{ $app('csrf')->token('login') }}"
                }).then(function(data) {

                    if (data && data.success) {

                        this.$user = data.user;

                        setTimeout(function(){
                            App.reroute(redirectTo);
                        }, 2000)

                    } else {

                        this.error = '@lang("Login failed")';

                        App.$(this.header).addClass('uk-bg-danger uk-contrast');
                        App.$('#login-dialog').removeClass('uk-animation-shake');

                        setTimeout(function(){
                            App.$('#login-dialog').addClass('uk-animation-shake');
                        }, 50);
                    }

                    this.update();

                }.bind(this), function(res) {
                    App.ui.notify(res && (res.message || res.error) ? (res.message || res.error) : 'Login failed.', 'danger');
                });

                return false;
            }

            // i18n for uikit-formPassword
            UIkit.components.formPassword.prototype.defaults.lblShow = '@lang("Show")';
            UIkit.components.formPassword.prototype.defaults.lblHide = '@lang("Hide")';

        </script>

    </div>

</body>
</html>
