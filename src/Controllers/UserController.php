<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Components\ChartJsComponent;
use Admin\Components\Component;
use Admin\Components\ModelInfoTableComponent;
use Admin\Components\ModelTableComponent;
use Admin\Components\TabContentComponent;
use Admin\Components\TimelineComponent;
use Admin\Delegates\Buttons;
use Admin\Delegates\Card;
use Admin\Delegates\ChartJs;
use Admin\Delegates\Form;
use Admin\Delegates\Modal;
use Admin\Delegates\ModelTable;
use Admin\Delegates\Row;
use Admin\Delegates\SearchForm;
use Admin\Delegates\Tab;
use Admin\Middlewares\BrowserDetectMiddleware;
use Admin\Models\AdminBrowser;
use Admin\Models\AdminLog;
use Admin\Models\AdminUser;
use Admin\Page;
use Admin\Resources\AdminLogResource;
use Admin\Respond;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Exceptions\MissingQrCodeServiceException;
use PragmaRX\Google2FAQRCode\Google2FA;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * System controller admin panel for displaying the user page.
 */
class UserController extends Controller
{
    /**
     * The model the admin panel controller works with.
     *
     * @var string
     */
    public static $model = AdminUser::class;

    /**
     * Current user of the admin panel.
     *
     * @var AdminUser|null
     */
    protected AdminUser|null $user = null;

    /**
     * Method for processing a modal window when 2FA is enabled, to check the entered administrator password.
     *
     * @param  Respond  $respond
     * @return Respond
     */
    public function showQr(Respond $respond): Respond
    {
        $password = $this->modelInput('password');

        if (Hash::check($password, admin()->password)) {
            $respond->put('modal:put', ['modal-1']);
        } else {
            $respond->toast_error('Wrong password!');
        }
        return $respond;
    }

    /**
     * Method for processing a modal window when 2FA is turned off, to check the entered administrator password.
     *
     * @param  Respond  $respond
     * @return Respond
     */
    public function disableQr(Respond $respond): Respond
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

        return $respond;
    }

    /**
     * Method for generating a modal window to complete the inclusion of 2FA authorization.
     *
     * @param  Form  $form
     * @return array
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     * @throws MissingQrCodeServiceException
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
            str_contains($qr_code, '</svg>')
                ? $form->center()->attr('data-2fa-qr')->appEnd($qr_code)
                : $form->center()->attr('data-2fa-qr')->img()->attr(['src' => $qr_code]),
            $form->p(),
            $form->p()->appEnd(__('admin.2fa_auth_finish_msg2')),
            $form->input('otp', ''),
            $form->hidden('secret', '')->default($secret),
        ];
    }

    /**
     * Method to complete enabling 2FA authorization.
     *
     * @param  Respond  $respond
     * @return Respond
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function twoFaEnable(Respond $respond): Respond
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

        return $respond;
    }

    /**
     * Method for describing the user page.
     *
     * @param  Request  $request
     * @param  Page  $page
     * @param  Card  $card
     * @param  Form  $form
     * @param  Tab  $tab
     * @param  ChartJs  $chartJs
     * @param  SearchForm  $searchForm
     * @param  Buttons  $buttons
     * @param  Row  $row
     * @param  Modal  $modal
     * @param  \Admin\Delegates\ModelTable  $modelTable
     * @return Page
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function index(
        Request $request,
        Page $page,
        Card $card,
        Form $form,
        Tab $tab,
        ChartJs $chartJs,
        SearchForm $searchForm,
        Buttons $buttons,
        Row $row,
        Modal $modal,
        ModelTable $modelTable,
    ): Page {

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
                $modal->submitEvent([$this, 'twoFaEnable']),
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
            ->row(
                $row->column(3)->card(
                    $card
                        ->title('admin.information')
                        ->primaryType()
                        ->card_body()
                        ->view('controllers.user_portfolio', ['user' => $this->model()])
                ),
                $row->column(9)->card(
                    $card->title('admin.edit')
                        ->successType(),
                    $card->tab(
                        $tab->right(),
                        $tab->active(!$request->has('adminlog1_per_page') && !$request->has('adminlog1_page') && !$request->has('q')),
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
                        $tab->if(!admin()->two_factor_confirmed_at)->h1()->appEnd(__('admin.2fa_secure_not_enable_title')),
                        $tab->if(admin()->two_factor_confirmed_at)->h1()->appEnd(__('admin.you_have_enabled_two_factor_authentication')),
                        $tab->p()->appEnd(__('admin.2fa_secure_not_enable_info')),
                        $tab->if(admin()->two_factor_confirmed_at)->buttons(
                            $buttons->danger()
                                ->modal('modal-2')
                                ->attr('data-2fa-disable')
                                ->text(__('admin.2fa_secure_enable_disable'))
                        ),
                        $tab->if(!admin()->two_factor_confirmed_at)->buttons(
                            $buttons->primary()
                                ->modal()
                                ->attr('data-2fa-enable')
                                ->text(__('admin.2fa_secure_enable_button'))
                        )
                    ),
                    $card->tab(
                        $tab->active(request()->has('adminlog1_per_page') || request()->has('adminlog1_page')),
                        $tab->icon_history()->title('admin.timeline'),
                        $tab->use(fn(TabContentComponent $content) => static::timelineComponent(
                            $content,
                            $this->model()->logs(),
                            $this
                        ))
                    ),
                    $card->tab(
                        $tab->icon_internet_explorer()->title('admin.browsers'),
                        $tab->model_table(
                            $modelTable->model(admin()->browsers()),
                            $modelTable->col('admin.name', 'name')->sort(),
                            $modelTable->col('admin.ip', 'ip')->sort(),
                            $modelTable->col('admin.active', function (AdminBrowser $adminBrowser) {
                                if (BrowserDetectMiddleware::$browser && $adminBrowser->id !== BrowserDetectMiddleware::$browser->id) {
                                    return ModelTableComponent::callExtension('input_switcher', [
                                        $adminBrowser->active,
                                        [],
                                        $adminBrowser,
                                        'active'
                                    ]);
                                } else {
                                    return ModelTableComponent::callExtension('badge', [__('admin.current_browser')]);
                                }
                            })->sort(),
                        ),
                    ),
                    $card->tab(
                        $tab->title('admin.activity')->icon_chart_line(),
                        $tab->active($request->has('q')),
                        $tab->chart_js(
                            $chartJs->model($this->model()->logs())
                                ->size(150)
                                ->hasSearch(
                                    $searchForm->date_range('created_at', 'admin.created_at')
                                        ->default(implode(' - ', $this->defaultDateRange()))
                                )
                                ->load(function (ChartJsComponent $component) {

                                    $logTitles = $this->model()->logs()->distinct('title')->pluck('title');

                                    $component->setDefaultDataBetween('created_at', ...$this->defaultDateRange())
                                        ->groupDataByAt('created_at')
                                        ->withCollection($logTitles, function ($title) {
                                            return $this->chart_js->eachPoint($title,
                                                static function ($c) use ($title) {
                                                    return $c->where('title', $title)->count();
                                                });
                                        })->miniChart();
                                }),
                        )
                    ),
                    $card->tab(
                        $tab->title('admin.day_activity')->icon_chart_line(),
                        $tab->chart_js(
                            $chartJs->model($this->model()->logs())->size(200)
                                ->load(function (ChartJsComponent $component) {

                                    $logTitles = $this->model()->logs()->distinct('title')->pluck('title');

                                    $component->setDataBetween('created_at', now()->startOfDay(), now()->endOfDay())
                                        ->groupDataByAt('created_at', 'H:i')
                                        ->withCollection($logTitles, function ($title) {
                                            return $this->chart_js->eachPoint($title, function ($c) use ($title) {
                                                return $c->where('title', $title)->count();
                                            });
                                        })->miniChart();
                                }),
                        )
                    ),
                    $card->footer_form()->withOutRedirectRadios()->setType('edit'),
                )
            );
    }

    /**
     * The method is responsible for generating a timeline in the user page tab.
     *
     * @param $content
     * @param $model
     * @param  Controller  $controller
     * @return void
     */
    public static function timelineComponent(Component $content, $model, Controller $controller): void
    {
        $timeline = TimelineComponent::new();

        $content->column(12)->timeline(
            $timeline->model($model)->setFullBody(),
            $timeline->set_title(function (AdminLog $log) {
                return $log->title.($log->detail ? " <small>({$log->detail})</small>" : '');
            }),
            $timeline->set_icon(fn(AdminLog $log) => $log->icon),
            $timeline->resource(AdminLogResource::class),
            $timeline->set_body(function (TimelineComponent $timelineComponent, AdminLog $log) {
                return ModelInfoTableComponent::create()->withoutRealtime()->model($log)->use(function (ModelInfoTableComponent $table) {
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

    /**
     * Default date interval.
     *
     * @return array
     */
    public function defaultDateRange(): array
    {
        return [
            now()->subDay()->startOfDay()->toDateString(),
            now()->endOfDay()->toDateString(),
        ];
    }

    /**
     * Method event when data is updated.
     *
     * @param $form
     * @return void
     */
    public function on_updated($form): void
    {
        admin_log_success('Changed data', get_class($this->model()), 'far fa-id-card');

        if (isset($form['password']) && $form['password']) {
            admin_log_success('Changed the password', get_class($this->model()), 'fas fa-key');
        }
    }

    /**
     * A method that returns the model with which the controller works.
     *
     * @return Model|AdminUser|string|null
     */
    public function getModel(): Model|AdminUser|string|null
    {
        return admin();
    }

    /**
     * The method that is responsible for logging out the administrator.
     *
     * @param  Respond  $respond
     * @return RedirectResponse
     */
    public function logout(Respond $respond): RedirectResponse
    {
        admin_log_success('Was logout', null, 'fas fa-sign-out-alt');

        Auth::guard('admin')->logout();

        $respond->redirect(route('admin.login'));

        return back();
    }
}
