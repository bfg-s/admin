<?php

namespace Admin\Controllers;

use Admin\Delegates\Modal;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Lar\Layout\Respond;
use Lar\Layout\Tags\DIV;
use Admin\Components\ModelInfoTableComponent;
use Admin\Components\TabContentComponent;
use Admin\Delegates\Buttons;
use Admin\Delegates\Card;
use Admin\Delegates\ChartJs;
use Admin\Delegates\Column;
use Admin\Delegates\Form;
use Admin\Delegates\SearchForm;
use Admin\Delegates\Tab;
use Admin\Models\AdminLog;
use Admin\Models\AdminUser;
use Admin\Page;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Google2FA;

class UserController extends Controller
{
    /**
     * Static variable Model.
     * @var string
     */
    public static $model = AdminUser::class;
    /**
     * @var AdminUser
     */
    protected $user;

    public function showQr(Respond $respond)
    {
        $password = $this->modelInput('password');

        if (Hash::check($password, admin()->password)) {
            $respond->put('modal:put', ['modal-1']);
        } else {
            $respond->toast_error('Wrong password!');
        }
    }

    public function disableQr(Respond $respond)
    {
        $password = $this->modelInput('password');

        if (Hash::check($password, admin()->password)) {

            admin()->update([
                'two_factor_secret' => null,
                'two_factor_confirmed_at' => null,
            ]);

            $respond->toast_success('2fa is success disable!');
            $respond->reload();

        } else {
            $respond->toast_error('Wrong password!');
        }
    }

    /**
     * @param  Form  $form
     * @return array
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function qrGenerateFinish(Form $form): array
    {
        $google2fa = new Google2FA();

        $secret = $google2fa->generateSecretKey();
        $qr_code = $google2fa->getQRCodeInline(
            config('app.name'),
            admin()->email,
            $secret
        );

        return [
            $form->p(__('admin.2fa_auth_finish_msg')),
            str_contains($qr_code, '</svg>') ? $form->center($qr_code) : $form->center()->img(['src' => $qr_code]),
            $form->p(),
            $form->p(__('admin.2fa_auth_finish_msg2')),
            $form->input('otp', ''),
            $form->hidden('secret', '')->default($secret),
        ];
    }

    /**
     * @param  Request  $request
     * @param  Respond  $respond
     * @return Respond|void
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function twofaEnable(Request $request, Respond $respond)
    {
        $otp = $this->modelInput('otp');
        $secret = $this->modelInput('secret');

        if ($otp && $secret) {

            $google2fa = new Google2FA();

            if ($google2fa->verify($otp, $secret)) {

                admin()->update([
                    'two_factor_secret' => $secret,
                    'two_factor_confirmed_at' => now(),
                ]);

                session(["2fa_checked" => true]);

                $respond->toast_success(__('admin.2fa_is_enabled'));

                return $respond->reload();
            }
        }

        $respond->toast_error(__('admin.2fa_is_wrong'));
    }

    /**
     * @param  Request  $request
     * @param  Page  $page
     * @param  Column  $column
     * @param  Card  $card
     * @param  Form  $form
     * @param  Tab  $tab
     * @param  ChartJs  $chartJs
     * @param  SearchForm  $searchForm
     * @param  Buttons  $buttons
     * @param  Modal  $modal
     * @return Page
     */
    public function index(
        Request $request,
        Page $page,
        Column $column,
        Card $card,
        Form $form,
        Tab $tab,
        ChartJs $chartJs,
        SearchForm $searchForm,
        Buttons $buttons,
        Modal $modal,
    ) {
        $logTitles = $this->model()->logs()->distinct('title')->pluck('title');

        return $page
            ->modal(
                $modal->title(__('admin.2fa_secure_confirm_password'))->closable()->temporary(),
                $modal->submitEvent([$this, 'showQr']),
                $modal->form(
                    $form->p(__('admin.2fa_secure_confirm_password_info')),
                    $form->password('password', 'admin.password')->queryable(),
                ),
                $modal->buttons()
                    ->success()
                    ->icon_check()
                    ->title(__('admin.2fa_secure_confirm_password_confirm'))
                    ->modalSubmit(),
            )
            ->modal(
                $modal->title(__('admin.2fa_auth_finish'))->closable()->temporary(),
                $modal->submitEvent([$this, 'twofaEnable']),
                $modal->form(
                    [$this, 'qrGenerateFinish']
                ),
                $modal->buttons()
                    ->success()
                    ->icon_check()
                    ->title(__('admin.2fa_secure_confirm_password_confirm'))
                    ->modalSubmit()
            )
            ->modal(
                $modal->title(__('admin.2fa_secure_confirm_password'))->closable()->temporary(),
                $modal->submitEvent([$this, 'disableQr']),
                $modal->form(
                    $form->p(__('admin.2fa_secure_confirm_password_info')),
                    $form->password('password', 'admin.password')->queryable(),
                ),
                $modal->buttons()
                    ->success()
                    ->icon_check()
                    ->title(__('admin.2fa_secure_confirm_password_confirm'))
                    ->modalSubmit(),
            )
            ->title($this->model()->name)
            ->icon_user()
            ->breadcrumb('admin.administrator', 'admin.profile')
            ->column(
                $column->num(3)->card(
                    $card
                        ->title('admin.information')
                        ->primaryType()
                        ->card_body()
                        ->view('admin::auth.user_portfolio', ['user' => $this->model()])
                )
            )
            ->column(
                $column->num(9)->card(
                    $card->title('admin.edit')
                        ->successType(),
                    $card->tab(
                        $tab->right(),
                        $tab->active(!$request->has('ltelog_per_page') && !$request->has('ltelog_page') && !$request->has('q')),
                        $tab->icon_cogs()->title('admin.settings'),
                        $tab->form(
                            $form->vertical(),
                            $form->image('avatar', 'admin.avatar'),
                            $form->input('login', 'admin.login_name')
                                ->required()
                                ->unique(AdminUser::class, 'login', $this->model()->id),
                            $form->email('email', 'admin.email_address')
                                ->required()
                                ->unique(AdminUser::class, 'email', $this->model()->id),
                            $form->input('name', 'admin.name')
                                ->required(),
                            $form->divider(__('admin.password')),
                            $form->password('password', 'admin.new_password')
                                ->confirm(),
                        )
                    ),
                    $card->tab(
                        $tab->icon_shield_alt()->title('admin.2fa_secure'),
                        $tab->if(!admin()->two_factor_confirmed_at)->h1(__('admin.2fa_secure_not_enable_title')),
                        $tab->if(admin()->two_factor_confirmed_at)->h1('You have enabled two factor authentication.'),
                        $tab->p(__('admin.2fa_secure_not_enable_info')),
                        $tab->if(admin()->two_factor_confirmed_at)->buttons(
                            $buttons->danger()
                                ->modal('modal-2')
                                ->text('Disable')
                        ),
                        $tab->if(!admin()->two_factor_confirmed_at)->buttons(
                            $buttons->primary()
                                ->modal()
                                ->text(__('admin.2fa_secure_enable_button'))
                        )
                    ),
                    $card->tab(
                        $tab->active(request()->has('ltelog_per_page') || request()->has('ltelog_page')),
                        $tab->icon_history()->title('admin.timeline'),
                        $tab->with(fn(TabContentComponent $content) => static::timelineComponent(
                            $content,
                            $this->model()->logs(),
                            $this
                        ))
                    ),
                    $card->tab(
                        $tab->title('admin.activity')->icon_chart_line(),
                        $tab->active($request->has('q')),
                        $tab->chart_js(
                            $chartJs->model($this->model()->logs())
                                ->hasSearch(
                                    $searchForm->date_range('created_at', 'admin.created_at')
                                        ->default(implode(' - ', $this->defaultDateRange()))
                                )
                                ->setDefaultDataBetween('created_at', ...$this->defaultDateRange())
                                ->groupDataByAt('created_at')
                                ->withCollection($logTitles, function ($title) {
                                    return $this->chart_js->eachPoint($title, static function ($c) use ($title) {
                                        return $c->where('title', $title)->count();
                                    });
                                })->miniChart(),
                        )
                    ),
                    $card->tab(
                        $tab->title('admin.day_activity')->icon_chart_line(),
                        $tab->chart_js(
                            $chartJs->model($this->model()->logs())
                                ->setDataBetween('created_at', now()->startOfDay(), now()->endOfDay())
                                ->groupDataByAt('created_at', 'H:i')
                                ->withCollection($logTitles, function ($title) {
                                    return $this->chart_js->eachPoint($title, function ($c) use ($title) {
                                        return $c->where('title', $title)->count();
                                    });
                                })->miniChart(),
                        )
                    ),
                    $card->footer_form()->withOutRedirectRadios()->setType('edit'),
                )
            );
    }

    public static function timelineComponent($content, $model, Controller $controller)
    {
        $content->div(['col-md-12'])->timeline(
            $controller->timeline->model($model),
            $controller->timeline->set_title(static function (AdminLog $log) {
                return $log->title.($log->detail ? " <small>({$log->detail})</small>" : '');
            }),
            $controller->timeline->set_body(static function (DIV $div, AdminLog $log) {
                $div->p0()->model_info_table()->model($log)->when(static function (ModelInfoTableComponent $table) {
                    $table->row('IP', 'ip')->copied();
                    $table->row('URL', 'url')->to_prepend_link();
                    $table->row('Route', 'route')->copied();
                    $table->row('Method', 'method')->copied();
                    $table->row('User Agent', 'user_agent')->copied();
                    $table->row('Session ID', 'session_id')->copied();
                    $table->row('WEB ID', 'web_id')->copied();
                });
            }),
        );
    }

    public function defaultDateRange()
    {
        return [
            now()->subDay()->startOfDay()->toDateString(),
            now()->endOfDay()->toDateString(),
        ];
    }

    public function on_updated($form)
    {
        admin_log_success('Changed data', get_class($this->model()), 'far fa-id-card');

        if (isset($form['password']) && $form['password']) {
            admin_log_success('Changed the password', get_class($this->model()), 'fas fa-key');
        }
    }

    /**
     * @return Model|AdminUser|string|null
     */
    public function getModel()
    {
        return admin();
    }

    /**
     * @param  Respond  $respond
     * @return Respond
     */
    public function logout(Respond $respond)
    {
        admin_log_success('Was logout', null, 'fas fa-sign-out-alt');

        Auth::guard('admin')->logout();

        $respond->redirect(route('admin.login'));

        return $respond;
    }
}
