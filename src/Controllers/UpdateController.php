<?php

declare(strict_types=1);

namespace Admin\Controllers;

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
            ->title('Updates')
            ->icon_wrench()
            ->breadcrumb('Updates')
            ->card(
                $card->title('Updates'),
                $card->card_body(
                    $cardBody->load_content(function (LoadContentComponent $component, Row $row) {

                        $data = json_decode(file_get_contents('https://repo.packagist.org/p2/bfg/admin.json'), true);
                        $last = $data['packages']['bfg/admin'][0];
                        $currentVersion = Admin::version();
                        $lastRemoteVersion = '6.4.1';
                        //$lastRemoteVersion = $last['version'];

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
                                    return $respond->toast_success('The update request has been sent to the server. Please wait for the update to complete.');
                                }
                                return $respond->toast_error('You are using the latest version of the system.');
                            });
                    })
                ),
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
     * A method that returns the model with which the controller works.
     *
     * @return Model|AdminUser|string|null
     */
    public function getModel(): Model|AdminUser|string|null
    {
        return admin();
    }
}
