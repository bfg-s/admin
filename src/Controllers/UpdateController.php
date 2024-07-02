<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\AdminEngine;
use Admin\Components\ChartJsComponent;
use Admin\Components\Component;
use Admin\Components\LoadContentComponent;
use Admin\Components\ModelInfoTableComponent;
use Admin\Components\ModelTableComponent;
use Admin\Components\TabContentComponent;
use Admin\Components\TimelineComponent;
use Admin\Delegates\Buttons;
use Admin\Delegates\Card;
use Admin\Delegates\CardBody;
use Admin\Delegates\ChartJs;
use Admin\Delegates\Form;
use Admin\Delegates\Modal;
use Admin\Delegates\ModelTable;
use Admin\Delegates\Row;
use Admin\Delegates\SearchForm;
use Admin\Delegates\Tab;
use Admin\Facades\Admin;
use Admin\Jobs\UpdateBfgAdminJob;
use Admin\Middlewares\BrowserDetectMiddleware;
use Admin\Models\AdminBrowser;
use Admin\Models\AdminLog;
use Admin\Models\AdminUser;
use Admin\Page;
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
class UpdateController extends Controller
{
    /**
     * The model the admin panel controller works with.
     *
     * @var string
     */
    public static $model = AdminUser::class;

    /**
     * Disable all default tools.
     *
     * @return bool
     */
    public function defaultTools(): bool
    {
        return false;
    }

    /**
     * Method for describing the user page.
     *
     * @param  Page  $page
     * @param  \Admin\Delegates\Card  $card
     * @param  \Admin\Delegates\Row  $row
     * @return Page
     */
    public function index(
        Page $page,
        Card $card,
        CardBody $cardBody,
    ): Page {

        return $page
            ->title('admin.updates')
            ->icon_wrench()
            ->breadcrumb('admin.updates')
            ->card(
                $card->title('admin.updates'),
                $card->card_body(
                    $cardBody->load_content(function (LoadContentComponent $component, Row $row) {

                        $data = json_decode(file_get_contents('https://repo.packagist.org/p2/bfg/admin.json'), true);
                        $last = $data['packages']['bfg/admin'][0];
                        $currentVersion = Admin::version();
                        //$lastRemoteVersion = '6.4.1';
                        $lastRemoteVersion = $last['version'];

                        $component->row(
                            $row->column(6)->info_box()->title('Current version')->body($currentVersion)->icon_code_branch(),
                            $row->column(6)->info_box()->title('Remote version')->body($lastRemoteVersion)->icon_code_branch()->warningType(),
                        );

                        $component->center()->buttons()
                            ->success()
                            ->icon_cogs()
                            ->title('Update')
                            ->click(function (Respond $respond) use ($lastRemoteVersion, $currentVersion) {

                                if ($lastRemoteVersion > $currentVersion) {

                                    UpdateBfgAdminJob::dispatch();

                                    return $respond->toast_success('The update request has been sent to the server. Please wait for the update to complete.');
                                }
                                return $respond->toast_error('You are using the latest version of the system.');
                            });
                    })
                ),
            )->load_content(function (LoadContentComponent $component, Card $card, ModelTable $modelTable, Buttons $buttons) {

                $data = json_decode(file_get_contents('https://packagist.org/packages/list.json?type=bfg-admin-extension'), true);
                $data = collect($data['packageNames'])->map(function (string $package) {
                    $result = json_decode(file_get_contents('https://repo.packagist.org/p2/' . $package . '.json'), true);
                    return $result['packages'][$package][0] ?? null;
                })->filter()->values();

                $component->card(
                    $card->title('Extensions'),
                    $card->model_table(
                        $modelTable->model($data)->perPage(500),
                        $modelTable->col('Name', 'name'),
                        $modelTable->col('Description', 'description'),
                        $modelTable->col('Version', 'version')->badge(),
                        $modelTable->col('License', 'license.0'),
                        $modelTable->col('Authors', function ($model) {
                            $result = [];
                            foreach ($model['authors'] as $item) {
                                $result[] = $item['name'] . (isset($item['email']) ? ' <' . $item['email'] . '>' : '');
                            }
                            return implode('<br>', $result);
                        }),
                        $modelTable->col('Time', 'time')->beautiful_date(),
                        $modelTable->col('Status', function ($model) {
                            $name = $model['name'];
                            return isset(AdminEngine::$extensions[$name])
                                ? (AdminEngine::$extensions[$name] ? 'Enabled' : 'Disabled')
                                : 'Not installed';
                        })->badge(function ($model) {
                            $name = $model['name'];
                            return isset(AdminEngine::$extensions[$name])
                                ? (AdminEngine::$extensions[$name] ? 'success' : 'danger')
                                : 'primary';
                        }),
                        $modelTable->col('Downloaded', function ($model) {
                            $name = $model['name'];
                            return isset(AdminEngine::$installed_extensions[$name]) || isset(AdminEngine::$not_installed_extensions[$name]);
                        })->yes_no(),
                        $modelTable->col('Installed', function ($model) {
                            $name = $model['name'];
                            return isset(AdminEngine::$installed_extensions[$name]);
                        })->yes_no(),
                        $modelTable->buttons(
                            $buttons->success()->icon_cogs()->title('Install')->click(function (Respond $respond, $model) {

                                $name = $model['name'];
                                if (isset(AdminEngine::$installed_extensions[$name])) {

                                    return $respond->toast_error('Extension already installed.');
                                }
                                return $respond->toast_success('Extension installed successfully.');
                            }),
                        ),
                    ),
                );
            });
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
     * A method that returns the model with which the controller works.
     *
     * @return Model|AdminUser|string|null
     */
    public function getModel(): Model|AdminUser|string|null
    {
        return admin();
    }
}
